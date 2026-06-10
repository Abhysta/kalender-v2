<?php

namespace App\Http\Controllers;

use App\Services\ConflictCheckerService;

class ConflictController extends Controller
{
    public function __construct(
        private readonly ConflictCheckerService $checker
    ) {}

    public function index()
    {
        $seminarConflicts = $this->checker->getSeminarConflicts();
        $wiConflicts      = $this->checker->getWiConflicts();
        $total            = $seminarConflicts->count() + $wiConflicts->count();

        return view('admin.conflicts.index', compact('seminarConflicts', 'wiConflicts', 'total'));
    }
}
