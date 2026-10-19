<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Tahunan Baru — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<main class="max-w-[680px] mx-auto px-6 pt-10 pb-16 max-[640px]:px-4">
    <a href="{{ route('admin.annual-calendars.index') }}" class="inline-flex items-center gap-1.5 text-[13px] text-slate-500 hover:text-sky-700 no-underline mb-5">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke Kalender Tahunan
    </a>
    <h1 class="text-[22px] font-bold text-slate-900 mb-1">Kalender Tahunan Baru</h1>
    <p class="text-sm text-slate-500 mb-7">Rencana kegiatan untuk satu tahun. Opsional — tidak wajib untuk membuat Batch/Sequence.</p>

    <form method="POST" action="{{ route('admin.annual-calendars.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col gap-5">
        @csrf
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-[13px] text-red-800">{{ $errors->first() }}</div>
        @endif

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama Kalender *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Kalender Pelatihan 2027" required
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tahun *</label>
            <input type="number" name="year" value="{{ old('year', date('Y') + 1) }}" min="2020" max="2099" required
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
            @if(Auth::user()->hasRole('super_admin'))
            <select name="organizational_unit_id"
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                <option value="">— Pilih Unit —</option>
                @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
            @else
            <input type="hidden" name="organizational_unit_id" value="{{ Auth::user()->organizational_unit_id }}">
            <div class="w-full h-10 bg-slate-100 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-500 flex items-center cursor-not-allowed">
                {{ Auth::user()->organizationalUnit?->name ?? '—' }}
            </div>
            @endif
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.annual-calendars.index') }}" class="h-10 px-5 flex items-center bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold no-underline hover:bg-slate-200">Batal</a>
            <button type="submit" class="h-10 px-5 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600">Buat Kalender</button>
        </div>
    </form>
</main>
</body>
</html>
