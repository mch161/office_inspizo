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

    ## Jurnal
    Route::resource('jurnal', App\Http\Controllers\JurnalController::class);
    Route::get('jurnalku', [App\Http\Controllers\JurnalController::class, 'jurnalku'])->name('jurnalku');

    ## Pesanan
    Route::get('pesanan', [App\Http\Controllers\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/permintaan', [App\Http\Controllers\PesananController::class, 'permintaan'])->name('pesanan.permintaan');
    Route::post('pesanan', [App\Http\Controllers\PesananController::class, 'store'])->name('pesanan.store');
    Route::post('pesanan/{pesanan}/accept', [App\Http\Controllers\PesananController::class, 'accept'])->name('pesanan.accept');
    Route::get('pesanan/{pesanan}/detail', [App\Http\Controllers\PesananController::class, 'detail'])->name('pesanan.detail');

    ## Agenda
    Route::get('agenda/fetch', [App\Http\Controllers\AgendaController::class, 'fetch'])->name('fetch');
    Route::get('agenda', [App\Http\Controllers\AgendaController::class, 'index'])->name('agenda.index');
    Route::post('agenda/ajax', [App\Http\Controllers\AgendaController::class, 'ajax'])->name('agenda.ajax');

    ## Presensi
    Route::get('presensi/fetch', [App\Http\Controllers\PresensiController::class, 'fetch'])->name('presensi.fetch');
    Route::get('presensi/view', [App\Http\Controllers\PresensiController::class, 'view'])->name('presensi.view');
    Route::resource('presensi', App\Http\Controllers\PresensiController::class);
    Route::get('forms/izin', [App\Http\Controllers\IzinController::class, 'izinForm'])->name('izin.form');
    Route::resource('izin', App\Http\Controllers\IzinController::class);

    ## Barang
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::put('barang/{id}/updateStok', [App\Http\Controllers\BarangController::class, 'updateStok'])->name('updateStok');
    Route::resource('stok', App\Http\Controllers\StokController::class);

    ## Keuangan
    Route::resource('keuangan', App\Http\Controllers\KeuanganController::class);
    Route::resource('kotak', App\Http\Controllers\KotakKeuanganController::class);
    Route::resource('kategori', App\Http\Controllers\KategoriKeuanganController::class);
    Route::resource('reimburse', App\Http\Controllers\ReimburseController::class);
    Route::get('forms/reimburse', [App\Http\Controllers\ReimburseController::class, 'reimburseForm'])->name('reimburse.form');
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



