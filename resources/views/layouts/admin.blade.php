<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logogram.svg') }}">

    <title>@yield('title', 'Admin Back-Office — Sambilan')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="min-h-full flex flex-col md:flex-row bg-stone-100 text-slate-900 font-sans antialiased selection:bg-orange-100 selection:text-orange-900">
    <!-- Mobile Top Navigation Header -->
    <header class="md:hidden flex items-center justify-between h-16 px-4 bg-slate-900 text-white sticky top-0 z-40 border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-7 w-7 object-contain" />
            <span class="text-lg font-bold tracking-tight">Sambilan<span class="text-orange-500">.</span></span>
            <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">Admin</span>
        </a>
        <button type="button" id="admin-mobile-toggle" aria-label="Buka menu admin" class="min-h-[44px] min-w-[44px] p-2 flex items-center justify-center text-slate-300 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
            <svg id="admin-icon-open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg id="admin-icon-close" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </header>

    <!-- Sidebar (Desktop Fixed & Mobile Collapsible) -->
    <aside id="admin-sidebar" class="hidden md:flex flex-col w-full md:w-64 bg-slate-900 text-white shrink-0 md:sticky md:top-0 md:h-screen border-r border-slate-800 z-30 transition-all">
        <!-- Brand Header -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800/80">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-7 w-7 object-contain transition-transform group-hover:scale-105" />
                <span class="text-xl font-bold tracking-tight text-white group-hover:text-orange-400 transition-colors">
                    Sambilan<span class="text-orange-500">.</span>
                </span>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto" aria-label="Navigasi Back-Office">
            <a href="{{ route('admin.dashboard') }}" class="min-h-[44px] px-3.5 py-2.5 rounded-lg flex items-center gap-3 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard*') ? 'bg-orange-700 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.perusahaans.index') }}" class="min-h-[44px] px-3.5 py-2.5 rounded-lg flex items-center gap-3 text-sm font-medium transition-colors {{ request()->routeIs('admin.perusahaans*') ? 'bg-orange-700 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Perusahaan</span>
            </a>

            <a href="{{ route('admin.kategoris.index') }}" class="min-h-[44px] px-3.5 py-2.5 rounded-lg flex items-center gap-3 text-sm font-medium transition-colors {{ request()->routeIs('admin.kategoris*') ? 'bg-orange-700 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Kategori</span>
            </a>

            <a href="{{ route('admin.lowongans.index') }}" class="min-h-[44px] px-3.5 py-2.5 rounded-lg flex items-center gap-3 text-sm font-medium transition-colors {{ request()->routeIs('admin.lowongans*') ? 'bg-orange-700 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Lowongan Kerja</span>
            </a>
        </nav>

        <!-- Sidebar Footer / User Info & Actions -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50 space-y-3">
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="min-h-[44px] w-full px-3 py-2 rounded-lg flex items-center justify-between text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                <span>Lihat Katalog Publik</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>

            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                <div class="truncate mr-2">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-slate-400 font-mono truncate">{{ auth()->user()->email ?? 'admin@sambilan.id' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar dari sesi" class="min-h-[44px] min-w-[44px] p-2 flex items-center justify-center text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Admin Content Area -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1">
            <!-- Global Flash Alert Messages -->
            @if (session('success'))
                <x-alert type="success" :message="session('success')" class="mb-6" />
            @endif

            @if (session('error'))
                <x-alert type="error" :message="session('error')" class="mb-6" />
            @endif

            @if (session('warning'))
                <x-alert type="warning" :message="session('warning')" class="mb-6" />
            @endif

            @if ($errors->any())
                <x-alert type="error" class="mb-6">
                    <span class="font-semibold block mb-1">Terdapat kesalahan pada data yang dikirimkan:</span>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @yield('content')
        </div>

        <footer class="bg-white border-t border-stone-200 px-4 sm:px-8 py-4 text-xs text-stone-500 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
            <span>&copy; {{ date('Y') }} Sambilan &bull; Administrator Panel</span>
        </footer>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('admin-mobile-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            const iconOpen = document.getElementById('admin-icon-open');
            const iconClose = document.getElementById('admin-icon-close');

            if (!toggle || !sidebar) return;

            toggle.addEventListener('click', function () {
                const isHidden = sidebar.classList.contains('hidden');
                if (isHidden) {
                    sidebar.classList.remove('hidden');
                    if (iconOpen) iconOpen.classList.add('hidden');
                    if (iconClose) iconClose.classList.remove('hidden');
                } else {
                    sidebar.classList.add('hidden');
                    if (iconOpen) iconOpen.classList.remove('hidden');
                    if (iconClose) iconClose.classList.add('hidden');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
