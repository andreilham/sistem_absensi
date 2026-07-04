<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceScanController::class, 'index'])->name('attendance.scan');
Route::post('/api/absensi', [AttendanceScanController::class, 'store'])->name('attendance.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('mahasiswa', MahasiswaController::class);
    Route::get('mahasiswa/{mahasiswa}/register-face', [MahasiswaController::class, 'registerFace'])->name('mahasiswa.register-face');
    Route::post('mahasiswa/{mahasiswa}/face-photo', [MahasiswaController::class, 'storeFacePhoto'])->name('mahasiswa.store-face-photo');
    Route::post('mahasiswa/{mahasiswa}/complete-face', [MahasiswaController::class, 'completeFaceRegistration'])->name('mahasiswa.complete-face');

    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit'])->parameters(['kelas' => 'kelas']);
    Route::resource('dosen', DosenController::class)->except(['create', 'show', 'edit']);
    Route::resource('mata-kuliah', MataKuliahController::class)->except(['create', 'show', 'edit']);
    Route::resource('jadwal', JadwalController::class)->except(['create', 'show', 'edit']);
});
