<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grup Konflik — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-violet-400 rounded-full"></span>manage_conflict_groups
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Grup &amp; Rule Konflik</h1>
        <p class="text-[13px] text-white/70 mb-5">Identitas canonical aktivitas dari import Excel dipetakan otomatis ke sini (exact-match, tidak pernah fuzzy-merge). Atur grup dan aktif/nonaktifkan rule deteksi di sini.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $groups->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Conflict Group</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $catalogGrouped->count() + $catalogUngrouped->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Canonical Sequence</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $rules->where('is_active', true)->count() }}/{{ $rules->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Rule Aktif</div>
            </div>
        </div>
    </div>
</section>

<main class="max-w-[1200px] mx-auto px-6 pt-7 pb-16 max-[640px]:px-4">
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-green-800">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">{{ $errors->first() }}</div>
    @endif

    <!-- CONFLICT GROUPS -->
    <h2 class="text-base font-bold text-slate-900 mb-4">Conflict Groups</h2>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-4">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Code</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-center px-4 py-3">Phase</th>
                    <th class="text-center px-4 py-3">Sequence</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($groups as $group)
                <tr>
                    <td class="px-4 py-3 font-mono text-[12px] text-slate-700">{{ $group->code }}</td>
                    <td class="px-4 py-3 text-[13px] text-slate-900">{{ $group->name }}</td>
                    <td class="px-4 py-3 text-center font-mono text-[12px] text-slate-500">{{ $group->phases_count }}</td>
                    <td class="px-4 py-3 text-center font-mono text-[12px] text-slate-500">{{ $group->sequence_catalog_entries_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[10px] font-semibold {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $group->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button type="button" onclick="toggleEditRow('group-{{ $group->id }}')"
                            class="text-[11px] text-slate-500 hover:text-sky-700 bg-transparent border-none cursor-pointer mr-2">Edit</button>
                        <form method="POST" action="{{ route('admin.conflict-groups.toggle-active', $group) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] text-sky-600 hover:text-sky-800 bg-transparent border-none cursor-pointer mr-2">
                                {{ $group->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.conflict-groups.destroy', $group) }}" class="inline"
                              onsubmit="return confirmModal(event, 'Hapus conflict group &quot;{{ addslashes($group->code) }}&quot;? Phase/schedule/rule yang terhubung tidak terhapus, hanya kehilangan rujukan.', true)">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[11px] text-red-500 hover:text-red-700 bg-transparent border-none cursor-pointer">Hapus</button>
                        </form>
                    </td>
                </tr>
                <tr id="editRow-group-{{ $group->id }}" class="hidden bg-sky-50/60">
                    <td colspan="6" class="px-6 py-3">
                        <form method="POST" action="{{ route('admin.conflict-groups.update', $group) }}" class="flex items-end gap-3 flex-wrap">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Nama</label>
                                <input type="text" name="name" value="{{ $group->name }}" required
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Deskripsi</label>
                                <input type="text" name="description" value="{{ $group->description }}" placeholder="Opsional"
                                    class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <button type="submit" class="h-8 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                            <button type="button" onclick="toggleEditRow('group-{{ $group->id }}')"
                                class="h-8 px-3 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">Belum ada conflict group.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <details class="mb-10">
        <summary class="text-[12px] text-sky-600 cursor-pointer hover:text-sky-700 mb-3">+ Buat Conflict Group Manual</summary>
        <form method="POST" action="{{ route('admin.conflict-groups.store') }}" class="bg-white border border-slate-200 rounded-xl p-5 flex gap-3 flex-wrap items-end mt-2">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Code</label>
                <input type="text" name="code" required class="h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama</label>
                <input type="text" name="name" required class="h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-[12px] text-slate-900 outline-none focus:border-sky-700">
            </div>
            <button type="submit" class="h-9 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-sky-600">Buat</button>
        </form>
    </details>

    <!-- CONFLICT RULES -->
    <h2 class="text-base font-bold text-slate-900 mb-4">Conflict Rules</h2>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-10">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Code</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Tipe</th>
                    <th class="text-left px-4 py-3">Scope</th>
                    <th class="text-left px-4 py-3">Severity</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($rules as $rule)
                <tr>
                    <td class="px-4 py-3 font-mono text-[12px] text-slate-700">{{ $rule->code }}</td>
                    <td class="px-4 py-3 text-[13px] text-slate-900">{{ $rule->name }}</td>
                    <td class="px-4 py-3 text-[12px] text-slate-500">{{ $rule->rule_type }}</td>
                    <td class="px-4 py-3 text-[12px] text-slate-500">{{ $rule->scope }}</td>
                    <td class="px-4 py-3 text-[12px] text-slate-500">{{ $rule->severity }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[10px] font-semibold {{ $rule->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button type="button" onclick="toggleEditRow('rule-{{ $rule->id }}')"
                            class="text-[11px] text-slate-500 hover:text-sky-700 bg-transparent border-none cursor-pointer mr-2">Edit</button>
                        <form method="POST" action="{{ route('admin.conflict-rules.toggle', $rule) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] text-sky-600 hover:text-sky-800 bg-transparent border-none cursor-pointer mr-2">
                                {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.conflict-rules.destroy', $rule) }}" class="inline"
                              onsubmit="return confirmModal(event, 'Hapus rule &quot;{{ addslashes($rule->code) }}&quot;? Alert yang sudah tercatat tidak terhapus, hanya kehilangan rujukan rule.', true)">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[11px] text-red-500 hover:text-red-700 bg-transparent border-none cursor-pointer">Hapus</button>
                        </form>
                    </td>
                </tr>
                <tr id="editRow-rule-{{ $rule->id }}" class="hidden bg-sky-50/60">
                    <td colspan="7" class="px-6 py-3">
                        <form method="POST" action="{{ route('admin.conflict-rules.update', $rule) }}" class="flex items-end gap-3 flex-wrap">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Nama</label>
                                <input type="text" name="name" value="{{ $rule->name }}" required
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Tipe</label>
                                <input type="text" name="rule_type" value="{{ $rule->rule_type }}" required
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Scope</label>
                                <input type="text" name="scope" value="{{ $rule->scope }}" placeholder="global"
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Severity</label>
                                <select name="severity" class="h-8 bg-white border border-slate-200 rounded-lg px-2 text-[12px] text-slate-900 outline-none focus:border-sky-700 select-custom">
                                    @foreach(['blocking', 'warning'] as $sev)
                                    <option value="{{ $sev }}" {{ $rule->severity === $sev ? 'selected' : '' }}>{{ $sev }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Conflict Group</label>
                                <select name="conflict_group_id" class="h-8 bg-white border border-slate-200 rounded-lg px-2 text-[12px] text-slate-900 outline-none focus:border-sky-700 select-custom">
                                    <option value="">—</option>
                                    @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ $rule->conflict_group_id === $g->id ? 'selected' : '' }}>{{ $g->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="h-8 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                            <button type="button" onclick="toggleEditRow('rule-{{ $rule->id }}')"
                                class="h-8 px-3 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- SEQUENCE CATALOG (read-only — resolved automatically, no manual approval step) -->
    <h2 class="text-base font-bold text-slate-900 mb-1">Sequence Catalog — Punya Conflict Group</h2>
    <p class="text-[12px] text-slate-500 mb-4">Identitas canonical aktivitas yang sudah terhubung ke conflict group — ini yang relevan buat deteksi overlap.</p>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Nama (pertama ditemui)</th>
                    <th class="text-left px-4 py-3">Normalized</th>
                    <th class="text-left px-4 py-3">Conflict Group</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalogGrouped as $entry)
                <tr>
                    <td class="px-4 py-3 text-[13px] text-slate-900">{{ $entry->display_name }}</td>
                    <td class="px-4 py-3 font-mono text-[11px] text-slate-500">{{ $entry->normalized_name }}</td>
                    <td class="px-4 py-3 text-[12px] text-slate-600">
                        <span class="font-mono">{{ $entry->defaultConflictGroup->code }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400 text-sm">Belum ada aktivitas yang terhubung ke conflict group.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="text-base font-bold text-slate-900 mb-1">Sequence Catalog — Ungrouped</h2>
    <p class="text-[12px] text-slate-500 mb-4">Identitas canonical aktivitas yang belum/tidak punya conflict group (misal CekIn, On-Campus) — gak relevan buat deteksi overlap, tapi tetap dicatat biar nama yang sama di import lain dikenali konsisten.</p>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Nama (pertama ditemui)</th>
                    <th class="text-left px-4 py-3">Normalized</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalogUngrouped as $entry)
                <tr>
                    <td class="px-4 py-3 text-[13px] text-slate-900">{{ $entry->display_name }}</td>
                    <td class="px-4 py-3 font-mono text-[11px] text-slate-500">{{ $entry->normalized_name }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-8 text-center text-slate-400 text-sm">Belum ada data — akan terisi otomatis saat import/tambah phase.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
<script>
function toggleEditRow(id) {
    const row = document.getElementById('editRow-' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
</body>
</html>
