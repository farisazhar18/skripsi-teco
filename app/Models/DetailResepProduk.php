<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailResepProduk extends Model
{
    protected $fillable = [
        'resep_produk_id',
        'bahan_baku_id',
        'jumlah',
    ];

    public function resepProduk()
    {
        return $this->belongsTo(ResepProduk::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}