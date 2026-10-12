<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiSaldo extends Model
{
    use HasFactory;

    protected $table = 'mutasi_saldo';

    protected $fillable = [
        'nasabah_id',
        'jenis_mutasi',
        'nominal',
        'saldo_sebelum',
        'saldo_sesudah',
        'referensi_id',
        'tipe_referensi',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'saldo_sebelum' => 'decimal:2',
            'saldo_sesudah' => 'decimal:2',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
}
