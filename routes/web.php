<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\BkCaseController;
use App\Http\Controllers\BkSessionController;
use App\Http\Controllers\AttendanceDailyController;
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

    Route::get('/admin/guru', [DashboardController::class, 'adminGuruIndex'])->name('admin.guru.index');
    Route::post('/admin/guru', [DashboardController::class, 'storeGuru'])->name('admin.guru.store');
    Route::patch('/admin/guru/{id}', [DashboardController::class, 'updateGuru'])->name('admin.guru.update');
    Route::delete('/admin/guru/{id}', [DashboardController::class, 'deleteGuru'])->name('admin.guru.delete');
    Route::post('/admin/guru/{id}/toggle-status', [DashboardController::class, 'toggleGuruStatus'])->name('admin.guru.toggle_status');
    Route::get('/admin/profil-madrasah', [DashboardController::class, 'profilMadrasahIndex'])->name('admin.profil.index');
    Route::post('/admin/profil-madrasah', [DashboardController::class, 'updateProfilMadrasah'])->name('admin.profil.update');
    Route::get('/admin/kelas', [DashboardController::class, 'kelasIndex'])->name('admin.kelas.index');
    Route::post('/admin/kelas', [DashboardController::class, 'storeKelas'])->name('admin.kelas.store');
    Route::patch('/admin/kelas/{id}', [DashboardController::class, 'updateKelas'])->name('admin.kelas.update');
    Route::delete('/admin/kelas/{id}', [DashboardController::class, 'deleteKelas'])->name('admin.kelas.delete');
    Route::get('/admin/mapel', [DashboardController::class, 'mapelIndex'])->name('admin.mapel.index');
    Route::post('/admin/mapel', [DashboardController::class, 'storeMapel'])->name('admin.mapel.store');
    Route::patch('/admin/mapel/{id}', [DashboardController::class, 'updateMapel'])->name('admin.mapel.update');
    Route::delete('/admin/mapel/{id}', [DashboardController::class, 'deleteMapel'])->name('admin.mapel.delete');
    Route::get('/admin/tahun-ajaran', [DashboardController::class, 'academicYearsIndex'])->name('admin.tahun_ajaran.index');
    Route::post('/admin/tahun-ajaran', [DashboardController::class, 'storeAcademicYear'])->name('admin.tahun_ajaran.store');
    Route::post('/admin/tahun-ajaran/{id}/activate', [DashboardController::class, 'activateAcademicYear'])->name('admin.tahun_ajaran.activate');
    Route::post('/admin/tahun-ajaran/{id}/lock', [DashboardController::class, 'lockAcademicYear'])->name('admin.tahun_ajaran.lock');
    Route::post('/admin/tahun-ajaran/{id}/unlock', [DashboardController::class, 'unlockAcademicYear'])->name('admin.tahun_ajaran.unlock');
    Route::post('/admin/tahun-ajaran/{id}/copy', [DashboardController::class, 'copyAcademicYearConfig'])->name('admin.tahun_ajaran.copy');
    Route::get('/admin/kalender-libur', [DashboardController::class, 'holidaysIndex'])->name('admin.holidays.index');
    Route::post('/admin/kalender-libur', [DashboardController::class, 'storeHoliday'])->name('admin.holidays.store');
    Route::delete('/admin/kalender-libur/{id}', [DashboardController::class, 'deleteHoliday'])->name('admin.holidays.delete');
    Route::get('/admin/siswa', [DashboardController::class, 'siswaIndex'])->name('admin.siswa.index');
    Route::post('/admin/siswa', [DashboardController::class, 'storeSiswa'])->name('admin.siswa.store');
    Route::patch('/admin/siswa/{id}', [DashboardController::class, 'updateSiswa'])->name('admin.siswa.update');
    Route::delete('/admin/siswa/{id}', [DashboardController::class, 'deleteSiswa'])->name('admin.siswa.delete');
    Route::get('/admin/siswa/{id}/history', [DashboardController::class, 'siswaHistory'])->name('admin.siswa.history');
    Route::get('/admin/pindah-kelas', [DashboardController::class, 'pindahKelasIndex'])->name('admin.pindah_kelas.index');
    Route::post('/admin/pindah-kelas', [DashboardController::class, 'pindahKelasStore'])->name('admin.pindah_kelas.store');
    Route::get('/admin/naik-kelas', [DashboardController::class, 'naikKelasIndex'])->name('admin.naik_kelas.index');
    Route::post('/admin/naik-kelas', [DashboardController::class, 'naikKelasStore'])->name('admin.naik_kelas.store');
    Route::get('/admin/alumni', [DashboardController::class, 'alumniIndex'])->name('admin.alumni.index');
    Route::patch('/admin/alumni/{id}', [DashboardController::class, 'updateAlumniContact'])->name('admin.alumni.update_contact');

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

    // Wali Kelas Routes (Enhanced)
    Route::get('/wali/dashboard', [WaliKelasController::class, 'index'])->name('wali.dashboard');
    Route::get('/wali/siswa', [DashboardController::class, 'waliSiswa'])->name('wali.siswa.index');
    Route::get('/wali/siswa/360/{id}', [WaliKelasController::class, 'profile360'])->name('wali.siswa.360');
    Route::get('/wali/absensi/verifikasi', [WaliKelasController::class, 'absensiIndex'])->name('wali.absensi.verifikasi.index');
    Route::post('/wali/absensi/verifikasi/{id}', [WaliKelasController::class, 'verifySubmission'])->name('wali.absensi.verifikasi.store');
    Route::get('/wali/absensi', [DashboardController::class, 'waliAbsensi'])->name('wali.absensi.index');
    Route::post('/wali/presensi', [DashboardController::class, 'storePresensi'])->name('wali.presensi.store');
    Route::patch('/wali/presensi/{id}', [DashboardController::class, 'updatePresensi'])->name('wali.presensi.update');
    Route::delete('/wali/presensi/{id}', [DashboardController::class, 'deletePresensi'])->name('wali.presensi.delete');

    // Siswa Routes
    Route::get('/siswa/elearning', [DashboardController::class, 'siswaElearning'])->name('siswa.elearning.index');
    Route::get('/siswa/cbt', [DashboardController::class, 'siswaCbt'])->name('siswa.cbt.index');
    Route::get('/siswa/jadwal', [DashboardController::class, 'siswaJadwal'])->name('siswa.jadwal.index');
    Route::get('/siswa/presensi', [DashboardController::class, 'siswaPresensi'])->name('siswa.presensi.index');

    // BK Routes (Enhanced Case Management & Sessions)
    Route::get('/bk/triase', [BkCaseController::class, 'triaseIndex'])->name('bk.triase.index');
    Route::post('/bk/triase/{id}/process', [BkCaseController::class, 'processTriase'])->name('bk.triase.process');
    Route::get('/bk/cases/{id}', [BkCaseController::class, 'show'])->name('bk.cases.show');
    Route::post('/bk/cases/{id}/close', [BkCaseController::class, 'closeCase'])->name('bk.cases.close');
    Route::post('/bk/cases/{caseId}/sessions', [BkSessionController::class, 'store'])->name('bk.sessions.store');
    Route::get('/bk/sessions/{id}', [BkSessionController::class, 'show'])->name('bk.sessions.show');
    Route::get('/bk/pelanggaran', [DashboardController::class, 'bkPelanggaranIndex'])->name('bk.pelanggaran.index');
    Route::post('/bk/pelanggaran', [DashboardController::class, 'storePelanggaran'])->name('bk.pelanggaran.store');
    Route::post('/bk/pelanggaran/{id}/status', [DashboardController::class, 'updatePelanggaranStatus'])->name('bk.pelanggaran.status');
    Route::patch('/bk/pelanggaran/{id}', [DashboardController::class, 'updatePelanggaran'])->name('bk.pelanggaran.update');
    Route::delete('/bk/pelanggaran/{id}', [DashboardController::class, 'deletePelanggaran'])->name('bk.pelanggaran.delete');

    // Seksi Absensi Per Kelas Routes
    Route::get('/absensi/input/{classId?}', [AttendanceDailyController::class, 'inputIndex'])->name('absensi.officer.index');
    Route::post('/absensi/save-draft', [AttendanceDailyController::class, 'saveDraft'])->name('absensi.officer.draft');
    Route::post('/absensi/submit-to-wali', [AttendanceDailyController::class, 'submitToWali'])->name('absensi.officer.submit');
    Route::post('/absensi/submit-correction', [AttendanceDailyController::class, 'submitCorrection'])->name('absensi.officer.correction');

    // Import/Export Routes
    Route::get('/data/export/{type}', [DashboardController::class, 'exportData'])->name('data.export');
    Route::get('/data/template/{type}', [DashboardController::class, 'downloadTemplate'])->name('data.template');
    Route::post('/data/import/{type}', [DashboardController::class, 'importData'])->name('data.import');
});

require __DIR__.'/auth.php';
