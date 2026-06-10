<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\TrainingBatch;
use App\Models\TrainingTemplate;
use App\Services\ConflictCheckerService;
use App\Services\ScheduleGeneratorService;
use App\Services\ScheduleRecalculationService;
use App\Services\TrainingBatchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrainingBatchController extends Controller
{
    public function __construct(
        private readonly ScheduleGeneratorService      $generator,
        private readonly ScheduleRecalculationService  $recalculator,
        private readonly TrainingBatchService          $batchService,
        private readonly ConflictCheckerService        $conflictChecker,
    ) {}

    public function index()
    {
        $user    = Auth::user();
        $batches = $user->applyOwnership(
            TrainingBatch::with(['template', 'organizationalUnit'])->withCount('schedules')->latest()
        )->get();

        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $user      = Auth::user();
        $templates = $user->applyOwnership(
            TrainingTemplate::where('is_active', true)
        )->get();
        $units = OrganizationalUnit::where('is_active', true)->get();

        return view('admin.batches.create', compact('templates', 'units'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'training_template_id'   => ['required', 'exists:training_templates,id'],
            'batch_number'           => ['required', 'integer', 'min:1'],
            'year'                   => ['required', 'integer', 'min:2020', 'max:2099'],
            'name'                   => ['required', 'string', 'max:255'],
            'start_date'             => ['required', 'date'],
            'participant_count'      => ['required', 'integer', 'min:1'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
        ]);

        $batch = $this->batchService->create($data, $user);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch berhasil dibuat. Silakan generate jadwal.');
    }

    public function show(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        $batch->load(['template', 'organizationalUnit', 'schedules.wiAssignments.widyaiswara', 'schedules.phase']);
        return view('admin.batches.show', compact('batch'));
    }

    public function destroy(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        $batch->delete();
        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch berhasil dihapus.');
    }

    public function generate(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        try {
            $result = $this->generator->generate($batch);

            $batch->schedules()->update(['generated_by' => Auth::id()]);

            $warnings = [];

            if (! empty($result['seminar_conflicts'])) {
                $names      = collect($result['seminar_conflicts'])->pluck('name')->join(', ');
                $warnings[] = "Konflik seminar: {$names}";
            }

            if (! empty($result['wi_conflicts'])) {
                $wiNames    = collect($result['wi_conflicts'])->map(fn($w) => $w->widyaiswara?->name)->filter()->join(', ');
                $warnings[] = "Konflik WI: {$wiNames}";
            }

            $message = 'Jadwal berhasil di-generate.' . (! empty($warnings) ? ' ⚠ ' . implode(' | ', $warnings) : '');

            return redirect()->route('admin.batches.show', $batch)
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal generate jadwal: ' . $e->getMessage()]);
        }
    }

    public function updateSchedule(Request $request, TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);

        $data = $request->validate([
            'schedule_id' => ['required', 'exists:batch_schedules,id'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'gte:start_date'],
            'notes'       => ['nullable', 'string'],
        ]);

        $schedule = $batch->schedules()->where('id', $data['schedule_id'])
            ->where('is_locked', false)
            ->firstOrFail();

        $oldEnd = $schedule->end_date->copy();
        $newEnd = Carbon::parse($data['end_date']);
        $delta  = $oldEnd->diffInDays($newEnd, false); // signed: + = later, - = earlier

        $schedule->update([
            'start_date'           => $data['start_date'],
            'end_date'             => $data['end_date'],
            'notes'                => $data['notes'],
            'is_manual'            => true,
            'is_anchor'            => true,
            'recalculation_source' => 'manual_edit',
            'recalculated_at'      => now(),
        ]);

        $result   = $this->recalculator->recalculateFrom($schedule, $delta);
        $warnings = [];

        if (! empty($result['seminar_conflicts'])) {
            $names      = collect($result['seminar_conflicts'])->pluck('name')->join(', ');
            $warnings[] = "Konflik seminar: {$names}";
        }

        if (! empty($result['wi_conflicts'])) {
            $wiNames    = collect($result['wi_conflicts'])->map(fn($w) => $w->widyaiswara?->name)->filter()->join(', ');
            $warnings[] = "Konflik WI: {$wiNames}";
        }

        if (! empty($result['locked_obstacles'])) {
            $names      = collect($result['locked_obstacles'])->pluck('name')->join(', ');
            $warnings[] = "Jadwal terkunci tidak ikut bergeser: {$names}";
        }

        $hasConflict = $this->conflictChecker->hasAnyBatchConflict($batch->refresh());
        $batch->update(['has_conflict_alert' => $hasConflict]);

        $message = 'Jadwal berhasil diubah.' . (! empty($warnings) ? ' ⚠ ' . implode(' | ', $warnings) : '');

        return back()->with('success', $message);
    }

    private function authorizeOwnership(TrainingBatch $batch): void
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) return;

        if ($batch->organizational_unit_id !== $user->organizational_unit_id) {
            abort(403);
        }

        if ($user->isAdmin() && $batch->created_by !== $user->id) {
            abort(403);
        }
    }
}
