<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 dark:text-slate-100 leading-tight">
            Dashboard Absensi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 overflow-hidden shadow-xl rounded-3xl p-8 flex flex-col items-center text-center space-y-6">
                {{-- Decorative Element --}}
                <div class="relative">
                    <div class="absolute inset-0 bg-emerald-500/10 rounded-full blur-2xl"></div>
                    <div class="relative w-24 h-24 bg-gradient-to-tr from-emerald-400 to-teal-500 rounded-3xl shadow-lg flex items-center justify-center text-white transform rotate-3">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>

                {{-- Status & Message --}}
                <div class="space-y-2 max-w-lg">
                    <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider">
                        Status: Belum Aktif
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white pt-2">
                        Menu Absensi Kelas Belum Ditugaskan
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed pt-1">
                        {{ $message }}
                    </p>
                </div>

                {{-- Help/Instructions --}}
                <div class="w-full max-w-md bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-5 border border-slate-100 dark:border-slate-800/60 text-left space-y-3">
                    <h4 class="font-bold text-xs text-slate-400 dark:text-slate-500 uppercase tracking-widest">Langkah Selanjutnya</h4>
                    <div class="flex gap-3 items-start">
                        <div class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0">1</div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">Hubungi <strong>Wali Kelas</strong> Anda untuk didaftarkan sebagai Petugas Absensi kelas.</p>
                    </div>
                    <div class="flex gap-3 items-start">
                        <div class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0">2</div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">Setelah ditugaskan, Anda dapat mengakses menu <strong>Input Absensi Kelas</strong> di sidebar kiri.</p>
                    </div>
                </div>

                {{-- Action / Back --}}
                <div class="pt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-sm">
                            Keluar dari Aplikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
