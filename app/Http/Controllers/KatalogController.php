<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lowongan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $hasLowongansTable = Schema::hasTable('lowongans');
        $hasKategorisTable = Schema::hasTable('kategoris');

        if ($hasLowongansTable) {
            $query = Lowongan::query()
                ->aktif()
                ->with(['perusahaan', 'kategori']);

            if ($request->filled('q')) {
                $search = trim((string) $request->input('q'));
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('judul', 'like', "%{$search}%")
                        ->orWhereHas('perusahaan', function (Builder $companyQuery) use ($search) {
                            $companyQuery->where('nama', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('kategori_id') && is_numeric($request->input('kategori_id'))) {
                $query->where('kategori_id', (int) $request->input('kategori_id'));
            }

            if ($request->filled('tipe') && in_array($request->input('tipe'), ['magang', 'paruh_waktu'], true)) {
                $query->where('tipe', $request->input('tipe'));
            }

            if ($request->filled('sistem_kerja') && in_array($request->input('sistem_kerja'), ['onsite', 'remote', 'hybrid'], true)) {
                $query->where('sistem_kerja', $request->input('sistem_kerja'));
            }

            $lowongans = $query->latest('id')
                ->paginate(9)
                ->withQueryString();
        } else {
            $lowongans = new LengthAwarePaginator([], 0, 9);
        }

        $kategoris = $hasKategorisTable
            ? Kategori::orderBy('nama', 'asc')->get()
            : collect();

        $hasActiveFilters = $request->filled('q')
            || $request->filled('kategori_id')
            || $request->filled('tipe')
            || $request->filled('sistem_kerja');

        $viewName = ($request->is('lowongan*') || $request->is('katalog*'))
            ? 'katalog.index'
            : 'welcome';

        return view($viewName, [
            'lowongans' => $lowongans,
            'kategoris' => $kategoris,
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }

    public function show(Lowongan $lowongan): View
    {
        $lowongan->load(['perusahaan', 'kategori']);

        $isAktif = $lowongan->isAktif();

        $relatedJobs = Lowongan::query()
            ->aktif()
            ->where('id', '!=', $lowongan->id)
            ->where('kategori_id', $lowongan->kategori_id)
            ->with(['perusahaan', 'kategori'])
            ->latest('id')
            ->take(3)
            ->get();

        return view('katalog.show', [
            'lowongan' => $lowongan,
            'isAktif' => $isAktif,
            'relatedJobs' => $relatedJobs,
        ]);
    }
}
