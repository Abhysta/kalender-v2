<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template Pelatihan — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10 relative overflow-hidden">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-sky-400 rounded-full"></span>manage_templates
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Template Pelatihan</h1>
        <p class="text-[13px] text-white/70 mb-6">Master template yang mendefinisikan phase dan aturan pelatihan.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="stat-card bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white" id="statTotal">{{ $templates->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Total Template</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $templates->where('is_active', true)->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Aktif</div>
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
    @if($errors->has('error'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        {{ $errors->first('error') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h2 class="text-lg font-bold text-slate-900">Semua Template</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.templates.import') }}"
               class="inline-flex items-center gap-1.5 h-9 px-4 bg-green-600 text-white rounded-[9px] text-[13px] font-semibold no-underline hover:bg-green-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Import CSV/Excel
            </a>
            <a href="{{ route('admin.templates.create') }}"
               class="inline-flex items-center gap-1.5 h-9 px-4 bg-sky-700 text-white rounded-[9px] text-[13px] font-semibold no-underline hover:bg-sky-600 transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Template Baru
            </a>
        </div>
    </div>

    @if($templates->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 opacity-40">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
        </svg>
        <p class="text-sm mb-4">Belum ada template.</p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('admin.templates.import') }}"
               class="inline-flex items-center gap-1.5 h-9 px-4 bg-green-600 text-white rounded-lg text-[13px] font-semibold no-underline hover:bg-green-500 transition-colors">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import CSV/Excel
            </a>
            <a href="{{ route('admin.templates.create') }}"
               class="inline-flex items-center gap-1.5 h-9 px-4 bg-sky-700 text-white rounded-lg text-[13px] font-semibold no-underline hover:bg-sky-600 transition-colors">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Input Manual
            </a>
        </div>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Template</th>
                    <th class="text-left px-4 py-3 max-[640px]:hidden">Unit</th>
                    <th class="text-center px-4 py-3 max-[768px]:hidden">Phase</th>
                    <th class="text-center px-4 py-3 max-[768px]:hidden">Batch</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($templates as $template)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-slate-900">{{ $template->name }}</div>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $template->code }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 max-[640px]:hidden">
                        {{ $template->organizationalUnit?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center max-[768px]:hidden">
                        <span class="font-mono text-[13px] text-slate-700">{{ $template->phases_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-center max-[768px]:hidden">
                        <span class="font-mono text-[13px] text-slate-700">{{ $template->batches_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($template->is_active)
                        <span class="inline-flex items-center gap-1 h-5 px-2 bg-green-100 text-green-700 rounded-full text-[10px] font-semibold">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Aktif
                        </span>
                        @else
                        <span class="inline-flex h-5 px-2 bg-slate-100 text-slate-500 rounded-full text-[10px] font-semibold items-center">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.templates.show', $template) }}"
                           class="inline-flex items-center gap-1 h-7 px-2.5 bg-sky-50 text-sky-700 border border-sky-200 rounded-lg text-[11px] font-semibold no-underline hover:bg-sky-100 transition-colors mr-1.5">
                            Detail
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                        <button type="button" onclick="openEditTemplateModal(this)"
                           data-id="{{ $template->id }}"
                           data-action="{{ route('admin.templates.update', $template) }}"
                           data-code="{{ $template->code }}"
                           data-name="{{ $template->name }}"
                           data-unit="{{ $template->organizational_unit_id }}"
                           data-description="{{ $template->description }}"
                           data-active="{{ $template->is_active ? '1' : '0' }}"
                           class="inline-flex items-center justify-center w-7 h-7 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all cursor-pointer mr-1.5">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" class="inline"
                              onsubmit="return confirmModal(event, 'Hapus template &quot;{{ addslashes($template->name) }}&quot;? Tindakan tidak dapat diurungkan.', true)">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all cursor-pointer inline-flex items-center justify-center">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</main>

<!-- EDIT TEMPLATE MODAL (shared, filled via JS per row) -->
<div id="editTemplateOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-[520px] w-full p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-[15px] font-bold text-slate-900">Edit Template</h3>
            <button type="button" onclick="closeEditTemplateModal()" class="w-7 h-7 flex items-center justify-center bg-transparent border-none text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editTemplateForm" method="POST" class="flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4 max-[480px]:grid-cols-1">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Kode *</label>
                    <input type="text" name="code" id="editTplCode" required
                        class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama *</label>
                    <input type="text" name="name" id="editTplName" required
                        class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
                <select name="organizational_unit_id" id="editTplUnit"
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                    <option value="">— Semua Unit —</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" id="editTplDescription" rows="3"
                    class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white resize-none"></textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="editTplActive" class="accent-sky-700">
                <label for="editTplActive" class="text-[13px] text-slate-700 cursor-pointer">Template Aktif</label>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEditTemplateModal()" class="h-10 px-5 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                <button type="submit" class="h-10 px-5 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTemplateModal(btn) {
    const overlay = document.getElementById('editTemplateOverlay');
    document.getElementById('editTemplateForm').action = btn.dataset.action;
    document.getElementById('editTplCode').value = btn.dataset.code;
    document.getElementById('editTplName').value = btn.dataset.name;
    document.getElementById('editTplUnit').value = btn.dataset.unit || '';
    document.getElementById('editTplDescription').value = btn.dataset.description || '';
    document.getElementById('editTplActive').checked = btn.dataset.active === '1';
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
}
function closeEditTemplateModal() {
    const overlay = document.getElementById('editTemplateOverlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
}
document.getElementById('editTemplateOverlay').addEventListener('click', e => {
    if (e.target.id === 'editTemplateOverlay') closeEditTemplateModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditTemplateModal(); });
</script>
</body>
</html>
