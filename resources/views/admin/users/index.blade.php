<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen User — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
    <style>
        .stat-card { opacity: 0; transform: translateY(20px); }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern px-6 pt-9 pb-10" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#4c1d95 100%)">
    <div class="max-w-[1200px] mx-auto">
        <div id="userHeroBadge" class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3" style="opacity:0">
            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full"></span>manage_users · super_admin only
        </div>
        <div id="userHeroTop" style="opacity:0">
            <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Manajemen User</h1>
            <p class="text-[13px] text-white/70 mb-5">Kelola akun admin. Non-aktifkan akun untuk blokir akses tanpa hapus data.</p>
        </div>
        <div class="flex gap-3 flex-wrap" id="userStatsRow">
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[100px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none" id="statUTotal">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Total User</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[100px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none" id="statUActive">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Aktif</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[100px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none" id="statUSA">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Super Admin</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[100px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none" id="statUUM">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Unit Manager</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[100px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none" id="statUAdmin">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Admin</div>
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

    <div class="grid grid-cols-[1fr_380px] gap-7 max-[900px]:grid-cols-1">
        <!-- USER TABLE -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-4">Semua User</h2>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3">Nama / Email</th>
                            <th class="text-left px-4 py-3 max-[768px]:hidden">Unit</th>
                            <th class="text-center px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors" id="uRow{{ $user->id }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center shrink-0">
                                        <span class="text-[11px] font-bold text-sky-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-semibold text-[13px] text-slate-900">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                            <span class="h-4 px-1.5 bg-sky-100 text-sky-700 rounded text-[9px] font-bold">Kamu</span>
                                            @endif
                                            @php $role = $user->roles->first() @endphp
                                            @if($role)
                                            <span class="h-4 px-1.5 rounded text-[9px] font-bold
                                                {{ $role->name === 'super_admin' ? 'bg-amber-100 text-amber-700' : ($role->name === 'unit_manager' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600') }}">
                                                {{ $role->name }}
                                            </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 max-[768px]:hidden">
                                <span class="text-[12px] text-slate-600">{{ $user->organizationalUnit?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center h-5 px-2 rounded-full text-[10px] font-semibold
                                    {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1.5 justify-end">
                                    <button onclick="toggleEditUser({{ $user->id }})"
                                        class="h-7 px-2.5 bg-slate-50 border border-slate-200 rounded text-[11px] text-slate-500 font-semibold cursor-pointer hover:border-sky-300 hover:text-sky-600 transition-colors">
                                        Edit
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-7 px-2.5 bg-red-50 border border-red-200 rounded text-[11px] text-red-600 font-semibold cursor-pointer hover:bg-red-100 transition-colors">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- Inline edit row -->
                        <tr id="editURow{{ $user->id }}" class="hidden bg-sky-50/60">
                            <td colspan="4" class="px-4 py-4">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-col gap-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3 max-[480px]:grid-cols-1">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Nama *</label>
                                            <input type="text" name="name" value="{{ $user->name }}" required
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Email *</label>
                                            <input type="email" name="email" value="{{ $user->email }}" required
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Password Baru <span class="font-normal text-slate-400">(kosong = tidak ubah)</span></label>
                                            <input type="password" name="password"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Unit Organisasi</label>
                                            <select name="organizational_unit_id"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                                <option value="">— Pilih Unit —</option>
                                                @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ $user->organizational_unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Role</label>
                                            <select name="role"
                                                class="w-full h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                                                @foreach($roles as $r)
                                                <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_active" value="1" id="uActive{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }} class="accent-sky-700">
                                        <label for="uActive{{ $user->id }}" class="text-[12px] text-slate-700 cursor-pointer">Aktif</label>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="h-7 px-4 bg-sky-700 text-white rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                                        <button type="button" onclick="toggleEditUser({{ $user->id }})"
                                            class="h-7 px-3 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD USER -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-3">Tambah User</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ahmad Fauzi" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@bpsdm.go.id" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
                    <select name="organizational_unit_id"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="">— Pilih Unit —</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Role</label>
                    <select name="role" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="newUActive" checked class="accent-sky-700">
                    <label for="newUActive" class="text-[13px] text-slate-700 cursor-pointer">Aktif</label>
                </div>
                <button type="submit" class="h-9 w-full bg-sky-700 text-white rounded-lg text-[13px] font-bold border-none cursor-pointer hover:bg-sky-600">
                    Buat User
                </button>
            </form>
        </div>
    </div>
</main>

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

const counter = (el, to) => {
    if (!el || to === 0) { if(el) el.textContent = 0; return; }
    const obj = { val: 0 };
    animate(obj, { val: to, duration: 1000, ease: 'outExpo',
        onUpdate: () => { el.textContent = Math.round(obj.val); }
    });
};

const tl = createTimeline({ defaults: { ease: 'outExpo' } });
tl.add('#userHeroBadge', { opacity:[0,1], translateY:[-10,0], duration:500 }, 100)
  .add('#userHeroTop',   { opacity:[0,1], translateY:[-16,0], duration:600 }, 180)
  .add('.stat-card',     { opacity:[0,1], translateY:[20,0], duration:450, delay:stagger(80) }, 380);

counter(document.getElementById('statUTotal'),  {{ $stats['total'] }});
counter(document.getElementById('statUActive'), {{ $stats['active'] }});
counter(document.getElementById('statUSA'),     {{ $stats['super_admin'] }});
counter(document.getElementById('statUUM'),     {{ $stats['unit_manager'] }});
counter(document.getElementById('statUAdmin'),  {{ $stats['admin'] }});
</script>
<script>
function toggleEditUser(id) {
    const row = document.getElementById('editURow' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
</body>
</html>
