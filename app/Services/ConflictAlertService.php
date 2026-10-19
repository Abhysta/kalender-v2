<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\ConflictAlert;
use App\Models\ConflictRule;
use App\Models\TrainingBatch;
use App\Models\WiAssignment;

class ConflictAlertService
{
    public function __construct(
        private readonly ConflictCheckerService $conflictChecker,
    ) {}

    /**
     * Evaluate the enabled conflict rules against a batch's current (post-recalculation)
     * schedule, persist deduplicated alerts, and resolve alerts whose conflict disappeared.
     * Idempotent: running this repeatedly against an unchanged schedule never creates
     * duplicate rows and never re-opens alerts an admin ignored.
     *
     * This is a pure evaluator over already-computed dates — it never changes a schedule's
     * dates itself, and it reuses ConflictCheckerService's detection, not a second engine.
     */
    public function evaluateBatch(TrainingBatch $batch): array
    {
        $openKeys = [];

        if ($this->ruleActive('group_overlap')) {
            foreach ($batch->schedules()->get()->filter(fn ($s) => $s->isGrouped()) as $s) {
                foreach ($this->conflictChecker->getConflictingSchedules($s) as $other) {
                    $openKeys[] = $this->upsertAlert('group_overlap', $s, $other);
                }
            }
        }

        if ($this->ruleActive('wi_overlap')) {
            foreach ($batch->schedules()->with('wiAssignments')->get() as $s) {
                foreach ($s->wiAssignments as $wi) {
                    $others = WiAssignment::where('widyaiswara_id', $wi->widyaiswara_id)
                        ->where('id', '!=', $wi->id)
                        ->whereDate('start_date', '<=', $wi->end_date)
                        ->whereDate('end_date', '>=', $wi->start_date)
                        ->with('schedule')
                        ->get();

                    foreach ($others as $otherWi) {
                        if (! $otherWi->schedule) {
                            continue;
                        }
                        $openKeys[] = $this->upsertAlert('wi_overlap', $s, $otherWi->schedule, (string) $wi->widyaiswara_id);
                    }
                }
            }
        }

        $scheduleIds = $batch->schedules()->pluck('id');

        ConflictAlert::where('status', 'open')
            ->where(function ($q) use ($scheduleIds) {
                $q->whereIn('batch_schedule_id', $scheduleIds)
                  ->orWhereIn('related_batch_schedule_id', $scheduleIds);
            })
            ->whereNotIn('dedup_key', $openKeys)
            ->update(['status' => 'resolved', 'resolved_at' => now()]);

        return $openKeys
            ? ConflictAlert::whereIn('dedup_key', $openKeys)->get()->all()
            : [];
    }

    private function ruleActive(string $code): bool
    {
        return ! ConflictRule::where('code', $code)->where('is_active', false)->exists();
    }

    private function upsertAlert(string $ruleType, BatchSchedule $a, BatchSchedule $b, ?string $details = null): string
    {
        [$first, $second] = $a->id < $b->id ? [$a, $b] : [$b, $a];
        $dedupKey          = "{$ruleType}:{$first->id}:{$second->id}";

        $existing = ConflictAlert::where('dedup_key', $dedupKey)->first();
        if ($existing) {
            if ($existing->status === 'resolved') {
                $existing->update(['status' => 'open', 'detected_at' => now(), 'resolved_at' => null, 'resolved_by' => null]);
            }
            return $dedupKey;
        }

        $rule = ConflictRule::where('code', $ruleType)->first();

        ConflictAlert::create([
            'conflict_rule_id'          => $rule?->id,
            'rule_type'                 => $ruleType,
            'batch_schedule_id'         => $first->id,
            'related_batch_schedule_id' => $second->id,
            'conflict_group_id'        => $a->conflict_group_id,
            'status'                    => 'open',
            'details'                   => $details,
            'dedup_key'                 => $dedupKey,
            'detected_at'               => now(),
        ]);

        return $dedupKey;
    }
}
