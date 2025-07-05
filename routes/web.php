<?php

use App\Http\Controllers\Auth\KaryawanLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\JurnalController;
use Illuminate\Support\Facades\App;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::middleware(['auth:karyawan'])->group(function () {
    Route::resource('jurnal', App\Http\Controllers\JurnalController::class);
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::resource('keuangan', App\Http\Controllers\KeuanganController::class);
    Route::resource('kotak', App\Http\Controllers\KotakKeuanganController::class);
    Route::resource('kategori', App\Http\Controllers\KategoriKeuanganController::class);
});

Route::middleware(['can:access'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});



