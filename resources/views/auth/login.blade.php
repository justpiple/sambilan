<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logogram.svg') }}">

    <title>Masuk Pengelola — Sambilan</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-stone-50 text-slate-900 font-sans antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 rounded-lg p-1">
                <img src="{{ asset('logogram.svg') }}" alt="Logogram Sambilan" class="h-10 w-10 object-contain transition-transform group-hover:scale-105" />
                <span class="text-2xl font-bold tracking-tight text-slate-900">
                    Sambilan<span class="text-orange-600">.</span>
                </span>
            </a>
            <h1 class="mt-4 text-xl font-bold tracking-tight text-slate-900">
                Masuk Pengelola
            </h1>
            <p class="mt-1 text-sm text-stone-600">
                Portal administrator direktori magang & kerja paruh waktu
            </p>
        </div>

        <div class="mt-8 bg-white py-8 px-6 shadow-sm border border-stone-200/90 rounded-2xl sm:px-10">
            @if (session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-800">
                        Alamat Email
                    </label>
                    <div class="mt-1.5">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="admin@sambilan.id"
                            class="block w-full min-h-[44px] rounded-lg border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-stone-400 focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20 transition-colors"
                        />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-800">
                        Kata Sandi
                    </label>
                    <div class="mt-1.5">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full min-h-[44px] rounded-lg border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-stone-400 focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-600/20 transition-colors"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            class="h-4 w-4 rounded border-stone-300 text-orange-600 focus:ring-orange-600"
                        />
                        <span class="text-sm text-stone-600">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full min-h-[44px] flex items-center justify-center rounded-lg bg-orange-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 active:scale-[0.98] transition-all"
                    >
                        Masuk ke Dashboard
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t border-stone-100 pt-5 text-center">
                <a href="{{ url('/') }}" class="text-sm font-medium text-stone-600 hover:text-orange-700 transition-colors">
                    &larr; Kembali ke Katalog Publik
                </a>
            </div>
        </div>
    </div>
</body>
</html>
