<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Profil Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(Auth::user()->isSiswa())
                {{-- ===== READ-ONLY VIEW FOR STUDENTS ===== --}}

                {{-- Profile Info Card (read-only) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-base text-slate-900">Informasi Profil</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Data profil bersifat hanya-baca. Perubahan dilakukan oleh administrator.</p>
                    </div>
                    <div class="px-8 py-6 space-y-5">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center font-black text-2xl font-display shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xl font-bold text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-slate-400">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium">{{ Auth::user()->name }}</div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email Akun</label>
                                <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium">{{ Auth::user()->email }}</div>
                            </div>
                            @if(Auth::user()->siswa)
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">NIS</label>
                                    <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 font-mono">{{ Auth::user()->siswa->nis ?? '-' }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</label>
                                    <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium">{{ Auth::user()->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Password Change Notice --}}
                <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 border-b border-amber-100 bg-amber-50/60 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Ingin Mengubah Password?</h3>
                            <p class="text-sm text-slate-600 mt-1">Untuk menjaga keamanan, siswa tidak dapat mengubah password secara mandiri. Silakan hubungi <strong>Wali Kelas</strong> Anda untuk melakukan permintaan perubahan password.</p>
                        </div>
                    </div>
                    <div class="px-8 py-6">
                        @if($waliKelas)
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                                    {{ substr($waliKelas->nama, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $waliKelas->nama }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Wali Kelas Anda</p>
                                </div>
                                <span class="ml-auto px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold text-xs uppercase tracking-wider">Wali Kelas</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-3">
                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Temui Wali Kelas Anda secara langsung atau melalui ketua kelas untuk meminta reset password.
                            </p>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm text-slate-500">Wali Kelas belum ditetapkan. Silakan hubungi <strong>Administrator Sekolah</strong> untuk permintaan perubahan password.</p>
                            </div>
                        @endif
                    </div>
                </div>

            @else
                {{-- ===== EDITABLE FORMS FOR OTHER ROLES ===== --}}

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
