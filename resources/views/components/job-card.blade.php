@props([
    'lowongan',
])

@php
$detailUrl = Route::has('lowongan.show')
    ? route('lowongan.show', $lowongan->id)
    : url('/lowongan/' . $lowongan->id);

$isPaid = $lowongan->kompensasi !== null && $lowongan->kompensasi > 0;
@endphp

<article {{ $attributes->merge(['class' => 'group relative bg-white rounded-xl border border-stone-200/90 p-5 sm:p-6 flex flex-col justify-between hover:border-stone-300 hover:shadow-sm transition-all duration-150']) }}>
    <div>
        <div class="flex items-start justify-between gap-3 mb-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 line-clamp-1">
                {{ $lowongan->perusahaan?->nama ?? 'Perusahaan Mitra' }}
            </p>
            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                <x-badge :variant="$lowongan->tipe">
                    {{ $lowongan->tipe_label }}
                </x-badge>
                <x-badge :variant="$lowongan->sistem_kerja">
                    {{ $lowongan->sistem_kerja_label }}
                </x-badge>
            </div>
        </div>

        <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-orange-700 transition-colors text-balance line-clamp-2 mb-2">
            <a href="{{ $detailUrl }}" class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 rounded">
                {{ $lowongan->judul }}
            </a>
        </h3>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-600 mb-4">
            <span class="inline-flex items-center gap-1">
                <svg class="h-3.5 w-3.5 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ $lowongan->lokasi }}</span>
            </span>
            <span class="text-stone-300" aria-hidden="true">•</span>
            <span class="font-mono tabular-nums font-medium text-stone-600">
                {{ $lowongan->jam_per_minggu }} jam/minggu
            </span>
        </div>
    </div>

    <div class="mt-auto pt-4 border-t border-stone-100 flex flex-col gap-3">
        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-[11px] uppercase tracking-wider text-stone-400 block mb-0.5">Kompensasi</span>
                @if ($isPaid)
                    <span class="font-mono tabular-nums font-semibold text-emerald-700 text-sm">
                        {{ $lowongan->formatted_kompensasi }}
                    </span>
                @else
                    <span class="font-medium text-stone-500 text-sm">
                        {{ $lowongan->formatted_kompensasi }}
                    </span>
                @endif
            </div>

            <div class="text-right">
                <span class="text-[11px] uppercase tracking-wider text-stone-400 block mb-0.5">Tenggat</span>
                <time datetime="{{ $lowongan->tenggat?->format('Y-m-d') }}" class="font-mono tabular-nums font-medium text-stone-700 text-xs">
                    {{ $lowongan->tenggat ? $lowongan->tenggat->format('d M Y') : '-' }}
                </time>
            </div>
        </div>

        <a href="{{ $detailUrl }}" class="inline-flex items-center justify-center min-h-[44px] w-full px-4 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-all duration-150 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
            <span>Lihat Detail</span>
            <svg class="ml-1.5 h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</article>
