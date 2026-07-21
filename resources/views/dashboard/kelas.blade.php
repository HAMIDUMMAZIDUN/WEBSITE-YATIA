<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Manajemen Kelas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ editModalOpen: false, editKelas: { id: '', nama_kelas: '', wali_kelas_id: '' } }">
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

            <!-- Main Management Layout -->
            <div class="grid lg:grid-cols-12 gap-8">
                <!-- Classes Table (Left Column) -->
                <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="font-bold text-lg text-slate-900">Daftar Kelas</h3>
                            <span class="px-2.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-bold text-[10px] uppercase tracking-wider">{{ count($kelas) }} Kelas Terdaftar</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Nama Kelas</th>
                                        <th class="px-6 py-3">Wali Kelas</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($kelas as $kls)
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $kls->nama_kelas }}</td>
                                            <td class="px-6 py-4">{{ $kls->waliGuru ? $kls->waliGuru->nama : 'Belum ditentukan' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <button 
                                                        @click="
                                                            editKelas = { 
                                                                id: '{{ $kls->id }}', 
                                                                nama_kelas: '{{ addslashes($kls->nama_kelas) }}', 
                                                                wali_kelas_id: '{{ $kls->wali_kelas_id ?? '' }}' 
                                                            }; 
                                                            editModalOpen = true;
                                                        "
                                                        class="text-blue-600 hover:text-blue-800 font-semibold text-xs transition-colors"
                                                    >
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('admin.kelas.delete', $kls->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-sm">
                                                Belum ada data kelas yang terdaftar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Forms Area (Right Column) -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Form Tambah Kelas -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Tambah Kelas Baru
                        </h3>
                        <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</label>
                                <input type="text" name="nama_kelas" required placeholder="XII IPA 3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</label>
                                <select name="wali_kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <option value="">Pilih Wali Kelas...</option>
                                    @foreach($gurus as $gr)
                                        <option value="{{ $gr->id }}">{{ $gr->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Kelas
                            </button>
                        </form>

                        <!-- Bulk Import/Export Widget -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Atau Import / Export Masal</span>
                            <div class="flex gap-2">
                                <a href="{{ route('data.template', 'kelas') }}" class="flex-1 text-center py-2 bg-slate-55 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl border border-slate-150 transition-colors">
                                    Template
                                </a>
                                <a href="{{ route('data.export', 'kelas') }}" class="flex-1 text-center py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-150 transition-colors">
                                    Ekspor CSV
                                </a>
                            </div>
                            <form action="{{ route('data.import', 'kelas') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
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

            <!-- Edit Kelas Modal -->
            <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-bold text-lg text-slate-900">Ubah Data Kelas</h3>
                        <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form :action="'/admin/kelas/' + editKelas.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</label>
                            <input type="text" name="nama_kelas" required x-model="editKelas.nama_kelas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</label>
                            <select name="wali_kelas_id" x-model="editKelas.wali_kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="">Pilih Wali Kelas...</option>
                                @foreach($gurus as $gr)
                                    <option value="{{ $gr->id }}">{{ $gr->nama }}</option>
                                @endforeach
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
