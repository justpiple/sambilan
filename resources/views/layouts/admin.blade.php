<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-100/60">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logogram.svg') }}">

    <title>@yield('title', 'Admin Dashboard — Sambilan')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="min-h-full flex flex-col md:flex-row bg-stone-100/60 text-slate-900 font-sans antialiased selection:bg-orange-100 selection:text-orange-900">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white border-r border-stone-200/90 z-30">
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <!-- Brand -->
            <div class="flex items-center gap-3 px-5 pb-5 border-b border-stone-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 rounded-lg">
                    <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-8 w-8 object-contain transition-transform group-hover:scale-105" />
                    <span class="text-xl font-bold tracking-tight text-slate-900">
                        Sambilan<span class="text-orange-600">.</span>
                    </span>
                </a>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded border border-orange-200 text-orange-800 bg-orange-50">
                    Admin
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-5 flex-1 px-3 space-y-1" aria-label="Navigasi Admin">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard*') ? 'bg-orange-50 text-orange-700 font-semibold border-r-2 border-orange-700' : 'text-stone-600 hover:bg-stone-50 hover:text-slate-900 font-medium' }} group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors min-h-[44px]"
                >
                    <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard*') ? 'text-orange-700' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a
                    href="{{ Route::has('admin.perusahaans.index') ? route('admin.perusahaans.index') : url('/admin/perusahaans') }}"
                    class="{{ request()->is('admin/perusahaans*') ? 'bg-orange-50 text-orange-700 font-semibold border-r-2 border-orange-700' : 'text-stone-600 hover:bg-stone-50 hover:text-slate-900 font-medium' }} group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors min-h-[44px]"
                >
                    <svg class="h-5 w-5 flex-shrink-0 {{ request()->is('admin/perusahaans*') ? 'text-orange-700' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Perusahaan</span>
                </a>

                <a
                    href="{{ Route::has('admin.kategoris.index') ? route('admin.kategoris.index') : url('/admin/kategoris') }}"
                    class="{{ request()->is('admin/kategoris*') ? 'bg-orange-50 text-orange-700 font-semibold border-r-2 border-orange-700' : 'text-stone-600 hover:bg-stone-50 hover:text-slate-900 font-medium' }} group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors min-h-[44px]"
                >
                    <svg class="h-5 w-5 flex-shrink-0 {{ request()->is('admin/kategoris*') ? 'text-orange-700' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Kategori</span>
                </a>

                <a
                    href="{{ Route::has('admin.lowongans.index') ? route('admin.lowongans.index') : url('/admin/lowongans') }}"
                    class="{{ request()->is('admin/lowongans*') ? 'bg-orange-50 text-orange-700 font-semibold border-r-2 border-orange-700' : 'text-stone-600 hover:bg-stone-50 hover:text-slate-900 font-medium' }} group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors min-h-[44px]"
                >
                    <svg class="h-5 w-5 flex-shrink-0 {{ request()->is('admin/lowongans*') ? 'text-orange-700' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Lowongan</span>
                </a>

                <div class="pt-4 border-t border-stone-100">
                    <a
                        href="{{ url('/') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-stone-600 hover:bg-stone-50 hover:text-slate-900 font-medium group flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors min-h-[44px]"
                    >
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Katalog Publik</span>
                        </span>
                        <span class="text-xs text-stone-400">&nearr;</span>
                    </a>
                </div>
            </nav>

            <!-- Bottom Profile & Logout -->
            <div class="p-3 border-t border-stone-200/90">
                <div class="flex items-center gap-3 px-3 py-2 bg-stone-50 rounded-xl mb-2">
                    <div class="h-9 w-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-900 truncate">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </p>
                        <p class="text-[11px] text-stone-500 truncate">
                            {{ Auth::user()->email ?? 'admin@sambilan.id' }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full min-h-[44px] flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-stone-700 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-stone-200/80 active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile Top Header -->
    <header class="md:hidden sticky top-0 z-40 bg-white border-b border-stone-200/90 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-7 w-7 object-contain" />
                <span class="text-lg font-bold tracking-tight text-slate-900">
                    Sambilan<span class="text-orange-600">.</span>
                </span>
            </a>
            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded border border-orange-200 text-orange-800 bg-orange-50">
                Admin
            </span>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button
                    type="submit"
                    class="min-h-[44px] px-3 py-1.5 text-xs font-semibold text-stone-700 hover:text-red-700 hover:bg-red-50 rounded-lg border border-stone-200 transition-colors"
                >
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile Navigation Bar -->
    <nav class="md:hidden bg-white border-b border-stone-200 px-2 py-1 flex items-center justify-around overflow-x-auto text-xs" aria-label="Navigasi Ponsel Admin">
        <a href="{{ route('admin.dashboard') }}" class="min-h-[44px] px-3 py-2 flex items-center font-medium {{ request()->routeIs('admin.dashboard*') ? 'text-orange-700 font-bold border-b-2 border-orange-700' : 'text-stone-600' }}">
            Dashboard
        </a>
        <a href="{{ Route::has('admin.perusahaans.index') ? route('admin.perusahaans.index') : url('/admin/perusahaans') }}" class="min-h-[44px] px-3 py-2 flex items-center font-medium {{ request()->is('admin/perusahaans*') ? 'text-orange-700 font-bold border-b-2 border-orange-700' : 'text-stone-600' }}">
            Perusahaan
        </a>
        <a href="{{ Route::has('admin.kategoris.index') ? route('admin.kategoris.index') : url('/admin/kategoris') }}" class="min-h-[44px] px-3 py-2 flex items-center font-medium {{ request()->is('admin/kategoris*') ? 'text-orange-700 font-bold border-b-2 border-orange-700' : 'text-stone-600' }}">
            Kategori
        </a>
        <a href="{{ Route::has('admin.lowongans.index') ? route('admin.lowongans.index') : url('/admin/lowongans') }}" class="min-h-[44px] px-3 py-2 flex items-center font-medium {{ request()->is('admin/lowongans*') ? 'text-orange-700 font-bold border-b-2 border-orange-700' : 'text-stone-600' }}">
            Lowongan
        </a>
    </nav>

    <!-- Main Content Area -->
    <div class="md:pl-64 flex flex-col flex-1 w-full min-h-screen">
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm flex items-start gap-3">
                    <svg class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800 shadow-sm flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                    <p class="font-semibold mb-1">Terdapat kesalahan pengisian formulir:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="mt-auto border-t border-stone-200/80 bg-white/50 px-4 py-4 sm:px-6 lg:px-8 text-center md:text-left text-xs text-stone-500">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} Sambilan. Hak Cipta Dilindungi. Back-Office Administrator.</span>
                <span class="font-mono text-[11px] text-stone-400">PAW-B Kelompok 8</span>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
