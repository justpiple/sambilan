@extends('layouts.admin')

@section('title', 'Edit Lowongan — Admin Sambilan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Lowongan</h1>
            <p class="text-sm text-stone-600 mt-1">Perbarui data publikasi lowongan kerja mahasiswa.</p>
        </div>
        <a href="{{ route('admin.lowongans.index') }}" class="min-h-[44px] px-4 inline-flex items-center rounded-lg text-sm font-medium text-stone-600 hover:text-slate-900 border border-stone-200 bg-white hover:bg-stone-50 transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.lowongans.update', $lowongan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-2">Informasi Posisi & Instansi</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Judul Lowongan <span class="text-rose-600">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $lowongan->judul) }}" required maxlength="150" class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('judul') border-rose-400 @enderror">
                        @error('judul') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="perusahaan_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Perusahaan Mitra <span class="text-rose-600">*</span></label>
                        <select id="perusahaan_id" name="perusahaan_id" required class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white @error('perusahaan_id') border-rose-400 @enderror">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach ($perusahaans as $p)
                                <option value="{{ $p->id }}" {{ old('perusahaan_id', $lowongan->perusahaan_id) == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                        @error('perusahaan_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kategori_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Bidang <span class="text-rose-600">*</span></label>
                        <select id="kategori_id" name="kategori_id" required class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white @error('kategori_id') border-rose-400 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id', $lowongan->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-2">Ketentuan Kerja & Kompensasi</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tipe" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Pekerjaan <span class="text-rose-600">*</span></label>
                        <select id="tipe" name="tipe" required class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white @error('tipe') border-rose-400 @enderror">
                            <option value="magang" {{ old('tipe', $lowongan->tipe) === 'magang' ? 'selected' : '' }}>Magang</option>
                            <option value="paruh_waktu" {{ old('tipe', $lowongan->tipe) === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                        </select>
                        @error('tipe') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sistem_kerja" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Sistem Kerja <span class="text-rose-600">*</span></label>
                        <select id="sistem_kerja" name="sistem_kerja" required class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white @error('sistem_kerja') border-rose-400 @enderror">
                            <option value="onsite" {{ old('sistem_kerja', $lowongan->sistem_kerja) === 'onsite' ? 'selected' : '' }}>Onsite (WFO)</option>
                            <option value="remote" {{ old('sistem_kerja', $lowongan->sistem_kerja) === 'remote' ? 'selected' : '' }}>Remote (WFH)</option>
                            <option value="hybrid" {{ old('sistem_kerja', $lowongan->sistem_kerja) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('sistem_kerja') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jam_per_minggu" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Jam / Minggu <span class="text-rose-600">*</span></label>
                        <input type="number" id="jam_per_minggu" name="jam_per_minggu" value="{{ old('jam_per_minggu', $lowongan->jam_per_minggu) }}" min="1" max="40" required class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('jam_per_minggu') border-rose-400 @enderror">
                        @error('jam_per_minggu') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="lokasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Lokasi Kerja <span class="text-rose-600">*</span></label>
                        <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $lowongan->lokasi) }}" required maxlength="150" class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('lokasi') border-rose-400 @enderror">
                        @error('lokasi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kompensasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Kompensasi Bulanan (Rp)</label>
                        <input type="number" id="kompensasi" name="kompensasi" value="{{ old('kompensasi', $lowongan->kompensasi) }}" min="0" step="50000" placeholder="Kosongkan jika non-kompensasi" class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('kompensasi') border-rose-400 @enderror">
                        <span class="text-[11px] text-stone-500 mt-1 block">Format rupiah akan ditransparansikan dengan Forest Emerald di kartu lowongan.</span>
                        @error('kompensasi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-2">Deskripsi & Kualifikasi</h2>

                <div>
                    <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Pekerjaan <span class="text-rose-600">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" required minlength="20" class="w-full p-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('deskripsi') border-rose-400 @enderror">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="persyaratan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Persyaratan & Kualifikasi <span class="text-rose-600">*</span></label>
                    <textarea id="persyaratan" name="persyaratan" rows="5" required minlength="20" class="w-full p-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('persyaratan') border-rose-400 @enderror">{{ old('persyaratan', $lowongan->persyaratan) }}</textarea>
                    @error('persyaratan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-2">Informasi Pendaftaran</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="link_pendaftaran" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Tautan Pendaftaran Eksternal <span class="text-rose-600">*</span></label>
                        <input type="url" id="link_pendaftaran" name="link_pendaftaran" value="{{ old('link_pendaftaran', $lowongan->link_pendaftaran) }}" required class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('link_pendaftaran') border-rose-400 @enderror">
                        @error('link_pendaftaran') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kontak_narahubung" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Kontak Narahubung / Rekruter <span class="text-rose-600">*</span></label>
                        <input type="text" id="kontak_narahubung" name="kontak_narahubung" value="{{ old('kontak_narahubung', $lowongan->kontak_narahubung) }}" required class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('kontak_narahubung') border-rose-400 @enderror">
                        @error('kontak_narahubung') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tenggat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Batas Tenggat Pendaftaran <span class="text-rose-600">*</span></label>
                        <input type="date" id="tenggat" name="tenggat" value="{{ old('tenggat', $lowongan->tenggat ? $lowongan->tenggat->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}" required class="w-full min-h-[44px] px-3.5 text-sm font-mono rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent @error('tenggat') border-rose-400 @enderror">
                        @error('tenggat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Status Publikasi <span class="text-rose-600">*</span></label>
                        <select id="status" name="status" required class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white @error('status') border-rose-400 @enderror">
                            <option value="buka" {{ old('status', $lowongan->status) === 'buka' ? 'selected' : '' }}>Buka</option>
                            <option value="ditutup" {{ old('status', $lowongan->status) === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                        @error('status') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.lowongans.index') }}" class="min-h-[44px] px-5 inline-flex items-center rounded-lg text-sm font-medium text-stone-600 hover:text-slate-900 border border-stone-200 bg-white hover:bg-stone-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="min-h-[44px] px-6 inline-flex items-center justify-center rounded-lg text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 transition-colors shadow-sm active:scale-[0.98]">
                    Perbarui Lowongan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
