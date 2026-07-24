<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Wali Kelas - Profil 360°</span>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ $siswa->nama }}
                </h2>
            </div>
            <a href="{{ route('wali.dashboard') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-250 font-bold text-xs rounded-xl transition-colors">
                ← Kembali ke Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Student Metadata Card -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-6 text-white shadow-lg md:col-span-2 flex flex-col justify-between">
                <div>
                    <span class="px-2.5 py-0.5 rounded bg-white/20 text-white font-bold text-[10px] uppercase tracking-wider">Biodata Siswa</span>
                    <h3 class="text-2xl font-extrabold mt-3">{{ $siswa->nama }}</h3>
                    <p class="text-emerald-100 text-xs font-mono mt-1">NIS: {{ $siswa->nis }} | Kelas: {{ $siswa->kelas->nama_kelas }}</p>
                </div>
                
                <div class="mt-6 pt-4 border-t border-white/10 flex flex-wrap gap-4 text-xs text-emerald-100">
                    <div>
                        <span class="block text-white/50 text-[10px] uppercase font-bold">Email</span>
                        <span class="font-medium">{{ $siswa->user->email ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-white/50 text-[10px] uppercase font-bold">Kontak Utama</span>
                        <span class="font-medium">{{ $siswa->studentGuardians->first()->phone ?? 'Belum ada kontak' }}</span>
                    </div>
                </div>
            </div>

            <!-- Attendance Stats Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Rekap Kehadiran</span>
                    <div class="flex items-baseline gap-2 mt-3">
                        @php
                            $totalAttendance = $siswa->attendanceDaily->count();
                            $hadir = $siswa->attendanceDaily->where('status', 'H')->count();
                            $alpa = $siswa->attendanceDaily->where('status', 'A')->count();
                            $persenHadir = $totalAttendance > 0 ? round(($hadir / $totalAttendance) * 100) : 100;
                        @endphp
                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ $persenHadir }}%</span>
                        <span class="text-xs text-slate-400 font-semibold">Kehadiran</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 flex justify-between text-xs font-semibold">
                    <span class="text-emerald-600">{{ $hadir }} Hadir</span>
                    <span class="text-rose-600">{{ $alpa }} Alpa</span>
                </div>
            </div>

            <!-- BK Points Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Akumulasi Poin Disiplin</span>
                    <div class="flex items-baseline gap-2 mt-3">
                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ $totalPoinPelanggaran }}</span>
                        <span class="text-xs text-slate-400 font-semibold">Poin Pelanggaran</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 flex justify-between text-xs font-semibold">
                    <span class="text-emerald-600">{{ $totalPoinPrestasi }} Poin Prestasi</span>
                    @if($totalPoinPelanggaran >= 50)
                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 text-[10px] font-bold uppercase">Status Merah</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase">Aman</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Academic Performance & BK Referrals -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Academic Performance Sheet (KKTP Status) -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Rekam Nilai & Ketuntasan (KKTP)</h3>
                        <p class="text-xs text-slate-400">Status evaluasi pembelajaran per mata pelajaran dalam semester berjalan.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3">Mata Pelajaran</th>
                                    <th class="px-6 py-3 text-center">Nilai Rata-rata</th>
                                    <th class="px-6 py-3 text-center">Kriteria (KKTP)</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($siswa->academicSnapshots as $snap)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                            {{ $snap->subject->nama_mapel ?? 'Mapel' }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono font-bold text-slate-700 dark:text-slate-300">
                                            {{ $snap->average_score }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono text-slate-400">
                                            {{ $snap->kktp_limit }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($snap->kktp_status === 'tuntas')
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400">Tuntas</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 animate-pulse">Belum Tuntas</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada rekam nilai atau evaluasi yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BK Case Referrals (Strict Access Control Panel) -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Pelaporan Bimbingan Konseling (BK)</h3>
                                <p class="text-xs text-slate-400">Rujukan resmi yang diajukan ke Guru BK. Detil konseling dilindungi undang-undang kerahasiaan.</p>
                            </div>
                            <span class="px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase">Privacy Guarded</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3">Tanggal Laporan</th>
                                    <th class="px-6 py-3">Alasan Rujukan</th>
                                    <th class="px-6 py-3 text-center">Prioritas</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3">Catatan Balikan BK</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($bkReferrals as $ref)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                        <td class="px-6 py-4 text-xs font-mono text-slate-400">
                                            {{ Carbon\Carbon::parse($ref->created_at)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                            {{ $ref->reason }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $ref->priority === 'darurat' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ $ref->priority === 'tinggi' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $ref->priority === 'sedang' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $ref->priority === 'rendah' ? 'bg-slate-100 text-slate-800' : '' }}">
                                                {{ $ref->priority }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $ref->status === 'pending' ? 'bg-slate-100 text-slate-600' : '' }}
                                                {{ $ref->status === 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $ref->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                                {{ $ref->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs italic text-slate-500">
                                            {{ $ref->summary_for_homeroom ?? 'Menunggu penanganan konselor...' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Siswa ini tidak memiliki riwayat kasus BK.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Interventions & Parent Communication (Sidebar) -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Academic Interventions -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        Program Intervensi Wali Kelas
                    </h3>
                    <p class="text-xs text-slate-400">Rencana tindakan untuk membantu siswa yang belum mencapai KKTP atau memiliki masalah disiplin.</p>
                    
                    <div class="space-y-3">
                        @forelse($siswa->studentInterventions as $int)
                            <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-850/50">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 font-bold text-[9px] uppercase tracking-wider">{{ $int->type }}</span>
                                    <span class="text-[9px] font-mono text-slate-450">{{ Carbon\Carbon::parse($int->start_date)->translatedFormat('d M Y') }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $int->notes }}</h4>
                                <span class="block text-[10px] text-slate-400 mt-1">Dibuat oleh: {{ $int->ownerUser->name ?? 'Wali Kelas' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-4">Belum ada program intervensi yang tercatat.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Parent Communication History -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Komunikasi Orang Tua / Wali
                    </h3>
                    <p class="text-xs text-slate-400">Jejak komunikasi resmi dengan orang tua/wali siswa terkait perkembangan belajar.</p>
                    
                    <div class="space-y-3">
                        @forelse($communications as $comm)
                            <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-850/50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[10px] font-bold text-slate-650 dark:text-slate-350">Via: {{ ucfirst($commclass->channel ?? 'WhatsApp') }}</span>
                                    <span class="text-[9px] font-mono text-slate-450">{{ Carbon\Carbon::parse($comm->created_at)->translatedFormat('d M H:i') }}</span>
                                </div>
                                <p class="text-xs text-slate-900 dark:text-slate-300">"{{ $comm->summary }}"</p>
                                <span class="block text-[9px] text-slate-400 mt-1">Orang tua: {{ $comm->guardian->name ?? 'Orang Tua' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-4">Belum ada jejak komunikasi tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
