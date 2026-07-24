<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\AttendanceDaily;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Default School Profile
        SchoolProfile::firstOrCreate(
            ['id' => 1],
            [
                'nama_yayasan' => 'Yayasan Tarbiyah Islamiyah Al-Azhar (YATIA)',
                'nama_mts' => 'MTs YATIA',
                'nama_ma' => 'MA YATIA',
                'nsm_mts' => '121232010001',
                'nsm_ma' => '131232010002',
                'npsn_mts' => '20201234',
                'npsn_ma' => '20205678',
                'status_sekolah' => 'Swasta',
                'akreditasi' => 'A',
                'alamat' => 'Jl. Pahlawan No. 45, Jakarta Selatan',
                'telepon' => '0217894561',
                'email' => 'info@yatia.sch.id',
                'website' => 'https://yatia.sch.id',
                'pimpinan_mts' => 'Dr. H. Ahmad Fauzi, M.Pd.',
                'pimpinan_ma' => 'Hj. Siti Aminah, M.A.',
                'nip_pimpinan_mts' => '197508122003121001',
                'nip_pimpinan_ma' => '198004152008012002',
            ]
        );

        // 1. Seed Roles
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin'],
            ['name' => 'admin',       'display_name' => 'Admin'],
            ['name' => 'guru',        'display_name' => 'Guru Mata Pelajaran'],
            ['name' => 'wali_kelas',  'display_name' => 'Wali Kelas'],
            ['name' => 'absensi',     'display_name' => 'Absensi'],
            ['name' => 'guru_bk',     'display_name' => 'Guru BK'],
            ['name' => 'wali_kelas_guru', 'display_name' => 'Wali Kelas & Guru Mapel'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $guruRole = Role::where('name', 'guru')->first();
        $waliRole = Role::where('name', 'wali_kelas')->first();
        $bkRole = Role::where('name', 'guru_bk')->first();
        $absensiRole = Role::where('name', 'absensi')->first();

        // 2. Create Super Admin accounts
        $adminHamid = User::firstOrCreate(
            ['email' => 'hamidabdulaziz36@gmail.com'],
            [
                'name' => 'Hamid Abdul Aziz',
                'nik' => '3204461404040001',
                'password' => Hash::make('hamidun123'),
                'role_id' => $superAdminRole->id,
            ]
        );

        $adminHidayat = User::firstOrCreate(
            ['email' => 'hidayat@gmail.com'],
            [
                'name' => 'hidayat',
                'nik' => 'hidayat',
                'password' => Hash::make('hamidun123'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // 3. Create Academic Year
        $ay = AcademicYear::firstOrCreate(
            ['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil'],
            [
                'active' => true,
            ]
        );


        // 4. Create 17 Subjects (Mata Pelajaran)
        $subjectsData = [
            ['kode_mapel' => 'QUR-01', 'nama_mapel' => 'Al-Quran Hadits'],
            ['kode_mapel' => 'AQD-02', 'nama_mapel' => 'Aqidah Akhlak'],
            ['kode_mapel' => 'FIQ-03', 'nama_mapel' => 'Fikih'],
            ['kode_mapel' => 'SKI-04', 'nama_mapel' => 'Sejarah Kebudayaan Islam'],
            ['kode_mapel' => 'PPK-05', 'nama_mapel' => 'Pendidikan Pancasila'],
            ['kode_mapel' => 'IND-06', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'ING-07', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'MAT-08', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'IPA-09', 'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'IPS-10', 'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'SEN-11', 'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'PJK-12', 'nama_mapel' => 'PJOK'],
            ['kode_mapel' => 'INF-13', 'nama_mapel' => 'Informatika'],
            ['kode_mapel' => 'PRA-14', 'nama_mapel' => 'Prakarya'],
            ['kode_mapel' => 'ASW-15', 'nama_mapel' => 'Aswaja'],
            ['kode_mapel' => 'ARB-16', 'nama_mapel' => 'Bahasa Arab'],
            ['kode_mapel' => 'KNU-17', 'nama_mapel' => 'Ke-NU-an'],
        ];
        foreach ($subjectsData as $sData) {
            MataPelajaran::firstOrCreate(['kode_mapel' => $sData['kode_mapel']], $sData);
        }

        // 5. Create 58 Teachers (32 Laki-laki, 26 Perempuan)
        $gurus = [];
        
        $realGurus = [
            [
                'nama' => 'Ahmad Ariyadi, S.Pd.I.',
                'jenis_kelamin' => 'L',
                'nik_nuptk' => '4854762663200012',
                'tempat_lahir' => 'BANDUNG',
                'tanggal_lahir' => '1984-05-22',
                'pendidikan' => 'Sarjana (S1)',
                'password_plain' => '3204462205840002',
                'jtm' => 62,
                'email' => 'ahmad.ariyadi@gmail.com',
            ],
            [
                'nama' => 'Ai Siriyah Nila, S.Pd.I.',
                'jenis_kelamin' => 'P',
                'nik_nuptk' => '525476648300033',
                'tempat_lahir' => 'BANDUNG',
                'tanggal_lahir' => '1968-09-22',
                'pendidikan' => 'Sarjana (S1)',
                'password_plain' => '3204466209680001',
                'jtm' => 0,
                'email' => 'ai.siriyah@gmail.com',
            ],
            [
                'nama' => 'Ajang Yahya Hambali, S.Ag.',
                'jenis_kelamin' => 'L',
                'nik_nuptk' => '6159745649200013',
                'tempat_lahir' => 'BANDUNG',
                'tanggal_lahir' => '1967-08-27',
                'pendidikan' => 'Sarjana (S1)',
                'password_plain' => '3204462708670002',
                'jtm' => 14,
                'email' => 'ajang.yahya@gmail.com',
            ],
            [
                'nama' => 'Andri Yana, S.Pd.I.',
                'jenis_kelamin' => 'L',
                'nik_nuptk' => '1761761662200012',
                'tempat_lahir' => 'BANDUNG',
                'tanggal_lahir' => '1983-04-29',
                'pendidikan' => 'Sarjana (S1)',
                'password_plain' => '3204462904830001',
                'jtm' => 48,
                'email' => 'andri.yana@gmail.com',
            ]
        ];

        // Additional realistic teacher names
        $maleNames = [
            'Budi Santoso, S.Pd.', 'Hendrawan, M.Pd.', 'Drs. M. Yusuf', 'H. Ridwan, S.Ag.', 'Agus Susanto, M.Si.',
            'Dr. Hermawan', 'Teguh Wibowo, S.Kom.', 'Eko Prasetyo, S.Pd.', 'Aris Munandar, M.Pd.', 'Faisal Rahman, S.Ag.',
            'Dedi Suhendar, S.T.', 'Rudi Hermawan, S.Pd.', 'Cecep Supriadi, M.M.', 'Hendra Wijaya, S.Pd.', 'Dian Nugraha, M.Pd.',
            'Wawan Setiawan, S.Pd.', 'Heri Kiswanto, S.Pd.', 'Asep Kurnia, S.Ag.', 'Taufik Hidayat, M.Si.', 'Bambang Triyono, S.Pd.',
            'Rian Hidayat, S.Kom.', 'Irfan Bachdim, S.Pd.', 'Joko Widodo, M.Pd.', 'Slamet Riyadi, S.Ag.', 'Guntur Pratama, S.Pd.',
            'Ade Suryana, S.Pd.', 'Rahmat Hidayat, M.Pd.', 'Yudi Hermawan, S.Pd.', 'Syarif Hidayatullah, M.A.'
        ];

        $femaleNames = [
            'Siti Rahmah, S.Pd.', 'Dewi Lestari, M.Pd.', 'Rina Wulandari, S.Pd.', 'Sri Wahyuni, M.Si.', 'Ani Maryani, S.Pd.',
            'Ika Kartika, S.Ag.', 'Novianti, S.Pd.', 'Euis Marlina, M.Pd.', 'Diana Fitri, S.Pd.', 'Lilis Karlina, S.Pd.',
            'Ratna Sari, M.Pd.', 'Endang Setyowati, S.Ag.', 'Yanti Susanti, S.Pd.', 'Fitriani, M.Pd.', 'Siti Aminah, S.Pd.',
            'Nining Ningsih, S.Pd.', 'Diah Permatasari, M.Si.', 'Imas Masruroh, S.Ag.', 'Mia Audina, S.Pd.', 'Sri Lestari, M.Pd.',
            'Elsa Amelia, S.Pd.', 'Putri Indah, S.Pd.', 'Siti Zubaidah, M.A.', 'Rini Astuti, S.Pd.', 'Yuliana, M.Pd.',
            'Indah Permatasari, S.Pd.', 'Kartika Sari, S.Ag.'
        ];

        $places = ['BANDUNG', 'JAKARTA', 'BOGOR', 'TASIKMALAYA', 'GARUT', 'SUMEDANG', 'CIAMIS', 'DEPOK'];
        $pendidikans = ['Sarjana (S1)', 'Magister (S2)', 'Diploma (D4)'];

        for ($i = 1; $i <= 58; $i++) {
            if ($i <= 4) {
                // Seed the 4 screenshot teachers
                $data = $realGurus[$i - 1];
                $name = $data['nama'];
                $gender = $data['jenis_kelamin'];
                $email = $data['email'];
                $nik = $data['nik_nuptk'];
                $tempat = $data['tempat_lahir'];
                $tanggal = $data['tanggal_lahir'];
                $pendidikan = $data['pendidikan'];
                $pwdPlain = $data['password_plain'];
                $jtm = $data['jtm'];
            } else {
                // Seed remaining teachers
                if ($i <= 32) {
                    $gender = 'L';
                    $name = $maleNames[($i - 5) % count($maleNames)];
                } else {
                    $gender = 'P';
                    $name = $femaleNames[($i - 33) % count($femaleNames)];
                }

                $email = "guru$i@gmail.com";
                $nik = mt_rand(1000, 9999) . mt_rand(1000, 9999) . mt_rand(1000, 9999) . mt_rand(1000, 9999);
                $tempat = $places[array_rand($places)];
                $year = mt_rand(1965, 1995);
                $month = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
                $day = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
                $tanggal = "$year-$month-$day";
                $pendidikan = $pendidikans[array_rand($pendidikans)];
                $pwdPlain = '320446' . $day . $month . substr($year, 2) . '000' . mt_rand(1, 9);
                $jtm = array_rand([0, 12, 14, 24, 36, 48, 62]);
            }

            // Let's create User account
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'nik' => $nik,
                    'password' => Hash::make($pwdPlain),
                    'role_id' => $guruRole->id,
                ]
            );

            // Let's create Guru record
            $guru = Guru::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => 'NIP' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'nama' => $name,
                    'jenis_kelamin' => $gender,
                    'nik_nuptk' => $nik,
                    'tempat_lahir' => $tempat,
                    'tanggal_lahir' => $tanggal,
                    'pendidikan' => $pendidikan,
                    'password_plain' => $pwdPlain,
                    'jtm' => $jtm,
                    'is_aktif' => true,
                ]
            );
            $gurus[] = $guru;
        }

        // 6. Create 21 Classes
        $classes = [];
        $classNames = [
            'VII.A', 'VII.B', 'VII.C', 'VII.D', 'VII.E',
            'VIII.A', 'VIII.B', 'VIII.C', 'VIII.D', 'VIII.E',
            'IX.A', 'IX.B', 'IX.C', 'IX.D', 'IX.E',
            'X.A', 'X.B', 'XI.A', 'XI.B', 'XII.A', 'XII.B'
        ];

        foreach ($classNames as $idx => $cName) {
            // Assign some teachers as wali kelas
            if ($cName === 'VIII.B') {
                $wali = $gurus[0]; // Ahmad Ariyadi
            } elseif ($cName === 'VIII.C') {
                $wali = $gurus[3]; // Andri Yana
            } else {
                $wali = $gurus[$idx % count($gurus)];
            }
            $cls = Kelas::firstOrCreate(
                ['nama_kelas' => $cName],
                ['wali_kelas_id' => $wali->id]
            );
            $classes[] = $cls;
        }

        // 7. Create 859 Students (421 Laki-laki, 438 Perempuan)
        $studentStatuses = [];
        // Hadir: 817, Sakit: 11, Izin: 17, Alpa: 9, Terlambat: 5
        for ($k = 0; $k < 817; $k++) $studentStatuses[] = 'H';
        for ($k = 0; $k < 11; $k++)  $studentStatuses[] = 'S';
        for ($k = 0; $k < 17; $k++)  $studentStatuses[] = 'I';
        for ($k = 0; $k < 9; $k++)   $studentStatuses[] = 'A';
        for ($k = 0; $k < 5; $k++)   $studentStatuses[] = 'T';

        shuffle($studentStatuses);

        // Bulk insert to speed things up
        $siswasInsert = [];
        $attendanceInsert = [];
        $today = Carbon::today()->toDateString();

        for ($i = 1; $i <= 859; $i++) {
            $gender = ($i <= 421) ? 'L' : 'P';
            $name = ($gender === 'L') ? "Siswa Laki-laki $i" : "Siswa Perempuan $i";
            
            // Assign to class cyclically
            $cls = $classes[($i - 1) % count($classes)];

            // Create User account first
            $u = User::firstOrCreate(
                ['email' => "siswa$i@gmail.com"],
                [
                    'name' => $name,
                    'nik' => 'NIS' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'password' => Hash::make('hamidun123'),
                    'role_id' => $absensiRole->id,
                ]
            );

            $siswa = Siswa::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'nis' => 'NIS' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'nama' => $name,
                    'kelas_id' => $cls->id,
                    'jenis_kelamin' => $gender,
                ]
            );

            // Generate today's attendance record
            $status = $studentStatuses[$i - 1];
            AttendanceDaily::firstOrCreate(
                [
                    'student_id' => $siswa->id,
                    'date' => $today,
                ],
                [
                    'class_id' => $cls->id,
                    'status' => $status,
                    'input_by' => $adminHidayat->id,
                    'verification_status' => 'verified',
                ]
            );
        }

        // 8. Seed Audit Logs for Activity widget
        $activities = [
            [
                'user_id' => $adminHidayat->id,
                'action' => 'login sebagai Super Admin',
                'entity' => 'users',
                'timestamp' => Carbon::now()->subMinutes(2),
            ],
            [
                'user_id' => $adminHamid->id, // Represent Seksi Absensi
                'action' => 'menyimpan absensi kelas VII.A',
                'entity' => 'attendance_submissions',
                'timestamp' => Carbon::now()->subMinutes(8),
            ],
            [
                'user_id' => $adminHamid->id,
                'action' => 'mengubah profil madrasah',
                'entity' => 'school_profiles',
                'timestamp' => Carbon::now()->subHour(),
            ],
            [
                'user_id' => $gurus[2]->user_id, // Guru Fikih
                'action' => 'unggah materi pertemuan 1',
                'entity' => 'materi_tugas',
                'timestamp' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => $adminHamid->id, // Counselor
                'action' => 'mencatat tindak lanjut siswa',
                'entity' => 'bk_sessions',
                'timestamp' => Carbon::now()->startOfDay(),
            ],
        ];

        foreach ($activities as $act) {
            AuditLog::create($act);
        }
    }
}
