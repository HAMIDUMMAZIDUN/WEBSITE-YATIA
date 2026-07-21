<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Pengaturan Tahun Ajaran & Semester') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        unlockModalOpen: false, 
        unlockYearId: '', 
        unlockYearName: '',
        copyModalOpen: false,
        copyYearId: '',
        copyYearName: ''
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
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Create Academic Year Form -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6 h-fit">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Tambah Periode Baru</h3>
                        <p class="text-xs text-slate-500">Mendaftarkan tahun ajaran dan semester baru.</p>
                    </div>

                    <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" required placeholder="Contoh: 2026/2027" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <span class="text-[10px] text-slate-400">Format standar: YYYY/YYYY (misal: 2026/2027)</span>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Semester</label>
                            <select name="semester" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unit Madrasah</label>
                            <select name="unit" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="Semua">Semua Unit</option>
                                <option value="MTs">MTs YATIA</option>
                                <option value="MA">MA YATIA</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Mulai</label>
                                <input type="date" name="tanggal_mulai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Selesai</label>
                                <input type="date" name="tanggal_selesai" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Hari Sekolah Efektif</label>
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                @foreach(['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'] as $eng => $ind)
                                    <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer">
                                        <input type="checkbox" name="hari_sekolah[]" value="{{ $eng }}" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                        <span>{{ $ind }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg transition-colors">
                            Tambah Periode
                        </button>
                    </form>
                </div>

                <!-- Academic Years List -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Daftar Periode Akademik</h3>
                        <p class="text-xs text-slate-500">Mengelola status aktif, pembatasan lock, dan penyalinan konfigurasi.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Unit / Tahun</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Rentang Tanggal</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($years as $year)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/55 transition-colors">
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-800">{{ $year->tahun_ajaran }} - {{ $year->semester }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase">Unit: {{ $year->unit }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-650">
                                            @if($year->tanggal_mulai && $year->tanggal_selesai)
                                                {{ \Carbon\Carbon::parse($year->tanggal_mulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($year->tanggal_selesai)->translatedFormat('d M Y') }}
                                            @else
                                                <span class="text-slate-400 font-bold">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                @if($year->active)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-250">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                                        Tidak Aktif
                                                    </span>
                                                @endif
                                                @if($year->locked)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-250">
                                                        🔐 Terkunci
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-end gap-2.5">
                                                <!-- Activation Trigger -->
                                                @if(!$year->active)
                                                    <form action="{{ route('admin.tahun_ajaran.activate', $year->id) }}" method="POST" onsubmit="return confirm('Mengaktifkan periode ini akan menonaktifkan periode aktif saat ini pada unit terkait. Apakah Anda yakin?')">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow transition-colors">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-emerald-600 font-black">Sedang Berjalan</span>
                                                @endif

                                                <!-- Lock / Unlock Action -->
                                                @if($year->locked)
                                                    <button type="button" @click="
                                                        unlockYearId = '{{ $year->id }}';
                                                        unlockYearName = '{{ $year->tahun_ajaran }} ({{ $year->semester }})';
                                                        unlockModalOpen = true;
                                                    " class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg text-xs font-bold transition-colors">
                                                        Buka Kunci
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.tahun_ajaran.lock', $year->id) }}" method="POST" onsubmit="return confirm('Mengunci semester ini akan mencegah segala perubahan data operasional. Lanjutkan?')">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                                            Kunci
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Copy Config Trigger -->
                                                @if(!$year->locked)
                                                    <button type="button" @click="
                                                        copyYearId = '{{ $year->id }}';
                                                        copyYearName = '{{ $year->tahun_ajaran }} ({{ $year->semester }})';
                                                        copyModalOpen = true;
                                                    " class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition-colors">
                                                        Salin Config
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Belum ada periode akademik terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unlock Semester Modal -->
        <div x-show="unlockModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="unlockModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900">Buka Kunci Semester</h3>
                    <button @click="unlockModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">&times;</button>
                </div>
                <form :action="'/admin/tahun-ajaran/' + unlockYearId + '/unlock'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-1">Periode</span>
                        <div class="font-bold text-slate-800 text-sm" x-text="unlockYearName"></div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alasan Pembukaan Kunci</label>
                        <textarea name="alasan_perubahan" required placeholder="Tuliskan alasan membuka kunci operational..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="unlockModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold rounded-xl text-xs transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">Buka Kunci</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Copy Config Modal -->
        <div x-show="copyModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="copyModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900">Salin Konfigurasi Akademik</h3>
                    <button @click="copyModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">&times;</button>
                </div>
                <form :action="'/admin/tahun-ajaran/' + copyYearId + '/copy'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-1">Target Periode</span>
                        <div class="font-bold text-slate-800 text-sm" x-text="copyYearName"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Salin Dari Tahun Sebelumnya</label>
                        <select name="from_year_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="">Pilih Tahun Sumber...</option>
                            @foreach($years as $y)
                                <option value="{{ $y->id }}">{{ $y->tahun_ajaran }} - {{ $y->semester }} ({{ $y->unit }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-center gap-2.5 text-xs text-slate-650 cursor-pointer">
                            <input type="checkbox" name="copy_homerooms" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="font-bold block">Salin Penugasan Wali Kelas & Kelas</span>
                                <span class="text-[10px] text-slate-400">Menyalin daftar kelas beserta wali kelasnya ke tahun ajaran baru.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 text-xs text-slate-650 cursor-pointer">
                            <input type="checkbox" name="copy_holidays" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="font-bold block">Salin Kalender Hari Libur</span>
                                <span class="text-[10px] text-slate-400">Menyalin hari libur yang telah terdaftar dari tahun ajaran asal.</span>
                            </div>
                        </label>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 text-[10px] rounded-xl leading-relaxed">
                        ⚠️ <strong>PENTING:</strong> Sistem <strong>TIDAK</strong> akan menyalin pemetaan kelas siswa saat ini. Siswa harus ditempatkan kembali secara manual atau menggunakan fitur Kenaikan Kelas Massal demi akurasi pelaporan EMIS/Kemenag.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="copyModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold rounded-xl text-xs transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">Salin Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
