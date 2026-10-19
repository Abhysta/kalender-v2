<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\WiAssignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConflictCheckerService
{
    /**
     * Find all cross-batch conflicts for any conflict group globally (not just a hardcoded
     * 'seminar' literal) — any two grouped activities in the SAME group (matched by canonical
     * conflict_group_id when resolved, else legacy conflict_group string) that overlap dates.
     * Returns pairs of conflicting schedules.
     */
    public function getGroupConflicts(): Collection
    {
        $grouped = BatchSchedule::with(['batch.organizationalUnit', 'batch.template', 'conflictGroupRef'])
            ->where(function ($q) {
                $q->whereNotNull('conflict_group_id')->orWhere('conflict_group', '!=', '');
            })
            ->orderBy('start_date')
            ->get()
            ->filter(fn (BatchSchedule $s) => $s->isGrouped())
            ->values();

        $conflicts = collect();

        for ($i = 0; $i < $grouped->count(); $i++) {
            for ($j = $i + 1; $j < $grouped->count(); $j++) {
                $a = $grouped[$i];
                $b = $grouped[$j];

                if ($a->training_batch_id === $b->training_batch_id) {
                    continue;
                }

                if (! $this->sameGroup($a, $b)) {
                    continue;
                }

                if ($this->datesOverlap($a->start_date, $a->end_date, $b->start_date, $b->end_date)) {
                    $conflicts->push(['a' => $a, 'b' => $b, 'type' => 'group']);
                }
            }
        }

        return $conflicts;
    }

    /**
     * Find all WI schedule conflicts globally.
     */
    public function getWiConflicts(): Collection
    {
        $assignments = WiAssignment::with(['widyaiswara', 'schedule.batch'])
            ->orderBy('widyaiswara_id')
            ->orderBy('start_date')
            ->get();

        $conflicts  = collect();
        $byWi       = $assignments->groupBy('widyaiswara_id');

        foreach ($byWi as $wiId => $wiAssignments) {
            $list = $wiAssignments->values();
            for ($i = 0; $i < $list->count(); $i++) {
                for ($j = $i + 1; $j < $list->count(); $j++) {
                    $a = $list[$i];
                    $b = $list[$j];

                    if ($this->datesOverlap($a->start_date, $a->end_date, $b->start_date, $b->end_date)) {
                        $conflicts->push(['a' => $a, 'b' => $b, 'type' => 'wi']);
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check if a new WI assignment overlaps with existing ones.
     */
    public function hasWiConflict(int $wiId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $query = WiAssignment::where('widyaiswara_id', $wiId)
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if a grouped activity overlaps with other batches' activities in the SAME
     * conflict group (global, excluding same batch). Matches by canonical conflict_group_id
     * when the schedule has one; falls back to the legacy conflict_group string otherwise —
     * so pre-canonicalization data keeps working without a backfill.
     */
    public function hasGroupConflict(BatchSchedule $schedule, ?int $excludeScheduleId = null): bool
    {
        $query = $this->groupConflictQuery($schedule, $excludeScheduleId);

        return $query !== null && $query->exists();
    }

    /**
     * Same matching rule as hasGroupConflict(), but returns the actual conflicting
     * schedules (used where the caller needs to record/alert on each one, not just
     * know whether any exist).
     */
    public function getConflictingSchedules(BatchSchedule $schedule, ?int $excludeScheduleId = null): Collection
    {
        $query = $this->groupConflictQuery($schedule, $excludeScheduleId);

        return $query === null ? collect() : $query->get();
    }

    private function groupConflictQuery(BatchSchedule $schedule, ?int $excludeScheduleId = null)
    {
        if (! $schedule->isGrouped()) {
            return null; // group membership is explicit — never inferred, never assumed
        }

        $query = BatchSchedule::where('training_batch_id', '!=', $schedule->training_batch_id)
            ->where(function ($q) use ($schedule) {
                if ($schedule->conflict_group_id) {
                    $q->where('conflict_group_id', $schedule->conflict_group_id);
                }
                if (! empty($schedule->conflict_group)) {
                    $q->orWhere('conflict_group', $schedule->conflict_group);
                }
            })
            ->whereDate('start_date', '<=', $schedule->end_date)
            ->whereDate('end_date', '>=', $schedule->start_date);

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query;
    }

    public function getAllConflicts(): array
    {
        return [
            'group' => $this->getGroupConflicts(),
            'wi'    => $this->getWiConflicts(),
            'total' => $this->getGroupConflicts()->count() + $this->getWiConflicts()->count(),
        ];
    }

    /**
     * Check if a specific batch currently has any group or WI conflicts.
     */
    public function hasAnyBatchConflict(\App\Models\TrainingBatch $batch): bool
    {
        $batch->loadMissing('schedules.wiAssignments');

        foreach ($batch->schedules as $schedule) {
            if ($schedule->isGrouped() && $this->hasGroupConflict($schedule, $schedule->id)) {
                return true;
            }

            foreach ($schedule->wiAssignments as $wi) {
                if ($this->hasWiConflict(
                    $wi->widyaiswara_id,
                    $wi->start_date->toDateString(),
                    $wi->end_date->toDateString(),
                    $wi->id
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    private function sameGroup(BatchSchedule $a, BatchSchedule $b): bool
    {
        if ($a->conflict_group_id && $b->conflict_group_id) {
            return $a->conflict_group_id === $b->conflict_group_id;
        }

        return ! empty($a->conflict_group) && $a->conflict_group === $b->conflict_group;
    }

    private function datesOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lte($bEnd) && $aEnd->gte($bStart);
    }
}
