@extends('layouts.admin')

@section('title', 'Manajemen Kategori — Admin Sambilan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kategori Lowongan</h1>
            <p class="text-sm text-stone-600 mt-1">Kelola taksonomi dan bidang keilmuan lowongan kerja mahasiswa.</p>
        </div>
        <div>
            <a href="{{ route('admin.kategoris.create') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98] shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kategori
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 p-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl" role="alert">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl" role="alert">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th scope="col" class="py-3.5 px-4 w-12 text-center">#</th>
                        <th scope="col" class="py-3.5 px-4">Nama Kategori</th>
                        <th scope="col" class="py-3.5 px-4">Deskripsi</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Jumlah Lowongan</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse ($kategoris as $kategori)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-center text-stone-500 font-mono text-xs">
                                {{ $loop->iteration + ($kategoris->firstItem() ? $kategoris->firstItem() - 1 : 0) }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $kategori->nama }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-600 max-w-md">
                                {{ $kategori->deskripsi ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium font-mono tabular-nums bg-stone-100 text-stone-800 border border-stone-200">
                                    {{ $kategori->lowongans_count }} lowongan
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.kategoris.edit', $kategori) }}" class="inline-flex items-center justify-center min-h-[44px] px-3 py-1.5 text-xs font-medium text-slate-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition-colors active:scale-[0.98]">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.kategoris.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors active:scale-[0.98]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center text-stone-500">
                                <p class="text-base font-medium text-slate-800">Belum ada data kategori</p>
                                <p class="text-sm text-stone-500 mt-1">Silakan tambahkan kategori lowongan pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategoris->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                {{ $kategoris->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
