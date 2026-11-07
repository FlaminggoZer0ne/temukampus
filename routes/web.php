<?php

use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlaimController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Petugas\VerifikasiController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/*
 | Middleware role dipanggil lewat nama kelas (RoleMiddleware::class.':admin'),
 | sehingga tidak perlu mendaftarkan alias di Kernel.php / bootstrap/app.php.
 */
// Nama bulan berbahasa Indonesia untuk translatedFormat().
\Carbon\Carbon::setLocale('id');

$role = fn (string ...$roles) => RoleMiddleware::class . ':' . implode(',', $roles);

// ---------------------------------------------------------------- Publik
Route::get('/', [LaporanController::class, 'beranda'])->name('beranda');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ---------------------------------------------------------------- Login wajib
Route::middleware('auth')->group(function () use ($role) {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AuthController::class, 'profil'])->name('profil');
    Route::put('/profil', [AuthController::class, 'updateProfil'])->name('profil.update');

    // Laporan: daftar & detail untuk semua; buat/ubah untuk petugas & mahasiswa
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::middleware($role('petugas', 'mahasiswa'))->group(function () {
        Route::get('/laporan/saya', [LaporanController::class, 'saya'])->name('laporan.saya');
        Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
        Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
        Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
        Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    });

    Route::get('/laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');

    // Mahasiswa: klaim
    Route::middleware($role('mahasiswa'))->group(function () {
        Route::get('/klaim', [KlaimController::class, 'index'])->name('klaim.index');
        Route::get('/laporan/{laporan}/klaim', [KlaimController::class, 'create'])->name('klaim.create');
        Route::post('/laporan/{laporan}/klaim', [KlaimController::class, 'store'])->name('klaim.store');
    });

    // Petugas
    Route::middleware($role('petugas'))->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/laporan', [VerifikasiController::class, 'laporan'])->name('laporan');
        Route::get('/klaim', [VerifikasiController::class, 'klaim'])->name('klaim');
        Route::get('/serah-terima', [VerifikasiController::class, 'serahTerima'])->name('serahterima');
    });

    // Admin
    Route::middleware($role('admin'))->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('lokasi', LokasiController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/laporan', [LaporanController::class, 'semua'])->name('laporan.index');
        Route::delete('/laporan/{laporan}', [LaporanController::class, 'adminDestroy'])->name('laporan.destroy');
    });

    // Endpoint AJAX / JSON
    Route::prefix('api')->group(function () use ($role) {
        Route::get('/laporan/search', [LaporanController::class, 'search'])->name('api.laporan.search');
        Route::get('/statistik', [DashboardController::class, 'statistik'])
            ->middleware($role('admin', 'petugas'))->name('api.statistik');
        Route::patch('/laporan/{laporan}/status', [VerifikasiController::class, 'updateStatusLaporan'])
            ->middleware($role('petugas'))->name('api.laporan.status');
        Route::patch('/klaim/{klaim}/status', [VerifikasiController::class, 'updateStatusKlaim'])
            ->middleware($role('petugas'))->name('api.klaim.status');
    });
});
