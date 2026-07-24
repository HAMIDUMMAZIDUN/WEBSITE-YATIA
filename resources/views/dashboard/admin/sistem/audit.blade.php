<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-250 leading-tight">
                {{ __('Laporan Audit & Keamanan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ 
            selectedLog: null,
            clearModalOpen: false,
            openDetailModal(log) {
                this.selectedLog = log;
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

            <!-- Top Cards Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <span class="p-4 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl text-2xl">📊</span>
                    <div>
                        <span class="text-xs text-slate-400 block uppercase font-bold">Total Log Audit</span>
                        <span class="text-2xl font-bold text-slate-800 dark:text-white">{{ \App\Models\AuditLog::count() }}</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <span class="p-4 bg-blue-50 dark:bg-blue-950/50 rounded-xl text-2xl">👥</span>
                    <div>
                        <span class="text-xs text-slate-400 block uppercase font-bold">Aktor Aktif</span>
                        <span class="text-2xl font-bold text-slate-800 dark:text-white">{{ \App\Models\AuditLog::distinct('user_id')->count('user_id') }}</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <span class="p-4 bg-purple-50 dark:bg-purple-950/50 rounded-xl text-2xl">🧱</span>
                    <div>
                        <span class="text-xs text-slate-400 block uppercase font-bold">Tabel Dipantau</span>
                        <span class="text-2xl font-bold text-slate-800 dark:text-white">{{ \App\Models\AuditLog::distinct('entity')->count('entity') }}</span>
                    </div>
                </div>
            </div>

            <!-- Filters & Export -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-850 pb-4">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Filter Log Audit</h3>
                    <div class="flex items-center gap-2">
                        <!-- Export Button -->
                        <a href="{{ route('admin.sistem.audit.export', ['format' => 'csv'] + request()->all()) }}" class="px-3.5 py-2 bg-slate-850 dark:bg-slate-800 hover:bg-slate-750 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                            📥 Ekspor CSV
                        </a>
                        <a href="{{ route('admin.sistem.audit.export', ['format' => 'json'] + request()->all()) }}" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350 hover:bg-slate-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                            📥 Ekspor JSON
                        </a>
                        <!-- Clear/Prune Button -->
                        <button type="button" @click="clearModalOpen = true" class="px-3.5 py-2 bg-rose-650 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                            🗑️ Bersihkan Log
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.sistem.audit') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-450 dark:text-slate-400 mb-1 uppercase">Pengguna</label>
                        <select name="user_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                            <option value="">Semua Pengguna</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-450 dark:text-slate-400 mb-1 uppercase">Tipe Aksi</label>
                        <select name="action_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                            <option value="">Semua Aksi</option>
                            <option value="create" {{ request('action_type') === 'create' ? 'selected' : '' }}>Create (Buat)</option>
                            <option value="update" {{ request('action_type') === 'update' ? 'selected' : '' }}>Update (Ubah)</option>
                            <option value="delete" {{ request('action_type') === 'delete' ? 'selected' : '' }}>Delete (Hapus)</option>
                            <option value="login" {{ request('action_type') === 'login' ? 'selected' : '' }}>Login</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-450 dark:text-slate-400 mb-1 uppercase">Tabel / Entitas</label>
                        <select name="entity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                            <option value="">Semua Entitas</option>
                            @foreach($entities as $ent)
                                <option value="{{ $ent }}" {{ request('entity') === $ent ? 'selected' : '' }}>{{ $ent }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-450 dark:text-slate-400 mb-1 uppercase">Mulai Tanggal</label>
                        <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-450 dark:text-slate-400 mb-1 uppercase">Hingga Tanggal</label>
                                <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                            </div>
                            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors">
                                Filter
                            </button>
                        </div>
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
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold capitalize
                                            {{ str_contains($log->action, 'create') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400' : '' }}
                                            {{ str_contains($log->action, 'update') ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400' : '' }}
                                            {{ str_contains($log->action, 'delete') ? 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450' : '' }}
                                            {{ !str_contains($log->action, 'create') && !str_contains($log->action, 'update') && !str_contains($log->action, 'delete') ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350' : '' }}
                                        ">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->entity)
                                            <span class="text-xs font-mono bg-slate-50 dark:bg-slate-950 px-2 py-1 rounded border border-slate-100 dark:border-slate-850">
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
                                        })" class="text-emerald-650 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-350 text-xs font-bold inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Bandingkan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        Tidak ada log audit ditemukan dengan filter ini.
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

            <!-- Detail Compare Modal -->
            <div x-show="selectedLog !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl max-w-4xl w-full overflow-hidden" @click.away="selectedLog = null">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="p-1.5 bg-emerald-50 dark:bg-emerald-950 rounded-lg text-emerald-600 dark:text-emerald-400">⚖️</span>
                            Perbandingan Data Log Audit #<span x-text="selectedLog?.id"></span>
                        </h3>
                        <button @click="selectedLog = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Pengguna</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.user"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Tindakan</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.action"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Entitas</span>
                                <span class="font-mono text-xs text-slate-700 dark:text-slate-350" x-text="selectedLog?.entity ? `${selectedLog.entity} #${selectedLog.entity_id}` : '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs uppercase font-bold block">Waktu</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200" x-text="selectedLog?.time"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <!-- Left: Old Data -->
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-slate-450 dark:text-slate-400 uppercase flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Nilai Sebelum Perubahan
                                </span>
                                <pre class="p-4 bg-rose-50/20 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 rounded-2xl text-[10px] font-mono text-rose-800 dark:text-rose-300 overflow-x-auto min-h-[150px]" x-text="selectedLog?.old_value ? JSON.stringify(JSON.parse(selectedLog.old_value), null, 2) : 'Tidak ada data lama'"></pre>
                            </div>
                            <!-- Right: New Data -->
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-slate-450 dark:text-slate-400 uppercase flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Nilai Setelah Perubahan
                                </span>
                                <pre class="p-4 bg-emerald-50/20 dark:bg-emerald-950/10 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl text-[10px] font-mono text-emerald-805 dark:text-emerald-300 overflow-x-auto min-h-[150px]" x-text="selectedLog?.new_value ? JSON.stringify(JSON.parse(selectedLog.new_value), null, 2) : 'Tidak ada data baru'"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear/Prune Dialog Modal -->
            <div x-show="clearModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl max-w-md w-full overflow-hidden" @click.away="clearModalOpen = false">
                    <div class="p-6 text-center space-y-4">
                        <div class="mx-auto w-12 h-12 bg-rose-50 dark:bg-rose-950/50 rounded-2xl flex items-center justify-center text-2xl text-rose-600">⚠️</div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Bersihkan Log Audit</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Pilih opsi pembersihan data audit. Tindakan ini tidak dapat dibatalkan!</p>
                        
                        <form action="{{ route('admin.sistem.audit.clear') }}" method="POST" class="space-y-4 pt-2">
                            @csrf
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-850 rounded-xl border border-slate-200 dark:border-slate-800 cursor-pointer">
                                    <input type="radio" name="clear_option" value="older_30" checked class="text-rose-600 focus:ring-rose-500">
                                    <div class="text-left">
                                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">Prune Log Lama (> 30 Hari)</span>
                                        <span class="block text-[10px] text-slate-400">Pertahankan data audit 30 hari terakhir.</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-rose-50/20 dark:bg-rose-950/10 hover:bg-rose-50/40 rounded-xl border border-rose-100 dark:border-rose-900/30 cursor-pointer">
                                    <input type="radio" name="clear_option" value="all" class="text-rose-600 focus:ring-rose-500">
                                    <div class="text-left">
                                        <span class="block text-xs font-bold text-rose-700 dark:text-rose-400">Reset Semua Log</span>
                                        <span class="block text-[10px] text-rose-400">Hapus seluruh isi tabel audit.</span>
                                    </div>
                                </label>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button type="button" @click="clearModalOpen = false" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors">
                                    Eksekusi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
