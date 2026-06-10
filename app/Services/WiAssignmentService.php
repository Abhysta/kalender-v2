<?php

namespace App\Services;

use App\Models\BatchSchedule;
use App\Models\Widyaiswara;
use App\Models\WiAssignment;

class WiAssignmentService
{
    public function __construct(
        private readonly ConflictCheckerService $conflictChecker,
    ) {}

    /**
     * Assign a WI to a schedule. Validates active status and date conflict.
     * Returns created WiAssignment or throws \RuntimeException on conflict.
     */
    public function assign(BatchSchedule $schedule, int $wiId, ?string $notes, int $assignedBy): WiAssignment
    {
        $wi = Widyaiswara::findOrFail($wiId);

        if (! $wi->is_active) {
            throw new \RuntimeException("WI {$wi->name} tidak aktif.");
        }

        if ($this->conflictChecker->hasWiConflict(
            $wiId,
            $schedule->start_date->toDateString(),
            $schedule->end_date->toDateString()
        )) {
            throw new \RuntimeException("WI {$wi->name} memiliki konflik jadwal pada tanggal tersebut.");
        }

        return WiAssignment::create([
            'batch_schedule_id' => $schedule->id,
            'widyaiswara_id'    => $wiId,
            'start_date'        => $schedule->start_date,
            'end_date'          => $schedule->end_date,
            'notes'             => $notes,
            'assigned_by'       => $assignedBy,
        ]);
    }

    public function remove(WiAssignment $assignment): void
    {
        $assignment->delete();
    }
}
