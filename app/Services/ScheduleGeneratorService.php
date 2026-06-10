<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\TemplatePhase;
use App\Models\TrainingBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleGeneratorService
{
    public function __construct(
        private readonly WorkingDayService    $workingDayService,
        private readonly ConflictCheckerService $conflictChecker,
    ) {}

    /**
     * Generate all batch_schedules for a given TrainingBatch.
     * Deletes existing draft schedules before regenerating.
     * Returns array with seminar_conflicts and wi_conflicts detected after generation.
     */
    public function generate(TrainingBatch $batch): array
    {
        $batch->load('template.phases');

        DB::transaction(function () use ($batch) {
            $batch->schedules()->where('is_locked', false)->delete();

            $this->workingDayService->setUnit($batch->organizational_unit_id);

            $phases   = $batch->template->phases;
            $cursor   = Carbon::parse($batch->start_date);
            $lastEnd  = null;

            foreach ($phases as $phase) {
                [$startDate, $endDate] = $this->calculatePhaseDates($phase, $cursor, $batch->start_date);

                BatchSchedule::create([
                    'training_batch_id' => $batch->id,
                    'template_phase_id' => $phase->id,
                    'name'              => $phase->name,
                    'start_date'        => $startDate->toDateString(),
                    'end_date'          => $endDate->toDateString(),
                    'sequence'          => $phase->sequence,
                    'activity_type'     => $phase->activity_type,
                    'conflict_group'    => $phase->conflict_group,
                    'color'             => $phase->color,
                    'is_alert'          => $phase->is_alert,
                ]);

                $lastEnd = $endDate;
                $cursor = $endDate->copy()->addDay();
            }

            $batch->update([
                'status'   => 'generated',
                'end_date' => $lastEnd?->toDateString(),
            ]);
        });

        $batch->refresh();

        $seminarConflicts = $batch->schedules()
            ->where('conflict_group', 'seminar')
            ->get()
            ->filter(fn($s) => $this->conflictChecker->hasSeminarConflict(
                $s->start_date->toDateString(),
                $s->end_date->toDateString(),
                $batch->id,
                $s->id
            ))->values()->all();

        $wiConflicts = [];
        foreach ($batch->schedules()->with('wiAssignments')->get() as $s) {
            foreach ($s->wiAssignments as $wi) {
                if ($this->conflictChecker->hasWiConflict(
                    $wi->widyaiswara_id,
                    $wi->start_date->toDateString(),
                    $wi->end_date->toDateString(),
                    $wi->id
                )) {
                    $wiConflicts[] = $wi->load('widyaiswara');
                }
            }
        }

        $hasConflict = ! empty($seminarConflicts) || ! empty($wiConflicts);
        $batch->update(['has_conflict_alert' => $hasConflict]);

        return [
            'batch'             => $batch->fresh(),
            'seminar_conflicts' => $seminarConflicts,
            'wi_conflicts'      => $wiConflicts,
        ];
    }

    private function calculatePhaseDates(TemplatePhase $phase, Carbon $cursor, string $batchStartDate): array
    {
        $batchStart = Carbon::parse($batchStartDate);

        // Apply offset_days relative to batch start (negative = H-n before start)
        if ($phase->offset_days !== 0) {
            $base = $batchStart->copy()->addDays($phase->offset_days);
        } else {
            $base = $cursor->copy();
        }

        $rawWorkDays = (int) ($phase->work_days ?? 5);
        $workDays    = in_array($rawWorkDays, [6, 7]) ? $rawWorkDays : 5;

        // Ensure start lands on a working/valid day
        if ($phase->day_type === 'working_day') {
            $start = $this->workingDayService->nextWorkingDay($base, $workDays);
        } else {
            $start = $base->copy();
            // Skip holidays for calendar_day
            while ($this->workingDayService->isHoliday($start)) {
                $start->addDay();
            }
        }

        // Calculate end date based on duration
        $durationDays = $phase->duration_unit === 'hour'
            ? 1
            : $phase->duration;

        if ($durationDays <= 1) {
            $end = $start->copy();
        } elseif ($phase->day_type === 'working_day') {
            $end = $this->workingDayService->addWorkingDays($start, $durationDays - 1, $workDays);
        } else {
            $end = $this->workingDayService->addCalendarDays($start, $durationDays - 1);
        }

        return [$start, $end];
    }
}
