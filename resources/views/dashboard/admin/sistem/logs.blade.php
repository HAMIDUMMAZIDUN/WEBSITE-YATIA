<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-250 leading-tight">
                {{ __('Log Aktivitas Sistem') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ 
            selectedLog: null,
            openDetailModal(log) {
                this.selectedLog = log;
            }
        }">
            <!-- Filtering & Search -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6">
                <form action="{{ route('admin.sistem.logs') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Aliran Aktivitas Terkini</h3>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, aksi..." class="pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-emerald-500 w-64 text-slate-900 dark:text-white">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors">
                            Cari
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('admin.sistem.logs') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 hover:bg-slate-200 rounded-xl text-xs font-bold transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-650 dark:text-slate-400">
                        <thead class="bg-slate-50/50 dark:bg-slate-950/50 text-slate-400 dark:text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3">Pengguna</th>
                                <th class="px-6 py-3">Peran</th>
                                <th class="px-6 py-3">Aksi</th>
                                <th class="px-6 py-3">Entitas</th>
                                <th class="px-6 py-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        {{ $log->created_at->format('d M Y, H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $log->user ? $log->user->name : 'Sistem / Guest' }}</span>
                                            <span class="text-xs text-slate-400">{{ $log->user ? $log->user->email : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->user)
                                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                {{ $log->user->role->display_name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                        {{ $log->action }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->entity)
                                            <span class="text-xs bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 px-2 py-1 rounded-md font-mono">
                                                {{ $log->entity }} #{{ $log->entity_id ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button @click="openDetailModal({
                                            id: '{{ $log->id }}',
                                            user: '{{ $log->user ? addslashes($log->user->name) : 'Sistem' }}',
                                            action: '{{ addslashes($log->action) }}',
                                            entity: '{{ $log->entity }}',
                                            entity_id: '{{ $log->entity_id }}',
                                            old_value: '{{ $log->old_value ? addslashes($log->old_value) : '' }}',
                                            new_value: '{{ $log->new_value ? addslashes($log->new_value) : '' }}',
                                            time: '{{ $log->created_at->format('d M Y, H:i:s') }}'
                                        })" class="text-emerald-650 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-350 text-xs font-bold flex items-center justify-center gap-1 mx-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        Tidak ada log aktivitas ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-slate-800">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

            <!-- Detail Modal -->
            <div x-show="selectedLog !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl max-w-2xl w-full overflow-hidden" @click.away="selectedLog = null">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="p-1.5 bg-emerald-50 dark:bg-emerald-950 rounded-lg text-emerald-600 dark:text-emerald-400">📝</span>
                            Detail Log Aktivitas #<span x-text="selectedLog?.id"></span>
                        </h3>
                        <button @click="selectedLog = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Pengguna</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.user"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Waktu</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.time"></span>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 text-xs uppercase font-bold block">Tindakan / Aksi</span>
                            <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.action"></span>
                        </div>

                        <div x-show="selectedLog?.entity">
                            <span class="text-slate-400 text-xs uppercase font-bold block">Entitas Terkait</span>
                            <span class="font-mono text-xs bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300 px-2 py-1 rounded border border-slate-100 dark:border-slate-850 inline-block">
                                <span x-text="selectedLog?.entity"></span> #<span x-text="selectedLog?.entity_id"></span>
                            </span>
                        </div>

                        <div class="space-y-3 pt-2">
                            <div x-show="selectedLog?.old_value">
                                <span class="text-slate-400 text-xs uppercase font-bold block mb-1">Nilai Lama (Sebelum Perubahan)</span>
                                <pre class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl text-[10px] font-mono border border-slate-100 dark:border-slate-850 text-slate-600 dark:text-slate-350 overflow-x-auto" x-text="JSON.stringify(JSON.parse(selectedLog?.old_value || '{}'), null, 2)"></pre>
                            </div>
                            <div x-show="selectedLog?.new_value">
                                <span class="text-slate-400 text-xs uppercase font-bold block mb-1">Nilai Baru (Setelah Perubahan)</span>
                                <pre class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl text-[10px] font-mono border border-slate-100 dark:border-slate-850 text-slate-600 dark:text-slate-350 overflow-x-auto" x-text="JSON.stringify(JSON.parse(selectedLog?.new_value || '{}'), null, 2)"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
