<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Manajemen Pengguna (Users & RBAC)') }}
            </h2>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ 
            editModalOpen: false, 
            showPasswordEdit: false,
            editUser: { id: '', name: '', email: '', role_id: '', jenis_kelamin: 'L' },
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

            <!-- Content Grid -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Users Table -->
                <div class="lg:col-span-8 space-y-8">
                    <div id="users-section" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden scroll-mt-6">
                        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="font-bold text-lg text-slate-900">Daftar Pengguna Aktif</h3>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="relative">
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama/email..." class="pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-emerald-500 w-44">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <select x-model="roleFilter" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-emerald-500">
                                    <option value="">Semua Peran</option>
                                    @foreach($roles as $rl)
                                        <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="window.bulkDeleteSelected('.item-checkbox', '{{ csrf_token() }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3 w-10">
                                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-6 py-3">Nama</th>
                                        <th class="px-6 py-3">Email</th>
                                        <th class="px-6 py-3">Hak Akses</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($users as $usr)
                                        <tr class="hover:bg-slate-50/40 transition-colors"
                                            x-show="(searchQuery === '' || '{{ strtolower(addslashes($usr->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($usr->email)) }}'.includes(searchQuery.toLowerCase())) && (roleFilter === '' || '{{ $usr->role_id }}' === roleFilter)">
                                            <td class="px-6 py-4">
                                                @if($usr->id !== Auth::id())
                                                    <input type="checkbox" class="item-checkbox rounded border-slate-300 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('admin.users.delete', $usr->id) }}">
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $usr->name }}</td>
                                            <td class="px-6 py-4">{{ $usr->email }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider 
                                                    {{ $usr->role->name === 'super_admin' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                    {{ $usr->role->name === 'guru' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $usr->role->name === 'wali_kelas' ? 'bg-amber-100 text-amber-700' : '' }}
                                                    {{ $usr->role->name === 'siswa' ? 'bg-purple-100 text-purple-700' : '' }}
                                                    {{ $usr->role->name === 'guru_bk' ? 'bg-rose-100 text-rose-700' : '' }}
                                                    {{ $usr->role->name === 'wali_kelas_guru' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                                ">
                                                    {{ $usr->role->display_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    @php
                                                        $usrGender = 'L';
                                                        if ($usr->guru) {
                                                            $usrGender = $usr->guru->jenis_kelamin ?? 'L';
                                                        } elseif ($usr->siswa) {
                                                            $usrGender = $usr->siswa->jenis_kelamin ?? 'L';
                                                        }
                                                    @endphp
                                                    <button 
                                                        @click="
                                                            editUser = { 
                                                                id: '{{ $usr->id }}', 
                                                                name: '{{ addslashes($usr->name) }}', 
                                                                email: '{{ addslashes($usr->email) }}', 
                                                                role_id: '{{ $usr->role_id }}',
                                                                jenis_kelamin: '{{ $usrGender }}'
                                                            }; 
                                                            editModalOpen = true;
                                                        "
                                                        class="text-blue-600 hover:text-blue-800 font-semibold text-xs transition-colors"
                                                    >
                                                        Edit
                                                    </button>
                                                    @if($usr->id !== Auth::id())
                                                        <form action="{{ route('admin.users.delete', $usr->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs transition-colors">Hapus</button>
                                                        </form>
                                                    @else
                                                        <span class="text-slate-400 text-xs">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Forms Area (Sidebar) -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Form Tambah User -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Tambah User Baru
                        </h3>
                        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Ahmad Yani" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                                <input type="email" name="email" required placeholder="user@gmail.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Password</label>
                                <div class="relative" x-data="{ showPasswordAdd: false }">
                                    <input :type="showPasswordAdd ? 'text' : 'password'" name="password" required placeholder="******" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 pr-10 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <button type="button" @click="showPasswordAdd = !showPasswordAdd" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                        <svg x-show="!showPasswordAdd" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg x-show="showPasswordAdd" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Peran (Role)</label>
                                <select name="role_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    @foreach($roles as $rl)
                                        <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                                <select name="jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan User
                            </button>
                        </form>
                        <!-- Import/Export section -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Atau Import / Export Masal</span>
                            <div class="flex gap-2">
                                <a href="{{ route('data.template', 'users') }}" class="flex-1 text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl border border-slate-150 transition-colors">
                                    Template
                                </a>
                                <a href="{{ route('data.export', 'users') }}" class="flex-1 text-center py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-150 transition-colors">
                                    Ekspor CSV
                                </a>
                            </div>
                            <form action="{{ route('data.import', 'users') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="file" name="file" required class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white text-[10px] font-bold rounded-lg transition-colors flex-shrink-0">
                                    Unggah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-bold text-lg text-slate-900">Ubah Pengguna (Edit User)</h3>
                        <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form :action="'/admin/users/' + editUser.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required x-model="editUser.name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" name="email" required x-model="editUser.email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Password Baru (Kosongkan jika tidak diubah)</label>
                            <div class="relative">
                                <input :type="showPasswordEdit ? 'text' : 'password'" name="password" placeholder="******" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 pr-10 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <button type="button" @click="showPasswordEdit = !showPasswordEdit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-emerald-600 focus:outline-none">
                                    <svg x-show="!showPasswordEdit" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showPasswordEdit" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Peran (Role)</label>
                            <select name="role_id" required x-model="editUser.role_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                @foreach($roles as $rl)
                                    <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required x-model="editUser.jenis_kelamin" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="editModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
