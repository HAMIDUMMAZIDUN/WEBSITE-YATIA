<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Manajemen Data Siswa') }} - {{ $unit === 'MTs' ? 'Tsanawiyah (MTs)' : 'Aliyah (MA)' }}
            </h2>
            <button onclick="document.getElementById('siswa-add-modal').style.display='flex'" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Siswa Baru
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ editModalOpen: false, showPasswordEdit: false, editSiswa: { id: '', nama: '', email: '', nis: '', nisn: '', kelas_id: '', jenis_kelamin: 'L', no_hp: '', alamat: '', status: 'Aktif' }, selectAll: false, toggleAll() { const checkboxes = document.querySelectorAll('.item-checkbox'); checkboxes.forEach(cb => { cb.checked = this.selectAll; }); } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filter Panel -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <form action="{{ route('admin.siswa.index') }}" method="GET" class="grid md:grid-cols-4 gap-4 items-end">
                    <input type="hidden" name="unit" value="{{ $unit }}">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cari Siswa</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS / NISN..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                        <select name="kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="Aktif" {{ request('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Mutasi" {{ request('status') == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                            <option value="Keluar" {{ request('status') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                            <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="Drop Out" {{ request('status') == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('admin.siswa.index', ['unit' => $unit]) }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl text-xs font-bold transition-colors text-center flex items-center justify-center">
                            Reset
                        </a>
                        <button type="button" @click="window.bulkDeleteSelected('.item-checkbox', '{{ csrf_token() }}')" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow transition-colors flex items-center gap-1">
                            Hapus Terpilih
                        </button>
                    </div>
                </form>
            </div>

            <!-- Import/Export Panel -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Import / Export Data Siswa Masal</h3>
                    <p class="text-[11px] text-slate-400">Unduh template, lengkapi data siswa, lalu unggah untuk impor masal.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6 items-center">
                    <div class="flex gap-2">
                        <a href="{{ route('data.template', 'siswa') }}" class="flex-1 text-center py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-655 text-xs font-bold rounded-xl border border-slate-200 transition-colors">
                            Unduh Template Impor
                        </a>
                        <a href="{{ route('data.export', 'siswa') }}" class="flex-1 text-center py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200 transition-colors">
                            Ekspor Seluruh Siswa (.csv)
                        </a>
                    </div>
                    <form action="{{ route('data.import', 'siswa') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-colors">
                        </div>
                        <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-colors">
                            Unggah & Impor
                        </button>
                    </form>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-emerald-650 focus:ring-emerald-500">
                                </th>
                                <th class="px-6 py-4">Nama / NIS</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Gender</th>
                                <th class="px-6 py-4">Kontak</th>
                                <th class="px-6 py-4 text-center">Poin BK</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswas as $siswa)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" class="item-checkbox rounded border-slate-300 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('admin.siswa.delete', $siswa->id) }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <div class="font-bold text-slate-900">{{ $siswa->nama }}</div>
                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold
                                                {{ $siswa->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                                {{ $siswa->status === 'Mutasi' ? 'bg-amber-50 text-amber-700 border border-amber-250' : '' }}
                                                {{ $siswa->status === 'Keluar' ? 'bg-rose-50 text-rose-700 border border-rose-250' : '' }}
                                                {{ $siswa->status === 'Lulus' ? 'bg-blue-50 text-blue-700 border border-blue-250' : '' }}
                                                {{ $siswa->status === 'Drop Out' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}">
                                                {{ $siswa->status }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">NIS: {{ $siswa->nis }} | NISN: {{ $siswa->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-800 font-medium">
                                        {{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $siswa->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $siswa->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-650">
                                        {{ $siswa->no_hp ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $siswa->poin_bk > 50 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $siswa->poin_bk }} Poin
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.siswa.history', $siswa->id) }}" class="text-emerald-600 hover:text-emerald-800 font-semibold text-xs transition-colors flex items-center gap-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Riwayat
                                            </a>
                                            <button @click="
                                                editSiswa = {
                                                    id: '{{ $siswa->id }}',
                                                    nama: '{{ addslashes($siswa->nama) }}',
                                                    email: '{{ addslashes($siswa->user->email ?? '') }}',
                                                    nis: '{{ $siswa->nis }}',
                                                    nisn: '{{ $siswa->nisn }}',
                                                    kelas_id: '{{ $siswa->kelas_id }}',
                                                    jenis_kelamin: '{{ $siswa->jenis_kelamin }}',
                                                    no_hp: '{{ $siswa->no_hp }}',
                                                    alamat: '{{ addslashes($siswa->alamat) }}',
                                                    status: '{{ $siswa->status }}'
                                                };
                                                editModalOpen = true;
                                            " class="text-blue-600 hover:text-blue-800 font-semibold text-xs transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.siswa.delete', $siswa->id) }}" method="POST" onsubmit="return confirm('Menghapus siswa juga akan menghapus akun loginnya. Anda yakin?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs transition-colors">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 text-sm">Tidak ada data siswa ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswas->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $siswas->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900">Ubah Data Siswa</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="'/admin/siswa/' + editSiswa.id" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama" x-model="editSiswa.nama" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email (Akun Login)</label>
                            <input type="email" name="email" x-model="editSiswa.email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIS</label>
                            <input type="text" name="nis" x-model="editSiswa.nis" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NISN</label>
                            <input type="text" name="nisn" x-model="editSiswa.nisn" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                            <select name="kelas_id" x-model="editSiswa.kelas_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gender</label>
                            <select name="jenis_kelamin" x-model="editSiswa.jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">No. HP</label>
                            <input type="text" name="no_hp" x-model="editSiswa.no_hp" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</label>
                            <select name="status" x-model="editSiswa.status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="Aktif">Aktif</option>
                                <option value="Mutasi">Mutasi</option>
                                <option value="Keluar">Keluar</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Drop Out">Drop Out</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                            <textarea name="alamat" x-model="editSiswa.alamat" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ubah Password (Kosongkan jika tetap)</label>
                            <div class="relative">
                                <input x-bind:type="showPasswordEdit ? 'text' : 'password'" name="password" placeholder="Minimal 8 karakter..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 pr-10 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                <button type="button" @click="showPasswordEdit = !showPasswordEdit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                    <svg x-show="!showPasswordEdit" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showPasswordEdit" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-650 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div id="siswa-add-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-900">Tambah Siswa Baru</h3>
                <button onclick="document.getElementById('siswa-add-modal').style.display='none'" class="text-slate-400 hover:text-slate-650 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap siswa..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email (Akun Login)</label>
                        <input type="email" name="email" required placeholder="siswa@yatia.sch.id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIS</label>
                        <input type="text" name="nis" required placeholder="Nomor Induk Siswa..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NISN</label>
                        <input type="text" name="nisn" placeholder="Nomor Induk Siswa Nasional..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                        <select name="kelas_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gender</label>
                        <select name="jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">No. HP</label>
                        <input type="text" name="no_hp" placeholder="Nomor telepon aktif..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" placeholder="Alamat rumah..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                    </div>
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Password Default</label>
                        <div class="relative" x-data="{ showPasswordAdd: false }">
                            <input x-bind:type="showPasswordAdd ? 'text' : 'password'" name="password" required placeholder="Minimal 8 karakter..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 pr-10 py-2 text-sm focus:outline-none focus:border-emerald-500">
                            <button type="button" @click="showPasswordAdd = !showPasswordAdd" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                <svg x-show="!showPasswordAdd" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg x-show="showPasswordAdd" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="document.getElementById('siswa-add-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-650 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
