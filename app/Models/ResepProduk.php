<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepProduk extends Model
{
    protected $fillable = [
        'produk_id',
        'ukuran',
        'tipe',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function detailResepProduks()
    {
        return $this->hasMany(DetailResepProduk::class);
    }
}