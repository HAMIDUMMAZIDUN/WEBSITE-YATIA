<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-450 uppercase tracking-widest">Kelas Binaan</span>
                <h2 class="font-display font-extrabold text-2xl text-slate-900 dark:text-white leading-tight">
                    Siswa Aktif Kelas Binaan
                </h2>
            </div>
            @if($kelas)
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 font-bold text-xs uppercase">
                        Kelas {{ $kelas->nama_kelas }}
                    </span>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ addModalOpen: false, showPassword: false }">
        <!-- Notification Alerts -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900 rounded-2xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900 rounded-2xl text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900 rounded-2xl text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <div class="font-semibold">{{ $err }}</div>
                @endforeach
            </div>
        @endif

        @if(!$kelas)
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Kelas Binaan Belum Ditugaskan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto">
                    Anda belum terdaftar sebagai wali kelas untuk tahun ajaran aktif ini. Silakan hubungi administrator madrasah.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Student List (lg:col-span-8) -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-slate-50/50 dark:bg-slate-850/50">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Siswa Kelas {{ $kelas->nama_kelas }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-350 font-bold text-[10px] uppercase tracking-wider">
                                {{ $siswas->count() }} Total
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                                <thead class="bg-slate-50/50 dark:bg-slate-850/50 text-slate-400 dark:text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                                    <tr>
                                        <th class="px-6 py-3.5">Nama Siswa</th>
                                        <th class="px-6 py-3.5">NIS</th>
                                        <th class="px-6 py-3.5 text-center">Poin BK</th>
                                        <th class="px-6 py-3.5 text-right">Rata-Rata Nilai</th>
                                        <th class="px-6 py-3.5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($siswas as $siswa)
                                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-slate-900 dark:text-white">{{ $siswa->nama }}</div>
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $siswa->nis }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                                    {{ $siswa->poin_bk > 50 ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-450' : 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}
                                                ">
                                                    {{ $siswa->poin_bk }} Poin
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right font-semibold text-slate-900 dark:text-white font-mono">
                                                @php
                                                    $siswaNilai = $nilais->where('siswa_id', $siswa->id);
                                                    $avg = $siswaNilai->count() > 0 ? round($siswaNilai->avg('nilai'), 1) : null;
                                                @endphp
                                                {{ $avg ? $avg : 'Belum dinilai' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <form action="{{ route('wali.siswa.remove', $siswa->id) }}" method="POST" onsubmit="return confirm('Keluarkan siswa {{ addslashes($siswa->nama) }} dari kelas Anda?')">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 text-[10px] font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/20 rounded-lg hover:underline transition-all">
                                                        Keluarkan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-medium">
                                                Tidak ada siswa yang terdaftar di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Management Panel (lg:col-span-4) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Assign Existing Student -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Masukkan Siswa Terdaftar
                        </h3>
                        <form action="{{ route('wali.siswa.assign') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cari Siswa</label>
                                <select name="siswa_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                    <option value="">Pilih Siswa...</option>
                                    @foreach($availableStudents as $avail)
                                        <option value="{{ $avail->id }}">
                                            {{ $avail->nama }} (NIS: {{ $avail->nis }}) {{ $avail->kelas ? '['.$avail->kelas->nama_kelas.']' : '[Tanpa Kelas]' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-500 dark:hover:bg-emerald-450 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">
                                Pindahkan ke Kelas Saya
                            </button>
                        </form>
                    </div>

                    <!-- Create New Student Option -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                            Tambah Siswa Baru
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mb-4">
                            Buat akun siswa baru yang belum terdaftar di SIMSYATIA dan langsung tempatkan di kelas Anda.
                        </p>
                        <button @click="addModalOpen = true" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-50 text-white dark:text-slate-900 font-bold rounded-xl text-xs transition-colors">
                            Buat Siswa Baru
                        </button>
                    </div>

                    <!-- Import / Export Section -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Import / Export Masal
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            Unduh template, lengkapi data siswa kelas Anda, lalu unggah untuk impor masal.
                        </p>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('data.template', 'siswa') }}" class="flex-1 text-center py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">
                                Template
                            </a>
                            <a href="{{ route('data.export', 'siswa') }}" class="flex-1 text-center py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-450 text-xs font-semibold rounded-xl border border-emerald-150 dark:border-emerald-900 transition-colors">
                                Ekspor CSV
                            </a>
                        </div>

                        <form action="{{ route('data.import', 'siswa') }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-2 border-t border-slate-50 dark:border-slate-800/80">
                            @csrf
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Pilih File CSV</label>
                                <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-350 hover:file:bg-slate-200 dark:hover:file:bg-slate-750 transition-colors">
                            </div>
                            <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-colors">
                                Unggah & Impor
                            </button>
                        </form>
                    </div>

                </div>

            </div>
        @endif

        <!-- Add Student Modal -->
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-800" @click.away="addModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Siswa Baru (Kelas {{ $kelas ? $kelas->nama_kelas : '' }})</h3>
                    <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('wali.siswa.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama lengkap siswa..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email (Akun Login)</label>
                            <input type="email" name="email" required placeholder="siswa@yatia.sch.id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</label>
                            <input type="text" name="nis" required placeholder="Masukkan NIS..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</label>
                            <input type="text" name="nisn" placeholder="Masukkan NISN (Opsional)..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gender</label>
                            <select name="jenis_kelamin" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">No. HP</label>
                            <input type="text" name="no_hp" placeholder="08xxxx..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" placeholder="Alamat rumah..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kata Sandi Default</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Minimal 8 karakter..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 pr-10 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showPassword" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="addModalOpen = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-850 hover:bg-slate-200 text-slate-650 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-50 text-white dark:text-slate-900 font-bold rounded-xl text-xs shadow-lg transition-colors">
                            Simpan Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
