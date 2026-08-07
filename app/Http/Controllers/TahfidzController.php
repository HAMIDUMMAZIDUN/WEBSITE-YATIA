<?php

namespace App\Http\Controllers;

use App\Models\TahfidzRecord;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TahfidzController extends Controller
{
    public function index()
    {
        $guru_id = Auth::user()->guru->id ?? null;
        if ($guru_id) {
            $tahfidz = TahfidzRecord::with(['siswa.kelas', 'guru'])->where('guru_id', $guru_id)->latest()->get();
        } else {
            $tahfidz = TahfidzRecord::with(['siswa.kelas', 'guru'])->latest()->get();
        }
        $siswas = Siswa::with('kelas')->get();
        return view('guru.tahfidz.index', compact('tahfidz', 'siswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'surah_juz' => 'required|string',
            'ayat' => 'required|string',
            'predikat' => 'required|in:A,B,C,D',
            'tanggal_setoran' => 'required|date',
        ]);
        
        $validated['guru_id'] = Auth::user()->guru->id ?? 1; // Fallback to 1 if no guru profile (e.g. admin testing)
        
        TahfidzRecord::create($validated);
        
        return back()->with('success', 'Setoran Hafalan Berhasil Disimpan');
    }
}
