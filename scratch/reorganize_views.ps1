$base = "d:\APLIKASI RUNNING\XAMPP\htdocs\APLIKASI\SIMSYATIA\resources\views\dashboard"

# Helper: create placeholder blade
function New-Placeholder {
    param([string]$Path, [string]$Title, [string]$Desc)
    if (-not (Test-Path $Path)) {
        $content = "@extends('layouts.app')`r`n@section('content')`r`n<div class=`"py-12`">`r`n    <div class=`"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`">`r`n        <div class=`"bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-8 text-center`">`r`n            <div class=`"w-16 h-16 mx-auto mb-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl flex items-center justify-center`">`r`n                <svg class=`"w-8 h-8 text-emerald-500`" fill=`"none`" stroke=`"currentColor`" viewBox=`"0 0 24 24`"><path stroke-linecap=`"round`" stroke-linejoin=`"round`" stroke-width=`"1.5`" d=`"M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10`"/></svg>`r`n            </div>`r`n            <h2 class=`"text-xl font-bold text-slate-900 dark:text-white mb-2`">$Title</h2>`r`n            <p class=`"text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto`">$Desc</p>`r`n            <span class=`"inline-block mt-4 px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 text-xs font-bold rounded-full`">Dalam Pengembangan</span>`r`n        </div>`r`n    </div>`r`n</div>`r`n@endsection"
        $dir = Split-Path $Path -Parent
        if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
        Set-Content -Path $Path -Value $content -Encoding UTF8
        Write-Host "  [NEW]  $Path" -ForegroundColor Green
    } else {
        Write-Host "  [SKIP] $Path (exists)" -ForegroundColor DarkGray
    }
}

# Helper: copy file (preserves original, creates destination)
function Move-View {
    param([string]$From, [string]$To)
    if (Test-Path $From) {
        $dir = Split-Path $To -Parent
        if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
        if (-not (Test-Path $To)) {
            Copy-Item $From $To
            Write-Host "  [MOVE] $(Split-Path $From -Leaf) -> $To" -ForegroundColor Cyan
        } else {
            Write-Host "  [SKIP] $To (exists)" -ForegroundColor DarkGray
        }
    } else {
        Write-Host "  [MISS] $From (not found)" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  SIMSYATIA - Reorganisasi Views per Role   " -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta

# ============================================================
# 1. SUPERADMIN
# ============================================================
Write-Host "`n[1] SUPERADMIN" -ForegroundColor Blue
Move-View "$base\admin\super_admin.blade.php" "$base\superadmin\dashboard.blade.php"
# users.blade.php sudah di superadmin/ (tidak perlu dipindah lagi)
# publications/ sudah ada

# ============================================================
# 2. ADMIN — subfolder: pengaturan, master_data, kesiswaan, sistem
# ============================================================
Write-Host "`n[2] ADMIN" -ForegroundColor Blue

# Pengaturan
New-Item -ItemType Directory -Path "$base\admin\pengaturan" -Force | Out-Null
Move-View "$base\admin\tahun_ajaran.blade.php"       "$base\admin\pengaturan\tahun_ajaran.blade.php"
Move-View "$base\admin\tahun_ajaran_libur.blade.php" "$base\admin\pengaturan\tahun_ajaran_libur.blade.php"

# Master Data
New-Item -ItemType Directory -Path "$base\admin\master_data" -Force | Out-Null
Move-View "$base\admin\kelas.blade.php"      "$base\admin\master_data\kelas.blade.php"
Move-View "$base\admin\mapel.blade.php"      "$base\admin\master_data\mapel.blade.php"
Move-View "$base\admin\guru_admin.blade.php" "$base\admin\master_data\guru.blade.php"
Move-View "$base\admin\siswa.blade.php"      "$base\admin\master_data\siswa.blade.php"

# Kesiswaan
New-Item -ItemType Directory -Path "$base\admin\kesiswaan" -Force | Out-Null
Move-View "$base\admin\siswa_history.blade.php" "$base\admin\kesiswaan\riwayat.blade.php"
Move-View "$base\admin\pindah_kelas.blade.php"  "$base\admin\kesiswaan\pindah_kelas.blade.php"
Move-View "$base\admin\naik_kelas.blade.php"    "$base\admin\kesiswaan\naik_kelas.blade.php"
Move-View "$base\admin\alumni.blade.php"        "$base\admin\kesiswaan\alumni.blade.php"
Move-View "$base\admin\absensi.blade.php"       "$base\admin\kesiswaan\absensi.blade.php"
New-Placeholder "$base\admin\kesiswaan\bk.blade.php" "BK Kesiswaan" "Rekap bimbingan konseling dari perspektif admin."

# Sistem
New-Item -ItemType Directory -Path "$base\admin\sistem" -Force | Out-Null
Move-View "$base\admin\system\audit.blade.php"  "$base\admin\sistem\audit.blade.php"
Move-View "$base\admin\system\backup.blade.php" "$base\admin\sistem\backup.blade.php"
Move-View "$base\admin\system\logs.blade.php"   "$base\admin\sistem\logs.blade.php"
Move-View "$base\admin\integrasi_mts.blade.php" "$base\admin\sistem\integrasi.blade.php"
Move-View "$base\admin\rekap_print.blade.php"   "$base\admin\sistem\rekap_print.blade.php"
Move-View "$base\superadmin\users.blade.php"    "$base\admin\sistem\users.blade.php"
New-Placeholder "$base\admin\sistem\roles.blade.php"   "Manajemen Role"   "Pengaturan role dan permission pengguna sistem."
New-Placeholder "$base\admin\sistem\laporan.blade.php" "Laporan Sistem"   "Laporan aktivitas dan penggunaan sistem."

# ============================================================
# 3. WALI KELAS
# ============================================================
Write-Host "`n[3] WALI KELAS" -ForegroundColor Blue

Move-View "$base\wali_kelas\wali_kelas.blade.php"              "$base\wali_kelas\dashboard.blade.php"
Move-View "$base\wali_kelas\wali_officer.blade.php"            "$base\wali_kelas\kenaikan_mutasi.blade.php"
Move-View "$base\wali_kelas\wali_siswa.blade.php"              "$base\wali_kelas\data_siswa.blade.php"
Move-View "$base\wali_kelas\wali_siswa_360.blade.php"          "$base\wali_kelas\siswa_360.blade.php"
Move-View "$base\wali_kelas\wali_absensi.blade.php"            "$base\wali_kelas\absensi\index.blade.php"
Move-View "$base\wali_kelas\wali_absensi_verifikasi.blade.php" "$base\wali_kelas\absensi\verifikasi.blade.php"

New-Placeholder "$base\wali_kelas\tugas_siswa.blade.php"          "Tugas Siswa"            "Melihat tugas lintas mapel: tenggat, status, keterlambatan."
New-Placeholder "$base\wali_kelas\prestasi_pelanggaran.blade.php" "Prestasi dan Pelanggaran" "Rekam pembinaan siswa: jenis, poin, bukti, penyelesaian."
New-Placeholder "$base\wali_kelas\bk_tindak_lanjut.blade.php"    "BK dan Tindak Lanjut"   "Rujukan dan koordinasi kasus BK: alasan, prioritas, status."
New-Placeholder "$base\wali_kelas\komunikasi_ortu.blade.php"      "Komunikasi Orang Tua"   "Pesan, undangan, konfirmasi, riwayat komunikasi orang tua."
New-Placeholder "$base\wali_kelas\surat_dokumen.blade.php"        "Surat dan Dokumen"      "Membuat surat kelas: undangan, izin, pernyataan, rekomendasi."
New-Placeholder "$base\wali_kelas\laporan.blade.php"              "Laporan dan Cetak"      "Mencetak rekap kelas dengan filter waktu dan jenis laporan."

# ============================================================
# 4. GURU MATA PELAJARAN
# ============================================================
Write-Host "`n[4] GURU MATA PELAJARAN" -ForegroundColor Blue

Move-View "$base\guru\guru.blade.php"           "$base\guru\dashboard.blade.php"
Move-View "$base\guru\guru_jadwal.blade.php"    "$base\guru\jadwal_jurnal.blade.php"
Move-View "$base\guru\guru_elearning.blade.php" "$base\guru\materi_tugas.blade.php"
Move-View "$base\guru\guru_nilai.blade.php"     "$base\guru\asesmen_nilai.blade.php"
Move-View "$base\guru\guru_cbt.blade.php"       "$base\guru\cbt.blade.php"

New-Placeholder "$base\guru\perencanaan.blade.php"   "Perencanaan Pembelajaran" "CP, TP, ATP, Prota, Promes, modul ajar, dan KKTP."
New-Placeholder "$base\guru\absensi_siswa.blade.php" "Absensi Siswa"            "Melihat absensi kelas; mengisi bila diberi kewenangan."
New-Placeholder "$base\guru\remedial.blade.php"      "Remedial dan Pengayaan"   "Daftar sasaran, kegiatan, dan nilai tindak lanjut remedial."
New-Placeholder "$base\guru\kokurikuler.blade.php"   "Kokurikuler"              "Rencana, peran guru, aktivitas, asesmen, dan dokumentasi."
New-Placeholder "$base\guru\administrasi.blade.php"  "Administrasi Guru"        "Arsip seluruh dokumen per semester sebagai portofolio."
New-Placeholder "$base\guru\laporan.blade.php"       "Laporan dan Cetak"        "Cetak per dokumen atau paket lengkap: PDF/DOCX/XLSX."
New-Placeholder "$base\guru\asisten_ai.blade.php"    "Asisten AI"               "Membuat draf terstruktur dengan Gemini yang wajib ditinjau guru."

# ============================================================
# 5. GURU BK
# ============================================================
Write-Host "`n[5] GURU BK" -ForegroundColor Blue

Move-View "$base\guru_bk\guru_bk.blade.php"        "$base\guru_bk\dashboard.blade.php"
Move-View "$base\guru_bk\bk_triase.blade.php"      "$base\guru_bk\rujukan_masuk.blade.php"
Move-View "$base\guru_bk\bk_case_detail.blade.php" "$base\guru_bk\data_siswa_kasus.blade.php"
Move-View "$base\guru_bk\bk_pelanggaran.blade.php" "$base\guru_bk\program_bk.blade.php"

New-Placeholder "$base\guru_bk\jadwal_konseling.blade.php"   "Jadwal Konseling"    "Penjadwalan layanan: jenis, waktu, tempat, dan peserta."
New-Placeholder "$base\guru_bk\catatan_layanan.blade.php"    "Catatan Layanan"     "Pencatatan sesi dan tindak lanjut konseling."
New-Placeholder "$base\guru_bk\bimbingan_klasikal.blade.php" "Bimbingan Klasikal"  "Materi dan pelaksanaan bimbingan di kelas."
New-Placeholder "$base\guru_bk\ortu_wali.blade.php"          "Orang Tua dan Wali"  "Komunikasi dan pertemuan dengan orang tua/wali siswa."
New-Placeholder "$base\guru_bk\rujukan_eksternal.blade.php"  "Rujukan Eksternal"   "Koordinasi pihak mitra dan surat rujukan."
New-Placeholder "$base\guru_bk\survei_asesmen.blade.php"     "Survei dan Asesmen"  "Pemetaan kebutuhan siswa: instrumen, respons, analisis."
New-Placeholder "$base\guru_bk\laporan.blade.php"            "Laporan dan Cetak"   "Laporan individu dan agregat BK: DOCX/PDF/XLSX."
New-Placeholder "$base\guru_bk\asisten_ai.blade.php"         "Asisten AI"          "Draf administrasi BK berbasis template dan data terpilih."

# ============================================================
# 6. ABSENSI
# ============================================================
Write-Host "`n[6] ABSENSI" -ForegroundColor Blue

Move-View "$base\absensi\absensi_input.blade.php" "$base\absensi\input.blade.php"

New-Placeholder "$base\absensi\dashboard.blade.php"     "Dashboard Absensi"    "Ringkasan absensi kelas: status dan notifikasi tindakan harian."
New-Placeholder "$base\absensi\riwayat.blade.php"       "Riwayat Absensi"      "Melihat data absensi tanggal sebelumnya."
New-Placeholder "$base\absensi\koreksi.blade.php"       "Koreksi Data"         "Mengajukan perubahan resmi: status lama/baru, alasan, bukti."
New-Placeholder "$base\absensi\bukti_izin.blade.php"    "Bukti Izin dan Sakit" "Mengelola lampiran keterangan: jenis bukti, file, pengirim."
New-Placeholder "$base\absensi\keterlambatan.blade.php" "Keterlambatan"        "Mencatat jam masuk dan alasan keterlambatan siswa."
New-Placeholder "$base\absensi\dispensasi.blade.php"    "Dispensasi"           "Mencatat izin kegiatan resmi: waktu, kegiatan, penanggung jawab."
New-Placeholder "$base\absensi\belum_hadir.blade.php"   "Siswa Belum Hadir"    "Daftar siswa tanpa status kehadiran untuk dikonfirmasi."
New-Placeholder "$base\absensi\rekap_kelas.blade.php"   "Rekap Kelas"          "Ringkasan periode: H/S/I/A/T/D dalam tabel dan grafik."
New-Placeholder "$base\absensi\laporan.blade.php"       "Laporan dan Cetak"    "Mencetak laporan absensi berdasarkan kelas dan periode."
New-Placeholder "$base\absensi\profil.blade.php"        "Profil Saya"          "Mengelola data akun: nama, foto, dan password."

# ============================================================
Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  SELESAI! Silakan update path di Controller" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Magenta
