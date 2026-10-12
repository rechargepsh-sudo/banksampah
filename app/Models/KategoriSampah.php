<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $table = 'kategori_sampah';

    protected $fillable = [
        'nama_kategori',
        'harga_per_kg',
        'satuan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function detailTransaksiSetor(): HasMany
    {
        return $this->hasMany(DetailTransaksiSetor::class, 'kategori_sampah_id');
    }
}
