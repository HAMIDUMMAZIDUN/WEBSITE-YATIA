<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Admin</span>
                <h2 class="font-bold text-2xl text-slate-900 dark:text-slate-100 leading-tight">Manajemen Absensi</h2>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('modal-assign').style.display='flex'"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tugaskan Petugas
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Active Year Info --}}
            @if($activeYear)
            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-3 flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-xs text-slate-500 dark:text-slate-400">Tahun Ajaran Aktif: <strong class="text-slate-800 dark:text-white">{{ $activeYear->nama_tahun_ajaran }}</strong></span>
                <span class="ml-auto text-xs text-slate-400">Rekap absensi: {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span>
            </div>
            @else
            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-400 rounded-2xl text-sm font-semibold">
                ⚠️ Belum ada Tahun Ajaran yang aktif. Aktivasi di menu Pengaturan → Tahun Ajaran terlebih dahulu.
            </div>
            @endif

            {{-- Class Status Summary --}}
            <div>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Status Absensi Hari Ini per Kelas</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    @foreach($classSummary as $item)
                        @php
                            $sub = $item['submission'];
                            $officers = $item['officers'];
                            $pct = $item['total'] > 0 ? round(($item['filled'] / $item['total']) * 100) : 0;
                            $statusColor = match($sub?->status ?? '') {
                                'verified'  => 'emerald',
                                'pending'   => 'blue',
                                'draft'     => 'amber',
                                'rejected'  => 'rose',
                                default     => 'slate',
                            };
                            $statusLabel = match($sub?->status ?? '') {
                                'verified'  => 'Terverifikasi',
                                'pending'   => 'Menunggu Verif.',
                                'draft'     => 'Draf',
                                'rejected'  => 'Dikembalikan',
                                default     => 'Belum Diisi',
                            };
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $item['kelas']->nama_kelas }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $statusColor === 'emerald' ? 'bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400' : '' }}
                                    {{ $statusColor === 'blue'    ? 'bg-blue-100 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400' : '' }}
                                    {{ $statusColor === 'amber'   ? 'bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400' : '' }}
                                    {{ $statusColor === 'rose'    ? 'bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400' : '' }}
                                    {{ $statusColor === 'slate'   ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' : '' }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            {{-- Progress bar --}}
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mb-2">
                                <div class="h-1.5 rounded-full bg-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>{{ $item['filled'] }}/{{ $item['total'] }} siswa</span>
                                <span>{{ $pct }}%</span>
                            </div>
                            @if($officers->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($officers as $off)
                                        <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-medium">
                                            {{ $off->user?->name ?? '—' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-2 text-[10px] text-rose-400 italic">Belum ada petugas</p>
                            @endif
                            @if($sub)
                                <a href="{{ route('absensi.officer.index', $item['kelas']->id) }}" class="mt-2 flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                                    Lihat Detail →
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Assignment Table --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Daftar Penugasan Petugas Absensi</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Semua siswa yang ditugaskan sebagai petugas absensi kelas</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-xs font-bold">
                        {{ $assignments->count() }} penugasan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-4">Siswa (Petugas)</th>
                                <th class="px-5 py-4">Kelas Bertugas</th>
                                <th class="px-5 py-4">Tahun Ajaran</th>
                                <th class="px-5 py-4 text-center">Status</th>
                                <th class="px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @forelse($assignments as $assignment)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($assignment->user?->photo_profile)
                                                <img src="{{ asset('storage/' . $assignment->user->photo_profile) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ strtoupper(substr($assignment->user?->name ?? '?', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="block font-semibold text-slate-900 dark:text-white">{{ $assignment->user?->name ?? '—' }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $assignment->user?->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-800 dark:text-white">{{ $assignment->class?->nama_kelas ?? '—' }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                        {{ $assignment->academicYear?->nama_tahun_ajaran ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($assignment->active)
                                            <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold">Aktif</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full text-xs font-bold">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Toggle Active --}}
                                            <form action="{{ route('admin.absensi.toggle', $assignment->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors
                                                    {{ $assignment->active ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100' : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100' }}">
                                                    {{ $assignment->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            {{-- View Input --}}
                                            @if($assignment->active)
                                            <a href="{{ route('absensi.officer.index', $assignment->class_id) }}"
                                                class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg hover:bg-blue-100 transition-colors">
                                                Input
                                            </a>
                                            @endif
                                            {{-- Delete --}}
                                            <form action="{{ route('admin.absensi.revoke', $assignment->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus permanen penugasan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-lg hover:bg-rose-100 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-sm font-semibold">Belum ada penugasan petugas absensi</p>
                                        <p class="text-xs mt-1">Klik "Tugaskan Petugas" untuk menambahkan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Tugaskan Petugas --}}
    <div id="modal-assign" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">Tugaskan Petugas Absensi</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih siswa dan kelas bertugas</p>
                </div>
                <button onclick="document.getElementById('modal-assign').style.display='none'" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
            </div>
            <form action="{{ route('admin.absensi.assign') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Siswa (Akun)</label>
                    <select name="user_id" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 dark:text-white">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswaUsers as $su)
                            <option value="{{ $su->id }}">{{ $su->name }} ({{ $su->siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas' }})</option>
                        @endforeach
                    </select>
                    @if($siswaUsers->isEmpty())
                        <p class="text-[10px] text-amber-500 mt-1">Tidak ada siswa dengan akun aktif ditemukan.</p>
                    @endif
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Kelas Bertugas</label>
                    <select name="class_id" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 dark:text-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasAll as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 text-xs text-amber-700 dark:text-amber-400">
                    💡 Siswa bisa ditugaskan ke kelas yang berbeda dari kelas asal mereka. Petugas hanya bisa mengisi absensi untuk kelas yang ditugaskan.
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-assign').style.display='none'"
                        class="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl text-xs font-bold transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg transition-colors">
                        Tugaskan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
