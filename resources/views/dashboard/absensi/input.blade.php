<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Seksi Absensi Per Kelas</span>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ __('Pencatatan Kehadiran Kelas') }} {{ $kelas->nama_kelas }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('absensi.officer.index', $kelas->id) }}" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 font-semibold">
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Notification Toast -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-450 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($holidayMessage) && $holidayMessage)
            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-250 dark:border-amber-800 text-amber-800 dark:text-amber-400 rounded-2xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-semibold">{{ $holidayMessage }}</span>
            </div>
        @endif

        <!-- Submission Info bar -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Status Laporan Hari Ini</span>
                <div class="flex items-center gap-2 mt-1">
                    @if(!$submission)
                        <span class="px-3 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase">Belum Diisi</span>
                        <p class="text-xs text-slate-400">Silakan isi dan simpan draf absensi siswa.</p>
                    @elseif($submission->status === 'draft')
                        <span class="px-3 py-1 rounded bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase">Draf</span>
                        <p class="text-xs text-slate-400">Data tersimpan di server tapi belum dikirim ke Wali Kelas.</p>
                    @elseif($submission->status === 'pending')
                        <span class="px-3 py-1 rounded bg-blue-100 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 text-xs font-bold uppercase">Menunggu Verifikasi</span>
                        <p class="text-xs text-slate-400">Telah dikirim. Data terkunci dan tidak dapat diedit langsung.</p>
                    @elseif($submission->status === 'verified')
                        <span class="px-3 py-1 rounded bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase">Terverifikasi</span>
                        <p class="text-xs text-slate-400">Sudah disetujui Wali Kelas. Perubahan hanya bisa via Pengajuan Koreksi.</p>
                    @else
                        <span class="px-3 py-1 rounded bg-rose-100 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 text-xs font-bold uppercase">Ditolak / Kembalikan</span>
                        <p class="text-xs text-slate-400">Perlu perbaikan sesuai catatan Wali Kelas.</p>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @if(!$isLocked)
                    <button type="button" onclick="markAllPresent()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition-colors">
                        ✓ Tandai Semua Hadir
                    </button>
                    <button type="submit" form="form-attendance-bulk" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                        Simpan Draf
                    </button>
                    @if($submission && $submission->status === 'draft')
                        <form method="POST" action="{{ route('absensi.officer.submit') }}" onsubmit="return confirm('Kirim absensi hari ini ke Wali Kelas? Data akan dikunci.')">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $kelas->id }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                                Kirim ke Wali Kelas
                            </button>
                        </form>
                    @endif
                @else
                    <span class="text-xs text-slate-400 font-semibold italic flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Absensi Terkunci
                    </span>
                @endif
            </div>
        </div>

        <!-- Attendance Entry Table/List -->
        <form id="form-attendance-bulk" method="POST" action="{{ route('absensi.officer.draft') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="class_id" value="{{ $kelas->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="hidden md:block">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4 text-center">Status Kehadiran</th>
                                <th class="px-6 py-4">Bukti Lampiran (S/I/D)</th>
                                @if($isLocked)
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($siswas as $idx => $siswa)
                                @php
                                    $record = $existingRecords->get($siswa->id);
                                    $statusVal = $record ? $record->status : '';
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 text-xs text-slate-400 font-mono">{{ $idx + 1 }}</span>
                                            <div>
                                                <span class="block font-bold text-slate-900 dark:text-white">{{ $siswa->nama }}</span>
                                                <span class="block text-[10px] text-slate-400 font-mono">{{ $siswa->nis }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center items-center gap-2">
                                            @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', 'T' => 'Terlambat', 'D' => 'Disp'] as $k => $label)
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="attendance[{{ $siswa->id }}]" value="{{ $k }}" 
                                                        {{ $statusVal === $k ? 'checked' : '' }}
                                                        {{ $isLocked ? 'disabled' : '' }}
                                                        onchange="handleStatusChange({{ $siswa->id }}, '{{ $k }}')"
                                                        class="sr-only peer">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs border border-slate-200 dark:border-slate-800 transition-all peer-checked:border-transparent
                                                        {{ $k === 'H' ? 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 peer-checked:bg-emerald-500 peer-checked:text-white' : '' }}
                                                        {{ $k === 'S' ? 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/20 peer-checked:bg-blue-500 peer-checked:text-white' : '' }}
                                                        {{ $k === 'I' ? 'text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/20 peer-checked:bg-amber-500 peer-checked:text-white' : '' }}
                                                        {{ $k === 'A' ? 'text-rose-600 dark:text-rose-450 hover:bg-rose-50 dark:hover:bg-rose-950/20 peer-checked:bg-rose-500 peer-checked:text-white' : '' }}
                                                        {{ $k === 'T' ? 'text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/20 peer-checked:bg-purple-500 peer-checked:text-white' : '' }}
                                                        {{ $k === 'D' ? 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 peer-checked:bg-slate-700 peer-checked:text-white' : '' }}">
                                                        {{ $k }}
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div id="evidence-container-{{ $siswa->id }}" class="{{ in_array($statusVal, ['S', 'I', 'D']) ? '' : 'hidden' }}">
                                            @if(!$isLocked)
                                                <input type="file" name="evidence[{{ $siswa->id }}]" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-350 hover:file:bg-slate-200">
                                            @else
                                                @if($record && $record->evidence->count() > 0)
                                                    <a href="{{ asset('storage/' . $record->evidence->first()->file) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                                        📁 Lihat Bukti
                                                    </a>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Tanpa bukti</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    @if($isLocked)
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" onclick="openCorrectionModal({{ $record->id }}, '{{ $siswa->nama }}', '{{ $statusVal }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition-colors">
                                                Ajukan Koreksi
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile-friendly card list -->
                <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($siswas as $idx => $siswa)
                        @php
                            $record = $existingRecords->get($siswa->id);
                            $statusVal = $record ? $record->status : '';
                        @endphp
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 text-xs text-slate-400 font-mono">{{ $idx + 1 }}</span>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $siswa->nama }}</span>
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $siswa->nis }}</span>
                                    </div>
                                </div>
                                @if($isLocked)
                                    <button type="button" onclick="openCorrectionModal({{ $record->id }}, '{{ $siswa->nama }}', '{{ $statusVal }}')" class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-bold">
                                        Koreksi
                                    </button>
                                @endif
                            </div>

                            <div class="flex justify-between items-center gap-2">
                                @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', 'T' => 'Terl', 'D' => 'Disp'] as $k => $label)
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" name="attendance[{{ $siswa->id }}]_mobile" value="{{ $k }}"
                                            {{ $statusVal === $k ? 'checked' : '' }}
                                            {{ $isLocked ? 'disabled' : '' }}
                                            onchange="syncMobileToDesktop({{ $siswa->id }}, '{{ $k }}')"
                                            class="sr-only peer">
                                        <div class="py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 font-bold text-xs transition-all peer-checked:border-transparent
                                            {{ $k === 'H' ? 'text-emerald-600 peer-checked:bg-emerald-500 peer-checked:text-white' : '' }}
                                            {{ $k === 'S' ? 'text-blue-600 peer-checked:bg-blue-500 peer-checked:text-white' : '' }}
                                            {{ $k === 'I' ? 'text-amber-600 peer-checked:bg-amber-500 peer-checked:text-white' : '' }}
                                            {{ $k === 'A' ? 'text-rose-600 peer-checked:bg-rose-500 peer-checked:text-white' : '' }}
                                            {{ $k === 'T' ? 'text-purple-600 peer-checked:bg-purple-500 peer-checked:text-white' : '' }}
                                            {{ $k === 'D' ? 'text-slate-600 peer-checked:bg-slate-700 peer-checked:text-white' : '' }}">
                                            {{ $k }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div id="evidence-container-{{ $siswa->id }}-mobile" class="{{ in_array($statusVal, ['S', 'I', 'D']) ? '' : 'hidden' }}">
                                @if(!$isLocked)
                                    <span class="block text-[10px] text-slate-400 font-bold mb-1">UNGGAH BUKTI:</span>
                                    <input type="file" name="evidence[{{ $siswa->id }}]_mobile" onchange="syncMobileFile({{ $siswa->id }}, this)" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100">
                                @else
                                    @if($record && $record->evidence->count() > 0)
                                        <a href="{{ asset('storage/' . $record->evidence->first()->file) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                            📁 Lihat Bukti
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <!-- Official Correction Modal -->
    <div id="modal-correction" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-850 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                <div>
                    <h4 class="font-bold text-slate-950 dark:text-white">Pengajuan Koreksi Resmi</h4>
                    <span class="text-xs text-slate-400">Perubahan data yang sudah dikunci</span>
                </div>
                <button type="button" onclick="closeModal('modal-correction')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
            </div>
            <form action="{{ route('absensi.officer.correction') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="attendance_id" id="correction-id">
                
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nama Siswa</label>
                    <input type="text" id="correction-name" disabled class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Lama</label>
                        <input type="text" id="correction-old-status" disabled class="w-full bg-slate-100 dark:bg-slate-800 border-0 rounded-xl px-4 py-2.5 text-sm font-mono text-center font-bold text-slate-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Baru</label>
                        <select name="new_status" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 font-semibold">
                            <option value="H">Hadir (H)</option>
                            <option value="S">Sakit (S)</option>
                            <option value="I">Izin (I)</option>
                            <option value="A">Alpa (A)</option>
                            <option value="T">Terlambat (T)</option>
                            <option value="D">Dispensasi (D)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Alasan Perubahan</label>
                    <textarea name="reason" required placeholder="Tuliskan alasan koreksi data..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 resize-none" rows="3"></textarea>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bukti Dukung / Surat (Opsional)</label>
                    <input type="file" name="evidence" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 hover:file:bg-slate-200">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-correction')" class="flex-1 py-3 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350 hover:bg-slate-50 rounded-xl text-xs font-bold transition-colors">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg transition-colors">Ajukan Koreksi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function handleStatusChange(studentId, status) {
            const container = document.getElementById(`evidence-container-${studentId}`);
            if (['S', 'I', 'D'].includes(status)) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }

            // Sync with mobile radios
            const mobileRadio = document.querySelector(`input[name="attendance[${studentId}]_mobile"][value="${status}"]`);
            if (mobileRadio) mobileRadio.checked = true;
        }

        function syncMobileToDesktop(studentId, status) {
            const desktopRadio = document.querySelector(`input[name="attendance[${studentId}]"][value="${status}"]`);
            if (desktopRadio) {
                desktopRadio.checked = true;
                handleStatusChange(studentId, status);
            }
            
            // Toggle evidence container on mobile
            const containerMobile = document.getElementById(`evidence-container-${studentId}-mobile`);
            if (['S', 'I', 'D'].includes(status)) {
                containerMobile.classList.remove('hidden');
            } else {
                containerMobile.classList.add('hidden');
            }
        }

        function syncMobileFile(studentId, input) {
            // Transfer files to desktop counterpart if needed, but since Laravel retrieves from evidence[student_id]
            // We can rename the name attribute on change to make sure it submits under correct array
            const desktopInput = document.querySelector(`input[name="evidence[${studentId}]"]`);
            if (desktopInput && input.files.length > 0) {
                desktopInput.files = input.files;
            }
        }

        function markAllPresent() {
            @foreach($siswas as $siswa)
                document.querySelector('input[name="attendance[{{ $siswa->id }}]"][value="H"]').checked = true;
                const mobileRadio = document.querySelector('input[name="attendance[{{ $siswa->id }}]_mobile"][value="H"]');
                if (mobileRadio) mobileRadio.checked = true;
                handleStatusChange({{ $siswa->id }}, 'H');
            @endforeach
        }

        function openCorrectionModal(id, name, currentStatus) {
            document.getElementById('correction-id').value = id;
            document.getElementById('correction-name').value = name;
            document.getElementById('correction-old-status').value = currentStatus;
            openModal('modal-correction');
        }
    </script>
</x-app-layout>
