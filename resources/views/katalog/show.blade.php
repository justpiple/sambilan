@extends('layouts.app')

@section('title', e($lowongan->judul) . ' — ' . e($lowongan->perusahaan?->nama ?? 'Sambilan'))

@section('content')
<div class="space-y-6 py-2">
    <!-- Breadcrumb & Back Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-stone-200/70">
        <nav class="flex items-center gap-2 text-xs text-stone-500 flex-wrap min-w-0" aria-label="Breadcrumb">
            <a href="{{ route('katalog.index') }}" class="hover:text-orange-700 transition-colors">Katalog</a>
            <span class="text-stone-300">/</span>
            @if ($lowongan->kategori)
                <a href="{{ route('katalog.index', ['kategori_id' => $lowongan->kategori_id]) }}" class="hover:text-orange-700 transition-colors">
                    {{ $lowongan->kategori->nama }}
                </a>
                <span class="text-stone-300">/</span>
            @endif
            <span class="text-slate-900 font-medium truncate max-w-xs">{{ $lowongan->judul }}</span>
        </nav>

        <a
            href="{{ url()->previous() !== url()->current() ? url()->previous() : route('katalog.index') }}"
            class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-1.5 text-xs font-semibold text-stone-700 hover:text-slate-900 bg-white border border-stone-200 rounded-lg hover:border-stone-300 transition-all active:scale-[0.98] shrink-0"
        >
            <svg class="w-4 h-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Katalog</span>
        </a>
    </div>

    <!-- Expired Job Alert -->
    @if (!$isAktif)
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4 sm:p-5 flex items-start gap-3 shadow-xs break-words" role="alert">
            <div class="p-2 rounded-lg bg-amber-100 text-amber-800 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-bold text-amber-900">
                    Lowongan Telah Berakhir
                </h2>
                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                    Masa pendaftaran posisi ini telah berakhir pada <span class="font-semibold">{{ $lowongan->tenggat ? $lowongan->tenggat->format('d M Y') : '-' }}</span> atau status telah ditutup oleh pengelola. Pendaftaran baru sudah tidak dapat diproses.
                </p>
                <div class="mt-3">
                    <a href="{{ route('katalog.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-semibold text-amber-900 hover:text-amber-950 underline">
                        Lihat lowongan aktif lainnya &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Vacancy Header Hero Card -->
    <section class="bg-white rounded-2xl border border-stone-200 p-5 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-bold uppercase tracking-wider text-stone-500 min-w-0 truncate">
                {{ $lowongan->perusahaan?->nama ?? 'Instansi Mitra' }}
            </p>
            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                <x-badge :variant="$lowongan->tipe">
                    {{ $lowongan->tipe_label }}
                </x-badge>
                <x-badge :variant="$lowongan->sistem_kerja">
                    {{ $lowongan->sistem_kerja_label }}
                </x-badge>
                @if ($lowongan->kategori)
                    <x-badge variant="default">
                        {{ $lowongan->kategori->nama }}
                    </x-badge>
                @endif
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-slate-900 text-balance break-words">
            {{ $lowongan->judul }}
        </h1>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-stone-600 pt-1">
            <span class="inline-flex items-center gap-1.5 min-w-0">
                <svg class="h-4 w-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ $lowongan->lokasi }}</span>
            </span>
            <span class="text-stone-300" aria-hidden="true">&bull;</span>
            <span class="inline-flex items-center gap-1.5 min-w-0">
                <svg class="h-4 w-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="truncate">{{ $lowongan->perusahaan?->nama }}</span>
            </span>
        </div>
    </section>

    <!-- Key Metadata Highlights Matrix (4 Boxes) -->
    <section aria-label="Ringkasan Kebutuhan Lowongan" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Box 1: Kompensasi -->
        <div class="bg-white rounded-xl border border-stone-200 p-4 sm:p-5 flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Kompensasi</span>
            <div class="mt-2">
                @if ($lowongan->kompensasi !== null && $lowongan->kompensasi > 0)
                    <p class="font-mono tabular-nums text-base sm:text-lg font-bold text-emerald-700">
                        {{ $lowongan->formatted_kompensasi }}
                    </p>
                @else
                    <p class="text-sm sm:text-base font-semibold text-stone-700">
                        Non-kompensasi
                    </p>
                @endif
            </div>
            <span class="text-[11px] text-stone-500 mt-1">Uang saku transparan</span>
        </div>

        <!-- Box 2: Beban Jam per Minggu -->
        <div class="bg-white rounded-xl border border-stone-200 p-4 sm:p-5 flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Beban Waktu</span>
            <div class="mt-2">
                <p class="font-mono tabular-nums text-base sm:text-lg font-bold text-slate-900">
                    {{ $lowongan->jam_per_minggu }} jam/minggu
                </p>
            </div>
            <span class="text-[11px] text-stone-500 mt-1">Ramah jadwal kuliah</span>
        </div>

        <!-- Box 3: Batas Pendaftaran (Tenggat) -->
        <div class="bg-white rounded-xl border border-stone-200 p-4 sm:p-5 flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Batas Tenggat</span>
            <div class="mt-2">
                <p class="font-mono tabular-nums text-base sm:text-lg font-bold text-slate-900">
                    {{ $lowongan->tenggat ? $lowongan->tenggat->format('d M Y') : '-' }}
                </p>
            </div>
            <div class="mt-1">
                @php
                $remainingDays = $lowongan->tenggat ? (int) now()->startOfDay()->diffInDays($lowongan->tenggat->startOfDay(), false) : -1;
                @endphp
                @if ($remainingDays > 1)
                    <span class="text-[11px] font-medium text-orange-700">Sisa {{ $remainingDays }} hari lagi</span>
                @elseif ($remainingDays === 1)
                    <span class="text-[11px] font-semibold text-orange-700">Sisa 1 hari lagi (Besok)</span>
                @elseif ($remainingDays === 0)
                    <span class="text-[11px] font-bold text-rose-700">Hari terakhir pendaftaran</span>
                @else
                    <span class="text-[11px] font-medium text-stone-400">Pendaftaran berakhir</span>
                @endif
            </div>
        </div>

        <!-- Box 4: Sistem Kerja & Format -->
        <div class="bg-white rounded-xl border border-stone-200 p-4 sm:p-5 flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Sistem Kerja</span>
            <div class="mt-2">
                <p class="text-base sm:text-lg font-bold text-slate-900">
                    {{ $lowongan->sistem_kerja_label }}
                </p>
            </div>
            <span class="text-[11px] text-stone-500 mt-1 truncate">{{ $lowongan->lokasi }}</span>
        </div>
    </section>

    <!-- Main Content & Sticky Action Card (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-2">
        <!-- Left Column: Detailed Description and Requirements (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Job Description Block -->
            <article class="bg-white rounded-2xl border border-stone-200 p-5 sm:p-8 shadow-xs space-y-3">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                    Deskripsi Pekerjaan
                </h2>
                <div class="text-sm sm:text-base text-stone-700 leading-relaxed whitespace-pre-line text-pretty break-words">
                    {{ $lowongan->deskripsi }}
                </div>
            </article>

            <!-- Job Requirements Block -->
            <article class="bg-white rounded-2xl border border-stone-200 p-5 sm:p-8 shadow-xs space-y-3">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                    Kualifikasi &amp; Persyaratan
                </h2>
                <div class="text-sm sm:text-base text-stone-700 leading-relaxed whitespace-pre-line text-pretty break-words">
                    {{ $lowongan->persyaratan }}
                </div>
            </article>
        </div>

        <!-- Right Column: Application CTA and Recruiter Profile (1 Col, Sticky) -->
        <aside class="space-y-6">
            <div class="bg-white rounded-2xl border border-stone-200 p-5 sm:p-6 shadow-xs space-y-5 lg:sticky lg:top-24">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-2 border-b border-stone-100">
                    Aksi &amp; Pendaftaran Mahasiswa
                </h3>

                <!-- Primary CTA: External Application Link -->
                @if ($isAktif && $lowongan->link_pendaftaran)
                    <div class="space-y-2">
                        <a
                            href="{{ $lowongan->link_pendaftaran }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full min-h-[48px] px-5 py-3 rounded-lg bg-orange-700 hover:bg-orange-800 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-xs transition-all duration-150 active:scale-[0.98]"
                        >
                            <span>Lamar Sekarang</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <p class="text-[11px] text-stone-500 text-center leading-normal">
                            Tautan eksternal resmi diarahkan ke sistem rekrutmen mitra.
                        </p>
                    </div>
                @else
                    <div class="space-y-2">
                        <button
                            type="button"
                            disabled
                            class="w-full min-h-[48px] px-5 py-3 rounded-lg bg-stone-200 text-stone-500 font-semibold text-sm flex items-center justify-center cursor-not-allowed"
                        >
                            Pendaftaran Ditutup
                        </button>
                        <p class="text-[11px] text-stone-400 text-center">
                            Posisi ini tidak lagi menerima pendaftaran baru.
                        </p>
                    </div>
                @endif

                <!-- Secondary CTA: Recruiter Contact (WhatsApp / Email / Phone) -->
                @php
                $kontak = $lowongan->kontak_pendaftaran ?? $lowongan->kontak_narahubung;
                $kontakUrl = null;
                $kontakLabel = 'Hubungi Narahubung';
                if ($kontak) {
                    $cleaned = preg_replace('/[^0-9]/', '', $kontak);
                    if (str_starts_with($cleaned, '08')) {
                        $waNum = '628' . substr($cleaned, 2);
                        $kontakUrl = 'https://wa.me/' . $waNum . '?text=' . rawurlencode('Halo, saya mahasiswa yang tertarik mendaftar lowongan ' . $lowongan->judul . ' di Sambilan.');
                        $kontakLabel = 'WhatsApp Narahubung';
                    } elseif (str_starts_with($cleaned, '62')) {
                        $kontakUrl = 'https://wa.me/' . $cleaned . '?text=' . rawurlencode('Halo, saya mahasiswa yang tertarik mendaftar lowongan ' . $lowongan->judul . ' di Sambilan.');
                        $kontakLabel = 'WhatsApp Narahubung';
                    } elseif (str_contains($kontak, '@')) {
                        $kontakUrl = 'mailto:' . $kontak . '?subject=' . rawurlencode('Pertanyaan Lowongan: ' . $lowongan->judul);
                        $kontakLabel = 'Email Narahubung';
                    } else {
                        $kontakUrl = 'tel:' . $kontak;
                        $kontakLabel = 'Telepon Narahubung';
                    }
                }
                @endphp

                @if ($kontakUrl)
                    <div class="pt-1">
                        <a
                            href="{{ $kontakUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full min-h-[44px] px-4 py-2.5 rounded-lg border border-stone-300 bg-white hover:bg-stone-50 text-slate-800 font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-150 active:scale-[0.98]"
                        >
                            <svg class="w-4 h-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>{{ $kontakLabel }}</span>
                        </a>
                    </div>
                @endif

                <!-- Company Profile Details -->
                <div class="pt-4 border-t border-stone-100 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Profil Instansi Mitra
                    </h4>
                    <p class="text-sm font-semibold text-slate-800 break-words">
                        {{ $lowongan->perusahaan?->nama ?? 'Instansi Mitra' }}
                    </p>

                    <div class="text-xs text-stone-600 space-y-2">
                        @if ($lowongan->perusahaan?->email)
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <a href="mailto:{{ $lowongan->perusahaan->email }}" class="hover:text-orange-700 transition-colors truncate break-all">
                                    {{ $lowongan->perusahaan->email }}
                                </a>
                            </div>
                        @endif

                        @if ($lowongan->perusahaan?->sosial_media)
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <span class="truncate break-all">{{ $lowongan->perusahaan->sosial_media }}</span>
                            </div>
                        @endif

                        @if ($kontak)
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="font-mono tabular-nums truncate">{{ $kontak }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Related Active Vacancies Section -->
    @if ($relatedJobs->isNotEmpty())
        <section class="pt-8 border-t border-stone-200/80 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                        Lowongan Terkait Lainnya
                    </h2>
                    <p class="text-xs text-stone-500">
                        Kesempatan aktif lainnya di bidang {{ $lowongan->kategori?->nama ?? 'serupa' }}
                    </p>
                </div>
                @if ($lowongan->kategori_id)
                    <a href="{{ route('katalog.index', ['kategori_id' => $lowongan->kategori_id]) }}" class="inline-flex items-center min-h-[44px] text-xs font-semibold text-orange-700 hover:text-orange-900 underline">
                        Lihat Semua &rarr;
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($relatedJobs as $related)
                    <x-job-card :lowongan="$related" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
