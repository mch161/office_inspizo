<?php

use Illuminate\Support\Facades\Route;

Route::get('/karyawan/login', [App\Http\Controllers\Auth\KaryawanLoginController::class, 'showLoginForm'])->name('karyawan.login');
Route::post('/karyawan/login', [App\Http\Controllers\Auth\KaryawanLoginController::class, 'login'])->name('karyawan.login.submit');
Route::get('/karyawan/logout', [App\Http\Controllers\Auth\KaryawanLoginController::class, 'logout'])->name('karyawan.logout');