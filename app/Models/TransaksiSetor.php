<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiSetor extends Model
{
    use HasFactory;

    protected $table = 'transaksi_setor';

    protected $fillable = [
        'kode_transaksi',
        'nasabah_id',
        'kader_id',
        'total_berat',
        'total_nominal',
        'tanggal_setor',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_berat' => 'decimal:2',
            'total_nominal' => 'decimal:2',
            'tanggal_setor' => 'datetime',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kader_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksiSetor::class, 'transaksi_setor_id');
    }
}
