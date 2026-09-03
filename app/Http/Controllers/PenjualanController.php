<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\ResepProduk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\RiwayatPenggunaanBahan;


class PenjualanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Siapkan query dasar
        if (request('tab') == 'selesai') {
            $query = Penjualan::with('detailPenjualans.produk')
                ->where('status', 'selesai')
                ->whereDate('tanggal', now()->toDateString());
        } else {
            $query = Penjualan::with('detailPenjualans.produk')
                ->whereIn('status', ['menunggu_pembayaran', 'menunggu', 'diproses', 'Siap diambil']);
        }

        // 2. LOGIKA FILTER OUTLET & ROLE: 
        if (in_array($user->role, ['kasir', 'barista'])) {
            $query->where('outlet', session('outlet_aktif'));
        }

        // Barista tidak boleh melihat pesanan yang belum dibayar
        if ($user->role == 'barista') {
            $query->where('status', '!=', 'menunggu_pembayaran');
        }

        // 3. Eksekusi query dan urutkan dari pesanan yang paling lama ngantre
        $penjualans = $query->orderBy('created_at', 'asc')->get();
        
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        // Hapus where('status', 'aktif') karena sekarang statusnya otomatis ngecek resep
        $produks = Produk::orderBy('nama_produk', 'asc')->get();

        return view('penjualan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari keranjang
        $request->validate([
            'cart_data' => 'required',
            'metode_bayar' => 'required'
        ]);

        // 2. Ubah data keranjang (JSON) jadi array PHP
        $cartData = json_decode($request->cart_data, true);

        if (empty($cartData)) {
            return back()->with('error', 'Keranjang belanja masih kosong!');
        }

        $totalHarga = 0;
        foreach ($cartData as $item) {
            $totalHarga += $item['subtotal'];
        }

        $outletAktif = session('outlet_aktif') ?? 'hasanuddin'; // Wajib ada untuk outlet!

        DB::beginTransaction();
        try {
            // LOGIKA PENTING: Kalau QRIS nunggu dibayar, kalau Tunai langsung masuk antrean Barista
            $statusAwal = ($request->metode_bayar === 'QRIS') ? 'menunggu_pembayaran' : 'menunggu';

            // --- LOGIKA NOMOR ANTREAN BULANAN ---
            $lastOrder = \App\Models\Penjualan::where('outlet', $outletAktif)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->orderBy('no_urut_bulanan', 'desc')
                ->first();

            $noUrutBaru = $lastOrder ? $lastOrder->no_urut_bulanan + 1 : 1;

            // 3. Simpan Data Utama ke tabel `penjualans`
            $penjualan = Penjualan::create([
                'no_urut_bulanan' => $noUrutBaru,
                'tanggal' => now(),
                'total_harga' => $totalHarga,
                'uang_diterima' => $request->uang_diterima ?? null,
                'metode_pembayaran' => $request->metode_bayar,
                'status' => $statusAwal, 
                'outlet' => $outletAktif, 
                'sumber_order' => 'kasir'
            ]);

            // 4. Simpan Data Item ke tabel `detail_penjualans` & POTONG STOK
            foreach ($cartData as $item) {
                $ukuran = $item['db_ukuran'] !== '-' ? $item['db_ukuran'] : null;
                $tipe = $item['db_tipe'] !== '-' ? $item['db_tipe'] : null;
                $jumlahBeli = $item['qty'];

                // Simpan ke struk detail
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['id'],
                    'ukuran' => $ukuran,
                    'tipe' => $tipe,
                    'jumlah' => $jumlahBeli,
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'keterangan' => $item['keterangan'] ?? null
                ]);

                // === MULAI LOGIKA POTONG STOK & RIWAYAT ===
                $produk = Produk::find($item['id']);
                
                // Cari resep berdasarkan produk, ukuran, dan tipe
                $queryResep = ResepProduk::with('detailResepProduks.bahanBaku')->where('produk_id', $produk->id);
                if ($ukuran) $queryResep->where('ukuran', $ukuran);
                if ($tipe) $queryResep->where('tipe', $tipe);
                $resep = $queryResep->first();

                // Kalau resepnya ada, potong bahan bakunya
                if ($resep) {
                    foreach ($resep->detailResepProduks as $detail) {
                        $bahanResep = $detail->bahanBaku;
                        
                        // Cari bahan baku fisik di outlet yang sedang aktif
                        $bahanOutlet = BahanBaku::where('nama_bahan', $bahanResep->nama_bahan)
                            ->where('satuan', $bahanResep->satuan)
                            ->where('outlet', $outletAktif)
                            ->first();

                        // Cek apakah stok cukup
                        if (!$bahanOutlet || $bahanOutlet->stok < ($detail->jumlah * $jumlahBeli)) {
                            throw new \Exception('Stok bahan ' . $bahanResep->nama_bahan . ' untuk produk ' . $produk->nama_produk . ' tidak mencukupi di outlet ini.');
                        }
                        
                        // Eksekusi potong stok
                        $bahanOutlet->stok -= ($detail->jumlah * $jumlahBeli);
                        $bahanOutlet->save();

                        // Catat ke buku Riwayat Penggunaan Harian
                        \App\Models\RiwayatPenggunaanBahan::create([
                            'bahan_baku_id' => $bahanOutlet->id,
                            'outlet' => $outletAktif,
                            'jumlah_terpakai' => ($detail->jumlah * $jumlahBeli),
                            'keterangan' => 'Terjual (Kasir) - Order #' . $penjualan->id
                        ]);
                    }
                } else {
                    // Kalau produk minuman tapi belum dibikinin resep, gagalkan transaksinya
                    if ($produk->kategori != 'Food' && $produk->tipe_produk != 'vendor') {
                        throw new \Exception('Resep untuk produk ' . $produk->nama_produk . ' belum diatur oleh Manajemen.');
                    }
                }

                // 🔥 SELIPKAN DI SINI (TEPAT DI ATAS KOMENTAR 'AKHIR LOGIKA') 🔥
                $keteranganKasir = $item['keterangan'] ?? '';
                
                if (!empty($keteranganKasir) && str_contains($keteranganKasir, 'Extra Syrup:')) {
                    preg_match('/Extra Syrup:\s*([^|]+)/', $keteranganKasir, $matches);
                    
                    if (isset($matches[1])) {
                        $namaSirup = trim($matches[1]);
                        
                        $bahanSirupOutlet = \App\Models\BahanBaku::where('nama_bahan', 'LIKE', '%' . $namaSirup . '%')
                            ->where('outlet', $outletAktif) // Di controller ini variabelnya $outletAktif
                            ->first();

                        if ($bahanSirupOutlet) {
                            $takaranSirup = 20; // Ubah sesuai takaran
                            $jumlahSirupTerpakai = $takaranSirup * $item['qty']; // Di controller ini nama qty-nya 'qty' atau variabel $jumlahBeli

                            if ($bahanSirupOutlet->stok >= $jumlahSirupTerpakai) {
                                $bahanSirupOutlet->stok -= $jumlahSirupTerpakai;
                                $bahanSirupOutlet->save();

                                \App\Models\RiwayatPenggunaanBahan::create([
                                    'bahan_baku_id' => $bahanSirupOutlet->id,
                                    'outlet' => $outletAktif,
                                    'jumlah_terpakai' => $jumlahSirupTerpakai,
                                    'keterangan' => 'Extra Syrup ' . $namaSirup . ' (Kasir) - Order #' . $penjualan->id
                                ]);
                            }
                        }
                    }
                }
                // ----------------------------

                // === AKHIR LOGIKA POTONG STOK & RIWAYAT ===
            }

            DB::commit(); // ACC Simpan permanen ke database

            // Langsung arahkan ke halaman detail/struk.
            return redirect('/penjualan/' . $penjualan->id)->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalin simpan kalau ada sistem yang gagal (stok gak jadi kepotong)
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);

        if (in_array(auth()->user()->role, ['kasir', 'barista']) && $penjualan->outlet != session('outlet_aktif')) {
            abort(403);
        }

        // GENERATE TOKEN MIDTRANS DI SINI (Hanya jika metode QRIS dan belum lunas)
        $snapToken = null;
        if ($penjualan->metode_pembayaran === 'QRIS' && $penjualan->status === 'menunggu_pembayaran') {
            if ($penjualan->snap_token) {
                $snapToken = $penjualan->snap_token;
            } else {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $penjualan->id,
                        'gross_amount' => $penjualan->total_harga,
                    ],
                    'customer_details' => [
                        'first_name' => 'Customer Kedai',
                    ],
                    'enabled_payments' => ['gopay', 'other_qris'], 
                ];
                
                $snapToken = Snap::getSnapToken($params);
                $penjualan->snap_token = $snapToken;
                $penjualan->save();
            }
        }

        // Kirim $snapToken ke view show
        return view('penjualan.show', compact('penjualan', 'snapToken'));
    }

    // Fungsi buat update status pesanan (aksi tombol barista)
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu_pembayaran,menunggu,diproses,Siap diambil,selesai',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $role = auth()->user()->role;

        if (in_array($role, ['kasir', 'barista']) && $penjualan->outlet != session('outlet_aktif')) {
            abort(403);
        }

        if ($role == 'barista') {
            if (!in_array($request->status, ['diproses', 'Siap diambil'])) {
                return redirect('/penjualan')
                    ->with('success', 'Barista hanya dapat memproses pesanan sampai Siap diambil.');
            }
        }

        if ($role == 'kasir') {
            if ($request->status != 'selesai') {
                return redirect('/penjualan')
                    ->with('success', 'Kasir hanya dapat menyelesaikan pesanan.');
            }
        }

        if (!in_array($role, ['owner', 'barista', 'kasir'])) {
            return redirect('/penjualan')
                ->with('success', 'Anda tidak memiliki akses untuk mengubah status pesanan.');
        }

        $penjualan->update([
            'status' => $request->status,
        ]);

        return redirect('/penjualan')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function konfirmasiPembayaran(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        // Ubah status ke 'menunggu' supaya barista bisa mulai proses, dan simpan uang diterima
        $penjualan->update([
            'status' => 'menunggu',
            'uang_diterima' => $request->uang_diterima ?? $penjualan->uang_diterima
        ]); 
        
        return redirect()->back()->with('success', 'Pembayaran tunai berhasil dikonfirmasi!');
    }

    // Jangan lupa import PDF di paling atas file:
    public function cetakStruk($id)
    {
        // 1. Ambil data penjualan beserta relasinya dari database
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);
        
        // 2. Langsung lempar ke view 'bill.blade.php' (Tanpa embel-embel DomPDF backend!)
        return view('penjualan.bill', compact('penjualan'));
    }
}