<?php

use Illuminate\Support\Facades\Route;

Route::get('/karyawan/login', [App\Http\Controllers\Auth\KaryawanLoginController::class, 'showLoginForm'])->name('karyawan.login');
Route::post('/karyawan/login', [App\Http\Controllers\Auth\KaryawanLoginController::class, 'login'])->name('karyawan.login.submit');

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\LoginController::class, 'register'])->name('register.submit');