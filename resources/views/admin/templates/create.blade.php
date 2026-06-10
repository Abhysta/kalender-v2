<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Template — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<main class="max-w-[680px] mx-auto px-6 pt-10 pb-16 max-[640px]:px-4">
    <div class="mb-7">
        <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-1.5 text-[13px] text-slate-500 hover:text-sky-700 no-underline mb-4">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Template
        </a>
        <h1 class="text-[22px] font-bold text-slate-900">Template Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Buat template pelatihan. Tambah phase setelah template tersimpan.</p>
    </div>

    <form method="POST" action="{{ route('admin.templates.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col gap-5">
        @csrf
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-[13px] text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 gap-4 max-[480px]:grid-cols-1">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Kode Template *</label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="PKA" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none transition-all focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)] focus:bg-white">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama Template *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Pelatihan Kepemimpinan Tingkat II" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none transition-all focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)] focus:bg-white">
            </div>
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
            @if(Auth::user()->hasRole('super_admin'))
            <select name="organizational_unit_id"
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none transition-all focus:border-sky-700 focus:bg-white select-custom">
                <option value="">— Semua Unit —</option>
                @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
                @endforeach
            </select>
            @else
            <input type="hidden" name="organizational_unit_id" value="{{ Auth::user()->organizational_unit_id }}">
            <div class="w-full h-10 bg-slate-100 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-500 flex items-center cursor-not-allowed">
                {{ Auth::user()->organizationalUnit?->name ?? '—' }}
            </div>
            @endif
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" placeholder="Deskripsi singkat template..."
                class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-900 outline-none transition-all focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)] focus:bg-white resize-none">{{ old('description') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.templates.index') }}" class="h-10 px-5 flex items-center bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold no-underline hover:bg-slate-200 transition-colors">Batal</a>
            <button type="submit" class="h-10 px-5 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600 transition-colors">Simpan Template</button>
        </div>
    </form>
</main>
</body>
</html>
