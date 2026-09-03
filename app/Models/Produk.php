<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'foto',
        'nama_produk',
        'deskripsi',
        'kategori',
        'is_event',
        'bisa_extra_syrup',
        'harga_reguler',
        'harga_large',
        'tersedia_hot',
        'tersedia_ice',
        'tipe_produk',
        'stok_produk',
        'status',
    ];

    public function resepProduks()
    {
        return $this->hasMany(ResepProduk::class);
    }

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function statusOtomatis($outlet = 'hasanuddin')
    {
        $resepProduks = $this->resepProduks()->with('detailResepProduks.bahanBaku')->get();

        // LOGIKA BARU: APAPUN JENIS PRODUKNYA (Kopi atau Makanan), KALAU GAK ADA RESEP = SOLD OUT
        if ($resepProduks->isEmpty()) {
            return 'Tidak Aktif';
        }

        // Cek resepnya satu-satu
        foreach ($resepProduks as $resep) {
            $resepBisaDibuat = true; 

            foreach ($resep->detailResepProduks as $detail) {
                $bahanAsli = $detail->bahanBaku;

                if (!$bahanAsli) {
                    $resepBisaDibuat = false;
                    break; 
                }

                $stokDiOutlet = \App\Models\BahanBaku::where('nama_bahan', $bahanAsli->nama_bahan)
                    ->where('outlet', $outlet)
                    ->sum('stok');

                if ($stokDiOutlet < $detail->jumlah) {
                    $resepBisaDibuat = false;
                    break; 
                }
            }

            // Kalau ada 1 aja varian resep yang bahannya aman, langsung aktifkan!
            if ($resepBisaDibuat) {
                return 'Aktif';
            }
        }

        // Kalau udah dicek semua resepnya tapi bahannya kurang, berarti Sold Out
        return 'Tidak Aktif';
    }

    public function varianTersedia($outlet = 'hasanuddin')
    {
        $resepProduks = $this->resepProduks()->with('detailResepProduks.bahanBaku')->get();
        $tersedia = [];

        foreach ($resepProduks as $resep) {
            
            // ==========================================
            // LOGIKA SINKRONISASI JAVASCRIPT
            // ==========================================
            // Jika produk adalah makanan (vendor), paksa key-nya jadi 'vendor_standar'
            if ($this->tipe_produk == 'vendor') {
                $key = 'vendor_standar';
            } else {
                $key = $resep->tipe . '_' . $resep->ukuran;
            }

            $bisaDibuat = true;

            foreach ($resep->detailResepProduks as $detail) {
                $bahanAsli = $detail->bahanBaku;
                
                if (!$bahanAsli) {
                    $bisaDibuat = false;
                    break;
                }

                $stokDiOutlet = \App\Models\BahanBaku::where('nama_bahan', $bahanAsli->nama_bahan)
                    ->where('outlet', $outlet)
                    ->sum('stok');

                if ($stokDiOutlet < $detail->jumlah) {
                    $bisaDibuat = false;
                    break;
                }
            }
            
            // Simpan status ketersediaannya
            // Khusus vendor: Kalau sebelumnya udah ada yang true, pertahankan true
            if (isset($tersedia[$key])) {
                $tersedia[$key] = $tersedia[$key] || $bisaDibuat;
            } else {
                $tersedia[$key] = $bisaDibuat;
            }
        }

        return $tersedia;
    }
}