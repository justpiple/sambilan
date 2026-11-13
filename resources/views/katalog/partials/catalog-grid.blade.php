@php
    $lowongans = $lowongans ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9);
@endphp

<div id="katalog" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-stone-200/80 pb-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 text-balance">
                Daftar Lowongan Tersedia
            </h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                Menampilkan <span class="font-mono font-semibold text-slate-800">{{ $lowongans->total() }}</span> lowongan aktif
            </p>
        </div>
    </div>

    @if ($lowongans->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @foreach ($lowongans as $lowongan)
                <x-job-card :lowongan="$lowongan" />
            @endforeach
        </div>

        @if ($lowongans->hasPages())
            <div class="pt-6">
                {{ $lowongans->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl border border-stone-200/90 p-8 sm:p-12 text-center shadow-xs">
            <div class="max-w-md mx-auto space-y-3">
                <div class="h-12 w-12 rounded-full bg-stone-100 text-stone-400 mx-auto flex items-center justify-center">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                    Belum Ada Lowongan Aktif
                </h3>
                <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                    Saat ini belum ada data lowongan aktif yang dapat ditampilkan. Silakan periksa kembali nanti.
                </p>
            </div>
        </div>
    @endif
</div>
