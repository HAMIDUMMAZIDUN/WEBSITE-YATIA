<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-250 leading-tight">
                {{ __('Backup & Pemulihan Database') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ 
            restoreModalOpen: false,
            selectedBackup: null,
            confirmRestore(backup) {
                this.selectedBackup = backup;
                this.restoreModalOpen = true;
            }
        }">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-350 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-350 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Database Connection Status & Upload Restore -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- DB Status Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📡</span> Info Database Aktif
                        </h3>
                        <div class="space-y-3 pt-2 text-xs">
                            <div class="flex justify-between border-b border-slate-50 dark:border-slate-850 pb-2">
                                <span class="text-slate-400">Driver Koneksi</span>
                                <span class="font-bold uppercase text-slate-800 dark:text-slate-200">{{ $dbConnection }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 dark:border-slate-850 pb-2">
                                <span class="text-slate-400">Nama Database</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $dbName }}</span>
                            </div>
                            <div class="flex justify-between pb-1">
                                <span class="text-slate-400">Penyimpanan Cadangan</span>
                                <span class="font-mono text-slate-600 dark:text-slate-400 text-right overflow-hidden text-ellipsis whitespace-nowrap max-w-[150px]" title="storage/app/backups">storage/app/backups</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.sistem.backup.create') }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-emerald-650 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                                <span>📥</span> Buat Cadangan Baru
                            </button>
                        </form>
                    </div>

                    <!-- Upload Restore Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📤</span> Pulihkan dari File
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Unggah file cadangan database Anda (.sql untuk MySQL atau .sqlite untuk SQLite) untuk memulihkan keadaan data SIMSYATIA.
                        </p>

                        <form action="{{ route('admin.sistem.backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-center cursor-pointer hover:border-emerald-500 transition-colors relative">
                                <input type="file" name="backup_file" required class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" onchange="document.getElementById('file-upload-name').innerText = this.files[0].name">
                                <span class="text-2xl">file_upload</span>
                                <span id="file-upload-name" class="block text-[11px] font-bold text-slate-600 dark:text-slate-350 mt-1">Pilih atau Seret File</span>
                                <span class="block text-[9px] text-slate-400 mt-0.5">Maks. 50MB (.sql, .sqlite)</span>
                            </div>
                            <button type="submit" onclick="return confirm('Apakah Anda benar-benar yakin ingin memulihkan database dari file eksternal? Semua data saat ini akan digantikan!')" class="w-full py-2.5 bg-slate-850 dark:bg-slate-800 hover:bg-slate-750 text-white rounded-xl text-xs font-bold transition-colors">
                                Jalankan Pemulihan File
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Right: Backup File List -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-850">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Arsip Cadangan Lokal</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-650 dark:text-slate-400">
                                <thead class="bg-slate-50/50 dark:bg-slate-950/50 text-slate-400 dark:text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                                    <tr>
                                        <th class="px-6 py-3">Nama File</th>
                                        <th class="px-6 py-3">Ukuran</th>
                                        <th class="px-6 py-3">Dibuat Pada</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($files as $file)
                                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-6 py-4 font-mono text-xs text-slate-800 dark:text-slate-200">
                                                {{ $file['filename'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                                {{ $file['size'] }} KB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                                {{ $file['created_at'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <!-- Download -->
                                                    <a href="{{ route('admin.sistem.backup.download', $file['filename']) }}" class="text-emerald-650 dark:text-emerald-450 hover:text-emerald-700 text-xs font-bold flex items-center gap-0.5">
                                                        📥 Unduh
                                                    </a>
                                                    <!-- Delete -->
                                                    <form action="{{ route('admin.sistem.backup.delete', $file['filename']) }}" method="POST" onsubmit="return confirm('Hapus file cadangan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-bold">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                                Belum ada file cadangan yang disimpan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
