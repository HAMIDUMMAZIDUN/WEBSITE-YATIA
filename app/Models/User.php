<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
        'username',
        'email',
        'password',
        'photo_profile',
        'password_plain',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }


    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isGuru()
    {
        return $this->hasRole('guru') || $this->hasRole('wali_kelas') || $this->hasRole('wali_kelas_guru');
    }

    public function isWaliKelas()
    {
        return $this->hasRole('wali_kelas') || $this->hasRole('wali_kelas_guru');
    }

    public function isSiswa()
    {
        return $this->hasRole('absensi');
    }

    public function isAbsensi()
    {
        return $this->hasRole('absensi');
    }

    public function isGuruBk()
    {
        return $this->hasRole('guru_bk');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    protected static function booted()
    {
        // Ensure every new account starts as active
        static::creating(function ($user) {
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
        });

        static::saved(function ($user) {
            if ($user->role_id) {
                $role = \App\Models\Role::find($user->role_id);
                if ($role) {
                    if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
                        if (!\App\Models\Guru::where('user_id', $user->id)->exists()) {
                            \App\Models\Guru::create([
                                'user_id'        => $user->id,
                                'nama'           => $user->name,
                                'nip'            => 'NIP-' . time() . '-' . rand(10, 99),
                                'jenis_kelamin'  => 'L',
                                'password_plain' => $user->password_plain ?? 'password123',
                                'is_aktif'       => true,
                             ]);
                        } else {
                            $user->guru->update(['nama' => $user->name]);
                        }
                    } elseif ($role->name === 'absensi') {
                        if (!\App\Models\Siswa::where('user_id', $user->id)->exists()) {
                            $defaultKelas = \App\Models\Kelas::first();
                            \App\Models\Siswa::create([
                                'user_id'       => $user->id,
                                'nama'          => $user->name,
                                'nis'           => 'NIS-' . time() . '-' . rand(10, 99),
                                'jenis_kelamin' => 'L',
                                'kelas_id'      => $defaultKelas ? $defaultKelas->id : null,
                                'status'        => 'Aktif',
                            ]);
                        } else {
                            $user->siswa->update(['nama' => $user->name]);
                        }
                    }
                }
            }
        });
    }
}
