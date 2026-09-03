<?php
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use Carbon\Carbon;

$produk = Produk::first();

if (!$produk) {
    echo "Tidak ada produk di database!";
    exit;
}

$now = Carbon::now();

// Hapus pesanan dummy lama (yang no urutnya >= 900) agar tidak menumpuk
$dummyLama = Penjualan::where('no_urut_bulanan', '>=', 900)->get();
foreach($dummyLama as $dl) {
    DetailPenjualan::where('penjualan_id', $dl->id)->delete();
    $dl->delete();
}

$dummies = [
    // 3 Pesanan Aktif (Menunggu)
    ['nama' => 'Budi', 'status' => 'menunggu', 'metode' => 'tunai', 'waktu' => $now->copy()->setTime(0, 30)],
    ['nama' => 'Siti', 'status' => 'menunggu', 'metode' => 'qris', 'waktu' => $now->copy()->setTime(2, 15)],
    ['nama' => 'Andi', 'status' => 'menunggu', 'metode' => 'tunai', 'waktu' => $now->copy()->setTime(4, 05)],
    
    // 3 Pesanan Selesai
    ['nama' => 'Riski', 'status' => 'selesai', 'metode' => 'tunai', 'waktu' => $now->copy()->setTime(1, 0)],
    ['nama' => 'Rini', 'status' => 'selesai', 'metode' => 'qris', 'waktu' => $now->copy()->setTime(3, 45)],
    ['nama' => 'Dewi', 'status' => 'selesai', 'metode' => 'tunai', 'waktu' => $now->copy()->setTime(4, 20)],
];

foreach ($dummies as $index => $d) {
    $p = Penjualan::create([
        'outlet' => 'hasanuddin',
        'kasir_id' => 1,
        'tanggal' => clone $d['waktu'], // Using carbon instance directly
        'no_urut_bulanan' => 900 + $index, // Biar nggak bentrok dengan aslinya
        'nama_customer' => $d['nama'],
        'metode_pembayaran' => $d['metode'],
        'status' => $d['status'],
        'total_harga' => $produk->harga_reguler * 2,
    ]);

    // Paksa update waktu pakai DB builder biar nggak di-override otomatis sama Eloquent
    \Illuminate\Support\Facades\DB::table('penjualans')->where('id', $p->id)->update([
        'created_at' => clone $d['waktu'],
        'updated_at' => clone $d['waktu'],
    ]);

    DetailPenjualan::create([
        'penjualan_id' => $p->id,
        'produk_id' => $produk->id,
        'jumlah' => 2,
        'ukuran' => 'reguler',
        'tipe' => 'ice',
        'harga' => $produk->harga_reguler,
        'subtotal' => $produk->harga_reguler * 2,
    ]);
}

echo "Berhasil membuat 6 pesanan dummy!";
