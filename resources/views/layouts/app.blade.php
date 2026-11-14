<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sambilan — Direktori lowongan magang dan kerja paruh waktu terkurasi untuk mahasiswa aktif perguruan tinggi.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logogram.svg') }}">

    <title>@yield('title', 'Sambilan — Direktori Lowongan Magang & Paruh Waktu Mahasiswa')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-stone-50 text-slate-900 font-sans antialiased selection:bg-orange-100 selection:text-orange-900 overflow-x-hidden min-w-0">
    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-stone-200/80 bg-white/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 rounded-lg p-1">
                        <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-8 w-8 object-contain transition-transform group-hover:scale-105" />
                        <span class="text-xl font-bold tracking-tight text-slate-900">
                            Sambilan<span class="text-orange-600">.</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-6" aria-label="Navigasi Utama">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-slate-700 hover:text-orange-700 transition-colors">
                        Katalog Lowongan
                    </a>
                    <a href="{{ url('/#panduan') }}" class="text-sm font-medium text-stone-600 hover:text-slate-900 transition-colors">
                        Panduan Mahasiswa
                    </a>
                    <a href="{{ url('/#tentang') }}" class="text-sm font-medium text-stone-600 hover:text-slate-900 transition-colors">
                        Tentang Platform
                    </a>

                    @auth
                        <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-colors active:scale-[0.98]">
                            Dashboard Admin
                        </a>
                    @endauth
                </nav>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button
                        type="button"
                        id="mobile-nav-toggle"
                        aria-label="Buka navigasi utama"
                        aria-expanded="false"
                        aria-controls="mobile-nav-menu"
                        class="min-h-[44px] min-w-[44px] p-2.5 rounded-lg flex items-center justify-center text-stone-700 hover:text-slate-900 hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 transition-colors"
                    >
                        <svg id="nav-icon-open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="nav-icon-close" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <nav id="mobile-nav-menu" class="hidden md:hidden border-b border-stone-200 bg-white px-4 pt-2 pb-4 space-y-1 shadow-sm" aria-label="Navigasi Ponsel">
            <a href="{{ url('/') }}" class="min-h-[44px] flex items-center px-3 py-2 rounded-md text-base font-medium text-slate-800 hover:bg-orange-50 hover:text-orange-700 transition-colors">
                Katalog Lowongan
            </a>
            <a href="{{ url('/#panduan') }}" class="min-h-[44px] flex items-center px-3 py-2 rounded-md text-base font-medium text-stone-600 hover:bg-stone-100 hover:text-slate-900 transition-colors">
                Panduan Mahasiswa
            </a>
            <a href="{{ url('/#tentang') }}" class="min-h-[44px] flex items-center px-3 py-2 rounded-md text-base font-medium text-stone-600 hover:bg-stone-100 hover:text-slate-900 transition-colors">
                Tentang Platform
            </a>
            @auth
                <div class="pt-2 border-t border-stone-100">
                    <a href="{{ url('/admin/dashboard') }}" class="w-full min-h-[44px] flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-colors active:scale-[0.98]">
                        Dashboard Admin
                    </a>
                </div>
            @endauth
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-stone-200/80 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Col 1: Brand Info -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-7 w-7 object-contain" />
                        <span class="text-lg font-bold tracking-tight text-slate-900">
                            Sambilan<span class="text-orange-600">.</span>
                        </span>
                    </div>
                    <p class="text-sm text-stone-600 leading-relaxed text-pretty max-w-sm">
                        Katalog informasi lowongan magang dan kerja paruh waktu terkurasi untuk mahasiswa aktif perguruan tinggi. Menunjang kemandirian finansial dan pengalaman profesional tanpa mengorbankan masa perkuliahan.
                    </p>
                </div>

                <!-- Col 2: Student Links -->
                <div>
                    <h2 class="text-xs font-semibold text-stone-900 tracking-wider uppercase mb-3">
                        Navigasi Mahasiswa
                    </h2>
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ url('/') }}" class="inline-flex items-center min-h-[44px] py-1 text-stone-600 hover:text-orange-700 transition-colors">Katalog Semua Posisi</a>
                        </li>
                        <li>
                            <a href="{{ url('/?tipe=magang') }}" class="inline-flex items-center min-h-[44px] py-1 text-stone-600 hover:text-orange-700 transition-colors">Lowongan Magang</a>
                        </li>
                        <li>
                            <a href="{{ url('/?tipe=paruh_waktu') }}" class="inline-flex items-center min-h-[44px] py-1 text-stone-600 hover:text-orange-700 transition-colors">Kerja Paruh Waktu</a>
                        </li>
                        <li>
                            <a href="{{ url('/#panduan') }}" class="inline-flex items-center min-h-[44px] py-1 text-stone-600 hover:text-orange-700 transition-colors">Panduan dan Tata Cara Melamar</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Pengelola -->
                <div>
                    <a href="{{ url('/login') }}" class="inline-flex items-center min-h-[44px] py-1 text-xs font-medium text-stone-600 hover:text-slate-900 underline underline-offset-4 transition-colors">
                        Masuk Pengelola
                    </a>
                </div>

                <!-- Col 4: Info Loker -->
                <div>
                    <h2 class="text-xs font-semibold text-stone-900 tracking-wider uppercase mb-3">
                        Informasi
                    </h2>
                    <p class="text-sm text-stone-600">
                        Pasang info loker: <a href="mailto:contact@sambilan.id" class="text-orange-600 hover:text-orange-700 underline underline-offset-4 transition-colors">contact@sambilan.id</a>
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
                <p>&copy; {{ date('Y') }} Sambilan. Hak Cipta Terpelihara.</p>
                <p class="text-[11px] text-stone-400">Direktori Magang &amp; Kerja Paruh Waktu Mahasiswa</p>
            </div>
        </div>
    </footer>

    <!-- Interactive Vanilla JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('mobile-nav-toggle');
            const mobileMenu = document.getElementById('mobile-nav-menu');
            const iconOpen = document.getElementById('nav-icon-open');
            const iconClose = document.getElementById('nav-icon-close');

            if (!toggleBtn || !mobileMenu) return;

            function toggleMenu(show) {
                const isExpanded = show !== undefined ? show : mobileMenu.classList.contains('hidden');
                if (isExpanded) {
                    mobileMenu.classList.remove('hidden');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    if (iconOpen) iconOpen.classList.add('hidden');
                    if (iconClose) iconClose.classList.remove('hidden');
                } else {
                    mobileMenu.classList.add('hidden');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    if (iconOpen) iconOpen.classList.remove('hidden');
                    if (iconClose) iconClose.classList.add('hidden');
                }
            }

            toggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu();
            });

            document.addEventListener('click', function (e) {
                if (!mobileMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
                    toggleMenu(false);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                    toggleMenu(false);
                    toggleBtn.focus();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
