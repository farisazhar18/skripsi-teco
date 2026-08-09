<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $fillable = [
        'outlet',
        'nama_bahan',
        'kategori',
        'satuan',
        'stok',
        'stok_minimum',
    ];

    public function pembelians()
    {
    return $this->hasMany(Pembelian::class);
    }

    public function detailResepProduks()
    {
    return $this->hasMany(DetailResepProduk::class);
    }
}