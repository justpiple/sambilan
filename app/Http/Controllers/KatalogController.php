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

            $lowongans = $query->latest('id')
                ->paginate(9)
                ->withQueryString();
        } else {
            $lowongans = new LengthAwarePaginator([], 0, 9);
        }

        $kategoris = $hasKategorisTable
            ? Kategori::orderBy('nama', 'asc')->get()
            : collect();

        $viewName = ($request->is('lowongan*') || $request->is('katalog*'))
            ? 'katalog.index'
            : 'welcome';

        return view($viewName, [
            'lowongans' => $lowongans,
            'kategoris' => $kategoris,
            'search' => $request->input('q', ''),
        ]);
    }
}
