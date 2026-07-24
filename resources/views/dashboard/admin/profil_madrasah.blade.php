<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Profil Resmi Madrasah YATIA') }}
            </h2>
            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('kop-preview-modal').style.display='flex'" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Pratinjau Kop Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'yayasan' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="flex border-b border-slate-200 bg-white p-2 rounded-2xl shadow-sm gap-2">
                <button @click="activeTab = 'yayasan'" :class="activeTab === 'yayasan' ? 'bg-slate-900 text-white shadow' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 py-3 px-4 rounded-xl text-sm font-bold transition-all text-center">
                    Identitas & Kontak Yayasan
                </button>
                <button @click="activeTab = 'mts'" :class="activeTab === 'mts' ? 'bg-slate-900 text-white shadow' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 py-3 px-4 rounded-xl text-sm font-bold transition-all text-center">
                    Unit Madrasah Tsanawiyah (MTs)
                </button>
                <button @click="activeTab = 'ma'" :class="activeTab === 'ma' ? 'bg-slate-900 text-white shadow' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 py-3 px-4 rounded-xl text-sm font-bold transition-all text-center">
                    Unit Madrasah Aliyah (MA)
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- Tab Content: Yayasan -->
                <div x-show="activeTab === 'yayasan'" class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h3 class="text-lg font-bold text-slate-800">Yayasan & Pengaturan Umum</h3>
                        <p class="text-xs text-slate-500">Informasi payung hukum dan kontak legal YATIA.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Yayasan</label>
                            <input type="text" name="nama_yayasan" value="{{ $profile->nama_yayasan }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Sekolah</label>
                                <input type="text" name="status_sekolah" value="{{ $profile->status_sekolah }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Akreditasi</label>
                                <input type="text" name="akreditasi" value="{{ $profile->akreditasi }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ $profile->alamat }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="text" name="telepon" value="{{ $profile->telepon }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" value="{{ $profile->email }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Website Resmi</label>
                            <input type="text" name="website" value="{{ $profile->website }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mode Tampilan Sistem (Tema)</label>
                            <select name="theme_mode" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="light" {{ $profile->theme_mode === 'light' ? 'selected' : '' }}>Mode Terang (Light Mode)</option>
                                <option value="dark" {{ $profile->theme_mode === 'dark' ? 'selected' : '' }}>Mode Gelap (Dark Mode)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Visual Assets Section -->
                    <div class="border-t border-slate-100 pt-6 mt-6">
                        <h4 class="text-sm font-bold text-slate-800 mb-4">Berkas Visual & Identitas Laporan</h4>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                                <span class="block text-xs font-bold text-slate-600 uppercase">Logo Yayasan</span>
                                @if($profile->logo_path)
                                    <img src="{{ $profile->logo_path }}" class="h-20 mx-auto object-contain mb-2 rounded border bg-white p-1">
                                @else
                                    <div class="h-20 flex items-center justify-center text-slate-300 text-xs bg-slate-200 rounded">No Image</div>
                                @endif
                                <input type="file" name="logo" class="text-xs w-full">
                            </div>

                            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                                <span class="block text-xs font-bold text-slate-600 uppercase">Stempel Resmi</span>
                                @if($profile->stempel_path)
                                    <img src="{{ $profile->stempel_path }}" class="h-20 mx-auto object-contain mb-2 rounded border bg-white p-1">
                                @else
                                    <div class="h-20 flex items-center justify-center text-slate-300 text-xs bg-slate-200 rounded">No Image</div>
                                @endif
                                <input type="file" name="stempel" class="text-xs w-full">
                            </div>

                            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                                <span class="block text-xs font-bold text-slate-600 uppercase">Tanda Tangan Pimpinan</span>
                                @if($profile->tanda_tangan_path)
                                    <img src="{{ $profile->tanda_tangan_path }}" class="h-20 mx-auto object-contain mb-2 rounded border bg-white p-1">
                                @else
                                    <div class="h-20 flex items-center justify-center text-slate-300 text-xs bg-slate-200 rounded">No Image</div>
                                @endif
                                <input type="file" name="tanda_tangan" class="text-xs w-full">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: MTs -->
                <div x-show="activeTab === 'mts'" class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm space-y-6" style="display: none;">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h3 class="text-lg font-bold text-slate-800">Unit Madrasah Tsanawiyah (MTs)</h3>
                        <p class="text-xs text-slate-500">Data legal dan pimpinan untuk tingkat MTs.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Unit</label>
                            <input type="text" name="nama_mts" value="{{ $profile->nama_mts }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pimpinan / Kepala Sekolah</label>
                            <input type="text" name="pimpinan_mts" value="{{ $profile->pimpinan_mts }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NSM (Nomor Statistik Madrasah)</label>
                            <input type="text" name="nsm_mts" value="{{ $profile->nsm_mts }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIP Pimpinan</label>
                            <input type="text" name="nip_pimpinan_mts" value="{{ $profile->nip_pimpinan_mts }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NPSN</label>
                            <input type="text" name="npsn_mts" value="{{ $profile->npsn_mts }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Tab Content: MA -->
                <div x-show="activeTab === 'ma'" class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm space-y-6" style="display: none;">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h3 class="text-lg font-bold text-slate-800">Unit Madrasah Aliyah (MA)</h3>
                        <p class="text-xs text-slate-500">Data legal dan pimpinan untuk tingkat MA.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Unit</label>
                            <input type="text" name="nama_ma" value="{{ $profile->nama_ma }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pimpinan / Kepala Sekolah</label>
                            <input type="text" name="pimpinan_ma" value="{{ $profile->pimpinan_ma }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NSM (Nomor Statistik Madrasah)</label>
                            <input type="text" name="nsm_ma" value="{{ $profile->nsm_ma }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIP Pimpinan</label>
                            <input type="text" name="nip_pimpinan_ma" value="{{ $profile->nip_pimpinan_ma }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">NPSN</label>
                            <input type="text" name="npsn_ma" value="{{ $profile->npsn_ma }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Mandatory Audit Log Input -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 space-y-3">
                    <h4 class="text-sm font-bold text-amber-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Konfirmasi Perubahan Profil Legal
                    </h4>
                    <p class="text-xs text-amber-700 leading-relaxed">Sesuai standar operasional madrasah, Anda wajib mencantumkan alasan perubahan data profil madrasah sebelum menyimpannya.</p>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Alasan Perubahan (Minimal 5 karakter)</label>
                        <input type="text" name="alasan_perubahan" required placeholder="Contoh: Mengubah nama pimpinan baru dan akreditasi terbaru." class="w-full bg-white border border-amber-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors">
                        Simpan Profil Resmi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Letterhead Preview Modal -->
    <div id="kop-preview-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-900">Kop Surat Laporan Resmi</h3>
                <button onclick="document.getElementById('kop-preview-modal').style.display='none'" class="text-slate-400 hover:text-slate-650 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 space-y-6">
                <!-- MTs Kop -->
                <div class="p-6 border border-slate-200 rounded-2xl bg-white shadow-inner">
                    <span class="text-xs font-bold text-slate-400 block mb-4 uppercase">Kop Laporan Unit MTs</span>
                    <div class="border-b-4 border-double border-slate-950 pb-4 text-center relative flex items-center gap-4">
                        @if($profile->logo_path)
                            <img src="{{ $profile->logo_path }}" class="h-16 w-16 object-contain">
                        @else
                            <div class="h-16 w-16 bg-slate-100 rounded border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400">LOGO</div>
                        @endif
                        <div class="flex-1">
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-widest">{{ $profile->nama_yayasan }}</h2>
                            <h1 class="text-base font-black text-slate-900 uppercase tracking-wider">{{ $profile->nama_mts }}</h1>
                            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">NSM: {{ $profile->nsm_mts }} &nbsp;|&nbsp; NPSN: {{ $profile->npsn_mts }}</p>
                            <p class="text-[9px] text-slate-500 font-medium">{{ $profile->alamat }} &nbsp;|&nbsp; Telp: {{ $profile->telepon }} &nbsp;|&nbsp; Web: {{ $profile->website }}</p>
                        </div>
                    </div>
                </div>

                <!-- MA Kop -->
                <div class="p-6 border border-slate-200 rounded-2xl bg-white shadow-inner">
                    <span class="text-xs font-bold text-slate-400 block mb-4 uppercase">Kop Laporan Unit MA</span>
                    <div class="border-b-4 border-double border-slate-950 pb-4 text-center relative flex items-center gap-4">
                        @if($profile->logo_path)
                            <img src="{{ $profile->logo_path }}" class="h-16 w-16 object-contain">
                        @else
                            <div class="h-16 w-16 bg-slate-100 rounded border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400">LOGO</div>
                        @endif
                        <div class="flex-1">
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-widest">{{ $profile->nama_yayasan }}</h2>
                            <h1 class="text-base font-black text-slate-900 uppercase tracking-wider">{{ $profile->nama_ma }}</h1>
                            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">NSM: {{ $profile->nsm_ma }} &nbsp;|&nbsp; NPSN: {{ $profile->npsn_ma }}</p>
                            <p class="text-[9px] text-slate-500 font-medium">{{ $profile->alamat }} &nbsp;|&nbsp; Telp: {{ $profile->telepon }} &nbsp;|&nbsp; Web: {{ $profile->website }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button onclick="document.getElementById('kop-preview-modal').style.display='none'" class="px-5 py-2.5 bg-slate-800 text-white hover:bg-slate-700 font-bold rounded-xl transition-colors">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
