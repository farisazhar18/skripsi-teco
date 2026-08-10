<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventDetail;
use App\Models\BahanBaku;
use App\Models\Pembelian;
use App\Models\Produk;             // Tambahkan ini
use App\Models\PaketEvent;         // Tambahkan ini
use App\Models\ResepProduk;        // Tambahkan ini
use App\Models\DetailResepProduk;  // Tambahkan ini

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('created_at', 'desc')->get();
        return view('event.index', compact('events'));
    }

    public function tugas()
    {
        $role = auth()->user()->role;
        $query = Event::query();

        if ($role == 'logistik') {
            $query->whereIn('status', ['menunggu_logistik', 'menunggu_pembelian', 'menunggu_barang_event', 'bahan_ready']);
        } elseif ($role == 'barista') {
            $query->where('status', 'diserahkan');
        } elseif (in_array($role, ['owner', 'operational_manager'])) {
            $query->whereIn('status', ['menunggu_acc_pengadaan', 'menunggu_logistik', 'menunggu_pembelian', 'menunggu_barang_event', 'bahan_ready', 'diserahkan']);
        }

        $events = $query->orderBy('tanggal_pelaksanaan', 'asc')->get();
        return view('event.tugas', compact('events'));
    }

    public function create()
    {
        // 1. Ambil semua produk untuk pilihan pesanan satuan
        $produks = Produk::all();

        // 2. Ambil semua paket untuk pilihan paket bundling
        $pakets = PaketEvent::with('makanan')->get(); 

        // Ganti nama-nama minuman ini sesuai dengan yang ada di menu lu bang
    $minumanPakets = Produk::whereIn('nama_produk', [
        'Dulce Latte', 
        'Arenga Latte', 
        'Ice Shaken Lychee Tea',
        'Ice Shaken Lemon Tea',
        'Green Tea Latte',
        'Thai Tea Latte',
        'Java Choco Latte'
    ])->get();

        return view('event.create', compact('produks', 'pakets', 'minumanPakets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'outlet' => 'required|string',
        ]);

        $catatanPesanan = "<ul style='margin-bottom: 0;'>";
        $kebutuhanBahan = []; // Array pintar buat nampung & jumlahin bahan baku otomatis

        // ==========================================
        // 1. BONGKAR RESEP PAKET BUNDLING
        // ==========================================
        if ($request->has('paket_id')) {
            foreach ($request->paket_id as $key => $paketId) {
                if ($paketId && !empty($request->jumlah_paket[$key])) {
                    $paket = PaketEvent::with('makanan')->find($paketId);
                    $minuman = Produk::find($request->minuman_id[$key]);
                    $qty = (int) $request->jumlah_paket[$key];
                    $ukuran = ucfirst($request->minuman_ukuran[$key] ?? 'Reguler');
                    $tipe = ucfirst($request->minuman_tipe[$key] ?? 'Ice');

                    if ($paket && $minuman) {
                        $namaMakanan = $paket->makanan ? $paket->makanan->nama_produk : 'Makanan/Snack';
                        
                        // Tulis ke catatan
                        $catatanPesanan .= "<li><strong>[Paket]</strong> {$paket->nama_paket} ({$namaMakanan} + {$minuman->nama_produk} {$ukuran} {$tipe}) &mdash; <strong>{$qty} Pax</strong></li>";

                        // A. Cari resep makanan dari paket
                        if ($paket->makanan) {
                            $resepMakanan = ResepProduk::where('produk_id', $paket->makanan->id)->first();
                            if ($resepMakanan) {
                                $detailReseps = DetailResepProduk::where('resep_produk_id', $resepMakanan->id)->get();
                                foreach ($detailReseps as $dr) {
                                    // 🔥 Sudah disesuaikan pakai kolom 'jumlah' 🔥
                                    $kebutuhanBahan[$dr->bahan_baku_id] = ($kebutuhanBahan[$dr->bahan_baku_id] ?? 0) + ($dr->jumlah * $qty);
                                }
                            }
                        }

                        // B. Cari resep minuman paketan
                        $resepMinuman = ResepProduk::where('produk_id', $minuman->id)->first();
                        if ($resepMinuman) {
                            $detailReseps = DetailResepProduk::where('resep_produk_id', $resepMinuman->id)->get();
                            foreach ($detailReseps as $dr) {
                                // 🔥 Sudah disesuaikan pakai kolom 'jumlah' 🔥
                                $kebutuhanBahan[$dr->bahan_baku_id] = ($kebutuhanBahan[$dr->bahan_baku_id] ?? 0) + ($dr->jumlah * $qty);
                            }
                        }
                    }
                }
            }
        }

        // ==========================================
        // 2. BONGKAR RESEP PRODUK SATUAN
        // ==========================================
        if ($request->has('produk_id')) {
            foreach ($request->produk_id as $key => $produkId) {
                if ($produkId && !empty($request->jumlah_pesanan[$key])) {
                    $produk = Produk::find($produkId);
                    $qty = (int) $request->jumlah_pesanan[$key];
                    $ukuran = ucfirst($request->ukuran[$key] ?? 'Standar');
                    $tipe = ucfirst($request->tipe[$key] ?? 'Hot');

                    if ($produk) {
                        // Tulis ke catatan
                        $catatanPesanan .= "<li><strong>[Satuan]</strong> {$produk->nama_produk} ({$ukuran}, {$tipe}) &mdash; <strong>{$qty} Pcs</strong></li>";

                        // Cari resep produknya
                        $resep = ResepProduk::where('produk_id', $produkId)->first();
                        if ($resep) {
                            $detailReseps = DetailResepProduk::where('resep_produk_id', $resep->id)->get();
                            foreach ($detailReseps as $dr) {
                                // 🔥 Sudah disesuaikan pakai kolom 'jumlah' 🔥
                                $kebutuhanBahan[$dr->bahan_baku_id] = ($kebutuhanBahan[$dr->bahan_baku_id] ?? 0) + ($dr->jumlah * $qty);
                            }
                        }
                    }
                }
            }
        }

        $catatanPesanan .= "</ul>";
        
        if (empty($kebutuhanBahan)) {
            $catatanPesanan = "<em>Tidak ada pesanan atau resep belum diatur pada produk yang dipilih.</em>";
        }

        // ==========================================
        // 3. SIMPAN KE TABEL EVENT (DENGAN CATATAN RAPI)
        // ==========================================
        $event = Event::create([
            'nama_event' => $request->nama_event,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'outlet' => $request->outlet,
            'penyelenggara' => $request->penyelenggara ?? '-',
            'detail_pesanan' => $catatanPesanan,
            'status' => 'menunggu_logistik',
        ]);

        // ==========================================
        // 4. SIMPAN KEBUTUHAN BAHAN KE EVENT DETAILS
        // ==========================================
        foreach ($kebutuhanBahan as $bahan_baku_id => $jumlah_dibutuhkan) {
            EventDetail::create([
                'event_id' => $event->id,
                'bahan_baku_id' => $bahan_baku_id,
                'jumlah_dibutuhkan' => $jumlah_dibutuhkan,
            ]);
        }

        return redirect()->route('event.index')->with('success', 'Perencanaan Event berhasil dibuat dan bahan baku otomatis terhitung!');
    }

    public function detailEvent($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        return view('event.detail', compact('event'));
    }

    // 1. LOGISTIK AJUKAN PENGADAAN
    public function ajukanPengadaan(Request $request, $id) 
    {
        $event = Event::findOrFail($id);
        
        if ($request->has('detail_id')) {
            foreach($request->detail_id as $key => $detail_id) {
                $detail = EventDetail::find($detail_id);
                if($detail && isset($request->jumlah_beli[$key])) {
                    $detail->jumlah_beli = $request->jumlah_beli[$key];
                    $detail->satuan_beli = $request->satuan_beli[$key];
                    $detail->save();
                }
            }
        }
        
        $event->update(['status' => 'menunggu_acc_pengadaan']);
        return redirect()->route('event.tugas')->with('success', 'Pengadaan Event berhasil diajukan! Menunggu ACC Manager.');
    }

    // 2. MANAGER ACC PENGADAAN
    // 2. MANAGER ACC PENGADAAN & BISA REVISI JUMLAH BELI
    public function accPengadaan(Request $request, $id) // <-- Tambahin Request $request
    {
        $event = Event::findOrFail($id);
        
        // Simpan perubahan angka pembelian final dari form Manager
        if ($request->has('detail_id')) {
            foreach($request->detail_id as $key => $detail_id) {
                $detail = EventDetail::find($detail_id);
                if($detail && isset($request->jumlah_beli[$key])) {
                    $detail->jumlah_beli = $request->jumlah_beli[$key];
                    if(isset($request->satuan_beli[$key])) {
                        $detail->satuan_beli = $request->satuan_beli[$key];
                    }
                    $detail->save();
                }
            }
        }

        // Ubah baris ini di dalam fungsi accPengadaan:
        $event->update(['status' => 'menunggu_pembelian']); 
        return redirect()->route('event.tugas')->with('success', 'Pengadaan Event telah di-ACC. Menunggu Logistik membuat PO!');
    }

    // BUKA HALAMAN KHUSUS BUAT PO MULTI SUPPLIER
    public function formPO($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        return view('event.po_form', compact('event'));
    }

    public function cetakPO(Request $request, $id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        $nama_supplier = $request->query('supplier', 'SUPPLIER EVENT');
        
        // Tangkap array ID yang dichecklist
        $selectedIds = $request->query('detail_ids', []);

        // Filter: Hanya kirim data bahan baku yang dicentang aja ke file PDF
        if (!empty($selectedIds)) {
            $detailsToPrint = $event->eventDetails->whereIn('id', $selectedIds);
        } else {
            // Jaga-jaga kalau lupa centang tapi kepencet, print semua
            $detailsToPrint = $event->eventDetails; 
        }
        
        // Tandai bahwa barang-barang event ini sudah dicetak PO-nya
        $idsToUpdate = $detailsToPrint->pluck('id')->toArray();
        if (!empty($idsToUpdate)) {
            \App\Models\EventDetail::whereIn('id', $idsToUpdate)->update(['is_po_dicetak' => true]);
        }
        
        $pdf = \PDF::loadView('event.po_event', compact('event', 'nama_supplier', 'detailsToPrint'));
        return $pdf->download('PO_Event_' . str_replace(' ', '_', $event->nama_event) . '.pdf');
    }

    public function prosesBeli($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'menunggu_barang_event']);
        return redirect()->route('event.tugas')->with('success', 'Barang sedang dipesan! Status berubah menjadi Menunggu Barang Datang.');
    }

    // 3. LOGISTIK TERIMA BARANG
    public function terimaBarang($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);

        foreach ($event->eventDetails as $detail) {
            $bahanBaku = BahanBaku::find($detail->bahan_baku_id);
            if ($bahanBaku) {
                $stokEvent = (float)($bahanBaku->stok_event ?? 0);
                $butuh = (float)($detail->jumlah_dibutuhkan ?? 0);
                $bahanBaku->stok_event = $stokEvent + $butuh;
                $bahanBaku->save();
            }
        }

        $event->update(['status' => 'bahan_ready']);
        return redirect()->route('event.tugas')->with('success', 'Barang diterima! Bahan baku untuk event siap digunakan.');
    }

    // 4. LOGISTIK SERAHKAN KE BARISTA
    public function serahkanBarang($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'diserahkan']);
        return redirect()->route('event.tugas')->with('success', 'Bahan baku berhasil diserahkan ke Barista!');
    }

    public function show($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        return view('event.show', compact('event'));
    }

    public function exportPdf($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        $pdf = \PDF::loadView('event.pdf', compact('event'));
        $namaFile = 'Kebutuhan_Bahan_' . str_replace(' ', '_', $event->nama_event) . '.pdf';
        return $pdf->download($namaFile);
    }

    public function exportPdfManager($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        $pdf = \PDF::loadView('event.pdf_manager', compact('event'));
        $namaFile = 'Rekap_Event_' . str_replace(' ', '_', $event->nama_event) . '.pdf';
        return $pdf->download($namaFile);
    }

    private function konversiSatuan($jumlah, $satuanBeli, $satuanDasar)
    {
        $sb = strtolower($satuanBeli ?? '');
        $sd = strtolower($satuanDasar ?? '');
        $jml = (float)$jumlah;

        if ($sb == $sd) { return $jml; }
        if ($sb == 'liter' && $sd == 'ml') { return $jml * 1000; }
        if ($sb == 'kg' && $sd == 'gram') { return $jml * 1000; }
        if ($sb == 'botol' && $sd == 'ml') { return $jml * 250; }
        if ($sb == 'pack' && $sd == 'pcs') { return $jml * 1; }
        return $jml;
    }

    // 5. MUNCULKAN FORM LAPORAN SISA FISIK
    public function laporSisa($id)
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);
        
        // Hitung sisa di atas kertas (sistem) untuk panduan Barista
        $sisaSistem = [];
        foreach($event->eventDetails as $detail) {
            $beliKonversi = $this->konversiSatuan($detail->jumlah_beli, $detail->satuan_beli, $detail->bahanBaku->satuan ?? '');
            $butuh = (float)($detail->jumlah_dibutuhkan ?? 0);
            $sisa = $beliKonversi - $butuh;
            
            $sisaSistem[$detail->id] = $sisa > 0 ? $sisa : 0;
        }

        return view('event.lapor_sisa', compact('event', 'sisaSistem'));
    }

    // 6. EKSEKUSI PENYELESAIAN, MASUKKAN SISA FISIK, DAN DOWNLOAD PDF
    public function selesaikanPesanan(Request $request, $id) 
    {
        $event = Event::with('eventDetails.bahanBaku')->findOrFail($id);

        foreach ($event->eventDetails as $detail) {
            $bahanBaku = BahanBaku::find($detail->bahan_baku_id);
            if ($bahanBaku) {
                // A. Reset/Kurangi stok khusus event
                $stokEvent = (float)($bahanBaku->stok_event ?? 0);
                $butuh = (float)($detail->jumlah_dibutuhkan ?? 0);
                
                if ($stokEvent >= $butuh) {
                    $bahanBaku->stok_event = $stokEvent - $butuh;
                } else {
                    $bahanBaku->stok_event = 0;
                }
                $bahanBaku->save();

                // B. Tangkap sisa fisik aktual dari form
                $sisaFisik = (float)($request->sisa_fisik[$detail->id] ?? 0);

                // 🔥 TAMBAHAN 1: Simpan di tabel detail event biar bisa dicetak di PDF 🔥
                $detail->sisa_bahan = $sisaFisik;
                $detail->save();

                // C. SIMPAN SISA FISIK AKTUAL KE GUDANG UTAMA DARI INPUTAN FORM
                if ($sisaFisik > 0) {
                    Pembelian::create([
                        'tanggal' => now()->toDateString(),
                        'bahan_baku_id' => $bahanBaku->id,
                        'jumlah' => $sisaFisik,
                        'satuan_beli' => $bahanBaku->satuan ?? 'pcs', // Masuk gudang pakai satuan dasar
                        'jumlah_konversi' => $sisaFisik,
                        'sisa_distribusi' => $sisaFisik,
                        'status_distribusi' => 'belum',
                        'status_acc' => 'disetujui', // Langsung acc karena ini balikin barang
                        'keterangan' => 'Sisa fisik aktual Event: ' . $event->nama_event
                    ]);
                }
            }
        }

        // D. Ubah status event jadi selesai
        $event->update(['status' => 'selesai']);

        // 🔥 TAMBAHAN 2: Bikin PDF Laporan & Langsung Download 🔥
        $namaFile = 'Laporan_Sisa_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $event->nama_event) . '_' . date('Ymd') . '.pdf';
        
        $pdf = \PDF::loadView('event.pdf_laporan_sisa', compact('event'));
        
        // Kita mereturn file download, bukan redirect
        return $pdf->download($namaFile);
    }
}