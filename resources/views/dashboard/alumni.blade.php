<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Direktori & Data Alumni') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ editModalOpen: false, editAlumni: { id: '', nama: '', no_hp: '', alamat: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search Panel -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <form action="{{ route('admin.alumni.index') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari alumni berdasarkan nama / NIS / NISN..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow transition-colors">
                        Cari Alumni
                    </button>
                    <a href="{{ route('admin.alumni.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl text-xs font-bold transition-colors flex items-center justify-center">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Alumni Directory Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Nama / NIS</th>
                                <th class="px-6 py-4">Gender</th>
                                <th class="px-6 py-4">Kontak (HP)</th>
                                <th class="px-6 py-4">Alamat Terakhir</th>
                                <th class="px-6 py-4 text-center">Status Akhir</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($alumni as $student)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $student->nama }}</div>
                                        <div class="text-[11px] text-slate-400">NIS: {{ $student->nis }} | NISN: {{ $student->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $student->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $student->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-850 font-medium">
                                        {{ $student->no_hp ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-650 max-w-xs truncate">
                                        {{ $student->alamat ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                            {{ $student->status === 'Lulus' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : '' }}
                                            {{ $student->status === 'Keluar' ? 'bg-amber-50 text-amber-700 border border-amber-250' : '' }}
                                            {{ $student->status === 'Drop Out' ? 'bg-rose-50 text-rose-700 border border-rose-250' : '' }}
                                        ">
                                            {{ $student->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.siswa.history', $student->id) }}" class="text-emerald-650 hover:text-emerald-800 font-semibold text-xs transition-colors flex items-center gap-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Riwayat
                                            </a>
                                            <button @click="
                                                editAlumni = {
                                                    id: '{{ $student->id }}',
                                                    nama: '{{ addslashes($student->nama) }}',
                                                    no_hp: '{{ $student->no_hp }}',
                                                    alamat: '{{ addslashes($student->alamat) }}'
                                                };
                                                editModalOpen = true;
                                            " class="text-blue-650 hover:text-blue-800 font-semibold text-xs transition-colors">
                                                Edit Kontak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Tidak ada data alumni ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($alumni->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $alumni->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Edit Alumni Contact Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900">Ubah Kontak Alumni</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="'/admin/alumni/' + editAlumni.id" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Alumni</label>
                        <div class="font-bold text-slate-800 text-sm py-1" x-text="editAlumni.nama"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor HP/WhatsApp</label>
                        <input type="text" name="no_hp" x-model="editAlumni.no_hp" placeholder="Contoh: 08123456789" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Terakhir</label>
                        <textarea name="alamat" x-model="editAlumni.alamat" rows="3" placeholder="Alamat tinggal/domisili terbaru..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-650 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
