<?php

namespace App\Http\Controllers;

use App\Models\AnnualCalendarActivity;
use App\Models\OrganizationalUnit;
use App\Models\TrainingBatch;
use App\Models\TrainingTemplate;
use App\Services\AnnualCalendarService;
use App\Services\ConflictAlertService;
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
        private readonly AnnualCalendarService         $annualCalendar,
        private readonly ConflictAlertService          $conflictAlerts,
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
        $units      = OrganizationalUnit::where('is_active', true)->get();
        $activities = $this->visibleActivities($user, $user->organizational_unit_id);

        return view('admin.batches.create', compact('templates', 'units', 'activities'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'training_template_id'        => ['required', 'exists:training_templates,id'],
            'batch_number'                => ['required', 'integer', 'min:1'],
            'year'                        => ['required', 'integer', 'min:2020', 'max:2099'],
            'name'                        => ['required', 'string', 'max:255'],
            'start_date'                  => ['required', 'date'],
            'participant_count'           => ['required', 'integer', 'min:1'],
            'organizational_unit_id'      => ['nullable', 'exists:organizational_units,id'],
            'annual_calendar_activity_id' => ['nullable', 'exists:annual_calendar_activities,id'],
        ]);

        $activityId = $data['annual_calendar_activity_id'] ?? null;
        unset($data['annual_calendar_activity_id']);

        $batch = $this->batchService->create($data, $user);

        if ($activityId) {
            $this->annualCalendar->linkActivity($batch, AnnualCalendarActivity::findOrFail($activityId));
        }

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch berhasil dibuat. Silakan generate jadwal.');
    }

    public function show(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        $batch->load([
            'template', 'organizationalUnit', 'schedules.wiAssignments.widyaiswara', 'schedules.phase',
            'annualCalendarActivity.calendar',
        ]);

        $activities = $this->visibleActivities(Auth::user(), $batch->organizational_unit_id);

        return view('admin.batches.show', compact('batch', 'activities'));
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

            $message = 'Jadwal berhasil di-generate.' . $this->formatWarnings($result);

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

        $result = $this->recalculator->recalculateFrom($schedule, $delta);

        $batch->refresh();
        $hasConflict = $this->conflictChecker->hasAnyBatchConflict($batch);
        $batch->update(['has_conflict_alert' => $hasConflict]);
        $this->conflictAlerts->evaluateBatch($batch);

        $message = 'Jadwal berhasil diubah.' . $this->formatWarnings($result);

        return back()->with('success', $message);
    }

    public function linkActivity(Request $request, TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);

        $data = $request->validate([
            'annual_calendar_activity_id' => ['required', 'exists:annual_calendar_activities,id'],
        ]);

        $activity = AnnualCalendarActivity::findOrFail($data['annual_calendar_activity_id']);
        $result   = $this->annualCalendar->linkActivity($batch, $activity);

        return back()->with('success', 'Batch dihubungkan ke kegiatan kalender tahunan.' . $this->formatWarnings($result));
    }

    public function unlinkActivity(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        $this->annualCalendar->unlinkActivity($batch);

        return back()->with('success', 'Hubungan ke kalender tahunan dihapus. Tanggal batch tidak berubah.');
    }

    public function setOverride(Request $request, TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);

        $data = $request->validate([
            'override_start_date' => ['required', 'date'],
        ]);

        $result = $this->annualCalendar->setOverride($batch, $data['override_start_date']);

        return back()->with('success', 'Override tanggal berhasil diterapkan.' . $this->formatWarnings($result));
    }

    public function clearOverride(TrainingBatch $batch)
    {
        $this->authorizeOwnership($batch);
        $result = $this->annualCalendar->clearOverride($batch);

        return back()->with('success', 'Override dihapus. Tanggal kembali ke rencana kalender tahunan / tanggal batch.' . $this->formatWarnings($result));
    }

    private function formatWarnings(array $result): string
    {
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

        return empty($warnings) ? '' : ' ⚠ ' . implode(' | ', $warnings);
    }

    private function visibleActivities(\App\Models\User $user, ?int $unitId): \Illuminate\Support\Collection
    {
        return AnnualCalendarActivity::whereHas('calendar', function ($q) use ($unitId, $user) {
            $q->active();
            if (! $user->isSuperAdmin()) {
                $q->where('organizational_unit_id', $unitId);
            }
        })->with('calendar')->get();
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
