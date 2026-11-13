<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lowongan;
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

        $lowongans = $hasLowongansTable
            ? Lowongan::query()
                ->aktif()
                ->with(['perusahaan', 'kategori'])
                ->latest('id')
                ->paginate(9)
                ->withQueryString()
            : new LengthAwarePaginator([], 0, 9);

        $kategoris = $hasKategorisTable
            ? Kategori::orderBy('nama', 'asc')->get()
            : collect();

        $viewName = ($request->is('lowongan*') || $request->is('katalog*'))
            ? 'katalog.index'
            : 'welcome';

        return view($viewName, [
            'lowongans' => $lowongans,
            'kategoris' => $kategoris,
        ]);
    }
}
