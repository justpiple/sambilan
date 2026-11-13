@extends('layouts.admin')

@section('title', 'Manajemen Lowongan — Admin Sambilan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Lowongan</h1>
            <p class="text-sm text-stone-600 mt-1">Kelola data lowongan magang dan kerja paruh waktu mahasiswa.</p>
        </div>
        <div>
            <a href="{{ route('admin.lowongans.create') }}" class="min-h-[44px] inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 transition-colors shadow-sm active:scale-[0.98]">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Lowongan
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-stone-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.lowongans.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari posisi atau instansi..." class="w-full min-h-[44px] px-3.5 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent">
            </div>
            <div>
                <select name="tipe" class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="magang" {{ request('tipe') === 'magang' ? 'selected' : '' }}>Magang</option>
                    <option value="paruh_waktu" {{ request('tipe') === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full min-h-[44px] px-3 text-sm rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:border-transparent bg-white">
                    <option value="">Semua Status</option>
                    <option value="buka" {{ request('status') === 'buka' ? 'selected' : '' }}>Buka</option>
                    <option value="ditutup" {{ request('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 min-h-[44px] inline-flex items-center justify-center px-4 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                    Saring
                </button>
                @if (request()->hasAny(['q', 'tipe', 'status']))
                    <a href="{{ route('admin.lowongans.index') }}" class="min-h-[44px] inline-flex items-center justify-center px-3 rounded-lg text-sm text-stone-600 hover:text-slate-900 border border-stone-200 hover:bg-stone-50 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800">
                <thead class="bg-stone-50 border-b border-stone-200 text-xs font-semibold uppercase tracking-wider text-stone-600">
                    <tr>
                        <th scope="col" class="px-6 py-4">Posisi & Perusahaan</th>
                        <th scope="col" class="px-4 py-4">Kategori</th>
                        <th scope="col" class="px-4 py-4">Tipe & Kerja</th>
                        <th scope="col" class="px-4 py-4">Tenggat & Status</th>
                        <th scope="col" class="px-4 py-4">Status Admin</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200/80">
                    @forelse ($lowongans as $lowongan)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $lowongan->judul }}</div>
                                <div class="text-xs text-stone-500 mt-0.5">{{ $lowongan->perusahaan->nama }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex text-xs font-medium px-2.5 py-1 rounded-md bg-stone-100 text-stone-700 border border-stone-200">
                                    {{ $lowongan->kategori->nama }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <x-badge :variant="$lowongan->tipe" size="xs">
                                        {{ $lowongan->tipe_label }}
                                    </x-badge>
                                    <span class="text-xs text-stone-500 font-mono">{{ $lowongan->sistem_kerja_label }} &bull; {{ $lowongan->jam_per_minggu }}j/mg</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-xs font-mono tabular-nums text-slate-900">
                                    {{ $lowongan->tenggat ? $lowongan->tenggat->format('d M Y') : '-' }}
                                </div>
                                <div class="mt-0.5">
                                    @if ($lowongan->isAktif())
                                        <span class="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                            Kedaluwarsa
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if ($lowongan->status === 'buka')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                        Ditutup
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.lowongans.edit', $lowongan) }}" class="min-h-[36px] px-3 inline-flex items-center justify-center rounded-lg text-xs font-medium text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 hover:text-slate-900 transition-colors active:scale-[0.98]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.lowongans.destroy', $lowongan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data lowongan ini? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-[36px] px-3 inline-flex items-center justify-center rounded-lg text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition-colors active:scale-[0.98]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-stone-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <p class="text-sm font-medium text-slate-800">Belum ada data lowongan pekerjaan.</p>
                                    <p class="text-xs text-stone-500">Mulai buat lowongan baru untuk ditampilkan pada katalog mahasiswa.</p>
                                    <div class="pt-2">
                                        <a href="{{ route('admin.lowongans.create') }}" class="inline-flex items-center text-xs font-semibold text-orange-700 hover:underline">
                                            Tambah lowongan pertama &rarr;
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lowongans->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50">
                {{ $lowongans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
