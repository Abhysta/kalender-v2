<?php

namespace App\Http\Controllers;

use App\Models\BatchSchedule;
use App\Models\Holiday;
use App\Models\OrganizationalUnit;
use App\Models\TrainingBatch;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $schedules = BatchSchedule::with([
            'batch.template',
            'batch.organizationalUnit',
            'wiAssignments.widyaiswara',
            'phase',
        ])->get();

        $batches = TrainingBatch::with(['template', 'organizationalUnit'])
            ->latest()
            ->get();

        $units = OrganizationalUnit::orderBy('name')->get();

        $events = $schedules->map(fn($s) => [
            'id'            => $s->id,
            'batchId'       => $s->training_batch_id,
            'batchName'     => $s->batch?->name ?? '—',
            'name'          => $s->name,
            'start'         => $s->start_date?->toDateString(),
            'end'           => $s->end_date?->toDateString(),
            'color'         => $s->color ?: '#0369A1',
            'isAlert'       => $s->is_alert,
            'activityType'  => $s->activity_type,
            'conflictGroup' => $s->conflict_group,
            'unitId'        => $s->batch?->organizational_unit_id,
            'unit'          => $s->batch?->organizationalUnit?->name,
            'wis'           => $s->wiAssignments->map(fn($w) => $w->widyaiswara?->name)->filter()->values(),
            'workDays'      => in_array((int)($s->phase?->work_days ?? 5), [6, 7])
                                   ? (int) $s->phase->work_days
                                   : 5,
        ])->filter(fn($e) => $e['start'] && $e['end'])->values();

        $batchList = $batches->map(fn($b) => [
            'id'              => $b->id,
            'name'            => $b->name,
            'templateName'    => $b->template?->name ?? '—',
            'unit'            => $b->organizationalUnit?->name,
            'unitId'          => $b->organizational_unit_id,
            'status'          => $b->status,
            'startDate'       => $b->start_date?->toDateString(),
            'endDate'         => $b->end_date?->toDateString(),
            'conflictAlert'   => (bool) $b->has_conflict_alert,
        ])->values();

        $holidays = Holiday::pluck('date')->map(fn($d) => $d->format('Y-m-d'))->flip()->all();

        return view('admin.calendar', compact('events', 'batchList', 'units', 'holidays'));
    }
}
