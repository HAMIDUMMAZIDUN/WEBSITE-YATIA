<x-app-layout>
    <x-slot name="header">
        <div class="bg-white dark:bg-slate-900 px-6 py-4 rounded-xl shadow-sm dark:shadow-lg border border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <h2 class="font-bold text-xl text-slate-900 dark:text-white tracking-wide">
                {{ __('Publikasi: Integrasi Media Sosial') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Toast Notification -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <form action="{{ route('admin.publikasi.medsos.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <p class="text-xs text-slate-500 dark:text-slate-400 pb-2">
                        Konfigurasikan tautan ke akun media sosial resmi madrasah. Tautan yang aktif akan ditampilkan pada landing page publik.
                    </p>

                    @foreach($socials as $social)
                        <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 last:border-0 last:pb-0">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-450 rounded-xl flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                {{ substr($social->platform, 0, 2) }}
                            </div>
                            <div class="flex-1 space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $social->platform }}</label>
                                <input type="url" name="urls[{{ $social->id }}]" value="{{ $social->url }}" placeholder="https://..." class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="flex items-center gap-2 pt-5">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="active[{{ $social->id }}]" value="1" {{ $social->is_active ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                                </label>
                                <span class="text-xs text-slate-500 font-semibold select-none">Aktif</span>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
