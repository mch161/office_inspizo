<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guard('karyawan')->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::middleware(['auth:karyawan'])->group(function () {
    Route::resource('pelanggan', App\Http\Controllers\PelangganController::class);
    Route::resource('jurnal', App\Http\Controllers\JurnalController::class);
    Route::get('jurnalku', action: [App\Http\Controllers\JurnalController::class, 'jurnalku'])->name('jurnalku');
    Route::resource('agenda', App\Http\Controllers\AgendaController::class);
    Route::get('pesanan', [App\Http\Controllers\PesananController::class, 'index'])->name('pesanan.index');
    Route::post('pesanan', [App\Http\Controllers\PesananController::class, 'store'])->name('pesanan.store');
    Route::get('pesanan/{pesanan}', [App\Http\Controllers\PesananController::class, 'show'])->name('pesanan.show');
    Route::get('agenda', [App\Http\Controllers\AgendaController::class, 'index'])->name('agenda.index');
    Route::post('agenda/ajax', [App\Http\Controllers\AgendaController::class, 'ajax'])->name('agenda.ajax');
    // Tambahkan rute update dan delete di sini nanti jika diperlukan
    Route::resource('presensi', App\Http\Controllers\PresensiController::class);
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::put('barang/{id}/updateStok', [App\Http\Controllers\BarangController::class, 'updateStok'])->name('updateStok');
    Route::resource('stok', App\Http\Controllers\StokController::class);
    Route::resource('keuangan', App\Http\Controllers\KeuanganController::class);
    Route::resource('kotak', App\Http\Controllers\KotakKeuanganController::class);
    Route::resource('kategori', App\Http\Controllers\KategoriKeuanganController::class);
    Route::resource('reimburse', App\Http\Controllers\ReimburseController::class);
    Route::get('forms/reimburse', [App\Http\Controllers\ReimburseController::class, 'reimburseForm'])->name('reimburse.form');
    Route::get('forms/izin', [App\Http\Controllers\IzinController::class, 'izinForm'])->name('izin.form');
    Route::resource('izin', App\Http\Controllers\IzinController::class);
});

Route::middleware(['can:superadmin'])->group(function () {
    Route::resource('users', App\Http\Controllers\Auth\UserController::class);
    Route::get('jurnal_kita', [App\Http\Controllers\JurnalController::class, 'jurnal_kita'])->name('jurnal_kita');
});

Route::middleware(['can:access'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});



