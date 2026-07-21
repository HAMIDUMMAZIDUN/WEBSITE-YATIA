<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Dashboard Wali Kelas</span>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ __('Binaan Kelas') }} {{ $kelas->nama_kelas }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 font-bold text-xs uppercase">Tahun Ajaran Aktif</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Top Class Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between">
                <div>
                    <span class="text-white/70 text-xs font-bold uppercase tracking-wider block">Jumlah Siswa Binaan</span>
                    <h3 class="text-4xl font-extrabold mt-2">{{ $totalSiswa }}</h3>
                </div>
                <p class="text-emerald-100 text-xs mt-4">Seluruh data siswa aktif terdaftar di kelas.</p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Kehadiran Hari Ini</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ $absensiPersen }}%</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-semibold">Berdasarkan data Seksi Absensi</span>
                    <a href="{{ route('wali.absensi.verifikasi.index') }}" class="text-emerald-600 font-bold hover:underline">Kelola Absensi →</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Siswa Prioritas Tindakan</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ count($priorityStudents) }}</span>
                        <span class="text-xs text-rose-500 font-bold uppercase">Butuh Atensi</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-4">Siswa di bawah KKTP, poin pelanggaran >= 50, atau alpa >= 3.</p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Student list & Priority warnings -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Priority Radar -->
                @if(count($priorityStudents) > 0)
                    <div class="bg-rose-50/50 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/50 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                            <h3 class="font-bold text-base text-rose-800 dark:text-rose-400">Atensi & Tindak Lanjut Prioritas</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($priorityStudents as $pSiswa)
                                <div class="bg-white dark:bg-slate-900 border border-rose-100 dark:border-rose-900/50 p-4 rounded-xl flex flex-col justify-between gap-3">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $pSiswa['siswa']->nama }}</h4>
                                        <div class="space-y-1 mt-2">
                                            @if($pSiswa['academic_issue'])
                                                <span class="block text-xs text-rose-600 font-medium">• {{ $pSiswa['academic_issue'] }}</span>
                                            @endif
                                            @if($pSiswa['discipline_issue'])
                                                <span class="block text-xs text-rose-600 font-medium">• {{ $pSiswa['discipline_issue'] }}</span>
                                            @endif
                                            @if($pSiswa['attendance_issue'])
                                                <span class="block text-xs text-rose-600 font-medium">• {{ $pSiswa['attendance_issue'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <a href="{{ route('wali.siswa.360', $pSiswa['siswa']->id) }}" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-colors">
                                            Rencana Intervensi
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Class Student Roster -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Roster Kelas Binaan</h3>
                            <p class="text-xs text-slate-400">Seluruh siswa binaan beserta profil perkembangan individu.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3">No</th>
                                    <th class="px-6 py-3">Nama Lengkap</th>
                                    <th class="px-6 py-3">NIS</th>
                                    <th class="px-6 py-3 text-center">Absensi Hari Ini</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($siswas as $idx => $sw)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $sw->nama }}</td>
                                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $sw->nis }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $todayRec = $sw->attendanceDaily->first();
                                                $status = $todayRec ? $todayRec->status : '-';
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $status === 'H' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $status === 'S' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $status === 'I' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $status === 'A' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ $status === '-' ? 'bg-slate-100 text-slate-400' : '' }}">
                                                {{ $status === '-' ? 'Belum Absen' : $status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('wali.siswa.360', $sw->id) }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/30 dark:hover:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold transition-colors">
                                                Profil 360°
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar: Pending approvals, Journal -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Pending Approvals Widget -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-950 dark:text-white flex items-center justify-between">
                        <span>Persetujuan Absensi</span>
                        @if($pendingSubmissions->count() > 0)
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400">Tinjau kiriman absensi harian kelas dari Seksi Absensi.</p>
                    
                    <div class="space-y-3">
                        @forelse($pendingSubmissions as $pSub)
                            <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-850/50 flex flex-col justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-mono text-slate-400 block">{{ Carbon\Carbon::parse($pSub->date)->translatedFormat('d M Y') }}</span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-1 block">Dikirim oleh: {{ $pSub->submittedBy->name ?? 'Petugas' }}</span>
                                </div>
                                <div class="flex justify-end">
                                    <a href="{{ route('wali.absensi.verifikasi.index') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[10px] font-bold shadow-sm transition-colors">
                                        Periksa Laporan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-4">Semua kiriman absensi kelas sudah terverifikasi.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Homeroom Journal logs -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-950 dark:text-white">Jurnal Pembinaan Kelas</h3>
                    <p class="text-xs text-slate-400">Jurnal harian pembinaan, rapat kelas, atau kunjungan rumah oleh Wali Kelas.</p>
                    
                    <div class="space-y-3">
                        @forelse($journals as $jrn)
                            <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-850/50">
                                <span class="text-[9px] font-mono text-slate-450 block">{{ Carbon\Carbon::parse($jrn->created_at)->translatedFormat('d M H:i') }}</span>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs mt-1">{{ $jrn->title }}</h4>
                                <p class="text-[11px] text-slate-600 mt-1">{{ $jrn->notes }}</p>
                                @if($jrn->student)
                                    <span class="block text-[9px] text-emerald-600 font-bold mt-1.5">Siswa: {{ $jrn->student->nama }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-450 italic text-center py-4">Belum ada jurnal pembinaan kelas.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
