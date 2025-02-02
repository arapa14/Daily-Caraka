<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, "index"])->name('dashboard')->middleware('auth');

// API
Route::get('/server-time', [DashboardController::class, 'getServerTime']);

// Rute untuk Admin
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // CRUD user
    Route::resource('/user', UserController::class);

    // CRUD location
    Route::resource('/location', LocationController::class);
});

// Rute untuk Reviewer
Route::middleware(['auth', 'isReviewer'])->group(function () {
    Route::get('/reviewer', function () {
        return view('reviewer.dashboard');
    })->name('reviewer.dashboard');

    // Laporan Status
    Route::get('/status', function() {
        $laporans = \App\Models\Laporan::orderBy('created_at', 'desc')->paginate(10);

        return view('reviewer.status', compact('laporans'));
    })->name('status');

    // Update Status
    Route::patch('/laporan/{id}/status', [LaporanController::class, 'updateStatus'])->name('update.status');
});

// Rute untuk Caraka
Route::middleware(['auth', 'isCaraka'])->group(function () {
    Route::get('/caraka', function () {
        return view('caraka.dashboard');
    })->name('caraka.dashboard');

    // Laporan
    Route::post('/submit-laporan', [LaporanController::class, 'store'])->name('laporan.store');

    // Riwayat
    Route::get('/riwayat', function() {
        $user = Auth::user(); //ambil data user yang login
        $laporans = \App\Models\Laporan::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(5);

        return view('caraka.riwayat', compact('user', 'laporans'));
    })->name('riwayat');
});
