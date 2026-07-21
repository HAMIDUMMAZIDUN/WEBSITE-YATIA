<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Dashboard Wali Kelas') }}
            </h2>
            <button onclick="document.getElementById('erapor-modal').classList.remove('hidden')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-600/10 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate e-Rapor Kelas
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Toast notifications --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- homeroom banner -->
            @if($kelas)
                <div id="kelas-binaan-section" class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 scroll-mt-6">
                    <div class="space-y-2">
                        <span class="px-3 py-1 rounded bg-amber-500/10 text-amber-600 font-semibold text-xs uppercase tracking-wider">Kelas Binaan</span>
                        <h3 class="text-3xl font-extrabold text-slate-900">Kelas {{ $kelas->nama_kelas }}</h3>
                        <p class="text-slate-500 text-sm">
                            Anda bertanggung jawab atas administrasi, presensi, akademik, dan bimbingan karakter siswa di kelas ini.
                        </p>
                    </div>
                    <div class="flex items-center gap-6 shrink-0 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-center">
                            <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Siswa</span>
                            <span class="text-2xl font-bold text-slate-900">{{ $siswas->count() }}</span>
                        </div>
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="text-center">
                            <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Hadir Hari Ini</span>
                            <span class="text-2xl font-bold text-emerald-600">{{ $presensi->where('tanggal', today()->toDateString())->where('status','hadir')->count() }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl">
                    <h3 class="font-bold text-lg">Wali Kelas belum ditetapkan</h3>
                    <p class="text-sm">Silakan hubungi Super Admin untuk menautkan Anda ke kelas binaan tertentu.</p>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">

                <!-- Daftar Siswa -->
                <div class="lg:col-span-8 space-y-8">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Daftar Siswa Kelas Binaan</h3>
                            <span class="px-3 py-1 rounded bg-slate-100 text-slate-600 font-semibold text-xs uppercase tracking-wider">{{ $siswas->count() }} Siswa</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Nama Siswa</th>
                                        <th class="px-6 py-3">NIS</th>
                                        <th class="px-6 py-3 text-center">Hadir</th>
                                        <th class="px-6 py-3 text-center">Sakit</th>
                                        <th class="px-6 py-3 text-center">Izin</th>
                                        <th class="px-6 py-3 text-center">Alpa</th>
                                        <th class="px-6 py-3 text-right">Poin BK</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($siswas as $siswa)
                                        @php
                                            $sp = $presensi->where('siswa_id', $siswa->id);
                                        @endphp
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $siswa->nis ?? '-' }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-emerald-600">{{ $sp->where('status','hadir')->count() }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-blue-600">{{ $sp->where('status','sakit')->count() }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-amber-600">{{ $sp->where('status','izin')->count() }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-rose-600">{{ $sp->where('status','alpa')->count() }}</td>
                                            <td class="px-6 py-4 text-right">
                                                @if($siswa->poin_bk > 0)
                                                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold text-xs">{{ $siswa->poin_bk }} Poin</span>
                                                @else
                                                    <span class="text-slate-300 text-xs font-mono">0</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada siswa di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Presensi Log -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="font-bold text-lg text-slate-900">Rekap Presensi Terbaru</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($presensi->sortByDesc('tanggal')->take(20) as $pr)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-3 font-semibold text-slate-900">{{ $pr->siswa->nama }}</td>
                                            <td class="px-6 py-3 font-mono text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($pr->tanggal)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-3">
                                                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider
                                                    @if($pr->status === 'hadir') bg-emerald-100 text-emerald-800
                                                    @elseif($pr->status === 'sakit') bg-blue-100 text-blue-800
                                                    @elseif($pr->status === 'izin') bg-amber-100 text-amber-800
                                                    @else bg-rose-100 text-rose-800 @endif">
                                                    {{ $pr->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button onclick="openEditPresensi({{ $pr->id }}, '{{ $pr->status }}')"
                                                        class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold transition-colors">Edit</button>
                                                    <form action="{{ route('wali.presensi.delete', $pr->id) }}" method="POST" onsubmit="return confirm('Hapus data presensi ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada presensi tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Input Presensi -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-1 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Input Presensi Siswa
                        </h3>
                        <p class="text-xs text-slate-400 mb-4">Presensi otomatis sinkron ke halaman siswa.</p>
                        <form action="{{ route('wali.presensi.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Siswa</label>
                                <select name="siswa_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    @foreach($siswas as $sw)
                                        <option value="{{ $sw->id }}">{{ $sw->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</label>
                                <input type="date" name="tanggal" required value="{{ today()->toDateString() }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Kehadiran</label>
                                <select name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <option value="hadir">✅ Hadir</option>
                                    <option value="sakit">❤️ Sakit</option>
                                    <option value="izin">📄 Izin</option>
                                    <option value="alpa">⊗ Alpa</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Presensi
                            </button>
                        </form>
                    </div>

                    <!-- Quick Generate e-Rapor -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-base text-slate-900 mb-3 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            e-Rapor Kelas
                        </h3>
                        <p class="text-xs text-slate-400 mb-4">Generate rekap nilai & kehadiran seluruh siswa kelas binaan.</p>
                        <button onclick="document.getElementById('erapor-modal').classList.remove('hidden')"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Generate e-Rapor
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- e-Rapor Mock Generator Modal -->
    <div id="erapor-modal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-bold">e-Rapor Otomatis Kurikulum Merdeka</h4>
                    <p class="text-xs text-slate-400">Pratinjau Hasil Belajar Siswa Kelas Binaan</p>
                </div>
                <button onclick="document.getElementById('erapor-modal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Report Card Meta -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-6 text-sm text-slate-600">
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Sekolah</span>
                        <span class="font-bold text-slate-900">LPK / Sekolah YATIA</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Kelas Binaan</span>
                        <span class="font-bold text-slate-900">{{ $kelas->nama_kelas ?? 'XII IPA 3' }}</span>
                    </div>
                </div>

                <!-- Rapor Table -->
                <div class="space-y-3">
                    <h5 class="font-bold text-slate-900 text-sm">Rata-Rata Nilai Akhir Mata Pelajaran</h5>
                    <div class="border border-slate-100 rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-400 font-semibold text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-2.5">Siswa</th>
                                    <th class="px-6 py-2.5 text-center">Tugas</th>
                                    <th class="px-6 py-2.5 text-center">UTS/UAS</th>
                                    <th class="px-6 py-2.5 text-right">Rerata</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($siswas as $siswa)
                                    @php
                                        $siswaNilai = $nilais->where('siswa_id', $siswa->id);
                                        $tugas = $siswaNilai->where('tipe_nilai', 'tugas')->avg('nilai');
                                        $ujian = $siswaNilai->whereIn('tipe_nilai', ['uts', 'uas'])->avg('nilai');
                                        $total = $siswaNilai->avg('nilai');
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-3 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                                        <td class="px-6 py-3 text-center font-mono">{{ $tugas ? round($tugas, 1) : '-' }}</td>
                                        <td class="px-6 py-3 text-center font-mono">{{ $ujian ? round($ujian, 1) : '-' }}</td>
                                        <td class="px-6 py-3 text-right font-bold text-emerald-600 font-mono">{{ $total ? round($total, 1) : 'Belum dinilai' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada nilai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button onclick="document.getElementById('erapor-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    Tutup
                </button>
                <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                    Cetak Lembar Rapor
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Edit Presensi Modal --}}
<div id="modal-edit-presensi" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-900">Edit Status Presensi</h4>
            <button onclick="document.getElementById('modal-edit-presensi').style.display='none'" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form id="form-edit-presensi" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Kehadiran</label>
                <select name="status" id="ep-status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="hadir">✅ Hadir</option>
                    <option value="sakit">❤️ Sakit</option>
                    <option value="izin">📄 Izin</option>
                    <option value="alpa">⊗ Alpa</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-presensi').style.display='none'" class="flex-1 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-sm font-bold">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPresensi(id, status) {
    document.getElementById('form-edit-presensi').action = '/wali/presensi/' + id;
    document.getElementById('ep-status').value = status;
    document.getElementById('modal-edit-presensi').style.display = 'flex';
}
</script>
