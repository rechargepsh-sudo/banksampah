<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke profil Nasabah (jika role = nasabah)
     */
    public function nasabah(): HasOne
    {
        return $this->hasOne(Nasabah::class, 'user_id');
    }

    /**
     * Relasi transaksi setor yang dicatat oleh user (kader)
     */
    public function transaksiSetorDicatat(): HasMany
    {
        return $this->hasMany(TransaksiSetor::class, 'kader_id');
    }

    /**
     * Relasi transaksi tarik dana yang divalidasi oleh user (kader/admin)
     */
    public function penarikanDiproses(): HasMany
    {
        return $this->hasMany(TransaksiTarikDana::class, 'diproses_oleh');
    }

    /**
     * Relasi nasabah yang didaftarkan oleh kader
     */
    public function nasabahDidaftarkan(): HasMany
    {
        return $this->hasMany(Nasabah::class, 'created_by');
    }
}
