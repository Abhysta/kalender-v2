<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\TrainingBatch;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $batches = TrainingBatch::with(['template', 'organizationalUnit'])
            ->withCount('schedules')
            ->latest()
            ->get();

        $units = OrganizationalUnit::orderBy('name')->get();

        return view('admin.catalog', compact('batches', 'units'));
    }
}
