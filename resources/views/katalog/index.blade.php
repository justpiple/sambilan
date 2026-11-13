@extends('layouts.app')

@section('title', 'Katalog Lowongan Magang & Paruh Waktu Mahasiswa — Sambilan')

@section('content')
<div class="space-y-8 py-4">
    <div class="border-b border-stone-200/80 pb-5">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 text-balance">
            Katalog Lowongan Mahasiswa
        </h1>
        <p class="mt-1.5 text-sm sm:text-base text-stone-600 text-pretty max-w-3xl">
            Eksplorasi kesempatan magang dan kerja paruh waktu terkurasi yang selaras dengan jadwal perkuliahan Anda.
        </p>
    </div>

    @include('katalog.partials.catalog-grid')
</div>
@endsection
