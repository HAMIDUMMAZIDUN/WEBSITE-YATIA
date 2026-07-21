<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Materi & Tugas E-learning') }}
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

            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- E-learning & Materials Card -->
                <div class="lg:col-span-8 space-y-8">
                    <div id="elearning-section" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Modul Terunggah</h3>
                            <span class="px-3 py-1 rounded bg-emerald-50 text-emerald-600 font-semibold text-xs uppercase tracking-wider">Aktif</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Judul</th>
                                        <th class="px-6 py-3">Tipe</th>
                                        <th class="px-6 py-3">Kelas / Mapel</th>
                                        <th class="px-6 py-3">Batas Pengumpulan</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($materiTugas as $mt)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4">
                                                <span class="block font-semibold text-slate-900">{{ $mt->judul }}</span>
                                                <span class="block text-xs text-slate-400 truncate max-w-xs">{{ $mt->deskripsi ?? 'Tanpa deskripsi' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider {{ $mt->tipe === 'materi' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ ucfirst($mt->tipe) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="block text-slate-700 font-semibold text-xs">{{ $mt->kelas->nama_kelas }}</span>
                                                <span class="block text-slate-400 text-xs">{{ $mt->mataPelajaran->nama_mapel }}</span>
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                                {{ $mt->batas_waktu ? Carbon\Carbon::parse($mt->batas_waktu)->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                     <button onclick="openEditMateri({{ $mt->id }}, {{ json_encode($mt->judul) }}, '{{ $mt->tipe }}', '{{ $mt->kelas_id }}', '{{ $mt->mata_pelajaran_id }}', {{ json_encode($mt->deskripsi ?? '') }}, '{{ $mt->batas_waktu ? \Carbon\Carbon::parse($mt->batas_waktu)->format('Y-m-d\TH:i') : '' }}')"
                                                        class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors">Edit</button>
                                                    <form action="{{ route('guru.materi_tugas.delete', $mt->id) }}" method="POST" onsubmit="return confirm('Hapus materi/tugas ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada materi atau tugas yang diunggah.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Upload E-learning -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Unggah Materi / Tugas
                        </h3>
                        <form action="{{ route('guru.materi_tugas.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Materi / Tugas</label>
                                <input type="text" name="judul" required placeholder="Latihan Trigonometri Dasar" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi Instruksi</label>
                                <textarea name="deskripsi" placeholder="Kerjakan latihan soal..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-none" rows="3"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</label>
                                    <select name="tipe" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <option value="materi">Materi</option>
                                        <option value="tugas">Tugas</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                                    <select name="kelas_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        @foreach($kelas as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    @foreach($mapels as $mpl)
                                        <option value="{{ $mpl->id }}">{{ $mpl->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Waktu (Opsional)</label>
                                <input type="datetime-local" name="batas_waktu" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Publikasikan Modul
                            </button>
                        </form>
                        <!-- Import/Export section -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Atau Import / Export Masal</span>
                            <div class="flex gap-2">
                                <a href="{{ route('data.template', 'materi_tugas') }}" class="flex-1 text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl border border-slate-150 transition-colors">
                                    Template
                                </a>
                                <a href="{{ route('data.export', 'materi_tugas') }}" class="flex-1 text-center py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-150 transition-colors">
                                    Ekspor CSV
                                </a>
                            </div>
                            <form action="{{ route('data.import', 'materi_tugas') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
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

    <!-- Edit Materi Modal -->
    <div id="modal-edit-materi" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h4 class="font-bold text-slate-900">Edit Materi / Tugas</h4>
                <button onclick="closeModal('modal-edit-materi')" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form id="form-edit-materi" method="POST" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</label>
                    <input type="text" name="judul" id="em-judul" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</label>
                        <select name="tipe" id="em-tipe" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="materi">Materi</option>
                            <option value="tugas">Tugas</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                        <select name="kelas_id" id="em-kelas" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                            @foreach($kelas as $kls)<option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="em-mapel" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                        @foreach($mapels as $mpl)<option value="{{ $mpl->id }}">{{ $mpl->nama_mapel }}</option>@endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi</label>
                    <textarea name="deskripsi" id="em-deskripsi" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Waktu</label>
                    <input type="datetime-local" name="batas_waktu" id="em-batas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-edit-materi')" class="flex-1 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-sm font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        function openModal(id)  { document.getElementById(id).style.display = 'flex'; }

        function openEditMateri(id, judul, tipe, kelasId, mapelId, deskripsi, batas) {
            document.getElementById('form-edit-materi').action = '/guru/materi-tugas/' + id;
            document.getElementById('em-judul').value    = judul;
            document.getElementById('em-tipe').value     = tipe;
            document.getElementById('em-kelas').value    = kelasId;
            document.getElementById('em-mapel').value    = mapelId;
            document.getElementById('em-deskripsi').value = deskripsi;
            document.getElementById('em-batas').value    = batas;
            openModal('modal-edit-materi');
        }
    </script>
</x-app-layout>
