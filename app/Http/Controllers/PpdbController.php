<?php

namespace App\Http\Controllers;

use App\Models\PpdbRegistration;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PpdbController extends Controller
{
    public function index()
    {
        $registrations = PpdbRegistration::with('academicYear')->latest()->get();
        $academicYears = AcademicYear::all();
        $kelas = Kelas::all();
        return view('admin.ppdb.index', compact('registrations', 'academicYears', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string',
            'nisn' => 'nullable|string',
            'jenis_kelamin' => 'required|in:L,P',
            'asal_sekolah' => 'required|string',
            'nomor_wa' => 'required|string',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        
        $validated['nomor_pendaftaran'] = 'PPDB-' . time() . '-' . rand(10, 99);
        
        PpdbRegistration::create($validated);
        
        return back()->with('success', 'Data Pendaftaran Berhasil Ditambahkan');
    }

    public function updateStatus(Request $request, $id)
    {
        $ppdb = PpdbRegistration::findOrFail($id);
        $ppdb->update(['status' => $request->status]);
        
        return back()->with('success', 'Status PPDB Diperbarui');
    }

    public function migrateToSiswa(Request $request, $id)
    {
        $ppdb = PpdbRegistration::findOrFail($id);
        
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|string|unique:siswas,nis',
        ]);

        $siswaRole = Role::where('name', 'absensi')->firstOrFail();
        $email = ($ppdb->nisn ? $ppdb->nisn : time()) . '@yatia.sch.id';
        
        $user = User::create([
            'name' => $ppdb->nama_lengkap,
            'email' => $email,
            'password' => Hash::make('password123'),
            'password_plain' => 'password123',
            'role_id' => $siswaRole->id,
        ]);

        $siswa = Siswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nis' => $request->nis,
                'nisn' => $ppdb->nisn,
                'nama' => $ppdb->nama_lengkap,
                'kelas_id' => $request->kelas_id,
                'jenis_kelamin' => $ppdb->jenis_kelamin,
                'no_hp' => $ppdb->nomor_wa,
                'alamat' => $ppdb->asal_sekolah,
            ]
        );

        $ppdb->update(['status' => 'diterima']);

        return back()->with('success', 'Calon siswa berhasil dimigrasikan ke Master Siswa dengan Akun: ' . $email);
    }

    public function destroy($id)
    {
        PpdbRegistration::findOrFail($id)->delete();
        return back()->with('success', 'Data PPDB Dihapus');
    }
}
