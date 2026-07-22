<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __("Perbarui data pribadi, username, dan foto profil Anda.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="flex items-center gap-6">
            <div class="relative w-20 h-20 shrink-0">
                @if ($user->photo_profile)
                    <img id="avatar-preview" src="{{ asset('storage/' . $user->photo_profile) }}" class="w-full h-full object-cover rounded-2xl border-2 border-slate-200 dark:border-slate-800 shadow-sm" alt="Profile Photo">
                @else
                    <div id="avatar-preview-placeholder" class="w-full h-full rounded-2xl bg-gradient-to-tr from-emerald-400 to-teal-500 text-white font-black text-3xl font-display flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <label for="photo_profile" class="px-4 py-2.5 bg-emerald-650 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl cursor-pointer shadow-sm transition-colors block">
                    Unggah Foto Profil
                </label>
                <input id="photo_profile" name="photo_profile" type="file" class="hidden" accept="image/*" onchange="
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const placeholder = document.getElementById('avatar-preview-placeholder');
                            const img = document.getElementById('avatar-preview');
                            if (img) {
                                img.src = e.target.result;
                            } else {
                                const newImg = document.createElement('img');
                                newImg.id = 'avatar-preview';
                                newImg.src = e.target.result;
                                newImg.className = 'w-full h-full object-cover rounded-2xl border-2 border-slate-200 dark:border-slate-800 shadow-sm';
                                if (placeholder) {
                                    placeholder.parentNode.replaceChild(newImg, placeholder);
                                }
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                ">
                <p class="text-[10px] text-slate-400 mt-1.5">Maksimal 2MB. Format: JPG, JPEG, PNG.</p>
                <x-input-error class="mt-2" :messages="$errors->get('photo_profile')" />
            </div>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-emerald-650 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 font-semibold"
                >{{ __('Berhasil disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
