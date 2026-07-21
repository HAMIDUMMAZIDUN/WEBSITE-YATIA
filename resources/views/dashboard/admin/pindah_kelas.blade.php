<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Mutasi / Pindah Kelas Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        sourceClass: '', 
        targetClass: '', 
        students: [
            @foreach($siswas as $siswa)
            { id: '{{ $siswa->id }}', nama: '{{ addslashes($siswa->nama) }}', nis: '{{ $siswa->nis }}', classId: '{{ $siswa->kelas_id }}', selected: false },
            @endforeach
        ],
        selectAll: false,
        get filteredStudents() {
            return this.students.filter(s => s.classId == this.sourceClass);
        },
        toggleAll() {
            this.filteredStudents.forEach(s => s.selected = this.selectAll);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.pindah_kelas.store') }}" method="POST" class="grid lg:grid-cols-3 gap-8">
                @csrf
                <!-- Setup Panel -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6 h-fit">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">1. Konfigurasi Mutasi</h3>
                        <p class="text-xs text-slate-500">Tentukan kelas asal dan tujuan perpindahan.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Asal</label>
                            <select name="source_class_id" x-model="sourceClass" @change="selectAll = false; students.forEach(s => s.selected = false)" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="">Pilih Kelas Asal...</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Tujuan</label>
                            <select name="target_class_id" x-model="targetClass" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                                <option value="">Pilih Kelas Tujuan...</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" x-show="'{{ $k->id }}' !== sourceClass">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alasan / Catatan</label>
                            <textarea name="catatan" rows="3" placeholder="Alasan pemindahan kelas..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                        </div>

                        <button type="submit" :disabled="!sourceClass || !targetClass" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg transition-colors disabled:opacity-50">
                            Proses Pemindahan
                        </button>
                    </div>
                </div>

                <!-- Students Selection List -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">2. Pilih Siswa</h3>
                            <p class="text-xs text-slate-500">Pilih satu atau beberapa siswa yang akan dipindahkan.</p>
                        </div>
                        <template x-if="sourceClass">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" id="select-all-checkbox" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <label for="select-all-checkbox" class="text-xs font-bold text-slate-650 cursor-pointer">Pilih Semua</label>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                        <!-- Empty State -->
                        <div x-show="!sourceClass" class="text-center py-12 text-slate-400 text-sm">
                            Silakan pilih kelas asal terlebih dahulu.
                        </div>

                        <!-- Active State -->
                        <template x-show="sourceClass" x-for="s in filteredStudents" :key="s.id">
                            <label :class="s.selected ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-150 hover:bg-slate-50'" class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition-all">
                                <input type="checkbox" name="siswa_ids[]" :value="s.id" x-model="s.selected" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <div class="font-bold text-slate-800" x-text="s.nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="'NIS: ' + s.nis"></div>
                                </div>
                            </label>
                        </template>

                        <div x-show="sourceClass && filteredStudents.length === 0" class="text-center py-12 text-slate-400 text-sm">
                            Tidak ada siswa aktif di kelas ini.
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
