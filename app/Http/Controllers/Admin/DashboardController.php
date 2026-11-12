<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPerusahaan = Perusahaan::count();
        $totalKategori = Kategori::count();
        $totalLowonganAktif = Lowongan::aktif()->count();
        $totalLowongan = Lowongan::count();

        return view('admin.dashboard', compact(
            'totalPerusahaan',
            'totalKategori',
            'totalLowonganAktif',
            'totalLowongan'
        ));
    }
}
