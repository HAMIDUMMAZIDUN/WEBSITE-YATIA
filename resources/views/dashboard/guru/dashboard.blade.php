<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard Guru Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        jadwalSearch: '',
        materiSearch: '',
        materiFilter: '',
        selectAllJadwal: false,
        selectAllMateri: false,
        toggleAllJadwal() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.selectAllJadwal;
            });
        },
        toggleAllMateri() {
            const checkboxes = document.querySelectorAll('.materi-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.selectAllMateri;
            });
        }
    }">
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

            <!-- Teacher Info Card -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 text-white shadow-lg shadow-emerald-600/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded bg-white/20 text-white font-semibold text-xs uppercase tracking-wider">Profil Guru</span>
                    <h3 class="text-3xl font-extrabold">{{ $guru->nama }}</h3>
                    <p class="text-emerald-100 flex items-center gap-2">
                        <span class="font-mono text-sm bg-black/15 px-2 py-0.5 rounded">NIP: {{ $guru->nip ?? '-' }}</span>
                        <span class="text-emerald-200">|</span>
                        <span>Hp: {{ $guru->no_hp ?? 'Belum diisi' }}</span>
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/10 shrink-0">
                    <span class="block text-emerald-200 text-xs font-semibold uppercase tracking-wider">Lokasi Tugas</span>
                    <span class="text-lg font-bold">{{ $guru->alamat ?? 'Kantor Guru Utama' }}</span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Main Tools -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Schedule Table -->
                    <div id="jadwal-mengajar" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="font-bold text-lg text-slate-900">Jadwal Mengajar</h3>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <input type="text" x-model="jadwalSearch" placeholder="Cari hari/kelas/mapel..." class="pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 w-44">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <button type="button" @click="window.bulkDeleteSelected('.jadwal-checkbox', '{{ csrf_token() }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3 w-10">
                                            <input type="checkbox" x-model="selectAllJadwal" @change="toggleAllJadwal()" class="rounded border-slate-300 text-blue-650 focus:ring-blue-500">
                                        </th>
                                        <th class="px-6 py-3">Hari</th>
                                        <th class="px-6 py-3">Mata Pelajaran</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Jam Belajar</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($jadwals as $jadwal)
                                        <tr class="hover:bg-slate-50/40 transition-colors"
                                            x-show="jadwalSearch === '' || '{{ strtolower(addslashes($jadwal->hari)) }}'.includes(jadwalSearch.toLowerCase()) || '{{ strtolower(addslashes($jadwal->mataPelajaran->nama_mapel)) }}'.includes(jadwalSearch.toLowerCase()) || '{{ strtolower(addslashes($jadwal->kelas->nama_kelas)) }}'.includes(jadwalSearch.toLowerCase())">
                                            <td class="px-6 py-4">
                                                <input type="checkbox" class="jadwal-checkbox rounded border-slate-300 text-blue-650 focus:ring-blue-500" data-delete-url="{{ route('guru.jadwal.delete', $jadwal->id) }}">
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $jadwal->hari }}</td>
                                            <td class="px-6 py-4 font-medium text-slate-700">{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                            <td class="px-6 py-4 font-mono text-xs uppercase tracking-wider text-emerald-600 font-bold">{{ $jadwal->kelas->nama_kelas }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    {{-- Edit Button --}}
                                                    <button
                                                        onclick="openEditJadwal({{ $jadwal->id }}, '{{ $jadwal->kelas_id }}', '{{ $jadwal->mata_pelajaran_id }}', '{{ $jadwal->hari }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                                        class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors">
                                                        Edit
                                                    </button>
                                                    {{-- Delete Button --}}
                                                    <form action="{{ route('guru.jadwal.delete', $jadwal->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada jadwal mengajar yang diinput.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
 
                    <!-- E-learning & Materials Card -->
                    <div id="elearning-section" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="font-bold text-lg text-slate-900">Materi & Tugas E-learning</h3>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="relative">
                                    <input type="text" x-model="materiSearch" placeholder="Cari judul..." class="pl-8 pr-3 py-1.5 bg-slate-5-0 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-500 w-36">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <select x-model="materiFilter" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-500">
                                    <option value="">Semua Tipe</option>
                                    <option value="materi">Materi</option>
                                    <option value="tugas">Tugas</option>
                                </select>
                                <button type="button" @click="window.bulkDeleteSelected('.materi-checkbox', '{{ csrf_token() }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3 w-10">
                                            <input type="checkbox" x-model="selectAllMateri" @change="toggleAllMateri()" class="rounded border-slate-300 text-emerald-650 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-6 py-3">Judul</th>
                                        <th class="px-6 py-3">Tipe</th>
                                        <th class="px-6 py-3">Kelas / Mapel</th>
                                        <th class="px-6 py-3">Batas Pengumpulan</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($materiTugas as $mt)
                                        <tr class="hover:bg-slate-50/40 transition-colors"
                                            x-show="(materiSearch === '' || '{{ strtolower(addslashes($mt->judul)) }}'.includes(materiSearch.toLowerCase())) && (materiFilter === '' || '{{ $mt->tipe }}' === materiFilter)">
                                            <td class="px-6 py-4">
                                                <input type="checkbox" class="materi-checkbox rounded border-slate-300 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('guru.materi_tugas.delete', $mt->id) }}">
                                            </td>
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
                                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada materi atau tugas yang diunggah.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
 
                    <!-- CBT & Question Bank Section -->
                    <div id="cbt-section" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4 scroll-mt-6">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-900">Computer Based Test (CBT) & Bank Soal</h3>
                            <button onclick="alert('Membuka modul pembuatan soal CBT baru...')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                                + Buat Soal Ujian
                            </button>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-semibold text-[10px] uppercase tracking-wider">Bank Soal Aktif</span>
                                    <h4 class="font-bold text-slate-900 mt-2 text-sm">Soal Pilihan Ganda Matematika Wajib X</h4>
                                    <p class="text-xs text-slate-400">Total: 40 Soal | Dibuat oleh Anda</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="px-3 py-1 bg-slate-800 text-white rounded text-[11px] font-semibold">Ubah Soal</button>
                                    <button class="px-3 py-1 border border-slate-200 text-slate-600 rounded text-[11px]">Lihat Pratinjau</button>
                                </div>
                            </div>
                            <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-semibold text-[10px] uppercase tracking-wider">Bank Soal Aktif</span>
                                    <h4 class="font-bold text-slate-900 mt-2 text-sm">Kuis Harian 1 Fisika Dasar X</h4>
                                    <p class="text-xs text-slate-400">Total: 15 Soal | Dibuat oleh Anda</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="px-3 py-1 bg-slate-800 text-white rounded text-[11px] font-semibold">Ubah Soal</button>
                                    <button class="px-3 py-1 border border-slate-200 text-slate-600 rounded text-[11px]">Lihat Pratinjau</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Forms (Sidebar) -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Form Tambah Jadwal Pelajaran -->
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
                    </div>

                    <!-- Form Upload E-learning -->
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

                    <!-- Form Input Nilai -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Input Nilai (Kurikulum Merdeka)
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

{{-- ===== EDIT MODALS ===== --}}

{{-- Edit Jadwal Modal --}}
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

{{-- Edit Materi Modal --}}
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

function openEditJadwal(id, kelasId, mapelId, hari, mulai, selesai) {
    document.getElementById('form-edit-jadwal').action = '/guru/jadwal/' + id;
    document.getElementById('ej-kelas').value  = kelasId;
    document.getElementById('ej-mapel').value  = mapelId;
    document.getElementById('ej-hari').value   = hari;
    document.getElementById('ej-mulai').value  = mulai;
    document.getElementById('ej-selesai').value = selesai;
    openModal('modal-edit-jadwal');
}

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
