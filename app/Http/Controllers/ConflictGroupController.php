<?php

namespace App\Http\Controllers;

use App\Models\ConflictGroup;
use App\Models\ConflictRule;
use App\Models\SequenceCatalog;
use Illuminate\Http\Request;

class ConflictGroupController extends Controller
{
    public function index()
    {
        $groups  = ConflictGroup::withCount(['phases', 'sequenceCatalogEntries'])->orderBy('code')->get();
        $rules   = ConflictRule::with('conflictGroup')->orderBy('code')->get();
        $catalog = SequenceCatalog::with('defaultConflictGroup')->orderBy('display_name')->get();

        $catalogGrouped   = $catalog->filter(fn ($c) => $c->default_conflict_group_id !== null)->values();
        $catalogUngrouped = $catalog->filter(fn ($c) => $c->default_conflict_group_id === null)->values();

        return view('admin.conflict-groups.index', compact('groups', 'rules', 'catalogGrouped', 'catalogUngrouped'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:conflict_groups,code'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = true;

        ConflictGroup::create($data);

        return back()->with('success', 'Conflict group berhasil dibuat.');
    }

    public function update(Request $request, ConflictGroup $group)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $group->update($data);

        return back()->with('success', 'Conflict group berhasil diperbarui.');
    }

    public function toggleActive(ConflictGroup $group)
    {
        $group->update(['is_active' => ! $group->is_active]);

        return back()->with('success', $group->is_active ? 'Conflict group diaktifkan.' : 'Conflict group dinonaktifkan.');
    }

    public function destroy(ConflictGroup $group)
    {
        // Safe to delete: every reference (phases, schedules, rules, catalog defaults,
        // alerts) uses nullOnDelete — no operational data is removed, only unlinked.
        $group->delete();

        return back()->with('success', 'Conflict group berhasil dihapus.');
    }

    public function updateRule(Request $request, ConflictRule $rule)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'rule_type'         => ['required', 'string', 'max:50'],
            'scope'             => ['nullable', 'string', 'max:50'],
            'severity'          => ['required', 'string', 'max:50'],
            'conflict_group_id' => ['nullable', 'exists:conflict_groups,id'],
        ]);

        $rule->update($data);

        return back()->with('success', 'Rule berhasil diperbarui.');
    }

    public function toggleRule(ConflictRule $rule)
    {
        $rule->update(['is_active' => ! $rule->is_active]);

        return back()->with('success', $rule->is_active ? 'Rule diaktifkan.' : 'Rule dinonaktifkan.');
    }

    public function destroyRule(ConflictRule $rule)
    {
        // Safe to delete: conflict_alerts.conflict_rule_id is nullOnDelete.
        $rule->delete();

        return back()->with('success', 'Rule berhasil dihapus.');
    }
}
