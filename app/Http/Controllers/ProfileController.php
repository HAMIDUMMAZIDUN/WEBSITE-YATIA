<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // For students, also pass their Wali Kelas info for the read-only view
        $waliKelas = null;
        if ($user->isSiswa() && $user->siswa && $user->siswa->kelas) {
            $kelas = $user->siswa->kelas;
            if ($kelas->waliGuru) {
                $waliKelas = $kelas->waliGuru;
            }
        }

        return view('profile.edit', [
            'user'      => $user,
            'waliKelas' => $waliKelas,
        ]);
    }

    /**
     * Update the user's profile information.
     * Blocked for students — they must go through their homeroom teacher.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        if ($request->hasFile('photo_profile')) {
            if ($user->photo_profile) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_profile);
            }
            $path = $request->file('photo_profile')->store('avatars', 'public');
            $validatedData['photo_profile'] = $path;
        }

        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     * Blocked for students.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->isSiswa()) {
            abort(403, 'Siswa tidak dapat menghapus akun.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
