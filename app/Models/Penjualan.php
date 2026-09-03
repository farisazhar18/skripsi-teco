<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'no_urut_bulanan',
        'tanggal',
        'outlet',
        'nama_customer',
        'no_hp',
        'total_harga',
        'uang_diterima',
        'metode_pembayaran',
        'status',
        'sumber_order',
    ];

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}