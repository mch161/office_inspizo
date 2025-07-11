<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\LoginController::class, 'register'])->name('register.submit');

Route::get('/profile', [App\Http\Controllers\Auth\ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('profile.update');