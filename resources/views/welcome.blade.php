@extends('layouts.app')

@section('title', 'Sambilan — Direktori Lowongan Magang & Paruh Waktu Mahasiswa')

@section('content')
<div class="space-y-12 py-4">
    <!-- Hero Section -->
    <section class="text-center max-w-3xl mx-auto space-y-4 pt-4 sm:pt-8">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-slate-900 text-balance">
            Katalog Lowongan Magang &amp; Kerja Paruh Waktu Mahasiswa
        </h1>
        <p class="text-base sm:text-lg text-stone-600 leading-relaxed text-pretty max-w-2xl mx-auto">
            Temukan kesempatan berkarya dan mandiri secara finansial yang ramah jadwal kuliah. Terkurasi dengan transparansi jam per minggu, sistem kerja, dan nominal kompensasi.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center min-h-[44px] px-6 py-2.5 text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98] shadow-sm">
                Jelajahi Lowongan
            </a>
            <a href="#panduan" class="w-full sm:w-auto inline-flex items-center justify-center min-h-[44px] px-5 py-2.5 text-sm font-semibold text-slate-800 bg-white hover:bg-stone-100 rounded-lg transition-colors border border-stone-200 active:scale-[0.98]">
                Pelajari Panduan
            </a>
        </div>
    </section>

    <!-- Value Highlights Grid -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
        <div class="bg-white rounded-xl border border-stone-200 p-6 space-y-2">
            <div class="text-xs font-semibold text-orange-700 uppercase tracking-wider">
                01 &bull; Fleksibilitas Waktu
            </div>
            <h2 class="text-lg font-bold text-slate-900">
                Ramah Jadwal Kuliah
            </h2>
            <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                Setiap posisi mencantumkan beban komitmen jam per minggu yang realistis, menjaga keseimbangan antara studi dan pekerjaan.
            </p>
        </div>

        <div class="bg-white rounded-xl border border-stone-200 p-6 space-y-2">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">
                02 &bull; Transparansi Finansial
            </div>
            <h2 class="text-lg font-bold text-slate-900">
                Kejelasan Kompensasi
            </h2>
            <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                Informasi uang saku dalam Rupiah atau penandaan transparan non-kompensasi tanpa biaya tersembunyi bagi mahasiswa pelamar.
            </p>
        </div>

        <div class="bg-white rounded-xl border border-stone-200 p-6 space-y-2">
            <div class="text-xs font-semibold text-slate-700 uppercase tracking-wider">
                03 &bull; Akses Terverifikasi
            </div>
            <h2 class="text-lg font-bold text-slate-900">
                Koneksi Mitra Langsung
            </h2>
            <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                Tautan pendaftaran eksternal dan kontak rekruter resmi yang dapat diakses langsung oleh mahasiswa tanpa perantara rumit.
            </p>
        </div>
    </section>
</div>
@endsection
