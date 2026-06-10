<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\WiAssignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConflictCheckerService
{
    /**
     * Find all seminar conflicts globally (across all batches).
     * Returns pairs of conflicting schedules.
     */
    public function getSeminarConflicts(): Collection
    {
        $seminars = BatchSchedule::with(['batch.organizationalUnit', 'batch.template'])
            ->where('conflict_group', 'seminar')
            ->orderBy('start_date')
            ->get();

        $conflicts = collect();

        for ($i = 0; $i < $seminars->count(); $i++) {
            for ($j = $i + 1; $j < $seminars->count(); $j++) {
                $a = $seminars[$i];
                $b = $seminars[$j];

                if ($a->training_batch_id === $b->training_batch_id) {
                    continue;
                }

                if ($this->datesOverlap($a->start_date, $a->end_date, $b->start_date, $b->end_date)) {
                    $conflicts->push(['a' => $a, 'b' => $b, 'type' => 'seminar']);
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
     * Check if a new seminar overlaps with existing ones (global, excluding same batch).
     */
    public function hasSeminarConflict(string $startDate, string $endDate, int $batchId, ?int $excludeScheduleId = null): bool
    {
        $query = BatchSchedule::where('conflict_group', 'seminar')
            ->where('training_batch_id', '!=', $batchId)
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query->exists();
    }

    public function getAllConflicts(): array
    {
        return [
            'seminar' => $this->getSeminarConflicts(),
            'wi'      => $this->getWiConflicts(),
            'total'   => $this->getSeminarConflicts()->count() + $this->getWiConflicts()->count(),
        ];
    }

    /**
     * Check if a specific batch currently has any seminar or WI conflicts.
     */
    public function hasAnyBatchConflict(\App\Models\TrainingBatch $batch): bool
    {
        $batch->loadMissing('schedules.wiAssignments');

        foreach ($batch->schedules as $schedule) {
            if ($schedule->conflict_group === 'seminar') {
                if ($this->hasSeminarConflict(
                    $schedule->start_date->toDateString(),
                    $schedule->end_date->toDateString(),
                    $batch->id,
                    $schedule->id
                )) {
                    return true;
                }
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

    private function datesOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lte($bEnd) && $aEnd->gte($bStart);
    }
}
