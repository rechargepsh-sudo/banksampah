<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksiSetor extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi_setor';

    protected $fillable = [
        'transaksi_setor_id',
        'kategori_sampah_id',
        'berat',
        'harga_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'berat' => 'decimal:2',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaksiSetor(): BelongsTo
    {
        return $this->belongsTo(TransaksiSetor::class, 'transaksi_setor_id');
    }

    public function kategoriSampah(): BelongsTo
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_sampah_id');
    }
}
