<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Batch — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<main class="max-w-[680px] mx-auto px-6 pt-10 pb-16 max-[640px]:px-4">
    <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center gap-1.5 text-[13px] text-slate-500 hover:text-sky-700 no-underline mb-5">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke Batch
    </a>
    <h1 class="text-[22px] font-bold text-slate-900 mb-1">Batch Baru</h1>
    <p class="text-sm text-slate-500 mb-7">Buat angkatan pelatihan. Jadwal akan di-generate setelah batch tersimpan.</p>

    <form method="POST" action="{{ route('admin.batches.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col gap-5">
        @csrf
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-[13px] text-red-800">{{ $errors->first() }}</div>
        @endif

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Template Pelatihan *</label>
            <select name="training_template_id" required
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                <option value="">— Pilih Template —</option>
                @foreach($templates as $template)
                <option value="{{ $template->id }}" {{ old('training_template_id') == $template->id ? 'selected' : '' }}>
                    [{{ $template->code }}] {{ $template->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama Batch *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="PKA Angkatan I 2026" required
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
        </div>

        <div class="grid grid-cols-2 gap-4 max-[480px]:grid-cols-1">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nomor Angkatan *</label>
                <input type="number" name="batch_number" value="{{ old('batch_number', 1) }}" min="1" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tahun *</label>
                <input type="number" name="year" value="{{ old('year', 2026) }}" min="2020" max="2099" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 max-[480px]:grid-cols-1">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tanggal Mulai *</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Jumlah Peserta *</label>
                <input type="number" name="participant_count" value="{{ old('participant_count', 30) }}" min="1" required
                    class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
            </div>
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
            <select name="organizational_unit_id"
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                <option value="">— Pilih Unit —</option>
                @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.batches.index') }}" class="h-10 px-5 flex items-center bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold no-underline hover:bg-slate-200">Batal</a>
            <button type="submit" class="h-10 px-5 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600">Buat Batch</button>
        </div>
    </form>
</main>
</body>
</html>
