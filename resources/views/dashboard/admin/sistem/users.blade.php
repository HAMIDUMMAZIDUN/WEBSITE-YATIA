<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                {{ __('Manajemen Pengguna (Users & RBAC)') }}
            </h2>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-850 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-lg text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ 
            editModalOpen: false, 
            showPasswordEdit: false,
            editUser: { id: '', name: '', username: '', email: '', role_id: '', jenis_kelamin: 'L' },
            searchQuery: '',
            roleFilter: '',
            selectAll: false,
            toggleAll() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.selectAll;
                });
            }
        }">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Users Table — Full Width -->
            <div class="w-full space-y-8">
                    <div id="users-section" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden scroll-mt-6 w-full min-w-0">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white shrink-0">Daftar Pengguna Aktif</h3>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                                <div class="relative w-full sm:w-44">
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama/email..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:border-emerald-500">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <select x-model="roleFilter" class="w-full sm:w-auto px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:border-emerald-500">
                                    <option value="">Semua Peran</option>
                                    @foreach($roles as $rl)
                                        <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="window.bulkDeleteSelected('.item-checkbox', '{{ csrf_token() }}')" class="w-full sm:w-auto px-3 py-1.5 bg-rose-650 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full min-w-[850px] text-left text-sm text-slate-600 dark:text-slate-400 table-layout-fixed">
                                <thead class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3 w-10">
                                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 dark:border-slate-700 text-emerald-650 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-4 py-3">Pengguna</th>
                                        <th class="px-4 py-3">Kata Sandi</th>
                                        <th class="px-4 py-3">Hak Akses</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($users as $usr)
                                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/40 transition-colors"
                                            x-show="(searchQuery === '' || '{{ strtolower(addslashes($usr->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($usr->email)) }}'.includes(searchQuery.toLowerCase())) && (roleFilter === '' || '{{ $usr->role_id }}' === roleFilter)">
                                            <td class="px-4 py-3.5">
                                                @if($usr->id !== Auth::id())
                                                    <input type="checkbox" class="item-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('admin.users.delete', $usr->id) }}">
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-700">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5">
                                                @php
                                                    $usrGender = 'L';
                                                    if ($usr->guru) {
                                                        $usrGender = $usr->guru->jenis_kelamin ?? 'L';
                                                    } elseif ($usr->siswa) {
                                                        $usrGender = $usr->siswa->jenis_kelamin ?? 'L';
                                                    }
                                                @endphp
                                                <div class="flex items-center gap-3">
                                                    @if($usr->photo_profile)
                                                        <img src="{{ asset('storage/' . $usr->photo_profile) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-800 shrink-0">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-350 flex items-center justify-center font-bold text-xs shrink-0">
                                                            {{ substr($usr->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="space-y-0.5 min-w-0">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-sm leading-tight">{{ $usr->name }}</span>
                                                            <span class="px-1 py-0.5 rounded text-[8px] font-bold shrink-0 {{ $usrGender === 'L' ? 'bg-blue-50 text-blue-750 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-900' : 'bg-rose-50 text-rose-750 dark:bg-rose-950/40 dark:text-rose-450 border border-rose-200 dark:border-rose-900' }}">
                                                                {{ $usrGender }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                                            <span class="font-mono text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/80 px-1 py-0.5 rounded border border-slate-150 dark:border-slate-700 shrink-0">{{ $usr->username ?? $usr->nik }}</span>
                                                            <span class="text-slate-300 dark:text-slate-700">|</span>
                                                            <span class="break-all">{{ $usr->email }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <div x-data="{ show: false }" class="flex items-center gap-1.5">
                                                    <span x-text="show ? '{{ $usr->password_plain ?? 'hamidun123' }}' : '••••••••'" class="font-mono text-xs text-slate-700 dark:text-slate-300"></span>
                                                    <button type="button" @click="show = !show" class="text-slate-400 hover:text-emerald-500 focus:outline-none transition-colors">
                                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                        <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                                    {{ $usr->role->name === 'super_admin' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : '' }}
                                                    {{ $usr->role->name === 'admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : '' }}
                                                    {{ $usr->role->name === 'guru' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' : '' }}
                                                    {{ $usr->role->name === 'wali_kelas' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' : '' }}
                                                    {{ $usr->role->name === 'absensi' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400' : '' }}
                                                    {{ $usr->role->name === 'guru_bk' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : '' }}
                                                    {{ $usr->role->name === 'wali_kelas_guru' ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400' : '' }}
                                                ">
                                                    {{ $usr->role->display_name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                @if($usr->is_active)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-450 border border-rose-200 dark:border-rose-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                                    <button 
                                                        @click="
                                                            editUser = { 
                                                                id: '{{ $usr->id }}', 
                                                                name: '{{ addslashes($usr->name) }}', 
                                                                username: '{{ addslashes($usr->username ?? $usr->nik) }}', 
                                                                email: '{{ addslashes($usr->email) }}', 
                                                                role_id: '{{ $usr->role_id }}',
                                                                jenis_kelamin: '{{ $usrGender }}'
                                                            }; 
                                                            editModalOpen = true;
                                                        "
                                                        class="text-blue-600 dark:text-blue-400 hover:underline font-semibold text-xs transition-colors"
                                                    >
                                                        Edit
                                                    </button>
                                                    @if($usr->id !== Auth::id())
                                                        {{-- Toggle Aktif/Nonaktif --}}
                                                        <form action="{{ route('admin.users.toggle_status', $usr->id) }}" method="POST" onsubmit="return confirm('{{ $usr->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ addslashes($usr->name) }}?')">
                                                            @csrf
                                                            <button type="submit" class="font-semibold text-xs transition-colors {{ $usr->is_active ? 'text-amber-600 dark:text-amber-400 hover:underline' : 'text-emerald-600 dark:text-emerald-400 hover:underline' }}">
                                                                {{ $usr->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.users.delete', $usr->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ addslashes($usr->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline font-semibold text-xs transition-colors">Hapus</button>
                                                        </form>
                                                    @else
                                                        <span class="text-slate-400 dark:text-slate-500 text-xs italic px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg">Anda</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List View -->
                        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($users as $usr)
                                @php
                                    $usrGender = 'L';
                                    if ($usr->guru) {
                                        $usrGender = $usr->guru->jenis_kelamin ?? 'L';
                                    } elseif ($usr->siswa) {
                                        $usrGender = $usr->siswa->jenis_kelamin ?? 'L';
                                    }
                                @endphp
                                <div class="p-4 sm:p-6 space-y-4"
                                     x-show="(searchQuery === '' || '{{ strtolower(addslashes($usr->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($usr->email)) }}'.includes(searchQuery.toLowerCase())) && (roleFilter === '' || '{{ $usr->role_id }}' === roleFilter)">
                                    
                                    <!-- Card Header -->
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 w-full sm:w-auto min-w-0">
                                            @if($usr->id !== Auth::id())
                                                <input type="checkbox" class="item-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-650 focus:ring-emerald-500 shrink-0" data-delete-url="{{ route('admin.users.delete', $usr->id) }}">
                                            @else
                                                <span class="text-slate-350 dark:text-slate-700 w-4 text-center shrink-0">-</span>
                                            @endif
                                            
                                            @if($usr->photo_profile)
                                                <img src="{{ asset('storage/' . $usr->photo_profile) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-800 shrink-0">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-350 flex items-center justify-center font-bold text-sm shrink-0">
                                                    {{ substr($usr->name, 0, 1) }}
                                                </div>
                                            @endif
                                            
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm leading-tight">{{ $usr->name }}</span>
                                                    <span class="px-1 py-0.5 rounded text-[8px] font-bold shrink-0 {{ $usrGender === 'L' ? 'bg-blue-50 text-blue-750 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-900' : 'bg-rose-50 text-rose-750 dark:bg-rose-950/40 dark:text-rose-450 border border-rose-200 dark:border-rose-900' }}">
                                                        {{ $usrGender }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-slate-500 dark:text-slate-400 break-all block">{{ $usr->email }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-row sm:flex-col items-center sm:items-end gap-1.5 w-full sm:w-auto sm:shrink-0 justify-start sm:justify-end pl-7 sm:pl-0 flex-wrap">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                                                {{ $usr->role->name === 'super_admin' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : '' }}
                                                {{ $usr->role->name === 'admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : '' }}
                                                {{ $usr->role->name === 'guru' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' : '' }}
                                                {{ $usr->role->name === 'wali_kelas' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' : '' }}
                                                {{ $usr->role->name === 'absensi' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400' : '' }}
                                                {{ $usr->role->name === 'guru_bk' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : '' }}
                                                {{ $usr->role->name === 'wali_kelas_guru' ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400' : '' }}
                                            ">
                                                {{ $usr->role->display_name }}
                                            </span>
                                            @if($usr->is_active)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Card Details -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl text-xs ml-7 sm:ml-0">
                                        <div>
                                            <span class="text-slate-400 dark:text-slate-500 block text-[9px] uppercase font-bold tracking-wider">Username / NIK</span>
                                            <span class="font-mono font-semibold text-slate-800 dark:text-slate-200 break-all">{{ $usr->username ?? $usr->nik }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 dark:text-slate-500 block text-[9px] uppercase font-bold tracking-wider">Kata Sandi</span>
                                            <div x-data="{ show: false }" class="flex items-center gap-1.5 mt-0.5">
                                                <span x-text="show ? '{{ $usr->password_plain ?? 'hamidun123' }}' : '••••••••'" class="font-mono font-semibold text-slate-800 dark:text-slate-200"></span>
                                                <button type="button" @click="show = !show" class="text-slate-400 hover:text-emerald-500 focus:outline-none transition-colors">
                                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                    <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Actions -->
                                    <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 ml-7 sm:ml-0">
                                        <button 
                                            @click="
                                                editUser = { 
                                                    id: '{{ $usr->id }}', 
                                                    name: '{{ addslashes($usr->name) }}', 
                                                    username: '{{ addslashes($usr->username ?? $usr->nik) }}', 
                                                    email: '{{ addslashes($usr->email) }}', 
                                                    role_id: '{{ $usr->role_id }}',
                                                    jenis_kelamin: '{{ $usrGender }}'
                                                }; 
                                                editModalOpen = true;
                                            "
                                            class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-750 dark:bg-blue-950/40 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors"
                                        >
                                            Edit
                                        </button>
                                        @if($usr->id !== Auth::id())
                                            <form action="{{ route('admin.users.toggle_status', $usr->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ $usr->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ addslashes($usr->name) }}?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $usr->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-450' }}">
                                                    {{ $usr->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.delete', $usr->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user {{ addslashes($usr->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-450 rounded-lg text-xs font-semibold transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-xs italic px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
            </div>

            <!-- Forms Area — Below Table -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <!-- Tambah User Column -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Tambah User Baru
                        </h3>
                        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Ahmad Yani" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Username</label>
                                <input type="text" name="username" placeholder="ahmadyani" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat Email</label>
                                <input type="email" name="email" required placeholder="user@gmail.com" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Password</label>
                                <div class="relative" x-data="{ showPasswordAdd: false }">
                                    <input x-bind:type="showPasswordAdd ? 'text' : 'password'" name="password" required placeholder="******" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 pr-10 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <button type="button" @click="showPasswordAdd = !showPasswordAdd" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                        <svg x-show="!showPasswordAdd" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg x-show="showPasswordAdd" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peran (Role)</label>
                                <select name="role_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    @foreach($roles as $rl)
                                        <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                                <select name="jenis_kelamin" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-bold rounded-xl shadow-lg transition-colors">
                                Simpan User
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Import / Export Column -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Import &amp; Export Data
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Export / Template</span>
                                <div class="flex gap-2">
                                    <a href="{{ route('data.template', 'users') }}" class="flex-1 text-center py-2.5 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                        Template CSV
                                    </a>
                                    <a href="{{ route('data.export', 'users') }}" class="flex-1 text-center py-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-450 text-xs font-semibold rounded-xl border border-emerald-200 dark:border-emerald-900 transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Ekspor CSV
                                    </a>
                                </div>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Import Massal dari CSV</span>
                                <form action="{{ route('data.import', 'users') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="file" name="file" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-700 cursor-pointer">
                                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        Unggah &amp; Import
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit User Modal -->
            <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-800" @click.away="editModalOpen = false">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Ubah Pengguna (Edit User)</h3>
                        <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form :action="'/admin/users/' + editUser.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required x-model="editUser.name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Username</label>
                            <input type="text" name="username" required x-model="editUser.username" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" name="email" required x-model="editUser.email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Password Baru (Kosongkan jika tidak diubah)</label>
                            <div class="relative">
                                <input x-bind:type="showPasswordEdit ? 'text' : 'password'" name="password" placeholder="******" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 pr-10 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <button type="button" @click="showPasswordEdit = !showPasswordEdit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                    <svg x-show="!showPasswordEdit" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showPasswordEdit" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peran (Role)</label>
                            <select name="role_id" required x-model="editUser.role_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                @foreach($roles as $rl)
                                    <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required x-model="editUser.jenis_kelamin" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="editModalOpen = false" class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 py-3 bg-slate-900 hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
