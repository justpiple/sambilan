@extends('layouts.admin')

@section('title', 'Tambah Kategori — Admin Sambilan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Kategori</h1>
            <p class="text-sm text-stone-600 mt-1">Tambahkan bidang atau klasifikasi pekerjaan baru ke dalam katalog.</p>
        </div>
        <a href="{{ route('admin.kategoris.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-3.5 py-2 text-sm font-medium text-stone-600 hover:text-slate-900 hover:bg-stone-100 rounded-lg transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.kategoris.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="nama" class="block text-sm font-medium text-slate-900 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama') }}"
                    required
                    maxlength="100"
                    placeholder="Contoh: Software Engineering"
                    class="w-full rounded-lg border @error('nama') border-red-500 @else border-stone-300 @enderror px-3.5 py-2.5 text-slate-900 text-sm focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                >
                @error('nama')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-slate-900 mb-1.5">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="3"
                    maxlength="255"
                    placeholder="Uraian singkat bidang pekerjaan atau kualifikasi umum..."
                    class="w-full rounded-lg border @error('deskripsi') border-red-500 @else border-stone-300 @enderror px-3.5 py-2.5 text-slate-900 text-sm focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <a href="{{ route('admin.kategoris.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-medium text-stone-700 bg-white border border-stone-300 hover:bg-stone-50 rounded-lg transition-colors active:scale-[0.98]">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-5 py-2 text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98] shadow-sm">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
