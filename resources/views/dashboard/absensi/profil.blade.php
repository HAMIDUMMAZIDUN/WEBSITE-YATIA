@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Profil Saya</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Mengelola data akun: nama, foto, dan password.</p>
            <span class="inline-block mt-4 px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 text-xs font-bold rounded-full">Dalam Pengembangan</span>
        </div>
    </div>
</div>
@endsection
