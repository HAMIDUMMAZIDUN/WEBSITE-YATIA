<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Super Admin Routes
    Route::get('/admin/users', [DashboardController::class, 'adminUsersIndex'])->name('admin.users.index');
    Route::post('/admin/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::patch('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/kelas', [DashboardController::class, 'kelasIndex'])->name('admin.kelas.index');
    Route::post('/admin/kelas', [DashboardController::class, 'storeKelas'])->name('admin.kelas.store');
    Route::patch('/admin/kelas/{id}', [DashboardController::class, 'updateKelas'])->name('admin.kelas.update');
    Route::delete('/admin/kelas/{id}', [DashboardController::class, 'deleteKelas'])->name('admin.kelas.delete');
    Route::get('/admin/mapel', [DashboardController::class, 'mapelIndex'])->name('admin.mapel.index');
    Route::post('/admin/mapel', [DashboardController::class, 'storeMapel'])->name('admin.mapel.store');
    Route::patch('/admin/mapel/{id}', [DashboardController::class, 'updateMapel'])->name('admin.mapel.update');
    Route::delete('/admin/mapel/{id}', [DashboardController::class, 'deleteMapel'])->name('admin.mapel.delete');

    // Guru Routes — full CRUD and pages
    Route::get('/guru/jadwal', [DashboardController::class, 'guruJadwalIndex'])->name('guru.jadwal.index');
    Route::get('/guru/elearning', [DashboardController::class, 'guruElearningIndex'])->name('guru.elearning.index');
    Route::get('/guru/nilai', [DashboardController::class, 'guruNilaiIndex'])->name('guru.nilai.index');
    Route::get('/guru/cbt', [DashboardController::class, 'guruCbtIndex'])->name('guru.cbt.index');
    Route::post('/guru/materi-tugas', [DashboardController::class, 'storeMateriTugas'])->name('guru.materi_tugas.store');
    Route::patch('/guru/materi-tugas/{id}', [DashboardController::class, 'updateMateriTugas'])->name('guru.materi_tugas.update');
    Route::delete('/guru/materi-tugas/{id}', [DashboardController::class, 'deleteMateriTugas'])->name('guru.materi_tugas.delete');
    Route::post('/guru/nilai', [DashboardController::class, 'storeNilai'])->name('guru.nilai.store');
    Route::patch('/guru/nilai/{id}', [DashboardController::class, 'updateNilai'])->name('guru.nilai.update');
    Route::delete('/guru/nilai/{id}', [DashboardController::class, 'deleteNilai'])->name('guru.nilai.delete');
    Route::post('/guru/jadwal', [DashboardController::class, 'storeJadwalGuru'])->name('guru.jadwal.store');
    Route::patch('/guru/jadwal/{id}', [DashboardController::class, 'updateJadwalGuru'])->name('guru.jadwal.update');
    Route::delete('/guru/jadwal/{id}', [DashboardController::class, 'deleteJadwalGuru'])->name('guru.jadwal.delete');

    // Wali Kelas Routes — full CRUD for presensi
    Route::get('/wali/siswa', [DashboardController::class, 'waliSiswa'])->name('wali.siswa.index');
    Route::get('/wali/absensi', [DashboardController::class, 'waliAbsensi'])->name('wali.absensi.index');
    Route::post('/wali/presensi', [DashboardController::class, 'storePresensi'])->name('wali.presensi.store');
    Route::patch('/wali/presensi/{id}', [DashboardController::class, 'updatePresensi'])->name('wali.presensi.update');
    Route::delete('/wali/presensi/{id}', [DashboardController::class, 'deletePresensi'])->name('wali.presensi.delete');

    // Siswa Routes
    Route::get('/siswa/elearning', [DashboardController::class, 'siswaElearning'])->name('siswa.elearning.index');
    Route::get('/siswa/cbt', [DashboardController::class, 'siswaCbt'])->name('siswa.cbt.index');
    Route::get('/siswa/jadwal', [DashboardController::class, 'siswaJadwal'])->name('siswa.jadwal.index');
    Route::get('/siswa/presensi', [DashboardController::class, 'siswaPresensi'])->name('siswa.presensi.index');

    // BK Routes — full CRUD for pelanggaran and page
    Route::get('/bk/pelanggaran', [DashboardController::class, 'bkPelanggaranIndex'])->name('bk.pelanggaran.index');
    Route::post('/bk/pelanggaran', [DashboardController::class, 'storePelanggaran'])->name('bk.pelanggaran.store');
    Route::post('/bk/pelanggaran/{id}/status', [DashboardController::class, 'updatePelanggaranStatus'])->name('bk.pelanggaran.status');
    Route::patch('/bk/pelanggaran/{id}', [DashboardController::class, 'updatePelanggaran'])->name('bk.pelanggaran.update');
    Route::delete('/bk/pelanggaran/{id}', [DashboardController::class, 'deletePelanggaran'])->name('bk.pelanggaran.delete');

    // Import/Export Routes
    Route::get('/data/export/{type}', [DashboardController::class, 'exportData'])->name('data.export');
    Route::get('/data/template/{type}', [DashboardController::class, 'downloadTemplate'])->name('data.template');
    Route::post('/data/import/{type}', [DashboardController::class, 'importData'])->name('data.import');
});

require __DIR__.'/auth.php';
