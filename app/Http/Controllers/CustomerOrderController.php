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

        $newItem = [
            'produk_id' => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'ukuran' => $ukuran,
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'harga' => $harga,
            'subtotal' => $subtotal,
            'keterangan' => $keteranganPesanan, 
        ];

        try {
            DB::beginTransaction();
            $this->adjustStockForCartItem($newItem, $outlet, true);
            $this->refreshCartTimer();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        $key = 'keranjang_' . $outlet;
        $keranjang = session($key, []);

        $keranjang[] = $newItem;

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
            $item = $keranjang[$request->index];
            
            try {
                DB::beginTransaction();
                $this->adjustStockForCartItem($item, $outlet, false); // Kembalikan stok
                $this->refreshCartTimer();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }

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

            // 3. Simpan Detail 
            // (Stok sudah dipotong saat masuk keranjang, jadi di sini cuma simpan DetailPenjualan)
            foreach ($keranjang as $item) {
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
            }

            // 4. Update status Riwayat Penggunaan Bahan (Ditahan -> Terjual)
            $sessionId = session()->getId();
            $riwayats = RiwayatPenggunaanBahan::where('keterangan', 'LIKE', '% - ' . $sessionId)->get();
            
            if ($riwayats->isEmpty()) {
                throw new \Exception('Keranjang Anda telah kedaluwarsa (15 menit tanpa aktivitas). Silakan pesan ulang.');
            }

            foreach($riwayats as $riwayat) {
                if (str_contains($riwayat->keterangan, 'Extra Syrup')) {
                    preg_match('/Extra Syrup\s+([^\s]+)/', $riwayat->keterangan, $matches);
                    $namaSirup = $matches[1] ?? '';
                    $riwayat->keterangan = 'Extra Syrup ' . $namaSirup . ' (QR Customer) - Order #' . $penjualan->id;
                } else {
                    $riwayat->keterangan = 'Terjual (QR Customer) - Order #' . $penjualan->id;
                }
                $riwayat->save();
            }

            DB::commit(); // Transaksi aman!
            session()->forget($key); // Kosongkan keranjang
            session()->forget('cart_last_activity');

            // --- INI TAMBAHAN KODENYA BANG ---
            // Simpan ID pesanan ke session biar kedetect di halaman menu
            session(['order_id_' . $outlet => $penjualan->id]);
            // ---------------------------------

            // 5. Proses Midtrans (Hanya jika QRIS)
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

            // 6. Akhirnya return view sekali saja
            return view('customer.bayar', compact('penjualan', 'outlet', 'snapToken'));

        } catch (\Exception $e) {
            DB::rollBack();
            if (str_contains($e->getMessage(), 'kedaluwarsa')) {
                session()->forget($key);
                session()->forget('cart_last_activity');
            }
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
            $oldItem = $keranjang[$index];
            $produk_id = $oldItem['produk_id'];
            $produk = \App\Models\Produk::find($produk_id);

            if($produk) {
                $newItem = $oldItem;
                $newItem['jumlah'] = $request->jumlah;
                $newItem['keterangan'] = $request->keterangan;
                
                if($request->tipe_bawaan != 'food') {
                    $newItem['ukuran'] = $request->ukuran;
                    $newItem['tipe'] = $request->tipe;
                }
                
                $hargaBase = 0;
                if ($produk->tipe_produk == 'vendor') {
                    $hargaBase = $produk->harga_reguler;
                } else {
                    $hargaBase = ($newItem['ukuran'] == 'large') ? $produk->harga_large : $produk->harga_reguler;
                }

                if (!empty($newItem['keterangan']) && str_contains(strtolower($newItem['keterangan']), 'extra syrup')) {
                    $hargaBase += 3000;
                }

                $newItem['harga'] = $hargaBase;
                $newItem['subtotal'] = $hargaBase * $request->jumlah;
                
                try {
                    DB::beginTransaction();
                    // 1. Kembalikan stok item lama
                    $this->adjustStockForCartItem($oldItem, $outlet, false);
                    // 2. Potong stok item baru
                    $this->adjustStockForCartItem($newItem, $outlet, true);
                    $this->refreshCartTimer();
                    DB::commit();
                    
                    // Jika sukses potong stok, simpan ke session
                    $keranjang[$index] = $newItem;
                    session()->put("keranjang_$outlet", $keranjang);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->withErrors([$e->getMessage()]);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Item keranjang berhasil diperbarui.');
    }

    private function adjustStockForCartItem($item, $outlet, $isAdding = true)
    {
        $produk = Produk::findOrFail($item['produk_id']);
        
        $resep = ResepProduk::with('detailResepProduks.bahanBaku')
            ->where('produk_id', $produk->id)
            ->where('ukuran', $item['ukuran'])
            ->where('tipe', $item['tipe'])
            ->first();

        if ($isAdding && !$resep) {
            $namaVarian = $produk->tipe_produk == 'vendor' ? $produk->nama_produk : ucfirst($item['tipe']) . ' ' . ucfirst($item['ukuran']);
            throw new \Exception('Maaf, resep/stok dasar untuk ' . $namaVarian . ' belum tersedia di sistem.');
        }

        if ($resep) {
            foreach ($resep->detailResepProduks as $detail) {
                $bahanResep = $detail->bahanBaku;
                if (!$bahanResep) continue;

                $bahanOutlet = BahanBaku::where('nama_bahan', $bahanResep->nama_bahan)
                    ->where('satuan', $bahanResep->satuan)
                    ->where('outlet', $outlet)
                    ->first();

                if ($bahanOutlet) {
                    $totalKebutuhan = $detail->jumlah * $item['jumlah'];
                    if ($isAdding) {
                        if ($bahanOutlet->stok < $totalKebutuhan) {
                            throw new \Exception('Maaf, stok ' . $produk->nama_produk . ' sedang tidak mencukupi.');
                        }
                        $bahanOutlet->stok -= $totalKebutuhan;
                        RiwayatPenggunaanBahan::create([
                            'bahan_baku_id' => $bahanOutlet->id,
                            'outlet' => $outlet,
                            'jumlah_terpakai' => $totalKebutuhan,
                            'keterangan' => 'Ditahan (Masuk Keranjang) - ' . session()->getId()
                        ]);
                    } else {
                        $bahanOutlet->stok += $totalKebutuhan;
                        
                        // Hapus riwayat penahanan stok
                        $riwayat = RiwayatPenggunaanBahan::where('bahan_baku_id', $bahanOutlet->id)
                            ->where('outlet', $outlet)
                            ->where('jumlah_terpakai', $totalKebutuhan)
                            ->where('keterangan', 'Ditahan (Masuk Keranjang) - ' . session()->getId())
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($riwayat) {
                            $riwayat->delete();
                        }
                    }
                    $bahanOutlet->save();
                } elseif ($isAdding) {
                     throw new \Exception('Maaf, stok ' . $produk->nama_produk . ' sedang tidak mencukupi.');
                }
            }
        }

        // Handle Extra Syrup
        $keterangan = $item['keterangan'] ?? '';
        if (!empty($keterangan) && str_contains(strtolower($keterangan), 'extra syrup:')) {
            preg_match('/Extra Syrup:\s*([^|]+)/i', $keterangan, $matches);
            if (isset($matches[1])) {
                $namaSirup = trim($matches[1]);
                $bahanSirupOutlet = BahanBaku::where('nama_bahan', 'LIKE', '%' . $namaSirup . '%')
                    ->where('outlet', $outlet)
                    ->first();

                if ($bahanSirupOutlet) {
                    $takaranSirup = 20; 
                    $jumlahSirupTerpakai = $takaranSirup * $item['jumlah']; 

                    if ($isAdding) {
                        if ($bahanSirupOutlet->stok < $jumlahSirupTerpakai) {
                            throw new \Exception('Maaf, stok sirup ' . $namaSirup . ' tidak mencukupi.');
                        }
                        $bahanSirupOutlet->stok -= $jumlahSirupTerpakai;
                        RiwayatPenggunaanBahan::create([
                            'bahan_baku_id' => $bahanSirupOutlet->id,
                            'outlet' => $outlet,
                            'jumlah_terpakai' => $jumlahSirupTerpakai,
                            'keterangan' => 'Ditahan Extra Syrup ' . $namaSirup . ' (Keranjang) - ' . session()->getId()
                        ]);
                    } else {
                        $bahanSirupOutlet->stok += $jumlahSirupTerpakai;
                        
                        // Hapus riwayat penahanan sirup
                        $riwayatSirup = RiwayatPenggunaanBahan::where('bahan_baku_id', $bahanSirupOutlet->id)
                            ->where('outlet', $outlet)
                            ->where('jumlah_terpakai', $jumlahSirupTerpakai)
                            ->where('keterangan', 'Ditahan Extra Syrup ' . $namaSirup . ' (Keranjang) - ' . session()->getId())
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($riwayatSirup) {
                            $riwayatSirup->delete();
                        }
                    }
                    $bahanSirupOutlet->save();
                }
            }
        }
    }

    private function refreshCartTimer()
    {
        session(['cart_last_activity' => now()]);
        
        $sessionId = session()->getId();
        RiwayatPenggunaanBahan::where('keterangan', 'LIKE', '% - ' . $sessionId)
            ->update(['created_at' => now()]);
    }
}