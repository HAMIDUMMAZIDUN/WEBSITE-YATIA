<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Input & Rekap Nilai Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Recent Grades -->
                <div class="lg:col-span-8 space-y-8">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Riwayat Input Nilai Terakhir</h3>
                            <span class="px-3 py-1 rounded bg-amber-50 text-amber-600 font-semibold text-xs uppercase tracking-wider">Terbaru</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Mapel</th>
                                        <th class="px-6 py-3">Tipe</th>
                                        <th class="px-6 py-3 text-center">Nilai</th>
                                        <th class="px-6 py-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($nilais as $nilai)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $nilai->siswa->nama }}</td>
                                            <td class="px-6 py-4 font-medium text-slate-700">{{ $nilai->mataPelajaran->nama_mapel }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider 
                                                    {{ $nilai->tipe_nilai === 'uas' ? 'bg-red-100 text-red-700' : '' }}
                                                    {{ $nilai->tipe_nilai === 'uts' ? 'bg-amber-100 text-amber-700' : '' }}
                                                    {{ $nilai->tipe_nilai === 'kuis' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $nilai->tipe_nilai === 'tugas' ? 'bg-slate-100 text-slate-700' : '' }}
                                                ">
                                                    {{ strtoupper($nilai->tipe_nilai) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center font-mono font-bold text-slate-950">{{ $nilai->nilai }}</td>
                                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $nilai->catatan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada riwayat input nilai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Input Grades Form -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Input Nilai Siswa
                        </h3>
                        <form action="{{ route('guru.nilai.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4" x-data="{
                                selectedKelasId: '',
                                siswas: [
                                    @foreach($siswas as $sw)
                                        { id: '{{ $sw->id }}', nama: '{{ addslashes($sw->nama) }}', kelas_id: '{{ $sw->kelas_id ?? '' }}' },
                                    @endforeach
                                ],
                                getOptionsHtml() {
                                    let html = '<option value=\x22\x22 disabled selected>Pilih Siswa...</option>';
                                    let filtered = this.selectedKelasId 
                                        ? this.siswas.filter(s => s.kelas_id == this.selectedKelasId) 
                                        : this.siswas;
                                    filtered.forEach(s => {
                                        html += `<option value=\x22${s.id}\x22>${s.nama}</option>`;
                                    });
                                    return html;
                                }
                            }">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kelas</label>
                                    <select x-model="selectedKelasId" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <option value="">Semua Kelas</option>
                                        @foreach($siswas->pluck('kelas')->filter()->unique('id') as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Siswa</label>
                                    <select name="siswa_id" required x-html="getOptionsHtml()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Asesmen</label>
                                    <select name="tipe_nilai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <option value="tugas">Tugas Harian</option>
                                        <option value="kuis">Kuis Formatif</option>
                                        <option value="uts">UTS (Tengah Semester)</option>
                                        <option value="uas">UAS (Akhir Semester)</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</label>
                                    <select name="mata_pelajaran_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        @foreach($mapels as $mpl)
                                            <option value="{{ $mpl->id }}">{{ $mpl->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nilai Angka (0-100)</label>
                                <input type="number" step="0.01" min="0" max="100" name="nilai" required placeholder="85.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan Capaian Belajar</label>
                                <input type="text" name="catatan" placeholder="Sangat baik dalam aljabar." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Nilai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
