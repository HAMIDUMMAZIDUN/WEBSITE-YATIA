<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 dark:text-white leading-tight">
            {{ __('Penerimaan Peserta Didik Baru (PPDB)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/30 text-emerald-800 dark:text-emerald-400 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Form Input Pendaftaran Baru -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6 h-fit">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Pendaftaran Baru</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Input data calon siswa secara manual.</p>
                    </div>

                    <form action="{{ route('admin.ppdb.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">NISN</label>
                            <input type="text" name="nisn" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Nomor WA / HP Orang Tua</label>
                            <input type="text" name="nomor_wa" required placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Tahun Ajaran PPDB</label>
                            <select name="academic_year_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}">{{ $ay->tahun_ajaran }} - {{ $ay->semester }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg transition-colors">
                            Simpan Pendaftaran
                        </button>
                    </form>
                </div>

                <!-- Tabel Data Calon Siswa -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">Daftar Pendaftar PPDB</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Status kelulusan dan pengelolaan data pendaftar.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="pb-3">No. Daftar / Nama</th>
                                    <th class="pb-3">NISN / Gender</th>
                                    <th class="pb-3">Asal Sekolah</th>
                                    <th class="pb-3">Kontak WA</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @forelse($registrations as $reg)
                                    <tr class="text-slate-800 dark:text-slate-300">
                                        <td class="py-4">
                                            <span class="font-bold text-xs text-emerald-600 block">{{ $reg->nomor_pendaftaran }}</span>
                                            <span class="font-semibold">{{ $reg->nama_lengkap }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-xs text-slate-500 block">{{ $reg->nisn ?? '-' }}</span>
                                            <span class="text-xs font-bold">{{ $reg->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                        </td>
                                        <td class="py-4 font-medium">{{ $reg->asal_sekolah }}</td>
                                        <td class="py-4 font-semibold text-slate-600 dark:text-slate-400">
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $reg->nomor_wa) }}" target="_blank" class="hover:underline text-emerald-600">{{ $reg->nomor_wa }}</a>
                                        </td>
                                        <td class="py-4">
                                            @if($reg->status === 'pending')
                                                <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">Pending</span>
                                            @elseif($reg->status === 'diterima')
                                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold">Diterima</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 rounded-full text-xs font-bold">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="py-4 text-right space-x-1 whitespace-nowrap">
                                            <form action="{{ route('admin.ppdb.update_status', $reg->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 font-bold text-slate-700 dark:text-slate-300 focus:outline-none">
                                                    <option value="pending" {{ $reg->status === 'pending' ? 'selected' : '' }}>Set Pending</option>
                                                    <option value="diterima" {{ $reg->status === 'diterima' ? 'selected' : '' }}>Terima</option>
                                                    <option value="ditolak" {{ $reg->status === 'ditolak' ? 'selected' : '' }}>Tolak</option>
                                                </select>
                                            </form>

                                            @if($reg->status === 'diterima')
                                                <button type="button" onclick="openMigrateModal({{ json_encode($reg) }})" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded-lg transition-colors font-bold inline-block">Migrasikan</button>
                                            @endif
                                            
                                            <form action="{{ route('admin.ppdb.delete', $reg->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs p-1 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-colors font-bold">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada calon siswa yang mendaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Migrate Modal -->
    <div id="migrate-modal" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 w-full max-w-md space-y-4 shadow-2xl">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Migrasikan ke Master Siswa</h3>
                <p class="text-xs text-slate-500">Mendaftarkan calon siswa menjadi siswa aktif di sekolah.</p>
            </div>
            
            <form id="migrate-form" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Calon Siswa</label>
                    <input type="text" id="migrate-name" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-505 focus:outline-none dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" id="migrate-nis" required placeholder="Contoh: 12345" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 dark:bg-slate-850 dark:border-slate-750 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Tujuan</label>
                    <select name="kelas_id" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 dark:bg-slate-850 dark:border-slate-750 dark:text-white">
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('migrate-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors dark:bg-slate-800 dark:text-slate-350 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors">
                        Migrasikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openMigrateModal(reg) {
            document.getElementById('migrate-form').action = `/admin/ppdb/${reg.id}/migrate`;
            document.getElementById('migrate-name').value = reg.nama_lengkap;
            // Generate a time-based unique NIS
            document.getElementById('migrate-nis').value = 'NIS' + Math.floor(Date.now() / 1000).toString().substring(5);
            document.getElementById('migrate-modal').style.display = 'flex';
        }
    </script>
</x-app-layout>
