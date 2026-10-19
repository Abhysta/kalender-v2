<?php

namespace App\Http\Controllers;

use App\Models\AnnualCalendar;
use App\Models\AnnualCalendarActivity;
use App\Models\OrganizationalUnit;
use App\Models\TrainingTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnualCalendarController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $calendars = $user->applyOwnership(
            AnnualCalendar::withCount('activities')->latest('year')
        )->get();

        return view('admin.annual_calendars.index', compact('calendars'));
    }

    public function create()
    {
        $units = OrganizationalUnit::where('is_active', true)->get();
        return view('admin.annual_calendars.create', compact('units'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'year'                   => ['required', 'integer', 'min:2020', 'max:2099'],
            'name'                   => ['required', 'string', 'max:255'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
        ]);

        if (! $user->isSuperAdmin()) {
            $data['organizational_unit_id'] = $user->organizational_unit_id;
        }

        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        $calendar = AnnualCalendar::create($data);

        return redirect()->route('admin.annual-calendars.show', $calendar)
            ->with('success', 'Kalender tahunan berhasil dibuat.');
    }

    public function show(AnnualCalendar $calendar)
    {
        $this->authorizeOwnership($calendar);
        $calendar->load(['activities.trainingBatches', 'organizationalUnit']);
        $templates = TrainingTemplate::where('is_active', true)->get();

        return view('admin.annual_calendars.show', compact('calendar', 'templates'));
    }

    public function destroy(AnnualCalendar $calendar)
    {
        $this->authorizeOwnership($calendar);
        // Activities cascade-delete; linked training_batches only lose the
        // reference (nullOnDelete) — their own dates/schedules are untouched.
        $calendar->delete();

        return redirect()->route('admin.annual-calendars.index')
            ->with('success', 'Kalender tahunan berhasil dihapus.');
    }

    public function archive(AnnualCalendar $calendar)
    {
        $this->authorizeOwnership($calendar);
        $calendar->update(['archived_at' => now(), 'updated_by' => Auth::id()]);

        return back()->with('success', 'Kalender tahunan diarsipkan.');
    }

    public function unarchive(AnnualCalendar $calendar)
    {
        $this->authorizeOwnership($calendar);
        $calendar->update(['archived_at' => null, 'updated_by' => Auth::id()]);

        return back()->with('success', 'Kalender tahunan diaktifkan kembali.');
    }

    public function storeActivity(Request $request, AnnualCalendar $calendar)
    {
        $this->authorizeOwnership($calendar);

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'training_template_id'  => ['nullable', 'exists:training_templates,id'],
            'planned_start_date'    => ['nullable', 'date'],
            'planned_end_date'      => ['nullable', 'date', 'after_or_equal:planned_start_date'],
            'notes'                 => ['nullable', 'string'],
        ]);

        $calendar->activities()->create($data);

        return back()->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function updateActivity(Request $request, AnnualCalendar $calendar, AnnualCalendarActivity $activity)
    {
        $this->authorizeOwnership($calendar);
        abort_unless($activity->annual_calendar_id === $calendar->id, 404);

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'training_template_id'  => ['nullable', 'exists:training_templates,id'],
            'planned_start_date'    => ['nullable', 'date'],
            'planned_end_date'      => ['nullable', 'date', 'after_or_equal:planned_start_date'],
            'notes'                 => ['nullable', 'string'],
        ]);

        $activity->update($data);

        return back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroyActivity(AnnualCalendar $calendar, AnnualCalendarActivity $activity)
    {
        $this->authorizeOwnership($calendar);
        abort_unless($activity->annual_calendar_id === $calendar->id, 404);

        // nullOnDelete on training_batches.annual_calendar_activity_id keeps batches intact.
        $activity->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function authorizeOwnership(AnnualCalendar $calendar): void
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) return;

        if ($calendar->organizational_unit_id !== $user->organizational_unit_id) {
            abort(403);
        }

        if ($user->isAdmin() && $calendar->created_by !== $user->id) {
            abort(403);
        }
    }
}
