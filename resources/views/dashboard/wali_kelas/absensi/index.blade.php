<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Rekap Absensi Kelas Binaan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- stats summary cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Hadir -->
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Hadir</span>
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $presensi->where('status', 'hadir')->count() }}</span>
                    </div>
                </div>

                <!-- Sakit -->
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Sakit</span>
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $presensi->where('status', 'sakit')->count() }}</span>
                    </div>
                </div>

                <!-- Izin -->
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Izin</span>
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $presensi->where('status', 'izin')->count() }}</span>
                    </div>
                </div>

                <!-- Alpa -->
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Alpa</span>
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $presensi->where('status', 'alpa')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Detailed Student Attendance Recap Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900">Statistik Kehadiran per Siswa</h3>
                    <span class="text-slate-400 text-xs font-medium">Berdasarkan data presensi terdaftar</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Nama Siswa</th>
                                <th class="px-6 py-3 text-center">Hadir</th>
                                <th class="px-6 py-3 text-center">Sakit</th>
                                <th class="px-6 py-3 text-center">Izin</th>
                                <th class="px-6 py-3 text-center">Alpa</th>
                                <th class="px-6 py-3 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswas as $siswa)
                                @php
                                    $siswaPresensi = $presensi->where('siswa_id', $siswa->id);
                                    $hadir = $siswaPresensi->where('status', 'hadir')->count();
                                    $sakit = $siswaPresensi->where('status', 'sakit')->count();
                                    $izin = $siswaPresensi->where('status', 'izin')->count();
                                    $alpa = $siswaPresensi->where('status', 'alpa')->count();
                                    $total = $siswaPresensi->count();
                                    $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 100;
                                @endphp
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                                    <td class="px-6 py-3 text-center font-mono font-bold text-emerald-600">{{ $hadir }}</td>
                                    <td class="px-6 py-3 text-center font-mono text-blue-600">{{ $sakit }}</td>
                                    <td class="px-6 py-3 text-center font-mono text-amber-600">{{ $izin }}</td>
                                    <td class="px-6 py-3 text-center font-mono text-rose-600">{{ $alpa }}</td>
                                    <td class="px-6 py-4 text-right font-bold font-mono text-slate-900">
                                        {{ $persentase }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">Tidak ada data kehadiran siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
