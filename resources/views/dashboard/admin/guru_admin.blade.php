<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Data Guru & Staff Pendidik') }}
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
            editGuru: { id: '', name: '', email: '', role_id: '', jenis_kelamin: 'L', nik_nuptk: '', tempat_lahir: '', tanggal_lahir: '', pendidikan: '', jtm: 0 },
            searchQuery: '',
            genderFilter: '',
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
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-850 rounded-xl">
                    <div class="font-bold text-xs uppercase tracking-wider text-rose-800 mb-2">Terjadi Kesalahan Validasi:</div>
                    <ul class="list-disc pl-5 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Content Grid -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Guru Table List -->
                <div class="lg:col-span-9 space-y-8">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">Daftar Guru / Tenaga Pendidik</h3>
                                <p class="text-xs text-slate-500">Kelola informasi detail guru, status keaktifan, dan jam tatap muka.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="relative">
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama/NIK..." class="pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-emerald-500 w-44">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <select x-model="genderFilter" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-emerald-500">
                                    <option value="">Semua Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <button type="button" @click="window.bulkDeleteSelected('.item-checkbox', '{{ csrf_token() }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse text-slate-650">
                                <thead class="bg-slate-50/50 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-4 py-3 w-10">
                                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-2 py-3 w-8 text-center">No</th>
                                        <th class="px-4 py-3">Foto</th>
                                        <th class="px-4 py-3">NIK / NUPTK</th>
                                        <th class="px-4 py-3">Nama</th>
                                        <th class="px-2 py-3 text-center">L/P</th>
                                        <th class="px-4 py-3">TTL</th>
                                        <th class="px-4 py-3">Pendidikan</th>
                                        <th class="px-4 py-3">Password</th>
                                        <th class="px-4 py-3 text-center">Wali Kelas</th>
                                        <th class="px-2 py-3 text-center">JTM</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @php $no = 1; @endphp
                                    @forelse($gurus as $gr)
                                        <tr class="hover:bg-slate-50/30 transition-colors {{ !$gr->is_aktif ? 'opacity-65' : '' }}"
                                            x-show="(searchQuery === '' || '{{ strtolower(addslashes($gr->nama)) }}'.includes(searchQuery.toLowerCase()) || '{{ $gr->nik_nuptk }}'.includes(searchQuery)) && (genderFilter === '' || '{{ $gr->jenis_kelamin }}' === genderFilter)">
                                            <td class="px-4 py-3.5">
                                                <input type="checkbox" class="item-checkbox rounded border-slate-300 text-emerald-650 focus:ring-emerald-500" data-delete-url="{{ route('admin.guru.delete', $gr->id) }}">
                                            </td>
                                            <td class="px-2 py-3.5 text-center text-slate-400 font-semibold">{{ $no++ }}</td>
                                            <td class="px-4 py-3.5">
                                                @if($gr->foto)
                                                    <img src="{{ $gr->foto }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                                                        {{ substr($gr->nama, 0, 2) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5 font-mono text-slate-700">{{ $gr->nik_nuptk ?? '-' }}</td>
                                            <td class="px-4 py-3.5">
                                                <div class="font-bold text-slate-900">{{ $gr->nama }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $gr->nip }}</div>
                                            </td>
                                            <td class="px-2 py-3.5 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $gr->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-750 border border-blue-200' : 'bg-rose-50 text-rose-750 border border-rose-200' }}">
                                                    {{ $gr->jenis_kelamin }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <div class="font-semibold text-slate-700">{{ $gr->tempat_lahir ?? '-' }}</div>
                                                <div class="text-[10px] text-slate-400">
                                                    {{ $gr->tanggal_lahir ? \Carbon\Carbon::parse($gr->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 text-slate-750 font-medium">{{ $gr->pendidikan ?? '-' }}</td>
                                            <td class="px-4 py-3.5 font-mono text-slate-500 text-[10px]">{{ $gr->password_plain ?? '-' }}</td>
                                            <td class="px-4 py-3.5 text-center">
                                                @if($gr->kelasWali->isNotEmpty())
                                                    @foreach($gr->kelasWali as $k)
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            {{ $k->nama_kelas }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-slate-300 font-bold">-</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-3.5 text-center font-bold text-slate-700">{{ $gr->jtm ?? 0 }}</td>
                                            <td class="px-4 py-3.5 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="
                                                        editGuru = {
                                                            id: '{{ $gr->id }}',
                                                            name: '{{ addslashes($gr->nama) }}',
                                                            email: '{{ $gr->user->email ?? '' }}',
                                                            role_id: '{{ $gr->user->role_id ?? '' }}',
                                                            jenis_kelamin: '{{ $gr->jenis_kelamin }}',
                                                            nik_nuptk: '{{ $gr->nik_nuptk }}',
                                                            tempat_lahir: '{{ addslashes($gr->tempat_lahir) }}',
                                                            tanggal_lahir: '{{ $gr->tanggal_lahir }}',
                                                            pendidikan: '{{ $gr->pendidikan }}',
                                                            jtm: '{{ $gr->jtm }}'
                                                        };
                                                        editModalOpen = true;
                                                    " class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-250 rounded-lg font-bold text-[10px] transition-all">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('admin.guru.toggle_status', $gr->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 {{ $gr->is_aktif ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-250' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-250' }} rounded-lg font-bold text-[10px] transition-all">
                                                            {{ $gr->is_aktif ? 'Non Aktif' : 'Aktifkan' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="py-8 text-center text-slate-400 text-sm font-semibold">Tidak ada data guru ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tambah Guru Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-slate-800 text-sm">Tambah Data Guru</h3>
                            <p class="text-[11px] text-slate-400">Pendaftaran akun guru dan melengkapi berkas biodata.</p>
                        </div>
                        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Ahmad Ariyadi, S.Pd.I." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Email (Gmail)</label>
                                <input type="email" name="email" required placeholder="contoh@gmail.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Password Awal</label>
                                <input type="text" name="password" required placeholder="Min 6 karakter / NIK" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hak Akses / Peran</label>
                                    <select name="role_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                        @foreach($roles as $rl)
                                            <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                        <option value="L">Laki-laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIK / NUPTK</label>
                                <input type="text" name="nik_nuptk" placeholder="16 Digit NIK" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" placeholder="BANDUNG" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pendidikan</label>
                                    <select name="pendidikan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                        <option value="Sarjana (S1)">Sarjana (S1)</option>
                                        <option value="Magister (S2)">Magister (S2)</option>
                                        <option value="Diploma (D4)">Diploma (D4)</option>
                                        <option value="Doktor (S3)">Doktor (S3)</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">JTM (Jam mengajar)</label>
                                    <input type="number" name="jtm" value="0" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Foto Profil</label>
                                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-250 transition-colors">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-lg transition-colors">
                                Simpan Data Guru
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Guru Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-base text-slate-900">Ubah Biodata Guru</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="'/admin/guru/' + editGuru.id" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editGuru.name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Email (Gmail)</label>
                        <input type="email" name="email" x-model="editGuru.email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Password (Kosongkan jika tidak diubah)</label>
                        <input type="text" name="password" placeholder="Ketik password baru jika ingin mengubah" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hak Akses / Peran</label>
                            <select name="role_id" x-model="editGuru.role_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                @foreach($roles as $rl)
                                    <option value="{{ $rl->id }}">{{ $rl->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                            <select name="jenis_kelamin" x-model="editGuru.jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIK / NUPTK</label>
                        <input type="text" name="nik_nuptk" x-model="editGuru.nik_nuptk" placeholder="16 Digit NIK" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" x-model="editGuru.tempat_lahir" placeholder="BANDUNG" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" x-model="editGuru.tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pendidikan</label>
                            <select name="pendidikan" x-model="editGuru.pendidikan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-emerald-500">
                                <option value="Sarjana (S1)">Sarjana (S1)</option>
                                <option value="Magister (S2)">Magister (S2)</option>
                                <option value="Diploma (D4)">Diploma (D4)</option>
                                <option value="Doktor (S3)">Doktor (S3)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">JTM (Jam mengajar)</label>
                            <input type="number" name="jtm" x-model="editGuru.jtm" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Foto Profil Baru (Opsional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-250 transition-colors">
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
