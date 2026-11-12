@extends('layouts.admin')

@section('title', 'Edit Perusahaan Mitra — Admin Sambilan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Perusahaan Mitra</h1>
            <p class="text-sm text-stone-600 mt-1">Perbarui profil dan kontak instansi penyedia lowongan.</p>
        </div>
        <a href="{{ route('admin.perusahaans.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-3.5 py-2 text-sm font-medium text-stone-600 hover:text-slate-900 hover:bg-stone-100 rounded-lg transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.perusahaans.update', $perusahaan) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-sm font-medium text-slate-900 mb-1.5">
                    Nama Perusahaan / Instansi <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $perusahaan->nama) }}"
                    required
                    maxlength="150"
                    class="w-full rounded-lg border @error('nama') border-red-500 @else border-stone-300 @enderror px-3.5 py-2.5 text-slate-900 text-sm focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                >
                @error('nama')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-900 mb-1.5">
                    Email Resmi Perusahaan <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $perusahaan->email) }}"
                    required
                    maxlength="255"
                    class="w-full rounded-lg border @error('email') border-red-500 @else border-stone-300 @enderror px-3.5 py-2.5 text-slate-900 text-sm focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sosial_media" class="block text-sm font-medium text-slate-900 mb-1.5">
                    Tautan Media Sosial / Website Resmi (Opsional)
                </label>
                <input
                    type="url"
                    id="sosial_media"
                    name="sosial_media"
                    value="{{ old('sosial_media', $perusahaan->sosial_media) }}"
                    maxlength="255"
                    class="w-full rounded-lg border @error('sosial_media') border-red-500 @else border-stone-300 @enderror px-3.5 py-2.5 text-slate-900 text-sm focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                >
                @error('sosial_media')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <a href="{{ route('admin.perusahaans.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-medium text-stone-700 bg-white border border-stone-300 hover:bg-stone-50 rounded-lg transition-colors active:scale-[0.98]">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-5 py-2 text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98] shadow-sm">
                    Perbarui Perusahaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
