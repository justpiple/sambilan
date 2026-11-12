<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerusahaanRequest;
use App\Models\Perusahaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PerusahaanController extends Controller
{
    public function index(): View
    {
        $perusahaans = Perusahaan::withCount('lowongans')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.perusahaans.index', compact('perusahaans'));
    }

    public function create(): View
    {
        return view('admin.perusahaans.create');
    }

    public function store(PerusahaanRequest $request): RedirectResponse
    {
        Perusahaan::create($request->validated());

        return redirect()
            ->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Perusahaan $perusahaan): View
    {
        return view('admin.perusahaans.edit', compact('perusahaan'));
    }

    public function update(PerusahaanRequest $request, Perusahaan $perusahaan): RedirectResponse
    {
        $perusahaan->update($request->validated());

        return redirect()
            ->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Perusahaan $perusahaan): RedirectResponse
    {
        if ($perusahaan->lowongans()->exists()) {
            return back()->with('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        }

        $perusahaan->delete();

        return redirect()
            ->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
