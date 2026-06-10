<?php

namespace App\Http\Controllers;

use App\Models\TrainingBatch;
use App\Models\TrainingTemplate;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly ConflictCheckerService $conflictChecker) {}

    public function index()
    {
        $user = Auth::user();

        $tQ = fn() => $user->applyOwnership(TrainingTemplate::query());
        $bQ = fn() => $user->applyOwnership(TrainingBatch::query());

        $seminarConflicts = $this->conflictChecker->getSeminarConflicts();
        $wiConflicts      = $this->conflictChecker->getWiConflicts();

        $stats = [
            'total'     => $tQ()->count(),
            'opened'    => $tQ()->where('is_active', true)->count(),
            'batches'   => $bQ()->count(),
            'conflicts' => $seminarConflicts->count() + $wiConflicts->count(),
            'seats'     => (int) $bQ()->sum('participant_count'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
