@php
    $lowongans = $lowongans ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9);
@endphp

<div id="katalog" class="space-y-6">
    <div class="bg-white rounded-xl border border-stone-200/90 p-4 sm:p-5 shadow-xs">
        <form method="GET" action="{{ url()->current() }}" class="w-full">
            <div class="relative flex items-center">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <input
                    type="search"
                    name="q"
                    id="search-input"
                    value="{{ request('q') }}"
                    placeholder="Cari judul posisi lowongan atau nama perusahaan mitra..."
                    aria-label="Cari posisi atau perusahaan mitra"
                    class="w-full min-h-[48px] rounded-xl border border-stone-300 bg-stone-50/50 pl-11 pr-28 py-3 text-sm text-slate-900 placeholder:text-stone-400 transition-colors focus:border-orange-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                />

                <div class="absolute inset-y-0 right-0 flex items-center pr-1.5 gap-1.5">
                    @if(request('q'))
                        <a
                            href="{{ url()->current() }}"
                            aria-label="Hapus kata kunci pencarian"
                            class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-colors active:scale-[0.98]"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="min-h-[44px] px-4 sm:px-5 inline-flex items-center justify-center rounded-lg bg-orange-700 hover:bg-orange-800 text-white text-sm font-semibold transition-all duration-150 active:scale-[0.98] shadow-xs"
                    >
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-stone-600">
        <div class="flex items-center gap-2 flex-wrap">
            @if(request('q'))
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 px-3 py-1 text-xs font-medium text-orange-900 border border-orange-200">
                    <span>Kata kunci: <strong>"{{ request('q') }}"</strong></span>
                    <a
                        href="{{ url()->current() }}"
                        class="text-orange-700 hover:text-orange-950 p-0.5 rounded focus:outline-none"
                        aria-label="Hapus filter pencarian {{ request('q') }}"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </span>
            @endif
            <p>
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
                @if(request('q'))
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">
                        Tidak Ada Lowongan yang Sesuai
                    </h3>
                    <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                        Tidak ditemukan lowongan dengan kata kunci &ldquo;{{ request('q') }}&rdquo;. Coba periksa ejaan atau bersihkan filter pencarian.
                    </p>
                    <div class="pt-2">
                        <a
                            href="{{ url()->current() }}"
                            class="inline-flex items-center justify-center min-h-[44px] px-5 py-2 text-xs font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98]"
                        >
                            Reset Pencarian
                        </a>
                    </div>
                @else
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">
                        Belum Ada Lowongan Aktif
                    </h3>
                    <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                        Saat ini belum ada data lowongan aktif yang dapat ditampilkan. Silakan periksa kembali nanti.
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>
