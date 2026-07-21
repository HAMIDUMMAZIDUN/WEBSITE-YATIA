<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard Bimbingan Konseling (BK)') }}
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
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Counseling Header Summary -->
            <div class="bg-gradient-to-r from-rose-600 to-red-600 rounded-3xl p-8 text-white shadow-lg shadow-rose-600/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded bg-white/20 text-white font-semibold text-xs uppercase tracking-wider">Statistik Pengawasan</span>
                    <h3 class="text-3xl font-extrabold">Buku Kasus & Karakter Siswa</h3>
                    <p class="text-rose-100 text-sm">
                        Kelola data kedisiplinan, sistem poin pelanggaran, serta bimbingan konseling secara intensif dan rahasia.
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/10 shrink-0 text-center">
                    <span class="block text-rose-200 text-xs font-semibold uppercase tracking-wider">Kasus Aktif</span>
                    <span class="text-2xl font-bold">{{ $pelanggarans->where('status', 'diproses')->count() }} Kasus</span>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Case Logs List -->
                <div class="lg:col-span-8 space-y-8">
                    <div id="kasus-section" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Buku Catatan Kasus Pelanggaran</h3>
                            <span class="px-3 py-1 rounded bg-slate-100 text-slate-600 font-semibold text-xs uppercase tracking-wider">Jurnal Harian</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Pelanggaran & Poin</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($pelanggarans as $plg)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                                {{ Carbon\Carbon::parse($plg->tanggal)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="block font-semibold text-slate-900">{{ $plg->siswa->nama }}</span>
                                                <span class="block text-xs text-slate-400">{{ $plg->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="block font-medium text-slate-700">{{ $plg->pelanggaran }}</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 mt-1">
                                                    +{{ $plg->poin }} Poin
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider 
                                                    {{ $plg->status === 'diselesaikan' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                    {{ $plg->status === 'diproses' ? 'bg-amber-100 text-amber-700' : '' }}
                                                    {{ $plg->status === 'panggilan_orang_tua' ? 'bg-rose-100 text-rose-700 animate-pulse' : '' }}
                                                ">
                                                    {{ str_replace('_', ' ', $plg->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center space-y-2">
                                                {{-- Status Quick-Update --}}
                                                <form action="{{ route('bk.pelanggaran.status', $plg->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                                    @csrf
                                                    <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-1 text-xs focus:outline-none">
                                                        <option value="diproses" {{ $plg->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                                        <option value="diselesaikan" {{ $plg->status === 'diselesaikan' ? 'selected' : '' }}>Selesai</option>
                                                        <option value="panggilan_orang_tua" {{ $plg->status === 'panggilan_orang_tua' ? 'selected' : '' }}>Panggilan OT</option>
                                                    </select>
                                                </form>

                                                {{-- Edit + Delete --}}
                                                <div class="flex items-center justify-center gap-2">
                                                    <button onclick="openEditPelanggaran({{ $plg->id }}, {{ json_encode($plg->pelanggaran) }}, {{ $plg->poin }}, {{ json_encode($plg->tindakan ?? '') }}, '{{ $plg->status }}')"
                                                        class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors">Edit</button>
                                                    <form action="{{ route('bk.pelanggaran.delete', $plg->id) }}" method="POST" onsubmit="return confirm('Hapus catatan ini? Poin siswa akan disesuaikan.')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">Hapus</button>
                                                    </form>
                                                </div>

                                                @if($plg->status === 'panggilan_orang_tua')
                                                    <button onclick="previewSummonLetter('{{ $plg->siswa->nama }}', '{{ $plg->siswa->nis }}', '{{ $plg->siswa->kelas->nama_kelas ?? 'XII' }}', '{{ $plg->pelanggaran }}', '{{ $plg->poin }}', '{{ Carbon\Carbon::parse($plg->tanggal)->format('d M Y') }}')" class="block w-full text-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition-colors mt-1">
                                                        Cetak Panggilan
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada catatan pelanggaran kedisiplinan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- BK Counseling Input Form -->
                <div class="lg:col-span-4 space-y-8">
                    <div id="tambah-kasus-section" class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm scroll-mt-6">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Catat Pelanggaran Baru
                        </h3>
                        <form action="{{ route('bk.pelanggaran.store') }}" method="POST" class="space-y-4">
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
                                    <select x-model="selectedKelasId" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                        <option value="">Semua Kelas</option>
                                        @foreach($siswas->pluck('kelas')->filter()->unique('id') as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Siswa</label>
                                    <select name="siswa_id" required x-html="getOptionsHtml()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Pelanggaran</label>
                                <input type="text" name="pelanggaran" required placeholder="Membolos sekolah / Atribut tidak lengkap" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bobot Poin</label>
                                    <input type="number" name="poin" required min="1" placeholder="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Awal</label>
                                    <select name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                        <option value="diproses">Diproses</option>
                                        <option value="diselesaikan">Diselesaikan</option>
                                        <option value="panggilan_orang_tua">Panggilan Orang Tua</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tindakan Langsung</label>
                                <input type="text" name="tindakan" placeholder="Diberikan peringatan verbal / Pembinaan lapangan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Catat Kasus BK
                            </button>
                        </form>
                        <!-- Import/Export section -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Atau Import / Export Masal</span>
                            <div class="flex gap-2">
                                <a href="{{ route('data.template', 'pelanggaran') }}" class="flex-1 text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl border border-slate-150 transition-colors">
                                    Template
                                </a>
                                <a href="{{ route('data.export', 'pelanggaran') }}" class="flex-1 text-center py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-150 transition-colors">
                                    Ekspor CSV
                                </a>
                            </div>
                            <form action="{{ route('data.import', 'pelanggaran') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="file" name="file" required class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white text-[10px] font-bold rounded-lg transition-colors flex-shrink-0">
                                    Unggah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Summon Letter Modal -->
    <div id="summon-modal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-8 py-6 bg-rose-600 text-white flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-bold">Surat Panggilan Orang Tua / Wali Siswa</h4>
                    <p class="text-xs text-rose-200">Pratinjau Cetak Formal Departemen Bimbingan Konseling</p>
                </div>
                <button onclick="document.getElementById('summon-modal').classList.add('hidden')" class="text-rose-200 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-8 space-y-6 text-sm text-slate-700 leading-relaxed font-serif" id="summon-letter-content">
                <!-- Formal Letter Header -->
                <div class="text-center border-b-2 border-slate-800 pb-4 mb-4 font-sans">
                    <h5 class="text-xl font-bold uppercase tracking-tight text-slate-900">DEPARTEMEN BIMBINGAN DAN KONSELING (BK)</h5>
                    <p class="text-xs uppercase font-semibold text-slate-500 tracking-wider">SEKOLAH / LPK YATIA INDONESIA</p>
                    <p class="text-[10px] text-slate-400 mt-1">Jl. Pembangunan No. 12, Senayan, Jakarta Pusat | Telp: (021) 555-0123</p>
                </div>

                <div class="flex justify-between items-start font-sans text-xs mb-6">
                    <div>
                        <span class="block">Nomor: B/BK-YATIA/{{ date('Y') }}/089</span>
                        <span class="block">Hal: Panggilan Orang Tua / Wali Siswa</span>
                    </div>
                    <span>Jakarta, {{ date('d F Y') }}</span>
                </div>

                <p>Kepada Yth.<br><strong>Orang Tua / Wali dari Ananda <span id="lbl-nama" class="underline font-bold"></span></strong><br>Di Tempat</p>

                <p>Dengan hormat,</p>
                
                <p>Melalui surat ini, kami mengharapkan kehadiran Bapak/Ibu Orang Tua / Wali siswa di kantor Bimbingan Konseling YATIA pada:</p>

                <div class="pl-8 space-y-1 font-sans text-xs">
                    <p><strong>Hari / Tanggal:</strong> Senin, {{ date('d F Y', strtotime('+2 days')) }}</p>
                    <p><strong>Waktu:</strong> 09.00 WIB s.d Selesai</p>
                    <p><strong>Tempat:</strong> Ruang Konseling BK - Gedung Utama Lantai 1</p>
                    <p><strong>Agenda:</strong> Pembahasan Pelanggaran Kedisiplinan Siswa</p>
                </div>

                <p>Kehadiran Bapak/Ibu sangat kami harapkan guna membahas tindak lanjut atas pelanggaran yang dilakukan oleh siswa yang bersangkutan, yaitu:</p>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 font-sans text-xs space-y-1">
                    <p><strong>Nama Siswa:</strong> <span id="lbl-nama-detail"></span> (NIS: <span id="lbl-nis"></span>)</p>
                    <p><strong>Kelas:</strong> <span id="lbl-kelas"></span></p>
                    <p><strong>Bentuk Pelanggaran:</strong> <span id="lbl-kasus" class="text-rose-600 font-bold"></span></p>
                    <p><strong>Tanggal Kejadian:</strong> <span id="lbl-tanggal"></span></p>
                    <p><strong>Poin Pelanggaran:</strong> <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold font-mono">+<span id="lbl-poin"></span> Poin</span></p>
                </div>

                <p>Demikian surat panggilan ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>

                <!-- Signature -->
                <div class="flex justify-end pt-8 font-sans text-xs">
                    <div class="text-center space-y-12">
                        <span>Mengetahui,<br>Kepala Bimbingan Konseling YATIA</span>
                        <div class="border-t border-slate-400 pt-1 font-bold text-slate-900">
                            {{ $guru->nama ?? 'Guru BK Utama' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button onclick="document.getElementById('summon-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    Batal
                </button>
                <button onclick="window.print()" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                    Cetak Surat Panggilan
                </button>
            </div>
        </div>
    </div>

    <!-- Letter preview logic -->
    <script>
        function previewSummonLetter(nama, nis, kelas, kasus, poin, tanggal) {
            document.getElementById('lbl-nama').innerText = nama;
            document.getElementById('lbl-nama-detail').innerText = nama;
            document.getElementById('lbl-nis').innerText = nis;
            document.getElementById('lbl-kelas').innerText = kelas;
            document.getElementById('lbl-kasus').innerText = kasus;
            document.getElementById('lbl-poin').innerText = poin;
            document.getElementById('lbl-tanggal').innerText = tanggal;
            document.getElementById('summon-modal').classList.remove('hidden');
        }

        function openEditPelanggaran(id, pelanggaran, poin, tindakan, status) {
            document.getElementById('form-edit-pelanggaran').action = '/bk/pelanggaran/' + id;
            document.getElementById('ebk-pelanggaran').value = pelanggaran;
            document.getElementById('ebk-poin').value = poin;
            document.getElementById('ebk-tindakan').value = tindakan;
            document.getElementById('ebk-status').value = status;
            document.getElementById('modal-edit-pelanggaran').style.display = 'flex';
        }
    </script>

{{-- Edit Pelanggaran Modal --}}
<div id="modal-edit-pelanggaran" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-900">Edit Catatan Pelanggaran</h4>
            <button onclick="document.getElementById('modal-edit-pelanggaran').style.display='none'" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form id="form-edit-pelanggaran" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Pelanggaran</label>
                <textarea name="pelanggaran" id="ebk-pelanggaran" rows="2" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Poin Sanksi</label>
                    <input type="number" name="poin" id="ebk-poin" min="1" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</label>
                    <select name="status" id="ebk-status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                        <option value="diproses">Diproses</option>
                        <option value="diselesaikan">Diselesaikan</option>
                        <option value="panggilan_orang_tua">Panggilan Orang Tua</option>
                    </select>
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tindakan / Catatan</label>
                <textarea name="tindakan" id="ebk-tindakan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500" placeholder="Siswa dipanggil untuk mediasi..."></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-pelanggaran').style.display='none'" class="flex-1 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-sm font-bold">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-sm font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>
