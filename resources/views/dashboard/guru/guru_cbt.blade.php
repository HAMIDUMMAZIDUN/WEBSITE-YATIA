<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('CBT & Bank Soal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Teacher Info Card -->
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-3xl p-8 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded bg-white/20 text-white font-semibold text-xs uppercase tracking-wider">Modul Ujian</span>
                    <h3 class="text-3xl font-extrabold">Computer Based Test (CBT)</h3>
                    <p class="text-violet-100 text-sm">Buat dan kelola soal ujian berbasis komputer untuk siswa secara terstruktur.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/10 shrink-0 text-center">
                    <span class="block text-violet-200 text-xs font-semibold uppercase tracking-wider">Status</span>
                    <span class="text-lg font-bold flex items-center gap-2 justify-center mt-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Aktif
                    </span>
                </div>
            </div>

            <!-- CBT Module Info -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900">Bank Soal & Ujian Aktif</h3>
                    <button onclick="alert('Fitur pembuatan soal CBT sedang dalam pengembangan.')"
                        class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                        + Buat Soal Ujian Baru
                    </button>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-700 font-semibold text-[10px] uppercase tracking-wider">Bank Soal Aktif</span>
                            <h4 class="font-bold text-slate-900 mt-2 text-sm">Soal Pilihan Ganda Matematika X</h4>
                            <p class="text-xs text-slate-400 mt-1">Total: 40 Soal | Dibuat oleh Anda</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Fitur ubah soal sedang dikembangkan.')" class="px-3 py-1 bg-slate-800 text-white rounded text-[11px] font-semibold">Ubah Soal</button>
                            <button onclick="alert('Pratinjau soal akan tampil di sini.')" class="px-3 py-1 border border-slate-200 text-slate-600 rounded text-[11px]">Lihat Pratinjau</button>
                        </div>
                    </div>
                    <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-700 font-semibold text-[10px] uppercase tracking-wider">Bank Soal Aktif</span>
                            <h4 class="font-bold text-slate-900 mt-2 text-sm">Kuis Harian 1 Fisika Dasar X</h4>
                            <p class="text-xs text-slate-400 mt-1">Total: 15 Soal | Dibuat oleh Anda</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Fitur ubah soal sedang dikembangkan.')" class="px-3 py-1 bg-slate-800 text-white rounded text-[11px] font-semibold">Ubah Soal</button>
                            <button onclick="alert('Pratinjau soal akan tampil di sini.')" class="px-3 py-1 border border-slate-200 text-slate-600 rounded text-[11px]">Lihat Pratinjau</button>
                        </div>
                    </div>
                    <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center space-y-3 cursor-pointer hover:border-violet-400 hover:bg-violet-50/30 transition-colors group"
                         onclick="alert('Fitur pembuatan soal CBT sedang dalam pengembangan.')">
                        <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-violet-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-violet-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-xs text-slate-400 group-hover:text-violet-600 font-semibold text-center transition-colors">Tambah Bank Soal Baru</span>
                    </div>
                </div>
            </div>

            <!-- Coming Soon Notice -->
            <div class="bg-violet-50 border border-violet-200 rounded-2xl p-6 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-violet-900">Modul CBT Sedang Dalam Pengembangan</h4>
                    <p class="text-sm text-violet-700 mt-1">
                        Fitur pembuatan soal ujian, manajemen peserta, pengaturan waktu, dan laporan hasil ujian akan segera tersedia. 
                        Saat ini Anda dapat mengelola bank soal melalui panel di atas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
