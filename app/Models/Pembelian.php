<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'tanggal',
        'bahan_baku_id',
        'jumlah',
        'satuan_beli',
        'jumlah_konversi',
        'sisa_distribusi',
        'status_distribusi',
        'keterangan',
        'status_acc', // <--- WAJIB TAMBAHKAN BARIS INI
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}