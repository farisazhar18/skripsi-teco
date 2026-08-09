<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanStok extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'outlet',
        'stok_seharusnya',
        'stok_aktual',
        'alasan',
        'foto_bukti',
        'status',
        'user_id'
    ];

    // Relasi balik ke Bahan Baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    // Relasi ke User (Barista/Logistik)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}