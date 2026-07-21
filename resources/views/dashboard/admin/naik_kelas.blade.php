<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
            {{ __('Kenaikan Kelas & Kelulusan Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Warning Card -->
            <div class="p-6 bg-amber-50 border border-amber-250 rounded-2xl flex items-start gap-4">
                <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="space-y-1">
                    <h4 class="font-bold text-amber-800 text-sm">Penting Sebelum Melakukan Kenaikan Kelas / Kelulusan</h4>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Proses ini akan memperbarui status kelas/kelulusan seluruh siswa aktif di kelas asal secara permanen. Pastikan Anda telah melakukan backup data akademik dan menyelesaikan seluruh penilaian raport sebelum melanjutkan.
                    </p>
                </div>
            </div>

            <!-- Promotion Form Wizard -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm max-w-2xl mx-auto space-y-6">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Formulir Kenaikan & Kelulusan</h3>
                    <p class="text-xs text-slate-500">Isi detail di bawah untuk memproses siswa.</p>
                </div>

                <form action="{{ route('admin.naik_kelas.store') }}" method="POST" x-data="{ action: 'promoted' }" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Asal</label>
                        <select name="source_class_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="">Pilih Kelas Asal...</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->siswas_count }} Siswa Aktif)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tindakan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label :class="action === 'promoted' ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-200'" class="flex items-center justify-center p-4 border rounded-xl cursor-pointer transition-all">
                                <input type="radio" name="action" value="promoted" x-model="action" class="sr-only">
                                <span class="text-xs font-bold text-slate-750">Kenaikan Kelas</span>
                            </label>
                            <label :class="action === 'graduated' ? 'border-emerald-500 bg-emerald-50/20' : 'border-slate-200'" class="flex items-center justify-center p-4 border rounded-xl cursor-pointer transition-all">
                                <input type="radio" name="action" value="graduated" x-model="action" class="sr-only">
                                <span class="text-xs font-bold text-slate-750">Kelulusan</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-1.5" x-show="action === 'promoted'">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Tujuan (Kenaikan)</label>
                        <select name="target_class_id" :required="action === 'promoted'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="">Pilih Kelas Tujuan...</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan Keputusan</label>
                        <textarea name="catatan" rows="3" placeholder="Rekomendasi/Catatan rapat kelulusan atau kenaikan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                            Proses Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
