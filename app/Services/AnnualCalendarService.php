<?php

namespace App\Services;

use App\Models\AnnualCalendarActivity;
use App\Models\TrainingBatch;
use Carbon\Carbon;

class AnnualCalendarService
{
    public function __construct(
        private readonly ScheduleRecalculationService $recalculator,
        private readonly ConflictCheckerService        $conflictChecker,
        private readonly WorkingDayService             $workingDayService,
        private readonly ConflictAlertService          $conflictAlerts,
    ) {}

    /**
     * Link a batch (Sequence) to an annual calendar activity and apply the
     * effective-date priority rule (override > activity plan > existing start_date).
     */
    public function linkActivity(TrainingBatch $batch, AnnualCalendarActivity $activity): array
    {
        $batch->update(['annual_calendar_activity_id' => $activity->id]);

        return $this->applyEffectiveDate($batch->refresh());
    }

    /**
     * Unlink without touching dates — the batch keeps whatever date it currently has,
     * per rule 4 (never rewrite a plan/override just because the link changed).
     */
    public function unlinkActivity(TrainingBatch $batch): void
    {
        $batch->update(['annual_calendar_activity_id' => null]);
    }

    /**
     * Set a start-date override on the batch (the "Sequence" in the task's vocabulary).
     * End date is never overridden directly — it is recalculated from duration + working-day
     * rules once the effective start is resolved. Does not touch the annual calendar's plan.
     */
    public function setOverride(TrainingBatch $batch, string $startDate): array
    {
        $batch->update(['override_start_date' => $startDate]);

        return $this->applyEffectiveDate($batch->refresh());
    }

    /**
     * Clear the override. Effective date falls back to the annual calendar's
     * planned date if linked, otherwise the batch's own existing start_date stands.
     */
    public function clearOverride(TrainingBatch $batch): array
    {
        $batch->update(['override_start_date' => null]);

        return $this->applyEffectiveDate($batch->refresh());
    }

    /**
     * Single source of truth for the effective start-date rule. Priority:
     * 1. override_start_date (explicit Sequence override)
     * 2. annualCalendarActivity.planned_start_date
     * 3. batch's own current start_date (plain Sequence scheduling, unchanged)
     *
     * Once the effective start is resolved, the anchor's end date is recalculated from
     * its phase's duration + working-day rules (never naively shifted) — then the existing
     * recalculateFrom() mechanism propagates the same delta to every subsequent Sequence
     * item, preserving locked schedules/manual edits/WI assignments and re-checking conflicts.
     * If nothing has been generated yet, just moves start_date — generate() will pick it up.
     */
    private function applyEffectiveDate(TrainingBatch $batch): array
    {
        $result = ['seminar_conflicts' => [], 'wi_conflicts' => [], 'locked_obstacles' => []];

        $target = $batch->override_start_date
            ?? $batch->annualCalendarActivity?->planned_start_date
            ?? $batch->start_date;

        $newStart = Carbon::parse($target);
        $target   = $newStart->toDateString();
        $current  = $batch->start_date->toDateString();

        if ($target === $current) {
            return $result;
        }

        $delta  = Carbon::parse($current)->diffInDays($newStart, false);
        $anchor = $batch->schedules()->orderBy('sequence')->first();

        if ($anchor) {
            $phase = $anchor->phase;

            if ($phase) {
                $this->workingDayService->setUnit($batch->organizational_unit_id);
                $durationDays = $phase->duration_unit === 'hour' ? 1 : $phase->duration;
                $workDays     = in_array((int) $phase->work_days, [6, 7]) ? (int) $phase->work_days : 5;
                $newEnd       = $this->workingDayService->computeEndDate($newStart, $durationDays, $phase->day_type, $workDays);
            } else {
                // Phase definition gone — fall back to preserving the anchor's existing span.
                $newEnd = $anchor->end_date->copy()->addDays($delta);
            }

            $anchor->update([
                'start_date'            => $newStart->toDateString(),
                'end_date'              => $newEnd->toDateString(),
                'is_anchor'             => true,
                'recalculation_source'  => 'override',
                'recalculated_at'       => now(),
            ]);

            $result = $this->recalculator->recalculateFrom($anchor, $delta);
        }

        $batch->update(['start_date' => $target]);

        $batch->refresh();
        $hasConflict = $this->conflictChecker->hasAnyBatchConflict($batch);
        $batch->update(['has_conflict_alert' => $hasConflict]);

        $this->conflictAlerts->evaluateBatch($batch);

        return $result;
    }
}
