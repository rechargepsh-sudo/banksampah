<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiTarikDana extends Model
{
    use HasFactory;

    protected $table = 'transaksi_tarik_dana';

    protected $fillable = [
        'kode_penarikan',
        'nasabah_id',
        'jumlah_penarikan',
        'metode_penarikan',
        'status',
        'diproses_oleh',
        'tanggal_tarik',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_penarikan' => 'decimal:2',
            'tanggal_tarik' => 'datetime',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
