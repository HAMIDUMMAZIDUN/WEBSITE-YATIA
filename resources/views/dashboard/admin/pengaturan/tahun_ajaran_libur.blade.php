<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Kalender Libur Akademik') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        searchQuery: '', 
        selectAll: false, 
        toggleAll() { 
            const checkboxes = document.querySelectorAll('.item-checkbox'); 
            checkboxes.forEach(cb => { cb.checked = this.selectAll; }); 
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
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Add Holiday Form -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6 h-fit">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Tambah Hari Libur</h3>
                        <p class="text-xs text-slate-500">Mendaftarkan hari libur nasional atau lokal.</p>
                    </div>

                    <form action="{{ route('admin.holidays.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Akademik</label>
                            <select name="academic_year_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="">Pilih Tahun Akademik...</option>
                                @foreach($years as $y)
                                    <option value="{{ $y->id }}" {{ $activeYear && $activeYear->id == $y->id ? 'selected' : '' }}>
                                        {{ $y->tahun_ajaran }} - {{ $y->semester }} ({{ $y->unit }})
                                    </option>
                                @endforeach
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
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan Libur</label>
                            <input type="text" name="keterangan" required placeholder="Contoh: Libur Idul Fitri" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unit Terdampak</label>
                            <select name="unit" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="Semua">Semua Unit</option>
                                <option value="MTs">MTs YATIA saja</option>
                                <option value="MA">MA YATIA saja</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer">
                                <input type="checkbox" name="is_nasional" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span>Libur Nasional (Tutup Semua Layanan)</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg transition-colors">
                            Simpan Hari Libur
                        </button>
                    </form>
                    <!-- Import/Export section -->
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Atau Import / Export Masal</span>
                        <div class="flex gap-2">
                            <a href="{{ route('data.template', 'holidays') }}" class="flex-1 text-center py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-150 dark:border-slate-700 transition-colors">
                                Template
                            </a>
                            <a href="{{ route('data.export', 'holidays') }}" class="flex-1 text-center py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-450 text-xs font-semibold rounded-xl border border-emerald-150 dark:border-emerald-900 transition-colors">
                                Ekspor CSV
                            </a>
                        </div>
                        <form action="{{ route('data.import', 'holidays') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                            @csrf
                            <input type="file" name="file" required class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-350 hover:file:bg-slate-200">
                            <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-[10px] font-bold rounded-xl transition-colors">
                                Impor CSV
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Holidays Table List -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Daftar Hari Libur Akademik</h3>
                            <p class="text-xs text-slate-500">Mengatur jadwal hari non-efektif untuk pembatasan absensi harian.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <input type="text" x-model="searchQuery" placeholder="Cari keterangan..." class="pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-500 w-44">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="button" @click="window.bulkDeleteSelected('.item-checkbox', '{{ csrf_token() }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>
 
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm text-slate-650">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="py-3 px-4 w-10">
                                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-emerald-650 focus:ring-emerald-500">
                                    </th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Rentang Tanggal</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Unit / Jenis</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $holiday)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/55 transition-colors"
                                        x-show="searchQuery === '' || '{{ strtolower(addslashes($holiday->keterangan)) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="py-4 px-4">
                                            <input type="checkbox" class="item-checkbox rounded border-slate-300 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('admin.holidays.delete', $holiday->id) }}">
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-800">{{ $holiday->keterangan }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase">TA: {{ $holiday->academicYear->tahun_ajaran ?? '-' }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-650">
                                            @if($holiday->tanggal_mulai === $holiday->tanggal_selesai)
                                                {{ \Carbon\Carbon::parse($holiday->tanggal_mulai)->translatedFormat('d M Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($holiday->tanggal_mulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($holiday->tanggal_selesai)->translatedFormat('d M Y') }}
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center space-y-1">
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700">
                                                {{ $holiday->unit }}
                                            </span>
                                            <br>
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold {{ $holiday->is_nasional ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $holiday->is_nasional ? 'Nasional' : 'Lokal' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <form action="{{ route('admin.holidays.delete', $holiday->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini? Absensi akan dapat diisi kembali.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada hari libur terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
