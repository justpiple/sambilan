@extends('layouts.admin')

@section('title', 'Manajemen Perusahaan — Admin Sambilan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Perusahaan Mitra</h1>
            <p class="text-sm text-stone-600 mt-1">Kelola direktori mitra industri dan lembaga penyedia lowongan magang & paruh waktu.</p>
        </div>
        <div>
            <a href="{{ route('admin.perusahaans.create') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 text-sm font-semibold text-white bg-orange-700 hover:bg-orange-800 rounded-lg transition-colors active:scale-[0.98] shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perusahaan
            </a>
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th scope="col" class="py-3.5 px-4 w-12 text-center">#</th>
                        <th scope="col" class="py-3.5 px-4">Nama Perusahaan</th>
                        <th scope="col" class="py-3.5 px-4">Email Resmi</th>
                        <th scope="col" class="py-3.5 px-4">Media Sosial / Web</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Jumlah Lowongan</th>
                        <th scope="col" class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse ($perusahaans as $perusahaan)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-center text-stone-500 font-mono text-xs">
                                {{ $loop->iteration + ($perusahaans->firstItem() ? $perusahaans->firstItem() - 1 : 0) }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $perusahaan->nama }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-600 font-mono text-xs">
                                <a href="mailto:{{ $perusahaan->email }}" class="hover:text-orange-700 transition-colors">
                                    {{ $perusahaan->email }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-sm text-stone-600">
                                @if ($perusahaan->sosial_media)
                                    <a href="{{ $perusahaan->sosial_media }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-orange-700 hover:text-orange-800 hover:underline text-xs gap-1">
                                        {{ \Illuminate\Support\Str::limit($perusahaan->sosial_media, 28) }}
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-stone-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium font-mono tabular-nums bg-stone-100 text-stone-800 border border-stone-200">
                                    {{ $perusahaan->lowongans_count }} lowongan
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 whitespace-nowrap">
                                    <a href="{{ route('admin.lowongans.create', ['perusahaan_id' => $perusahaan->id]) }}" class="inline-flex items-center justify-center min-h-[44px] px-3 py-1.5 text-xs font-semibold text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors border border-orange-200 active:scale-[0.98] whitespace-nowrap shrink-0">
                                        + Lowongan
                                    </a>
                                    <a href="{{ route('admin.perusahaans.edit', $perusahaan) }}" class="inline-flex items-center justify-center min-h-[44px] px-3 py-1.5 text-xs font-medium text-slate-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition-colors active:scale-[0.98] whitespace-nowrap shrink-0">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.perusahaans.destroy', $perusahaan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perusahaan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors active:scale-[0.98] whitespace-nowrap shrink-0">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center text-stone-500">
                                <p class="text-base font-medium text-slate-800">Belum ada data perusahaan</p>
                                <p class="text-sm text-stone-500 mt-1">Silakan tambahkan data instansi/perusahaan mitra pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($perusahaans->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                {{ $perusahaans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
