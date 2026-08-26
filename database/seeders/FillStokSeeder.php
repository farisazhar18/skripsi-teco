<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;

class FillStokSeeder extends Seeder
{
    /**
     * Seeder untuk mengisi stok bahan baku yang masih 0 di outlet Makmur.
     * 
     * Komposisi:
     * - 8 item → stok sehat (di atas minimum)
     * - 4 item → stok menipis (di bawah minimum)
     * - 2 item → tetap kosong (Powder Coklat & Thai Tea Base)
     * 
     * Aman dijalankan berulang kali — hanya update berdasarkan ID.
     * 
     * Jalankan: php artisan db:seed --class=FillStokSeeder
     */
    public function run(): void
    {
        // =========================================================
        // OUTLET MAKMUR — Update bahan baku yang stok-nya masih 0
        // =========================================================

        $updateData = [
            // ✅ STOK SEHAT (di atas stok_minimum)
            47 => ['stok' => 8],    // Brownies (min: 5)
            48 => ['stok' => 6],    // Chicken Lasagna (min: 3)
            45 => ['stok' => 60],   // Hot cup large 16 OZ (min: 50)
            42 => ['stok' => 90],   // Hot cup reg 8 OZ (min: 75)
            44 => ['stok' => 55],   // Hot lid large 16 OZ (min: 50)
            43 => ['stok' => 85],   // Hot lid reg 8 OZ (min: 75)

            // ⚠️ STOK MENIPIS (di bawah stok_minimum — biar alert kelihatan)
            33 => ['stok' => 80],   // Butterscotch Syrup (min: 200) → 40% dari minimum
            31 => ['stok' => 50],   // Caramel Syrup (min: 200) → 25% dari minimum
            36 => ['stok' => 150],  // Gula Pasir (min: 750) → 20% dari minimum
            32 => ['stok' => 100],  // Hazelnut Syrup (min: 200) → 50% dari minimum

            // 🔴 TETAP KOSONG (biar ada contoh bahan yang bener-bener habis)
            // 29 => Powder Coklat → tetap 0
            // 35 => Thai Tea Base → tetap 0
        ];

        foreach ($updateData as $id => $data) {
            BahanBaku::where('id', $id)->update($data);
        }

        $this->command->info('');
        $this->command->info('✅ Stok bahan baku outlet Makmur berhasil di-update!');
        $this->command->info('   → 6 item: stok sehat (di atas minimum)');
        $this->command->info('   → 4 item: stok menipis (di bawah minimum)');
        $this->command->info('   → 2 item: tetap kosong (Powder Coklat & Thai Tea Base)');
        $this->command->info('');
    }
}
