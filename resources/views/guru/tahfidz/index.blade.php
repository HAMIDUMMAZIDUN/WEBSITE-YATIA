<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 dark:text-white leading-tight">
            {{ __('Setoran Hafalan Al-Qur\'an (Tahfidz)') }}
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
                <!-- Form Input Setoran Baru -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6 h-fit">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Setoran Hafalan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Penyimakan setoran juz/surah baru siswa.</p>
                    </div>

                    <form action="{{ route('guru.tahfidz.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Pilih Siswa</label>
                            <select name="siswa_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                                <option value="">Pilih Siswa...</option>
                                @foreach($siswas as $siswa)
                                    <option value="{{ $siswa->id }}">{{ $siswa->nama }} - {{ $siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Juz / Surah</label>
                            <input type="text" name="surah_juz" required placeholder="Contoh: Juz 30 / An-Naba'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Ayat</label>
                            <input type="text" name="ayat" required placeholder="Contoh: 1-10 atau Seluruhnya" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Predikat / Kelancaran</label>
                            <select name="predikat" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                                <option value="A">Sangat Lancar (A)</option>
                                <option value="B">Lancar (B)</option>
                                <option value="C">Cukup (C)</option>
                                <option value="D">Kurang (D)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-wider">Tanggal Setoran</label>
                            <input type="date" name="tanggal_setoran" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg transition-colors">
                            Simpan Setoran
                        </button>
                    </form>
                </div>

                <!-- Tabel Riwayat Setoran -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">Riwayat Setoran Hafalan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Daftar setoran hafalan yang disimak oleh Anda.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="pb-3">Siswa / Kelas</th>
                                    <th class="pb-3">Hafalan</th>
                                    <th class="pb-3">Predikat</th>
                                    <th class="pb-3">Tanggal Setoran</th>
                                    <th class="pb-3">Disimak Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @forelse($tahfidz as $rec)
                                    <tr class="text-slate-800 dark:text-slate-300">
                                        <td class="py-4">
                                            <span class="font-semibold block">{{ $rec->siswa->nama }}</span>
                                            <span class="text-xs text-slate-500">{{ $rec->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="font-bold text-slate-800 dark:text-white block">{{ $rec->surah_juz }}</span>
                                            <span class="text-xs text-slate-500">Ayat: {{ $rec->ayat }}</span>
                                        </td>
                                        <td class="py-4">
                                            @if($rec->predikat === 'A')
                                                <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-450 rounded text-xs font-bold">A - Sangat Lancar</span>
                                            @elseif($rec->predikat === 'B')
                                                <span class="px-2.5 py-0.5 bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-450 rounded text-xs font-bold">B - Lancar</span>
                                            @elseif($rec->predikat === 'C')
                                                <span class="px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-450 rounded text-xs font-bold">C - Cukup</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-450 rounded text-xs font-bold">D - Kurang</span>
                                            @endif
                                        </td>
                                        <td class="py-4 font-semibold text-slate-600 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($rec->tanggal_setoran)->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="py-4 font-medium text-xs text-slate-500">
                                            {{ $rec->guru->nama ?? 'Sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat setoran hafalan.</td>
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
