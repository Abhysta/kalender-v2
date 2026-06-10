<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\TrainingBatch;

class PublicController extends Controller
{
    private const CATEGORIES = ['kepemimpinan', 'teknis', 'fungsional', 'prajabatan', 'manajemen'];
    private const MONTHS_ID  = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    private function mapBatch(TrainingBatch $b): array
    {
        $cat     = self::CATEGORIES[$b->training_template_id % 5];
        $dateStr = 'TBD';

        if ($b->start_date && $b->end_date) {
            $sm = self::MONTHS_ID[$b->start_date->month - 1];
            $em = self::MONTHS_ID[$b->end_date->month   - 1];
            $dateStr = $sm === $em
                ? "{$b->start_date->day} – {$b->end_date->day} {$em} {$b->end_date->year}"
                : "{$b->start_date->day} {$sm} – {$b->end_date->day} {$em} {$b->end_date->year}";
        }

        return [
            'id'       => $b->id,
            'category' => $cat,
            'unitId'   => $b->organizational_unit_id,
            'title'    => $b->name,
            'date'     => $dateStr,
            'start'    => $b->start_date?->toDateString(),
            'end'      => $b->end_date?->toDateString(),
            'location' => $b->organizationalUnit?->name ?? 'BPSDM',
            'seats'    => ['total' => max(1, $b->participant_count ?? 30), 'filled' => 0],
            'status'   => 'open',
        ];
    }

    public function catalog()
    {
        $trainings = TrainingBatch::with(['template', 'organizationalUnit'])
            ->where('status', '!=', 'draft')
            ->whereNotNull('start_date')
            ->latest()
            ->get()
            ->map(fn($b) => $this->mapBatch($b))
            ->values();

        $units = OrganizationalUnit::orderBy('name')->get();

        return view('catalog', compact('trainings', 'units'));
    }

    public function calendar()
    {
        $trainings = TrainingBatch::with(['template', 'organizationalUnit'])
            ->where('status', '!=', 'draft')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->latest()
            ->get()
            ->map(fn($b) => $this->mapBatch($b))
            ->values();

        return view('calendar', compact('trainings'));
    }
}
