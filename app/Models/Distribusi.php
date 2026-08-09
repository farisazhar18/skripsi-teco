<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    protected $fillable = [
        'tanggal',
        'pembelian_id',
        'bahan_baku_id',
        'outlet',
        'jumlah',
        'satuan',
        'keterangan',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}