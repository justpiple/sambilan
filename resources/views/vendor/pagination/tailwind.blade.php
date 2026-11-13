@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman Katalog" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-xs font-medium text-stone-400 bg-stone-100 border border-stone-200 cursor-not-allowed rounded-lg">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 active:scale-[0.98] transition-all">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 active:scale-[0.98] transition-all">
                    Berikutnya
                </a>
            @else
                <span class="relative inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-xs font-medium text-stone-400 bg-stone-100 border border-stone-200 cursor-not-allowed rounded-lg">
                    Berikutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-stone-600">
                    Menampilkan
                    <span class="font-mono font-semibold text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-mono font-semibold text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-mono font-semibold text-slate-900">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg shadow-2xs gap-1">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Halaman sebelumnya" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 active:scale-[0.98] transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-stone-400 bg-white border border-stone-200 rounded-lg">
                                {{ $element }}
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-bold text-white bg-orange-700 border border-orange-700 rounded-lg">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 active:scale-[0.98] transition-all" aria-label="Buka halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 active:scale-[0.98] transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Halaman berikutnya" class="relative inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 text-xs font-medium text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
