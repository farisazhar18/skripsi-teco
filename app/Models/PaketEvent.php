<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketEvent extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    // Relasi buat narik data makanan (produk)
    public function makanan()
    {
        return $this->belongsTo(Produk::class, 'makanan_produk_id');
    }
}