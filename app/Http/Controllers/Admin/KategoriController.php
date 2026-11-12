<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategoris = Kategori::withCount('lowongans')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function create(): View
    {
        return view('admin.kategoris.create');
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategoris.edit', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->lowongans()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        }

        $kategori->delete();

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
