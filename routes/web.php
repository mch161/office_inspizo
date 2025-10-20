<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::middleware(['can:superadmin'])->group(function () {
    ##Jurnal
    Route::get('jurnal_kita', [App\Http\Controllers\JurnalController::class, 'jurnal_kita'])->name('jurnal_kita');

    ##Presensi Bulanan
    Route::post('presensi/bulanan/create', [App\Http\Controllers\PresensiBulananController::class, 'create'])->name('presensi.bulanan.create');
    Route::put('presensi/bulanan/{kd_presensi_bulanan}/sync', [App\Http\Controllers\PresensiBulananController::class, 'sync'])->name('presensi.bulanan.sync');
    Route::put('presensi/bulanan/{kd_presensi_bulanan}/update', [App\Http\Controllers\PresensiBulananController::class, 'update'])->name('presensi.bulanan.update');
    Route::put('presensi/bulanan/{kd_presensi_bulanan}/verify', [App\Http\Controllers\PresensiBulananController::class, 'verify'])->name('presensi.bulanan.verify');

    ##Izin
    Route::put('lembur/approve', [App\Http\Controllers\LemburController::class, 'approve'])->name('lembur.approve');

    ##User
    Route::resource('users', App\Http\Controllers\Auth\UserController::class);

    Route::get('/view-as-user', [App\Http\Controllers\ViewController::class, 'switchToUserView'])->name('view.as_user');
});

Route::middleware(['can:session-view-as-karyawan'])->group(function () {
    Route::get('/view-as-admin', [App\Http\Controllers\ViewController::class, 'switchToAdminView'])->name('view.as_admin');
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('pelanggan', App\Http\Controllers\PelangganController::class);

    ## Jurnal
    Route::resource('jurnal', App\Http\Controllers\JurnalController::class);
    Route::get('jurnalku', [App\Http\Controllers\JurnalController::class, 'jurnalku'])->name('jurnalku');

    ## Pesanan
    Route::get('pesanan/permintaan', [App\Http\Controllers\PesananController::class, 'permintaan'])->name('pesanan.permintaan');
    Route::resource('pesanan', App\Http\Controllers\PesananController::class);
    Route::post('pesanan/agenda', [App\Http\Controllers\PesananController::class, 'agenda'])->name('pesanan.agenda');
    Route::post('pesanan/{pesanan}/complete', [App\Http\Controllers\PesananController::class, 'complete'])->name('pesanan.complete');
    Route::resource('pesanan/{pesanan}/galeri', App\Http\Controllers\GaleriController::class);
    Route::resource('pesanan/{pesanan}/signature', App\Http\Controllers\SignatureController::class);

    ## Pesanan Detail
    Route::get('pesanan/{pesanan}/detail', [App\Http\Controllers\PesananController::class, 'detail'])->name('pesanan.detail');
    Route::resource('pesanan/barang', App\Http\Controllers\PesananBarangController::class)->names([
        'store' => 'pesanan.barang.store',
        'destroy' => 'pesanan.barang.destroy',
        'update' => 'pesanan.barang.update',
    ]);
    Route::resource('pesanan/jasa', App\Http\Controllers\PesananJasaController::class)->names([
        'store' => 'pesanan.jasa.store',
        'destroy' => 'pesanan.jasa.destroy',
        'update' => 'pesanan.jasa.update',
    ]);
    Route::resource('pesanan/{pesanan}/progress', App\Http\Controllers\PesananProgressController::class);

    ## Agenda
    Route::get('agenda/fetch', [App\Http\Controllers\AgendaController::class, 'fetch'])->name('fetch');
    Route::get('agenda', [App\Http\Controllers\AgendaController::class, 'index'])->name('agenda.index');
    Route::post('agenda/ajax', [App\Http\Controllers\AgendaController::class, 'ajax'])->name('agenda.ajax');

    ## Presensi
    Route::get('presensi/fetch', [App\Http\Controllers\PresensiController::class, 'fetch'])->name('presensi.fetch');
    Route::get('presensi/view', [App\Http\Controllers\PresensiController::class, 'view'])->name('presensi.view');
    Route::get('presensi/bulanan', [App\Http\Controllers\PresensiBulananController::class, 'index'])->name('presensi.bulanan');
    Route::resource('presensi', App\Http\Controllers\PresensiController::class);
    Route::get('izin/form', [App\Http\Controllers\IzinController::class, 'izinForm'])->name('izin.form');
    Route::resource('izin', App\Http\Controllers\IzinController::class);
    Route::resource('lembur', App\Http\Controllers\LemburController::class);
    Route::get('/libur/import', [App\Http\Controllers\LiburController::class, 'importFromApi'])->name('libur.import');
    Route::resource('libur', App\Http\Controllers\LiburController::class);

    ## Barang
    Route::get('barang/search', [App\Http\Controllers\BarangController::class, 'search'])->name('barang.search');
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::put('barang/{id}/updateStok', [App\Http\Controllers\BarangController::class, 'updateStok'])->name('updateStok');
    Route::resource('stok', App\Http\Controllers\StokController::class);
    Route::resource('peminjaman', App\Http\Controllers\PeminjamanController::class);

    ## Jasa
    Route::resource('jasa', App\Http\Controllers\JasaController::class);

    ## Project
    Route::get('project/search', [App\Http\Controllers\ProjectController::class, 'search'])->name('project.search');
    Route::resource('project', App\Http\Controllers\ProjectController::class);
    Route::get('project/{project}/detail', [App\Http\Controllers\ProjectController::class, 'detail'])->name('project.detail');

    ## Pekerjaan
    Route::resource('pekerjaan', App\Http\Controllers\PekerjaanController::class);

    ## Tugas
    Route::resource('tugas', App\Http\Controllers\TugasController::class);

    ## Surat Perintah
    Route::resource('surat-perintah', App\Http\Controllers\SuratPerintahController::class);

    ## Kunjungan
    Route::resource('kunjungan', App\Http\Controllers\KunjunganController::class);
    Route::get('/get-pesanan-by-pelanggan/{kd_pelanggan}', [App\Http\Controllers\KunjunganController::class, 'getByPelanggan'])->name('pesanan.getByPelanggan');
    Route::patch('/kunjungan/{kunjungan}/update-status', [App\Http\Controllers\KunjunganController::class, 'updateStatus'])->name('kunjungan.updateStatus');
    Route::post('/kunjungan/upload-image', [App\Http\Controllers\KunjunganController::class, 'uploadImage'])->name('kunjungan.uploadImage');
    Route::post('/kunjungan/delete-image', [App\Http\Controllers\KunjunganController::class, 'deleteImage'])->name('kunjungan.deleteImage');

    ## Keuangan
    Route::resource('keuangan', App\Http\Controllers\KeuanganController::class);
    Route::resource('kotak', App\Http\Controllers\KotakKeuanganController::class);
    Route::resource('kategori', App\Http\Controllers\KategoriKeuanganController::class);
    Route::get('reimburse/form', [App\Http\Controllers\ReimburseController::class, 'reimburseForm'])->name('reimburse.form');
    Route::resource('reimburse', App\Http\Controllers\ReimburseController::class);

    ## PDF
    Route::get('/download-card', [App\Http\Controllers\PDFController::class, 'card'])->name('downloadCard');
    Route::get('/view-card', [App\Http\Controllers\PDFController::class, 'index'])->name('viewCard');
});
