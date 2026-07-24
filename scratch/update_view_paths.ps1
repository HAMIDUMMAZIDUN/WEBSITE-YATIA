$controllers = @(
    'app\Http\Controllers\DashboardController.php',
    'app\Http\Controllers\AttendanceDailyController.php',
    'app\Http\Controllers\BkCaseController.php',
    'app\Http\Controllers\MtsIntegrationController.php',
    'app\Http\Controllers\PublicationController.php',
    'app\Http\Controllers\SystemController.php',
    'app\Http\Controllers\WaliKelasController.php'
)

# Pairs: [old, new]
$replacements = @(
    @('dashboard.admin.rekap_print',                  'dashboard.admin.sistem.rekap_print'),
    @('dashboard.admin.super_admin',                  'dashboard.superadmin.dashboard'),
    @("dashboard.bk.guru_bk",                         'dashboard.guru_bk.dashboard'),
    @('dashboard.admin.kelas',                         'dashboard.admin.master_data.kelas'),
    @('dashboard.admin.mapel',                         'dashboard.admin.master_data.mapel'),
    @('dashboard.wali_kelas.wali_absensi_verifikasi',  'dashboard.wali_kelas.absensi.verifikasi'),
    @('dashboard.wali_kelas.wali_siswa_360',           'dashboard.wali_kelas.siswa_360'),
    @('dashboard.wali_kelas.wali_officer',             'dashboard.wali_kelas.kenaikan_mutasi'),
    @('dashboard.wali_kelas.wali_kelas_empty',         'dashboard.wali_kelas.wali_kelas_empty'),
    @('dashboard.wali_kelas.wali_kelas',               'dashboard.wali_kelas.dashboard'),
    @('dashboard.wali_kelas.wali_siswa',               'dashboard.wali_kelas.data_siswa'),
    @('dashboard.wali_kelas.wali_absensi',             'dashboard.wali_kelas.absensi.index'),
    @('dashboard.admin.absensi',                       'dashboard.admin.kesiswaan.absensi'),
    @('dashboard.admin.users',                         'dashboard.admin.sistem.users'),
    @('dashboard.guru.guru_jadwal',                    'dashboard.guru.jadwal_jurnal'),
    @('dashboard.guru.guru_elearning',                 'dashboard.guru.materi_tugas'),
    @('dashboard.guru.guru_nilai',                     'dashboard.guru.asesmen_nilai'),
    @('dashboard.guru.guru_cbt',                       'dashboard.guru.cbt'),
    @('dashboard.guru.guru',                           'dashboard.guru.dashboard'),
    @('dashboard.bk.bk_pelanggaran',                  'dashboard.guru_bk.program_bk'),
    @('dashboard.bk.bk_triase',                        'dashboard.guru_bk.rujukan_masuk'),
    @('dashboard.bk.bk_case_detail',                   'dashboard.guru_bk.data_siswa_kasus'),
    @('dashboard.admin.tahun_ajaran_libur',             'dashboard.admin.pengaturan.tahun_ajaran_libur'),
    @('dashboard.admin.tahun_ajaran',                   'dashboard.admin.pengaturan.tahun_ajaran'),
    @('dashboard.admin.siswa_history',                  'dashboard.admin.kesiswaan.riwayat'),
    @('dashboard.admin.pindah_kelas',                   'dashboard.admin.kesiswaan.pindah_kelas'),
    @('dashboard.admin.naik_kelas',                     'dashboard.admin.kesiswaan.naik_kelas'),
    @('dashboard.admin.alumni',                         'dashboard.admin.kesiswaan.alumni'),
    @('dashboard.admin.guru_admin',                     'dashboard.admin.master_data.guru'),
    @('dashboard.admin.siswa',                          'dashboard.admin.master_data.siswa'),
    @('dashboard.absensi.absensi_input',               'dashboard.absensi.input'),
    @('dashboard.admin.integrasi_mts',                 'dashboard.admin.sistem.integrasi'),
    @('dashboard.admin.publications.berita',            'dashboard.superadmin.publications.berita'),
    @('dashboard.admin.publications.galeri',            'dashboard.superadmin.publications.galeri'),
    @('dashboard.admin.publications.medsos',            'dashboard.superadmin.publications.medsos'),
    @('dashboard.admin.publications.banner',            'dashboard.superadmin.publications.banner'),
    @('dashboard.admin.system.logs',                    'dashboard.admin.sistem.logs'),
    @('dashboard.admin.system.audit',                   'dashboard.admin.sistem.audit'),
    @('dashboard.admin.system.backup',                  'dashboard.admin.sistem.backup')
)

Write-Host ""
Write-Host "=================================================" -ForegroundColor Magenta
Write-Host "  Updating controller view() paths" -ForegroundColor Magenta
Write-Host "=================================================" -ForegroundColor Magenta

foreach ($file in $controllers) {
    if (-not (Test-Path $file)) {
        Write-Host "[SKIP] $file not found" -ForegroundColor Yellow
        continue
    }
    $content = Get-Content $file -Raw -Encoding UTF8
    $changed = $false

    foreach ($pair in $replacements) {
        $old = $pair[0]
        $new = $pair[1]
        if ($content.Contains($old)) {
            $content = $content.Replace($old, $new)
            $changed = $true
            Write-Host "  [FIX] $(Split-Path $file -Leaf) : '$old' -> '$new'" -ForegroundColor Cyan
        }
    }

    if ($changed) {
        Set-Content $file -Value $content -Encoding UTF8 -NoNewline
        Write-Host "  [SAVED] $file" -ForegroundColor Green
    } else {
        Write-Host "  [OK]   $file (no changes needed)" -ForegroundColor DarkGray
    }
}

Write-Host ""
Write-Host "=================================================" -ForegroundColor Magenta
Write-Host "  Selesai! Semua path view sudah diupdate." -ForegroundColor Green
Write-Host "=================================================" -ForegroundColor Magenta
