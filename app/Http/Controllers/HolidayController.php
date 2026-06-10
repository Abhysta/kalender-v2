<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\TrainingBatch;
use App\Services\ConflictCheckerService;
use App\Services\ScheduleRecalculationService;
use App\Traits\ParsesCsvOrExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HolidayController extends Controller
{
    use ParsesCsvOrExcel;

    public function __construct(
        private readonly ScheduleRecalculationService $recalculator,
        private readonly ConflictCheckerService       $conflictChecker,
    ) {}

    public function index()
    {
        $user = Auth::user();

        $holidays = Holiday::when(! $user->isSuperAdmin(), function ($q) use ($user) {
            $q->where(function ($q2) use ($user) {
                $q2->whereNull('organizational_unit_id')
                   ->orWhere('organizational_unit_id', $user->organizational_unit_id);
            });
        })
        ->orderBy('date')
        ->get()
        ->groupBy(fn($h) => $h->date->year);

        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $user   = Auth::user();
        $unitId = $user->isSuperAdmin() ? null : $user->organizational_unit_id;

        $data = $request->validate([
            'date'        => ['required', 'date'],
            'name'        => ['required', 'string', 'max:255'],
            'is_national' => ['boolean'],
        ]);

        $exists = Holiday::where('date', $data['date'])
            ->where('organizational_unit_id', $unitId)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Tanggal ini sudah ada.'])->withInput();
        }

        $data['is_national']            = $request->boolean('is_national', true);
        $data['organizational_unit_id'] = $unitId;
        $holiday = Holiday::create($data);

        $result = $this->recalculator->recalculateForHolidayChange($data['date'], $unitId);

        if ($this->hasConflicts($result)) {
            session()->flash('conflict_alert', [
                'type'          => 'added',
                'holiday_name'  => $data['name'],
                'holiday_date'  => $data['date'],
                'seminar_count' => count($result['seminar_conflicts']),
                'wi_count'      => count($result['wi_conflicts']),
                'locked_count'  => count($result['locked_obstacles']),
                'revert'        => ['action' => 'delete', 'holiday_id' => $holiday->id],
            ]);
            return redirect()->route('admin.holidays.index')
                ->with('info', "Hari libur '{$data['name']}' ditambahkan. Konflik jadwal terdeteksi — harap tinjau.");
        }

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan. Jadwal terdampak telah diperbarui.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $user   = Auth::user();
        $unitId = $user->isSuperAdmin() ? $holiday->organizational_unit_id : $user->organizational_unit_id;

        $data = $request->validate([
            'date' => [
                'required', 'date',
                Rule::unique('holidays')->where('organizational_unit_id', $unitId)->ignore($holiday),
            ],
            'name'        => ['required', 'string', 'max:255'],
            'is_national' => ['boolean'],
        ]);

        $oldDate       = $holiday->date->format('Y-m-d');
        $oldName       = $holiday->name;
        $oldIsNational = $holiday->is_national;
        $newDate       = $data['date'];

        $data['is_national'] = $request->boolean('is_national', true);
        $holiday->update($data);

        $fromDate = min($oldDate, $newDate);
        $result   = $this->recalculator->recalculateForHolidayChange($fromDate, $unitId);

        if ($this->hasConflicts($result)) {
            session()->flash('conflict_alert', [
                'type'          => 'updated',
                'holiday_name'  => $data['name'],
                'holiday_date'  => $newDate,
                'seminar_count' => count($result['seminar_conflicts']),
                'wi_count'      => count($result['wi_conflicts']),
                'locked_count'  => count($result['locked_obstacles']),
                'revert'        => [
                    'action'        => 'restore',
                    'holiday_id'    => $holiday->id,
                    'old_date'      => $oldDate,
                    'old_name'      => $oldName,
                    'old_is_national' => (int) $oldIsNational,
                ],
            ]);
            return redirect()->route('admin.holidays.index')
                ->with('info', "Hari libur diperbarui. Konflik jadwal terdeteksi — harap tinjau.");
        }

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil diperbarui. Jadwal terdampak telah diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        $date       = $holiday->date->format('Y-m-d');
        $name       = $holiday->name;
        $isNational = $holiday->is_national;
        $unitId     = $holiday->organizational_unit_id;

        $holiday->delete();

        $result = $this->recalculator->recalculateForHolidayChange($date, $unitId);

        if ($this->hasConflicts($result)) {
            session()->flash('conflict_alert', [
                'type'          => 'deleted',
                'holiday_name'  => $name,
                'holiday_date'  => $date,
                'seminar_count' => count($result['seminar_conflicts']),
                'wi_count'      => count($result['wi_conflicts']),
                'locked_count'  => count($result['locked_obstacles']),
                'revert'        => [
                    'action'     => 'recreate',
                    'date'       => $date,
                    'name'       => $name,
                    'is_national' => (int) $isNational,
                    'unit_id'    => $unitId,
                ],
            ]);
            return redirect()->route('admin.holidays.index')
                ->with('info', "Hari libur '{$name}' dihapus. Konflik jadwal terdeteksi — harap tinjau.");
        }

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil dihapus. Jadwal terdampak telah diperbarui.');
    }

    public function revertConflict(Request $request)
    {
        $action = $request->input('action');
        $user   = Auth::user();
        $unitId = $user->isSuperAdmin() ? ($request->input('unit_id') ?: null) : $user->organizational_unit_id;

        if ($action === 'delete') {
            $holiday = Holiday::findOrFail($request->input('holiday_id'));
            $date    = $holiday->date->format('Y-m-d');
            $uid     = $holiday->organizational_unit_id;
            $holiday->delete();
            $this->recalculator->recalculateForHolidayChange($date, $uid);

        } elseif ($action === 'restore') {
            $holiday    = Holiday::findOrFail($request->input('holiday_id'));
            $oldDate    = $request->input('old_date');
            $uid        = $holiday->organizational_unit_id;
            $currentDate = $holiday->date->format('Y-m-d');
            $holiday->update([
                'date'        => $oldDate,
                'name'        => $request->input('old_name'),
                'is_national' => (bool) $request->input('old_is_national'),
            ]);
            $this->recalculator->recalculateForHolidayChange(min($currentDate, $oldDate), $uid);

        } elseif ($action === 'recreate') {
            Holiday::create([
                'date'                  => $request->input('date'),
                'name'                  => $request->input('name'),
                'is_national'           => (bool) $request->input('is_national'),
                'organizational_unit_id' => $unitId,
            ]);
            $this->recalculator->recalculateForHolidayChange($request->input('date'), $unitId);
        }

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Perubahan dibatalkan. Jadwal telah dikembalikan ke kondisi sebelumnya.');
    }

    public function dismissConflictAlert(TrainingBatch $batch)
    {
        if ($this->conflictChecker->hasAnyBatchConflict($batch)) {
            return back()->withErrors(['error' => 'Konflik jadwal masih ada. Selesaikan konflik terlebih dahulu sebelum menutup peringatan ini.']);
        }

        $batch->update(['has_conflict_alert' => false]);
        return back()->with('success', 'Tidak ada konflik. Peringatan berhasil ditutup.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate(['holidays_json' => ['required', 'string']]);

        $user   = Auth::user();
        $unitId = $user->isSuperAdmin() ? null : $user->organizational_unit_id;
        $items  = json_decode($request->holidays_json, true);

        if (! is_array($items) || empty($items)) {
            return back()->withErrors(['holidays_json' => 'Format JSON tidak valid.']);
        }

        $inserted = 0;
        $dates    = [];
        foreach ($items as $item) {
            if (empty($item['date']) || empty($item['name'])) continue;
            Holiday::firstOrCreate(
                ['date' => $item['date'], 'organizational_unit_id' => $unitId],
                ['name' => $item['name'], 'is_national' => (bool) ($item['is_national'] ?? true)]
            );
            $dates[] = $item['date'];
            $inserted++;
        }

        if (! empty($dates)) {
            $result = $this->recalculator->recalculateForHolidayChange(min($dates), $unitId);
            if ($this->hasConflicts($result)) {
                return redirect()->route('admin.holidays.index')
                    ->with('info', "{$inserted} hari libur ditambahkan. Konflik jadwal terdeteksi — tinjau batch terdampak.");
            }
        }

        return redirect()->route('admin.holidays.index')
            ->with('success', "{$inserted} hari libur berhasil ditambahkan. Jadwal terdampak telah diperbarui.");
    }

    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="holiday_import_template.csv"',
        ];

        $rows = [
            ['date', 'name', 'is_national'],
            ['2026-01-01', 'Tahun Baru Masehi', 1],
            ['2026-01-27', 'Isra Miraj Nabi Muhammad SAW', 1],
            ['2026-02-18', 'Tahun Baru Imlek', 1],
            ['2026-03-29', 'Hari Suci Nyepi', 1],
            ['2026-04-02', 'Wafat Yesus Kristus', 1],
            ['2026-05-01', 'Hari Buruh Internasional', 1],
            ['2026-08-17', 'Hari Kemerdekaan RI', 1],
            ['2026-12-25', 'Hari Raya Natal', 1],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) fputcsv($out, $row);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
        ]);

        $user   = Auth::user();
        $unitId = $user->isSuperAdmin() ? null : $user->organizational_unit_id;

        $file = $request->file('file');
        $rows = $this->parseCsvOrExcel($file->getRealPath(), $file->getClientOriginalExtension());

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File kosong atau format tidak valid.']);
        }

        $firstRow = array_keys($rows[0] ?? []);
        if (! in_array('date', $firstRow) || ! in_array('name', $firstRow)) {
            return back()->withErrors(['file' => "Kolom wajib 'date' dan 'name' tidak ditemukan."]);
        }

        $inserted = $skipped = 0;
        $dates    = [];
        foreach ($rows as $row) {
            if (empty($row['date']) || empty($row['name'])) { $skipped++; continue; }
            $date = date('Y-m-d', strtotime($row['date']));
            if (! $date || $date === '1970-01-01') { $skipped++; continue; }
            $isNational = in_array(strtolower($row['is_national'] ?? '1'), ['1', 'true', 'yes', 'ya']);
            Holiday::firstOrCreate(
                ['date' => $date, 'organizational_unit_id' => $unitId],
                ['name' => trim($row['name']), 'is_national' => $isNational]
            );
            $dates[] = $date;
            $inserted++;
        }

        if (! empty($dates)) {
            $result = $this->recalculator->recalculateForHolidayChange(min($dates), $unitId);
            if ($this->hasConflicts($result)) {
                $msg = "{$inserted} hari libur diimport. Konflik jadwal terdeteksi — tinjau batch terdampak.";
                if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";
                return redirect()->route('admin.holidays.index')->with('info', $msg);
            }
        }

        $msg = "{$inserted} hari libur berhasil diimport. Jadwal terdampak telah diperbarui.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";

        return redirect()->route('admin.holidays.index')->with('success', $msg);
    }

    private function hasConflicts(array $result): bool
    {
        return ! empty($result['seminar_conflicts'])
            || ! empty($result['wi_conflicts'])
            || ! empty($result['locked_obstacles']);
    }
}
