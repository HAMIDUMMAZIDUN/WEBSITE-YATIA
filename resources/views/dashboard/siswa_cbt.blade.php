<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Ujian Online / CBT Aktif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Student Header Profile Banner -->
            @if($siswa)
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 text-white shadow-lg shadow-purple-600/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <span class="px-3 py-1 rounded bg-white/20 text-white font-semibold text-xs uppercase tracking-wider">Profil Siswa</span>
                        <h3 class="text-3xl font-extrabold">{{ $siswa->nama }}</h3>
                        <p class="text-purple-100 flex items-center gap-2">
                            <span class="font-mono text-sm bg-black/15 px-2 py-0.5 rounded">NIS: {{ $siswa->nis ?? '-' }}</span>
                            <span class="text-purple-200">|</span>
                            <span>Kelas: {{ $kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Exam CBT Mock Panel -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-bold text-xl text-slate-900">Ujian Online / CBT Aktif</h3>
                    <span class="px-3 py-1 rounded bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-wider">Aman & Terkunci</span>
                </div>
                <div class="p-6 border border-slate-150 rounded-2xl bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-700 font-semibold text-[10px] uppercase tracking-wider">Ujian Akhir Semester</span>
                        <h4 class="font-bold text-lg text-slate-900 mt-1">Ujian Matematika Wajib Semester Ganjil</h4>
                        <p class="text-sm text-slate-500">Waktu: 90 Menit | Pengawas: Bimbingan Konseling / Guru Mapel</p>
                    </div>
                    <button onclick="alert('Membuka browser terkunci CBT Mock... Ujian dimulai!')" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm shadow-lg transition-colors shrink-0">
                        Mulai Ujian Online
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
