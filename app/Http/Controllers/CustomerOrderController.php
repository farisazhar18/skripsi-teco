<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\ResepProduk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\RiwayatPenggunaanBahan;

class CustomerOrderController extends Controller
{
    public function menu($outlet)
    {
        $produks = Produk::orderBy('nama_produk', 'asc')->get();

        $coffee = $produks->where('kategori', 'Coffee');
        $nonCoffee = $produks->where('kategori', 'Non Coffee');
        $food = $produks->where('kategori', 'Food');

        $keranjang = session('keranjang_' . $outlet, []);
        $jumlahKeranjang = collect($keranjang)->sum('jumlah');
        $totalKeranjang = collect($keranjang)->sum('subtotal');

        $keyOrder = 'order_id_' . $outlet;
        $order_id = session($keyOrder);
        $pesananAktif = null;

        if ($order_id) {
            $pesananAktif = Penjualan::find($order_id);
            
            // Hapus session jika pesanan sudah selesai
            if ($pesananAktif && $pesananAktif->status === 'selesai') {
                session()->forget($keyOrder);
                $pesananAktif = null;
            }
        }

        return view('customer.menu', compact(
            'produks',
            'coffee',
            'nonCoffee',
            'food',
            'outlet',
            'jumlahKeranjang',
            'totalKeranjang',
            'pesananAktif'
        ));
    }

    public function tambahKeranjang(Request $request, $outlet)
    {
        $order_id = session('order_id');
        if ($order_id) {
            $pesananAktif = Penjualan::find($order_id);
            // Kalau pesanan ada dan statusnya BELUM selesai
            if ($pesananAktif && $pesananAktif->status !== 'selesai') {
                return redirect()->route('customer.menu', $outlet)
                    ->withErrors(['Selesaikan atau ambil pesanan Anda sebelumnya terlebih dahulu sebelum membuat pesanan baru.']);
            }
        }
        // ----------------------------------------

        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|numeric|min:1',
            'ukuran' => 'nullable',
            'tipe' => 'nullable',
        ]);

        $produk = Produk::findOrFail($request->produk_id);

        $ukuran = $request->ukuran ?? 'reguler';
        $tipe = $request->tipe ?? 'ice';
        $jumlah = (int) $request->jumlah;

        // 1. TENTUIN HARGA DAN TIPE
        if ($produk->tipe_produk == 'vendor') {
            $harga = $produk->harga_reguler;
            $ukuran = 'standar';
            $tipe = 'food';
        } else {
            $harga = $ukuran == 'large' ? $produk->harga_large : $produk->harga_reguler;
        }

        // 2. CEK STOK BAHAN BAKU VIA RESEP (SATU PINTU BUAT SEMUA PRODUK)
        $resep = ResepProduk::with('detailResepProduks.bahanBaku')
                    ->where('produk_id', $produk->id)
                    ->where('ukuran', $ukuran)
                    ->where('tipe', $tipe)
                    ->first();

        if (!$resep) {
            $namaVarian = $produk->tipe_produk == 'vendor' ? $produk->nama_produk : ucfirst($tipe) . ' ' . ucfirst($ukuran);
            return redirect()->back()->withErrors(['Maaf, resep/stok dasar untuk ' . $namaVarian . ' belum tersedia di sistem.']);
        }

        foreach ($resep->detailResepProduks as $detail) {
            $bahanAsli = $detail->bahanBaku;
            
            if (!$bahanAsli) continue;

            $stokDiOutlet = BahanBaku::where('nama_bahan', $bahanAsli->nama_bahan)
                    ->where('outlet', $outlet)
                    ->sum('stok');

            $totalKebutuhan = $detail->jumlah * $jumlah;

            if ($stokDiOutlet < $totalKebutuhan) {
                return redirect()->back()->withErrors(['Maaf, stok ' . $produk->nama_produk . ' sedang tidak mencukupi.']);
            }
        }

        $keteranganPesanan = $request->keterangan ?? ''; 
        
        // Kita cek, apakah customer milih sirup dan bukan "tidak"
        if ($request->has('extra_syrup') && $request->extra_syrup != 'tidak') {
            // Nambah harga satuan 3000
            $harga += 3000; 
            
            // Bikin catatan buat barista
            $tambahanTulisan = "Extra Syrup: " . ucfirst($request->extra_syrup);
            
            // Gabungin ke keterangan yang udah ada (misal customer nulis "less ice")
            $keteranganPesanan = $keteranganPesanan ? $keteranganPesanan . ' | ' . $tambahanTulisan : $tambahanTulisan;
        }
        // -----------------------------------------

        // Ngitung subtotal pakai harga yang udah ditambah sirup
        $subtotal = $harga * $jumlah;

        $key = 'keranjang_' . $outlet;
        $keranjang = session($key, []);

        $keranjang[] = [
            'produk_id' => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'ukuran' => $ukuran,
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'harga' => $harga,
            'subtotal' => $subtotal,
            
            // 🔥 PENTING: Keterangan ubah pakai variabel baru kita 🔥
            'keterangan' => $keteranganPesanan, 
        ];

        session([$key => $keranjang]);

        return redirect()->route('customer.menu', ['outlet' => strtolower($outlet)])
            ->with('success', 'Menu berhasil ditambahkan ke keranjang.');
    
    }

    public function keranjang($outlet)
    {
        $keranjang = session('keranjang_' . $outlet, []);

        $total = collect($keranjang)->sum('subtotal');
        $jumlahItem = collect($keranjang)->sum('jumlah');

        $outlet = strtolower($outlet);

        return view('customer.keranjang', compact(
            'keranjang',
            'total',
            'jumlahItem',
            'outlet'
        ));
    }

    public function hapusKeranjang(Request $request, $outlet)
    {
        $request->validate([
            'index' => 'required|numeric',
        ]);

        $key = 'keranjang_' . $outlet;
        $keranjang = session($key, []);

        if (isset($keranjang[$request->index])) {
            unset($keranjang[$request->index]);
            $keranjang = array_values($keranjang);
            session([$key => $keranjang]);
        }

        return redirect()->route('customer.keranjang', ['outlet' => strtolower($outlet)])
            ->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function checkout(Request $request, $outlet)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:Tunai,QRIS',
            'nama_customer' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $key = 'keranjang_' . $outlet;
        if (!session()->has($key) || empty(session($key))) {
            return redirect()->route('customer.menu', $outlet)->withErrors(['Keranjang kosong.']);
        }

        $keranjang = session($key);
        $totalHarga = collect($keranjang)->sum('subtotal');

        DB::beginTransaction();
        try {
            // 1. Tentukan status awal
            $statusAwal = 'menunggu_pembayaran';

            // --- LOGIKA NOMOR ANTREAN BULANAN ---
            $lastOrder = \App\Models\Penjualan::where('outlet', $outlet)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->orderBy('no_urut_bulanan', 'desc')
                ->first();

            $noUrutBaru = $lastOrder ? $lastOrder->no_urut_bulanan + 1 : 1;
            // ------------------------------------

            // 2. Simpan Penjualan
            $penjualan = Penjualan::create([
                'no_urut_bulanan' => $noUrutBaru,
                'tanggal' => now()->toDateString(),
                'outlet' => $outlet,
                'nama_customer' => $request->nama_customer,
                'no_hp' => $request->no_hp,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $statusAwal,
                'sumber_order' => 'customer_qr',
            ]);

            // 3. Simpan Detail & Update Stok
            foreach ($keranjang as $item) {
                $produk = Produk::findOrFail($item['produk_id']);
                $resep = ResepProduk::with('detailResepProduks.bahanBaku')
                    ->where('produk_id', $produk->id)
                    ->where('ukuran', $item['ukuran'])
                    ->where('tipe', $item['tipe'])
                    ->first();

                if (!$resep) throw new \Exception('Resep produk ' . $item['nama_produk'] . ' belum tersedia.');

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'ukuran' => $item['ukuran'],
                    'tipe' => $item['tipe'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);

                foreach ($resep->detailResepProduks as $detail) {
                    $bahanResep = $detail->bahanBaku;
                    $bahanOutlet = BahanBaku::where('nama_bahan', $bahanResep->nama_bahan)
                        ->where('satuan', $bahanResep->satuan)
                        ->where('outlet', $outlet)
                        ->first();

                    if (!$bahanOutlet || $bahanOutlet->stok < ($detail->jumlah * $item['jumlah'])) {
                        throw new \Exception('Stok untuk ' . $item['nama_produk'] . ' tidak cukup.');
                    }
                    $bahanOutlet->stok -= ($detail->jumlah * $item['jumlah']);
                    $bahanOutlet->save();

                    // --- SELIPKAN KODE INI UNTUK MENCATAT RIWAYAT PENGGUNAAN ---
                    RiwayatPenggunaanBahan::create([
                        'bahan_baku_id' => $bahanOutlet->id,
                        'outlet' => $outlet,
                        'jumlah_terpakai' => ($detail->jumlah * $item['jumlah']),
                        'keterangan' => 'Terjual (QR Customer) - Order #' . $penjualan->id
                    ]);
                }

                    // 🔥 SELIPKAN DI SINI BANG 🔥
                $keterangan = $item['keterangan'] ?? '';
                
                if (!empty($keterangan) && str_contains($keterangan, 'Extra Syrup:')) {
                    preg_match('/Extra Syrup:\s*([^|]+)/', $keterangan, $matches);
                    
                    if (isset($matches[1])) {
                        $namaSirup = trim($matches[1]);
                        
                        $bahanSirupOutlet = \App\Models\BahanBaku::where('nama_bahan', 'LIKE', '%' . $namaSirup . '%')
                            ->where('outlet', $outlet) // Di controller ini variabelnya $outlet
                            ->first();

                        if ($bahanSirupOutlet) {
                            $takaranSirup = 20; // Ubah sesuai takaran ml/pcs di kafe lu
                            $jumlahSirupTerpakai = $takaranSirup * $item['jumlah']; 

                            if ($bahanSirupOutlet->stok >= $jumlahSirupTerpakai) {
                                $bahanSirupOutlet->stok -= $jumlahSirupTerpakai;
                                $bahanSirupOutlet->save();

                                \App\Models\RiwayatPenggunaanBahan::create([
                                    'bahan_baku_id' => $bahanSirupOutlet->id,
                                    'outlet' => $outlet,
                                    'jumlah_terpakai' => $jumlahSirupTerpakai,
                                    'keterangan' => 'Extra Syrup ' . $namaSirup . ' (QR Customer) - Order #' . $penjualan->id
                                ]);
                            }
                        }
                    }
                }
                // ----------------------------
            }

            DB::commit(); // Transaksi aman!
            session()->forget($key); // Kosongkan keranjang

            // --- INI TAMBAHAN KODENYA BANG ---
            // Simpan ID pesanan ke session biar kedetect di halaman menu
            session(['order_id_' . $outlet => $penjualan->id]);
            // ---------------------------------

            // 4. Proses Midtrans (Hanya jika QRIS)
            $snapToken = null;
            if ($request->metode_pembayaran === 'QRIS') {
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
                            'gross_amount' => $totalHarga,
                        ],
                        'enabled_payments' => ['gopay', 'other_qris'],
                    ];
                    $snapToken = Snap::getSnapToken($params);
                    
                    $penjualan->snap_token = $snapToken;
                    $penjualan->save();
                }
            }

            // 5. Akhirnya return view sekali saja
            return view('customer.bayar', compact('penjualan', 'outlet', 'snapToken'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function status($id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);
        
        session(['order_id_' . $penjualan->outlet => $id]);

        return view('customer.status', compact('penjualan'));
    }

    public function downloadBill($id)
    {
        // Ambil data penjualan beserta detail produknya
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);

        // Buat PDF dari tampilan blade 'customer.bill_pdf'
        $pdf = Pdf::loadView('customer.bill_pdf', compact('penjualan'));

        // Atur ukuran kertas struk (misal: ukuran struk kasir 80mm / custom)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); 

        // Otomatis ter-download dengan nama file yang unik
        return $pdf->download('Bill_TerminalCoffee_' . $penjualan->id . '.pdf');
    }

    public function cekCustomer($no_hp)
    {
        // Cari riwayat transaksi terakhir dengan no_hp yang sama & namanya tidak kosong
        $riwayat = Penjualan::where('no_hp', $no_hp)
            ->whereNotNull('nama_customer')
            ->orderBy('created_at', 'desc')
            ->first();

        // Kalau datanya pernah ada, kembalikan nama customernya
        if ($riwayat) {
            return response()->json([
                'ditemukan' => true,
                'nama' => $riwayat->nama_customer
            ]);
        }

        // Kalau pelanggan baru, kembalikan false
        return response()->json(['ditemukan' => false]);
    }

    // Tambahkan di dalam CustomerOrderController
    public static function getCartData($outlet)
    {
        $keranjang = session('keranjang_' . $outlet, []);
        return [
            'jumlah' => collect($keranjang)->sum('jumlah'),
            'total' => collect($keranjang)->sum('subtotal')
        ];
    }

    public function cekStatus($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return response()->json(['status' => $penjualan->status]);
    }

    public function pilihPembayaran($outlet, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('customer.pilih_pembayaran', compact('penjualan', 'outlet'));
    }

    public function prosesPembayaran(Request $request, $outlet, $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($request->metode === 'tunai') {
            // Update status ke 'menunggu_pembayaran' atau 'diproses' sesuai alur lu
            return redirect()->route('customer.status', $id)
                            ->with('message', 'Harap tunjukkan nomor pesanan ini ke kasir.');
        } elseif ($request->metode === 'qris') {
            return view('customer.qris', compact('penjualan', 'outlet'));
        }
        
        return back()->withErrors(['Pilih metode pembayaran terlebih dahulu.']);
    }

    public function formRiwayat($outlet)
    {
        // Langsung lempar ke view form input nomor HP
        return view('customer.riwayat_form', compact('outlet'));
    }

    public function cariRiwayat(Request $request, $outlet)
    {
        $request->validate([
            'no_hp' => 'required|string|max:20',
        ]);

        $no_hp = $request->no_hp;

        // Ambil data riwayat transaksi berdasarkan nomor HP & outlet dengan aman (Eager Loading)
        $riwayat = Penjualan::with('detailPenjualans.produk')
            ->where('no_hp', $no_hp)
            ->where('outlet', $outlet)
            ->orderBy('created_at', 'desc')
            ->get();

        // Lempar data ke halaman riwayat lu yang kemarin
        return view('customer.riwayat', compact('riwayat', 'outlet', 'no_hp'));
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                // Update status pesanan ke 'menunggu' (siap diproses barista)
                $penjualan = Penjualan::find($request->order_id);
                if ($penjualan) {
                    $penjualan->update(['status' => 'menunggu']);
                }
            }
        }
    }

    public function simulateSuccess($id)
    {
        $penjualan = Penjualan::find($id);
        if ($penjualan && $penjualan->status === 'menunggu_pembayaran') {
            $penjualan->update(['status' => 'menunggu']);
        }
        return response()->json(['status' => 'ok']);
    }

    public function updateKeranjang(Request $request, $outlet)
    {
        $keranjang = session()->get("keranjang_$outlet", []);
        $index = $request->index;

        if(isset($keranjang[$index])) {
            // 1. Panggil data produk aslinya dari database buat ngecek harga
            $produk_id = $keranjang[$index]['produk_id'];
            $produk = \App\Models\Produk::find($produk_id);

            if($produk) {
                // 2. Update jumlah & keterangan dari inputan modal
                $keranjang[$index]['jumlah'] = $request->jumlah;
                $keranjang[$index]['keterangan'] = $request->keterangan;
                
                // 3. Kalau bukan food, update ukuran & tipe
                if($request->tipe_bawaan != 'food') {
                    $keranjang[$index]['ukuran'] = $request->ukuran;
                    $keranjang[$index]['tipe'] = $request->tipe;
                }
                
                // 4. LOGIKA HITUNG ULANG HARGA
                $hargaBase = 0;
                if ($produk->tipe_produk == 'vendor') {
                    $hargaBase = $produk->harga_reguler;
                } else {
                    // Cek kalau ukurannya large, pakai harga large
                    $hargaBase = ($keranjang[$index]['ukuran'] == 'large') ? $produk->harga_large : $produk->harga_reguler;
                }

                // 5. Cek kalau di keterangan ada tulisan Extra Syrup, tambahin 3000
                if (!empty($keranjang[$index]['keterangan']) && str_contains(strtolower($keranjang[$index]['keterangan']), 'extra syrup')) {
                    $hargaBase += 3000;
                }

                // 6. Update harga satuan dan subtotal (harga satuan x jumlah beli)
                $keranjang[$index]['harga'] = $hargaBase;
                $keranjang[$index]['subtotal'] = $hargaBase * $request->jumlah;
                
                // 7. Simpan kembali ke session
                session()->put("keranjang_$outlet", $keranjang);
            }
        }
        
        // Balikin ke halaman keranjang
        return redirect()->back()->with('success', 'Item keranjang berhasil diperbarui.');
    }
}