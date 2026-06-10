<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Widyaiswara — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-teal-400 rounded-full"></span>assign_wi
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Widyaiswara</h1>
        <p class="text-[13px] text-white/70 mb-5">Narasumber / instruktur pelatihan. Konflik jadwal dicek otomatis saat assign.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $widyaiswaras->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Total</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $widyaiswaras->where('is_active', true)->count() }}</div>
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
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-[1fr_360px] gap-7 max-[900px]:grid-cols-1">
        <!-- LIST -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Widyaiswara</h2>
            @if($widyaiswaras->isEmpty())
            <div class="text-center py-12 text-slate-400 bg-white border border-slate-200 rounded-xl">
                <p class="text-sm">Belum ada widyaiswara.</p>
            </div>
            @else
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3">Nama / NIP</th>
                            <th class="text-left px-4 py-3 max-[640px]:hidden">Keahlian</th>
                            <th class="text-center px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($widyaiswaras as $wi)
                        <tr class="hover:bg-slate-50 transition-colors" id="wiRow{{ $wi->id }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-[13px] text-slate-900">{{ $wi->name }}</div>
                                <div class="font-mono text-[11px] text-slate-400">{{ $wi->nip }}</div>
                            </td>
                            <td class="px-4 py-3 max-[640px]:hidden">
                                <span class="text-[12px] text-slate-600">{{ $wi->expertise ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center h-5 px-2 rounded-full text-[10px] font-semibold
                                    {{ $wi->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $wi->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1.5 justify-end">
                                    <button onclick="toggleEditWi({{ $wi->id }})"
                                        class="h-7 px-2.5 bg-slate-50 border border-slate-200 rounded text-[11px] text-slate-500 font-semibold cursor-pointer hover:border-sky-300 hover:text-sky-600 transition-colors">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.widyaiswaras.destroy', $wi) }}" onsubmit="return confirm('Hapus widyaiswara {{ addslashes($wi->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-7 px-2.5 bg-red-50 border border-red-200 rounded text-[11px] text-red-600 font-semibold cursor-pointer hover:bg-red-100 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <!-- Inline edit row -->
                        <tr id="editWiRow{{ $wi->id }}" class="hidden bg-sky-50/60">
                            <td colspan="4" class="px-4 py-4">
                                <form method="POST" action="{{ route('admin.widyaiswaras.update', $wi) }}" class="flex flex-col gap-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3 max-[480px]:grid-cols-1">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Nama *</label>
                                            <input type="text" name="name" value="{{ $wi->name }}" required
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">NIP *</label>
                                            <input type="text" name="nip" value="{{ $wi->nip }}" required
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Keahlian</label>
                                            <input type="text" name="expertise" value="{{ $wi->expertise }}"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Email</label>
                                            <input type="email" name="email" value="{{ $wi->email }}"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Telepon</label>
                                            <input type="text" name="phone" value="{{ $wi->phone }}"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div class="flex items-center gap-2 pt-4">
                                            <input type="checkbox" name="is_active" value="1" id="wiActive{{ $wi->id }}" {{ $wi->is_active ? 'checked' : '' }} class="accent-sky-700">
                                            <label for="wiActive{{ $wi->id }}" class="text-[12px] text-slate-700 cursor-pointer">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="h-7 px-4 bg-sky-700 text-white rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                                        <button type="button" onclick="toggleEditWi({{ $wi->id }})"
                                            class="h-7 px-3 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- ADD FORM -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-4">Tambah Widyaiswara</h2>
            <form method="POST" action="{{ route('admin.widyaiswaras.store') }}" class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Drs. Ahmad Fauzi, M.Pd" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">NIP *</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="197501012005011001" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Keahlian</label>
                    <input type="text" name="expertise" value="{{ old('expertise') }}" placeholder="Kepemimpinan, Manajemen SDM"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="newWiActive" checked class="accent-sky-700">
                    <label for="newWiActive" class="text-[13px] text-slate-700 cursor-pointer">Aktif</label>
                </div>
                <button type="submit" class="h-9 w-full bg-sky-700 text-white rounded-lg text-[13px] font-bold border-none cursor-pointer hover:bg-sky-600 mt-1">
                    Tambah Widyaiswara
                </button>
            </form>
        </div>
    </div>
</main>

<script>
function toggleEditWi(id) {
    const row = document.getElementById('editWiRow' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
</body>
</html>
