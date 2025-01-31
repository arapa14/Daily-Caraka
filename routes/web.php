<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Rute untuk Admin
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/iniAdmin', function () {
        return view('welcome');
    });
});

// Rute untuk Reviewer
Route::middleware(['auth', 'isReviewer'])->group(function () {
    Route::get('/reviewer', function () {
        return view('reviewer.dashboard');
    })->name('reviewer.dashboard');
});

// Rute untuk Caraka
Route::middleware(['auth', 'isCaraka'])->group(function () {
    Route::get('/caraka', function () {
        return view('caraka.dashboard');
    })->name('caraka.dashboard');
});

Route::get('/dashboard', [DashboardController::class, "index"])->name('dashboard')->middleware('auth');