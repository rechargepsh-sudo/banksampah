<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabah';

    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'no_hp',
        'nomor_rw',
        'nomor_rt',
        'alamat',
        'saldo_aktif',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'saldo_aktif' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kaderPendaftar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transaksiSetor(): HasMany
    {
        return $this->hasMany(TransaksiSetor::class, 'nasabah_id');
    }

    public function transaksiTarikDana(): HasMany
    {
        return $this->hasMany(TransaksiTarikDana::class, 'nasabah_id');
    }

    public function mutasiSaldo(): HasMany
    {
        return $this->hasMany(MutasiSaldo::class, 'nasabah_id');
    }
}
