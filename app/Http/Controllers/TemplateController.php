<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\TemplatePhase;
use App\Models\TrainingTemplate;
use App\Traits\ParsesCsvOrExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    use ParsesCsvOrExcel;
    public function index()
    {
        $user      = Auth::user();
        $templates = $user->applyOwnership(
            TrainingTemplate::with('organizationalUnit')->withCount('phases', 'batches')->latest()
        )->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $units = OrganizationalUnit::where('is_active', true)->get();
        return view('admin.templates.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'                   => ['required', 'string', 'max:20', 'unique:training_templates'],
            'name'                   => ['required', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
        ]);

        $user = Auth::user();
        if (! $user->isSuperAdmin()) {
            $data['organizational_unit_id'] = $user->organizational_unit_id;
        }
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        $template = TrainingTemplate::create($data);

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Template berhasil dibuat.');
    }

    public function show(TrainingTemplate $template)
    {
        $template->load('phases', 'organizationalUnit', 'batches');
        return view('admin.templates.show', compact('template'));
    }

    public function edit(TrainingTemplate $template)
    {
        $units = OrganizationalUnit::where('is_active', true)->get();
        return view('admin.templates.edit', compact('template', 'units'));
    }

    public function update(Request $request, TrainingTemplate $template)
    {
        $data = $request->validate([
            'code'                   => ['required', 'string', 'max:20', Rule::unique('training_templates')->ignore($template)],
            'name'                   => ['required', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'is_active'              => ['boolean'],
        ]);

        $data['updated_by'] = Auth::id();
        $template->update($data);

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(TrainingTemplate $template)
    {
        if ($template->batches()->exists()) {
            return back()->withErrors(['error' => 'Template tidak dapat dihapus karena sudah digunakan batch.']);
        }
        $template->delete();
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    // ── Phase management ─────────────────────────────────────

    public function storePhase(Request $request, TrainingTemplate $template)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'sequence'        => ['required', 'integer', 'min:0'],
            'duration'        => ['required', 'integer', 'min:1'],
            'duration_unit'   => ['required', 'in:day,hour'],
            'offset_days'     => ['nullable', 'integer'],
            'day_type'        => ['required', 'in:working_day,calendar_day'],
            'work_days'       => ['required', 'integer', 'in:5,6,7'],
            'activity_type'   => ['nullable', 'string', 'max:50'],
            'conflict_group'  => ['nullable', 'in:seminar'],
            'is_alert'        => ['boolean'],
            'alert_type'      => ['nullable', 'string', 'max:50'],
            'can_manual_edit' => ['boolean'],
            'color'           => ['nullable', 'string', 'max:20'],
        ]);

        $data['training_template_id'] = $template->id;
        $data['offset_days']     = $data['offset_days'] ?? 0;
        $data['is_alert']        = $request->boolean('is_alert');
        $data['can_manual_edit'] = $request->boolean('can_manual_edit');

        TemplatePhase::create($data);

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Phase berhasil ditambahkan.');
    }

    public function updatePhase(Request $request, TrainingTemplate $template, TemplatePhase $phase)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'sequence'        => ['required', 'integer', 'min:0'],
            'duration'        => ['required', 'integer', 'min:1'],
            'duration_unit'   => ['required', 'in:day,hour'],
            'offset_days'     => ['nullable', 'integer'],
            'day_type'        => ['required', 'in:working_day,calendar_day'],
            'work_days'       => ['required', 'integer', 'in:5,6,7'],
            'activity_type'   => ['nullable', 'string', 'max:50'],
            'conflict_group'  => ['nullable', 'in:seminar'],
            'is_alert'        => ['boolean'],
            'alert_type'      => ['nullable', 'string', 'max:50'],
            'can_manual_edit' => ['boolean'],
            'color'           => ['nullable', 'string', 'max:20'],
        ]);

        $data['offset_days']     = $data['offset_days'] ?? 0;
        $data['is_alert']        = $request->boolean('is_alert');
        $data['can_manual_edit'] = $request->boolean('can_manual_edit');

        $phase->update($data);

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Phase berhasil diperbarui.');
    }

    public function destroyPhase(TrainingTemplate $template, TemplatePhase $phase)
    {
        $phase->delete();
        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Phase berhasil dihapus.');
    }

    // ── Excel / CSV Import ───────────────────────────────────

    public function importForm()
    {
        $units = OrganizationalUnit::where('is_active', true)->get();
        return view('admin.templates.import', compact('units'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'code'                   => ['required', 'string', 'max:20', 'unique:training_templates'],
            'name'                   => ['required', 'string', 'max:255'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'file'                   => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
        ]);

        $file = $request->file('file');
        $rows = $this->parseCsvOrExcel($file->getRealPath(), $file->getClientOriginalExtension());

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File kosong atau format tidak valid.'])->withInput();
        }

        // Validate required columns exist
        $required = ['sequence', 'name', 'duration'];
        $firstRow = array_keys($rows[0] ?? []);
        foreach ($required as $col) {
            if (! in_array($col, $firstRow)) {
                return back()->withErrors(['file' => "Kolom wajib '{$col}' tidak ditemukan. Periksa header CSV."])->withInput();
            }
        }

        DB::transaction(function () use ($request, $rows) {
            $user   = Auth::user();
            $unitId = ! $user->isSuperAdmin()
                ? $user->organizational_unit_id
                : $request->input('organizational_unit_id');

            $template = TrainingTemplate::create([
                'code'                   => $request->input('code'),
                'name'                   => $request->input('name'),
                'description'            => $request->input('description'),
                'organizational_unit_id' => $unitId,
                'is_active'              => true,
                'created_by'             => $user->id,
                'updated_by'             => $user->id,
            ]);

            $colors = ['#0369A1','#0891B2','#059669','#D97706','#DC2626','#7C3AED','#BE185D','#0F172A'];
            foreach ($rows as $i => $row) {
                if (empty($row['name'])) {
                    continue;
                }
                TemplatePhase::create([
                    'training_template_id' => $template->id,
                    'name'                 => trim($row['name']),
                    'sequence'             => (int)($row['sequence'] ?? $i + 1),
                    'duration'             => max(1, (int)($row['duration'] ?? 1)),
                    'duration_unit'        => in_array($row['duration_unit'] ?? '', ['day','hour']) ? $row['duration_unit'] : 'day',
                    'day_type'             => in_array($row['day_type'] ?? '', ['working_day','calendar_day']) ? $row['day_type'] : 'working_day',
                    'work_days'            => in_array((int)($row['work_days'] ?? 5), [5,6,7]) ? (int)$row['work_days'] : 5,
                    'offset_days'          => (int)($row['offset_days'] ?? 0),
                    'activity_type'        => $row['activity_type'] ?? null,
                    'conflict_group'       => ($row['conflict_group'] ?? '') === 'seminar' ? 'seminar' : null,
                    'color'                => ! empty($row['color']) ? $row['color'] : $colors[$i % count($colors)],
                    'is_alert'             => in_array(strtolower($row['is_alert'] ?? ''), ['1','true','yes']),
                    'can_manual_edit'      => in_array(strtolower($row['can_manual_edit'] ?? ''), ['1','true','yes']),
                ]);
            }

            session(['import_template_id' => $template->id]);
        });

        return redirect()->route('admin.templates.show', TrainingTemplate::where('code', $request->input('code'))->first())
            ->with('success', 'Template berhasil diimport dari file. ' . count($rows) . ' phase dibuat.');
    }

    public function importPhases(Request $request, TrainingTemplate $template)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
        ]);

        $file = $request->file('file');
        $rows = $this->parseCsvOrExcel($file->getRealPath(), $file->getClientOriginalExtension());

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File kosong atau format tidak valid.']);
        }

        $required = ['sequence', 'name', 'duration'];
        $firstRow = array_keys($rows[0] ?? []);
        foreach ($required as $col) {
            if (! in_array($col, $firstRow)) {
                return back()->withErrors(['file' => "Kolom wajib '{$col}' tidak ditemukan. Periksa header CSV."]);
            }
        }

        $created = 0;
        DB::transaction(function () use ($template, $rows, &$created) {
            $existingCount = $template->phases()->count();
            $colors = ['#0369A1','#0891B2','#059669','#D97706','#DC2626','#7C3AED','#BE185D','#0F172A'];

            foreach ($rows as $i => $row) {
                if (empty($row['name'])) {
                    continue;
                }
                TemplatePhase::create([
                    'training_template_id' => $template->id,
                    'name'                 => trim($row['name']),
                    'sequence'             => (int)($row['sequence'] ?? $existingCount + $i + 1),
                    'duration'             => max(1, (int)($row['duration'] ?? 1)),
                    'duration_unit'        => in_array($row['duration_unit'] ?? '', ['day','hour']) ? $row['duration_unit'] : 'day',
                    'day_type'             => in_array($row['day_type'] ?? '', ['working_day','calendar_day']) ? $row['day_type'] : 'working_day',
                    'work_days'            => in_array((int)($row['work_days'] ?? 5), [5,6,7]) ? (int)$row['work_days'] : 5,
                    'offset_days'          => (int)($row['offset_days'] ?? 0),
                    'activity_type'        => $row['activity_type'] ?? null,
                    'conflict_group'       => ($row['conflict_group'] ?? '') === 'seminar' ? 'seminar' : null,
                    'color'                => ! empty($row['color']) ? $row['color'] : $colors[$i % count($colors)],
                    'is_alert'             => in_array(strtolower($row['is_alert'] ?? ''), ['1','true','yes']),
                    'can_manual_edit'      => in_array(strtolower($row['can_manual_edit'] ?? ''), ['1','true','yes']),
                ]);
                $created++;
            }
        });

        return redirect()->route('admin.templates.show', $template)
            ->with('success', $created . ' phase berhasil diimport ke template ini.');
    }

    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_phase_import.csv"',
        ];

        $rows = [
            ['sequence','name','duration','duration_unit','day_type','work_days','offset_days','activity_type','conflict_group','color','is_alert','can_manual_edit'],
            [1,'CekIn1',1,'day','working_day',5,-1,'checkin','','#0369A1',0,0],
            [2,'On-Campus1',3,'day','working_day',5,0,'classroom','','#0891B2',0,0],
            [3,'Klasikal1',5,'day','working_day',6,0,'classroom','','#059669',0,0],
            [4,'Seminar',1,'day','working_day',5,0,'seminar','seminar','#EF4444',1,0],
            [5,'OJT1',10,'day','working_day',6,0,'ojt','','#7C3AED',0,1],
            [6,'Post-Test',1,'day','working_day',5,0,'test','','#D97706',0,0],
            [7,'CekOut',1,'day','working_day',5,0,'checkin','','#64748B',0,0],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

}
