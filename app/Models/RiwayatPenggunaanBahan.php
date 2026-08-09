<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenggunaanBahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'outlet',
        'jumlah_terpakai',
        'keterangan'
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}