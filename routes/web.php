<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LowonganController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/lowongan', [KatalogController::class, 'index'])->name('lowongan.index');
Route::get('/katalog', [KatalogController::class, 'index']);
Route::get('/lowongan/{lowongan}', [KatalogController::class, 'show'])->name('lowongan.show');
Route::get('/katalog/{lowongan}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kategoris', KategoriController::class);
    Route::resource('perusahaans', PerusahaanController::class);
    Route::resource('lowongans', LowonganController::class);
});
