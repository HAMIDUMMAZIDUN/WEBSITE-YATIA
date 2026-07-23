<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Jadwal Mengajar Anda') }}
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
                <!-- Schedule Table -->
                <div class="lg:col-span-8 space-y-8">
                    <div id="jadwal-mengajar" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Jadwal Mengajar</h3>
                            <span class="px-3 py-1 rounded bg-slate-100 text-slate-600 font-semibold text-xs uppercase tracking-wider">Aktif</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Hari</th>
                                        <th class="px-6 py-3">Mata Pelajaran</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Jam Belajar</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($jadwals as $jadwal)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $jadwal->hari }}</td>
                                            <td class="px-6 py-4 font-medium text-slate-700">{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                            <td class="px-6 py-4 font-mono text-xs uppercase tracking-wider text-emerald-600 font-bold">{{ $jadwal->kelas->nama_kelas }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button
                                                        onclick="openEditJadwal({{ $jadwal->id }}, '{{ $jadwal->kelas_id }}', '{{ $jadwal->mata_pelajaran_id }}', '{{ $jadwal->hari }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                                        class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('guru.jadwal.delete', $jadwal->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada jadwal mengajar yang diinput.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Jadwal -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Tambah Jadwal Mengajar
                        </h3>
                        <p class="text-xs text-slate-400 mb-4 -mt-2">Jadwal yang ditambah otomatis tampil di halaman siswa.</p>
                        <form action="{{ route('guru.jadwal.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                                    <select name="kelas_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @foreach($kelas as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hari</label>
                                    <select name="hari" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                            <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @foreach($mapels as $mpl)
                                        <option value="{{ $mpl->id }}">{{ $mpl->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Tambah Jadwal
                            </button>
                        </form>
                        <!-- Import/Export section -->
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                            <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Atau Import / Export Masal</span>
                            <div class="flex gap-2">
                                <a href="{{ route('data.template', 'jadwal') }}" class="flex-1 text-center py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-150 dark:border-slate-700 transition-colors">
                                    Template
                                </a>
                                <a href="{{ route('data.export', 'jadwal') }}" class="flex-1 text-center py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-450 text-xs font-semibold rounded-xl border border-emerald-150 dark:border-emerald-900 transition-colors">
                                    Ekspor CSV
                                </a>
                            </div>
                             <form action="{{ route('data.import', 'jadwal') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                 @csrf
                                 <input type="file" name="file" required class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-350 hover:file:bg-slate-200">
                                 <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-[10px] font-bold rounded-xl transition-colors">
                                     Impor CSV
                                 </button>
                             </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Jadwal Modal -->
    <div id="modal-edit-jadwal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h4 class="font-bold text-slate-900">Edit Jadwal Mengajar</h4>
                <button onclick="closeModal('modal-edit-jadwal')" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form id="form-edit-jadwal" method="POST" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                        <select name="kelas_id" id="ej-kelas" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                            @foreach($kelas as $kls)<option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>@endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hari</label>
                        <select name="hari" id="ej-hari" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="ej-mapel" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                        @foreach($mapels as $mpl)<option value="{{ $mpl->id }}">{{ $mpl->nama_mapel }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="ej-mulai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="ej-selesai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-edit-jadwal')" class="flex-1 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-sm font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        function openModal(id)  { document.getElementById(id).style.display = 'flex'; }

        function openEditJadwal(id, kelasId, mapelId, hari, mulai, selesai) {
            document.getElementById('form-edit-jadwal').action = '/guru/jadwal/' + id;
            document.getElementById('ej-kelas').value  = kelasId;
            document.getElementById('ej-mapel').value  = mapelId;
            document.getElementById('ej-hari').value   = hari;
            document.getElementById('ej-mulai').value  = mulai;
            document.getElementById('ej-selesai').value = selesai;
            openModal('modal-edit-jadwal');
        }
    </script>
</x-app-layout>
