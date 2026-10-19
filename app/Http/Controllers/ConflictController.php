<?php

namespace App\Http\Controllers;

use App\Models\ConflictAlert;
use App\Services\ConflictCheckerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConflictController extends Controller
{
    public function __construct(
        private readonly ConflictCheckerService $checker
    ) {}

    public function index(Request $request)
    {
        $groupConflicts = $this->checker->getGroupConflicts();
        $wiConflicts    = $this->checker->getWiConflicts();
        $total          = $groupConflicts->count() + $wiConflicts->count();

        $alertStatus = $request->query('status', 'open');
        $alerts = ConflictAlert::with(['rule', 'scheduleItem.batch', 'relatedScheduleItem.batch', 'conflictGroup', 'resolver'])
            ->when($alertStatus !== 'all', fn($q) => $q->where('status', $alertStatus))
            ->latest('detected_at')
            ->get();

        return view('admin.conflicts.index', compact('groupConflicts', 'wiConflicts', 'total', 'alerts', 'alertStatus'));
    }

    public function resolveAlert(ConflictAlert $alert)
    {
        $alert->update(['status' => 'resolved', 'resolved_by' => Auth::id(), 'resolved_at' => now()]);

        return back()->with('success', 'Alert ditandai selesai.');
    }

    public function ignoreAlert(ConflictAlert $alert)
    {
        $alert->update(['status' => 'ignored', 'resolved_by' => Auth::id(), 'resolved_at' => now()]);

        return back()->with('success', 'Alert diabaikan.');
    }
}
