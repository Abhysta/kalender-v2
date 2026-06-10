<?php

namespace App\Http\Controllers;

use App\Models\BatchSchedule;
use App\Models\Widyaiswara;
use App\Models\WiAssignment;
use App\Services\ConflictCheckerService;
use App\Services\WiAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WidyaiswaraController extends Controller
{
    public function __construct(
        private readonly ConflictCheckerService $conflictChecker,
        private readonly WiAssignmentService    $wiService,
    ) {}

    public function index()
    {
        $widyaiswaras = Widyaiswara::withCount('assignments')->latest()->get();
        return view('admin.widyaiswaras.index', compact('widyaiswaras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nip'       => ['required', 'string', 'max:20', 'unique:widyaiswaras'],
            'name'      => ['required', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:255'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Widyaiswara::create($data);
        return redirect()->route('admin.widyaiswaras.index')->with('success', 'WI berhasil ditambahkan.');
    }

    public function update(Request $request, Widyaiswara $widyaiswara)
    {
        $data = $request->validate([
            'nip'       => ['required', 'string', 'max:20', Rule::unique('widyaiswaras')->ignore($widyaiswara)],
            'name'      => ['required', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $widyaiswara->update($data);
        return redirect()->route('admin.widyaiswaras.index')->with('success', 'WI berhasil diperbarui.');
    }

    public function destroy(Widyaiswara $widyaiswara)
    {
        if ($widyaiswara->assignments()->exists()) {
            return back()->withErrors(['error' => 'WI tidak dapat dihapus karena masih memiliki assignment.']);
        }
        $widyaiswara->delete();
        return redirect()->route('admin.widyaiswaras.index')->with('success', 'WI berhasil dihapus.');
    }

    // ── WI Assignment ─────────────────────────────────────────

    public function assignWi(Request $request, BatchSchedule $schedule)
    {
        $data = $request->validate([
            'widyaiswara_id' => ['required', 'exists:widyaiswaras,id'],
            'notes'          => ['nullable', 'string'],
        ]);

        try {
            $assignment = $this->wiService->assign($schedule, $data['widyaiswara_id'], $data['notes'] ?? null, Auth::id());
            return back()->with('success', "WI {$assignment->widyaiswara->name} berhasil di-assign.");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function removeAssignment(WiAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Assignment WI berhasil dihapus.');
    }
}
