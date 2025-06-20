<?php

use App\Http\Controllers\Auth\KaryawanLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [KaryawanLoginController::class, 'dashboard'])->name('karyawan.dashboard');

});