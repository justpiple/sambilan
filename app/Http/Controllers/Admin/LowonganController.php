<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LowonganRequest;
use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $request): View
    {
        $lowongans = Lowongan::with(['perusahaan', 'kategori'])
            ->when($request->query('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->query('tipe'), function ($query, $tipe) {
                return $query->where('tipe', $tipe);
            })
            ->when($request->query('q'), function ($query, $search) {
                return $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhereHas('perusahaan', fn ($q) => $q->where('nama', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lowongans.index', compact('lowongans'));
    }

    public function create(): View
    {
        $perusahaans = Perusahaan::orderBy('nama')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.lowongans.create', compact('perusahaans', 'kategoris'));
    }

    public function store(LowonganRequest $request): RedirectResponse
    {
        Lowongan::create($request->validated());

        return redirect()
            ->route('admin.lowongans.index')
            ->with('success', 'Lowongan kerja berhasil ditambahkan.');
    }

    public function edit(Lowongan $lowongan): View
    {
        $perusahaans = Perusahaan::orderBy('nama')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.lowongans.edit', compact('lowongan', 'perusahaans', 'kategoris'));
    }

    public function update(LowonganRequest $request, Lowongan $lowongan): RedirectResponse
    {
        $lowongan->update($request->validated());

        return redirect()
            ->route('admin.lowongans.index')
            ->with('success', 'Data lowongan berhasil diperbarui.');
    }

    public function destroy(Lowongan $lowongan): RedirectResponse
    {
        $lowongan->delete();

        return redirect()
            ->route('admin.lowongans.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }
}
