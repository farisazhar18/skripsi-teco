<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;

class DeskripsiProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = Produk::all();

        foreach ($produks as $produk) {
            $nama = strtolower($produk->nama_produk);
            $deskripsi = '';

            // 1. MENU KOPI
            if (str_contains($nama, 'arenga latte')) {
                $deskripsi = 'Perpaduan sempurna espresso pekat dengan susu segar dan sirup gula aren asli yang legit.';
            } elseif (str_contains($nama, 'dulce latte')) {
                $deskripsi = 'Es Kopi Susu dengan sensasi rasa manis creamy yang memanjakan lidah, cocok untuk menemani hari manismu.';
            } elseif (str_contains($nama, 'sweet sour') || str_contains($nama, 'sweet sour lime coffee')) {
                $deskripsi = 'Sensasi kopi segar yang unik, memadukan kopi espresso, soda, dan sirup jeruk nipis (lime) yang asam manis menyegarkan.';
            } elseif (str_contains($nama, 'gembira coffee') || str_contains($nama, 'gembira kopi')) {
                $deskripsi = 'Kopi segar yang ceria! Kombinasi espresso ringan dengan soda dan manisnya sirup pisang susu.';
            } elseif (str_contains($nama, 'java choco latte')) {
                $deskripsi = 'Kombinasi klasik antara cokelat premium dan espresso, menghadirkan rasa mocha (moka) yang kaya dan pekat.';
            } elseif (str_contains($nama, 'tecosu')) {
                $deskripsi = 'Kopi Susu khas Terminal Coffee (TeCoSu) dengan racikan rahasia yang menghasilkan keseimbangan kopi dan susu yang pas.';
            } elseif (str_contains($nama, 'caffe latte')) {
                $deskripsi = 'Espresso pekat yang dipadukan dengan susu segar yang creamy, menghadirkan rasa kopi yang sangat lembut.';
            } elseif (str_contains($nama, 'americano')) {
                $deskripsi = 'Espresso murni dengan paduan air mineral. Pilihan klasik untuk penikmat kopi hitam sejati.';
            } 
            // 2. MENU NON KOPI
            elseif (str_contains($nama, 'green tea latte')) {
                $deskripsi = 'Bubuk matcha green tea premium yang diseduh sempurna bersama susu creamy. Rasanya sangat umami dan menenangkan.';
            } elseif (str_contains($nama, 'thai tea latte')) {
                $deskripsi = 'Teh khas Thailand yang pekat dan wangi, disajikan dengan susu creamy yang manis legit.';
            } elseif (str_contains($nama, 'lemon tea')) {
                $deskripsi = 'Teh segar yang dikocok (shaken) dengan perasan lemon asli. Sangat menyegarkan untuk cuaca panas.';
            } elseif (str_contains($nama, 'lychee tea')) {
                $deskripsi = 'Teh segar yang dikocok dengan sirup leci manis dan tambahan keharuman leci yang khas.';
            }
            // 3. MENU MAKANAN (PASTRY & CAKE & MEALS)
            elseif (str_contains($nama, 'brownies')) {
                $deskripsi = 'Kue cokelat panggang yang padat (fudgy) dan nyoklat banget. Sangat cocok dinikmati bersama secangkir kopi hangat.';
            } elseif (str_contains($nama, 'chicken lasagna')) {
                $deskripsi = 'Pasta lasagna berlapis dengan saus, daging ayam cincang, dan limpahan keju leleh yang gurih mengenyangkan.';
            } elseif (str_contains($nama, 'almond croissant')) {
                $deskripsi = 'Croissant renyah khas Perancis dengan isian krim almond yang manis dan taburan kacang almond panggang di atasnya.';
            } elseif (str_contains($nama, 'roti sosis')) {
                $deskripsi = 'Roti empuk klasik dengan sosis sapi gurih di tengahnya, disiram dengan saus lezat.';
            } elseif (str_contains($nama, 'spagetti brulee') || str_contains($nama, 'spaghetti')) {
                $deskripsi = 'Spaghetti panggang dengan balutan saus bolognese daging sapi dan lapisan saus bechamel keju yang lumer di mulut.';
            } elseif (str_contains($nama, 'banana cake')) {
                $deskripsi = 'Bolu pisang panggang yang lembut, manis alami dari pisang asli, dan beraroma sangat harum.';
            } elseif (str_contains($nama, 'kougin amman')) {
                $deskripsi = 'Pastry khas Bretagne Perancis (Kouign-Amann) dengan lapisan adonan yang renyah dan karamelisasi gula manis di luarnya.';
            } elseif (str_contains($nama, 'beef ham & cheese croissant')) {
                $deskripsi = 'Croissant renyah yang diisi dengan irisan beef ham gurih dan keju leleh yang melimpah.';
            } elseif (str_contains($nama, 'tripple cheese croissant')) {
                $deskripsi = 'Surganya pecinta keju! Croissant renyah dengan isian paduan tiga jenis keju lezat yang lumer saat digigit.';
            } elseif (str_contains($nama, 'roti manis')) {
                $deskripsi = 'Roti klasik yang super empuk dan manis, cocok sebagai teman ngopi atau ngeteh santai.';
            } else {
                // Fallback jika ada produk lain yang belum terdaftar di atas
                $kategori = strtolower($produk->kategori);
                if ($kategori === 'food' || $kategori === 'makanan') {
                    $deskripsi = 'Sajian lezat dari Terminal Coffee, sangat cocok untuk menemani waktu santai Anda.';
                } else {
                    $deskripsi = 'Minuman spesial racikan khas Terminal Coffee yang diramu menggunakan bahan premium pilihan.';
                }
            }

            // TIMPA SEMUA DESKRIPSI (OVERWRITE) AGAR UPDATE
            $produk->deskripsi = $deskripsi;
            $produk->save();
        }

        $this->command->info('Deskripsi produk berhasil di-update dan diperbaiki sesuai permintaan!');
    }
}
