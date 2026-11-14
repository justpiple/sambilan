@php
    $lowongans = $lowongans ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9);
    $kategoris = $kategoris ?? collect();
    $hasActiveFilters = $hasActiveFilters ?? false;
@endphp

<div id="katalog" class="space-y-6">
    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-xl border border-stone-200/90 p-4 sm:p-5 shadow-xs space-y-4">
        <form method="GET" action="{{ url()->current() }}" class="space-y-4">
            <!-- Row 1: Search & Category -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-7 relative">
                    <label for="search-input" class="sr-only">Cari judul posisi atau instansi mitra</label>
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
                        class="w-full min-h-[44px] rounded-lg border border-stone-300 bg-stone-50/50 pl-11 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-stone-400 transition-colors focus:border-orange-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-600/20"
                    />
                </div>

                <div class="md:col-span-3">
                    <label for="kategori-select" class="sr-only">Pilih Kategori</label>
                    <select
                        name="kategori_id"
                        id="kategori-select"
                        onchange="this.form.submit()"
                        class="w-full min-h-[44px] rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-slate-800 transition-colors focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20 cursor-pointer"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected((string) request('kategori_id') === (string) $kategori->id)>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button
                        type="submit"
                        class="w-full min-h-[44px] px-4 py-2.5 inline-flex items-center justify-center rounded-lg bg-orange-700 hover:bg-orange-800 text-white text-sm font-semibold transition-all duration-150 active:scale-[0.98] shadow-xs"
                    >
                        Cari
                    </button>
                </div>
            </div>

            <!-- Row 2: Tactile Filter Pills for Type and Work Arrangement -->
            <div class="pt-3 border-t border-stone-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Job Type Filter Pills -->
                <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter Tipe Lowongan">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 mr-1">Tipe:</span>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe" value="" class="sr-only peer" {{ !request('tipe') ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-orange-700 peer-checked:text-white peer-checked:border-orange-700 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Semua
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe" value="magang" class="sr-only peer" {{ request('tipe') === 'magang' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-orange-700 peer-checked:text-white peer-checked:border-orange-700 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Magang
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe" value="paruh_waktu" class="sr-only peer" {{ request('tipe') === 'paruh_waktu' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-orange-700 peer-checked:text-white peer-checked:border-orange-700 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Paruh Waktu
                        </span>
                    </label>
                </div>

                <!-- Work Arrangement Filter Pills -->
                <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Filter Sistem Kerja">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-500 mr-1">Sistem Kerja:</span>
                    <label class="cursor-pointer">
                        <input type="radio" name="sistem_kerja" value="" class="sr-only peer" {{ !request('sistem_kerja') ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Semua
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="sistem_kerja" value="onsite" class="sr-only peer" {{ request('sistem_kerja') === 'onsite' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Onsite
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="sistem_kerja" value="remote" class="sr-only peer" {{ request('sistem_kerja') === 'remote' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Remote
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="sistem_kerja" value="hybrid" class="sr-only peer" {{ request('sistem_kerja') === 'hybrid' ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="min-h-[38px] px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all inline-flex items-center peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 bg-stone-50 text-stone-700 border-stone-200 hover:bg-stone-100 active:scale-[0.98]">
                            Hybrid
                        </span>
                    </label>
                </div>
            </div>

            <!-- Active Filter Badges Bar -->
            @if ($hasActiveFilters)
                <div class="pt-3 border-t border-stone-100 flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-medium text-stone-500">Filter Aktif:</span>

                    @if (request('q'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 text-orange-900 border border-orange-200 font-medium">
                            <span>Kata Kunci: "{{ request('q') }}"</span>
                            <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="hover:text-orange-950 p-0.5 rounded" aria-label="Hapus filter pencarian">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </span>
                    @endif

                    @if (request('kategori_id') && ($activeCat = $kategoris->firstWhere('id', request('kategori_id'))))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 text-orange-900 border border-orange-200 font-medium">
                            <span>Kategori: {{ $activeCat->nama }}</span>
                            <a href="{{ request()->fullUrlWithQuery(['kategori_id' => null, 'page' => null]) }}" class="hover:text-orange-950 p-0.5 rounded" aria-label="Hapus filter kategori">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </span>
                    @endif

                    @if (request('tipe'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 text-orange-900 border border-orange-200 font-medium">
                            <span>Tipe: {{ request('tipe') === 'magang' ? 'Magang' : 'Paruh Waktu' }}</span>
                            <a href="{{ request()->fullUrlWithQuery(['tipe' => null, 'page' => null]) }}" class="hover:text-orange-950 p-0.5 rounded" aria-label="Hapus filter tipe">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </span>
                    @endif

                    @if (request('sistem_kerja'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-900 border border-slate-200 font-medium">
                            <span>Sistem: {{ ucfirst(request('sistem_kerja')) }}</span>
                            <a href="{{ request()->fullUrlWithQuery(['sistem_kerja' => null, 'page' => null]) }}" class="hover:text-slate-950 p-0.5 rounded" aria-label="Hapus filter sistem kerja">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </span>
                    @endif

                    <a href="{{ url()->current() }}" class="inline-flex items-center text-xs font-semibold text-orange-700 hover:text-orange-900 underline ml-auto">
                        Hapus Semua Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Results Status Count -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-stone-600">
        <p>
            Menampilkan <span class="font-mono font-semibold text-slate-800">{{ $lowongans->total() }}</span> lowongan aktif
            @if ($hasActiveFilters)
                berdasarkan filter yang dipilih
            @endif
        </p>
    </div>

    <!-- Vacancy Grid or Empty State -->
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
                <h3 class="text-base sm:text-lg font-bold text-slate-900 text-balance">
                    Tidak Ada Lowongan yang Sesuai
                </h3>
                <p class="text-sm text-stone-600 leading-relaxed text-pretty">
                    Tidak ditemukan data lowongan kerja dengan kombinasi filter yang Anda pilih. Coba sesuaikan kata kunci pencarian atau hapus filter untuk melihat semua posisi aktif.
                </p>
                <div class="pt-2">
                    <a
                        href="{{ url()->current() }}"
                        class="inline-flex items-center justify-center min-h-[44px] px-5 py-2 text-xs font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98]"
                    >
                        Reset Semua Filter
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
