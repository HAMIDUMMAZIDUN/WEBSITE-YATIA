<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
            {{ __('Dashboard Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-3xl p-8 text-center shadow-sm space-y-6">
            <div class="w-16 h-16 bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-full flex items-center justify-center mx-auto text-amber-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            
            <div class="space-y-2">
                <h3 class="font-bold text-xl text-slate-900 dark:text-white">Penugasan Wali Kelas Tidak Ditemukan</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $message }}</p>
            </div>

            <div class="pt-4">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-xs font-bold transition-colors">
                    Kembali ke Dashboard Utama
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
