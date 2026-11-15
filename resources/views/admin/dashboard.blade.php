@extends('layouts.admin')

@section('title', 'Dasbor Statistik — Admin Sambilan')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-stone-200/80">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Dasbor Statistik Direktori
            </h1>
            <p class="mt-1 text-sm text-stone-600">
                Ringkasan metrik direktori lowongan magang & paruh waktu mahasiswa Sambilan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ url('/') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2 text-sm font-semibold text-slate-800 bg-white hover:bg-stone-50 border border-stone-200/90 rounded-xl shadow-sm active:scale-[0.98] transition-all"
            >
                <span>Lihat Katalog Publik</span>
                <span class="text-xs text-stone-400">&nearr;</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Metric Card 1: Total Perusahaan -->
        <div class="bg-white rounded-2xl border border-stone-200/90 p-6 shadow-sm hover:border-stone-300 transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                        Mitra Industri
                    </span>
                    <span class="p-2 rounded-lg bg-stone-100 text-stone-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold tracking-tight text-slate-900 font-mono tabular-nums">
                        {{ $totalPerusahaan }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        Total Perusahaan
                    </p>
                    <p class="mt-0.5 text-xs text-stone-500">
                        Perusahaan mitra kampus terdaftar
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-stone-100">
                <a
                    href="{{ Route::has('admin.perusahaans.index') ? route('admin.perusahaans.index') : url('/admin/perusahaans') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-700 hover:text-orange-800 transition-colors min-h-[36px]"
                >
                    <span>Kelola Data Perusahaan</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Metric Card 2: Total Kategori -->
        <div class="bg-white rounded-2xl border border-stone-200/90 p-6 shadow-sm hover:border-stone-300 transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                        Klasifikasi Bidang
                    </span>
                    <span class="p-2 rounded-lg bg-stone-100 text-stone-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold tracking-tight text-slate-900 font-mono tabular-nums">
                        {{ $totalKategori }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        Total Kategori
                    </p>
                    <p class="mt-0.5 text-xs text-stone-500">
                        Bidang peminatan karier mahasiswa
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-stone-100">
                <a
                    href="{{ Route::has('admin.kategoris.index') ? route('admin.kategoris.index') : url('/admin/kategoris') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-700 hover:text-orange-800 transition-colors min-h-[36px]"
                >
                    <span>Kelola Kategori</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Metric Card 3: Total Lowongan Aktif -->
        <div class="bg-white rounded-2xl border border-stone-200/90 p-6 shadow-sm hover:border-stone-300 transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        Katalog Publik
                    </span>
                    <span class="p-2 rounded-lg bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold tracking-tight text-emerald-700 font-mono tabular-nums">
                        {{ $totalLowonganAktif }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        Total Lowongan Aktif
                    </p>
                    <p class="mt-0.5 text-xs text-stone-500">
                        Status 'buka' & tenggat masa depan (dari total {{ $totalLowongan }} lowongan)
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-stone-100">
                <a
                    href="{{ Route::has('admin.lowongans.index') ? route('admin.lowongans.index') : url('/admin/lowongans') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-700 hover:text-orange-800 transition-colors min-h-[36px]"
                >
                    <span>Kelola Publikasi Lowongan</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
