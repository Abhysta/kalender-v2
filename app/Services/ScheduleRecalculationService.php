<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\TrainingBatch;
use Carbon\Carbon;

class ScheduleRecalculationService
{
    public function __construct(
        private readonly ConflictCheckerService $conflictChecker,
        private readonly WorkingDayService      $workingDayService,
    ) {}

    /**
     * Shift all subsequent non-locked schedules (and their WI assignments) by $delta calendar days.
     * Marks each shifted schedule as recalculation_source='recalculated'.
     * Locked schedules are skipped but reported as obstacles.
     * Returns ['seminar_conflicts', 'wi_conflicts', 'locked_obstacles'].
     */
    public function recalculateFrom(BatchSchedule $anchor, int $delta): array
    {
        $result = ['seminar_conflicts' => [], 'wi_conflicts' => [], 'locked_obstacles' => []];

        if ($delta === 0) {
            return $result;
        }

        $subsequent = BatchSchedule::where('training_batch_id', $anchor->training_batch_id)
            ->where('sequence', '>', $anchor->sequence)
            ->orderBy('sequence')
            ->get();

        foreach ($subsequent as $s) {
            if ($s->is_locked) {
                $result['locked_obstacles'][] = $s;
                continue;
            }

            $newStart = $s->start_date->copy()->addDays($delta);
            $newEnd   = $s->end_date->copy()->addDays($delta);

            // Shift WI assignments first (before conflict check uses the new dates)
            foreach ($s->wiAssignments as $wi) {
                $wi->update([
                    'start_date' => $wi->start_date->copy()->addDays($delta)->toDateString(),
                    'end_date'   => $wi->end_date->copy()->addDays($delta)->toDateString(),
                ]);
            }

            $s->update([
                'start_date'           => $newStart->toDateString(),
                'end_date'             => $newEnd->toDateString(),
                'is_anchor'            => false,
                'recalculation_source' => 'recalculated',
                'recalculated_at'      => now(),
            ]);

            // Seminar conflict check
            if ($s->conflict_group === 'seminar') {
                if ($this->conflictChecker->hasSeminarConflict(
                    $newStart->toDateString(),
                    $newEnd->toDateString(),
                    $s->training_batch_id,
                    $s->id
                )) {
                    $result['seminar_conflicts'][] = $s;
                }
            }

            // WI conflict check on shifted assignments
            $s->refresh();
            foreach ($s->wiAssignments as $wi) {
                if ($this->conflictChecker->hasWiConflict(
                    $wi->widyaiswara_id,
                    $wi->start_date->toDateString(),
                    $wi->end_date->toDateString(),
                    $wi->id
                )) {
                    $result['wi_conflicts'][] = $wi->load('widyaiswara');
                }
            }
        }

        // Sync batch end_date to last schedule's end_date
        $last = BatchSchedule::where('training_batch_id', $anchor->training_batch_id)
            ->orderByDesc('sequence')
            ->first();

        if ($last) {
            $anchor->batch->update(['end_date' => $last->end_date->toDateString()]);
        }

        return $result;
    }

    /**
     * Recalculate all non-draft batches affected by a holiday change (add/update/delete).
     * Re-runs proper working-day-aware date calculation from the earliest affected schedule.
     * Preserves WI assignments (shifted proportionally), skips locked schedules.
     * $fromDate: earliest date to start recalculating from (holiday date or min of old/new dates).
     * $unitId: null = global holiday (affects all batches); int = only that unit's batches.
     */
    public function recalculateForHolidayChange(string $fromDate, ?int $unitId): array
    {
        $query = TrainingBatch::where('status', '!=', 'draft')
            ->whereHas('schedules', fn($q) => $q->whereDate('end_date', '>=', $fromDate));

        if ($unitId !== null) {
            $query->where('organizational_unit_id', $unitId);
        }

        $batches = $query->get();

        $allResults = ['seminar_conflicts' => [], 'wi_conflicts' => [], 'locked_obstacles' => []];

        foreach ($batches as $batch) {
            $this->workingDayService->setUnit($batch->organizational_unit_id);
            $res = $this->recalculateBatchFromDate($batch, $fromDate);

            $hasConflict = ! empty($res['seminar_conflicts']) || ! empty($res['wi_conflicts']);
            $batch->update(['has_conflict_alert' => $hasConflict]);

            foreach (['seminar_conflicts', 'wi_conflicts', 'locked_obstacles'] as $key) {
                $allResults[$key] = array_merge($allResults[$key], $res[$key]);
            }
        }

        return $allResults;
    }

    private function recalculateBatchFromDate(TrainingBatch $batch, string $fromDate): array
    {
        $result = ['seminar_conflicts' => [], 'wi_conflicts' => [], 'locked_obstacles' => []];

        $schedules = $batch->schedules()->with(['phase', 'wiAssignments'])->orderBy('sequence')->get();

        // Find index of first schedule whose end_date >= fromDate (the earliest affected)
        $firstIdx = null;
        foreach ($schedules as $idx => $schedule) {
            if ($schedule->end_date->format('Y-m-d') >= $fromDate) {
                $firstIdx = $idx;
                break;
            }
        }

        if ($firstIdx === null) {
            return $result;
        }

        // Cursor: day after the previous (unaffected) schedule, or the first affected schedule's current start
        $cursor = $firstIdx > 0
            ? $schedules[$firstIdx - 1]->end_date->copy()->addDay()
            : $schedules[$firstIdx]->start_date->copy();

        $batchStart = $batch->start_date->copy();

        foreach ($schedules->slice($firstIdx) as $schedule) {
            $phase = $schedule->phase;

            if ($schedule->is_locked) {
                $result['locked_obstacles'][] = $schedule;
                $cursor = $schedule->end_date->copy()->addDay();
                continue;
            }

            if (! $phase) {
                $cursor = $schedule->end_date->copy()->addDay();
                continue;
            }

            $workDays = in_array((int) $phase->work_days, [6, 7]) ? (int) $phase->work_days : 5;

            // offset_days phases anchor to batch start; sequential phases follow cursor
            $base = $phase->offset_days !== 0
                ? $batchStart->copy()->addDays($phase->offset_days)
                : $cursor->copy();

            if ($phase->day_type === 'working_day') {
                $newStart = $this->workingDayService->nextWorkingDay($base, $workDays);
            } else {
                $newStart = $base->copy();
                while ($this->workingDayService->isHoliday($newStart)) {
                    $newStart->addDay();
                }
            }

            $durationDays = $phase->duration_unit === 'hour' ? 1 : (int) $phase->duration;

            if ($durationDays <= 1) {
                $newEnd = $newStart->copy();
            } elseif ($phase->day_type === 'working_day') {
                $newEnd = $this->workingDayService->addWorkingDays($newStart, $durationDays - 1, $workDays);
            } else {
                $newEnd = $this->workingDayService->addCalendarDays($newStart, $durationDays - 1);
            }

            $oldStart   = $schedule->start_date->copy();
            $oldEnd     = $schedule->end_date->copy();
            $startDelta = (int) $oldStart->diffInDays($newStart, false);

            if ($newStart->format('Y-m-d') !== $oldStart->format('Y-m-d')
                || $newEnd->format('Y-m-d') !== $oldEnd->format('Y-m-d')
            ) {
                foreach ($schedule->wiAssignments as $wi) {
                    $wi->update([
                        'start_date' => $wi->start_date->copy()->addDays($startDelta)->toDateString(),
                        'end_date'   => $wi->end_date->copy()->addDays($startDelta)->toDateString(),
                    ]);
                }

                $schedule->update([
                    'start_date'           => $newStart->toDateString(),
                    'end_date'             => $newEnd->toDateString(),
                    'recalculation_source' => 'recalculated',
                    'recalculated_at'      => now(),
                ]);

                if ($schedule->conflict_group === 'seminar') {
                    if ($this->conflictChecker->hasSeminarConflict(
                        $newStart->toDateString(),
                        $newEnd->toDateString(),
                        $schedule->training_batch_id,
                        $schedule->id
                    )) {
                        $result['seminar_conflicts'][] = $schedule;
                    }
                }

                $schedule->refresh();
                foreach ($schedule->wiAssignments as $wi) {
                    if ($this->conflictChecker->hasWiConflict(
                        $wi->widyaiswara_id,
                        $wi->start_date->toDateString(),
                        $wi->end_date->toDateString(),
                        $wi->id
                    )) {
                        $result['wi_conflicts'][] = $wi->load('widyaiswara');
                    }
                }
            }

            $cursor = $newEnd->copy()->addDay();
        }

        $last = BatchSchedule::where('training_batch_id', $batch->id)
            ->orderByDesc('sequence')
            ->first();

        if ($last) {
            $batch->update(['end_date' => $last->end_date->toDateString()]);
        }

        return $result;
    }
}
