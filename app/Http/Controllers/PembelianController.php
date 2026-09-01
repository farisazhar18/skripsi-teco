<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function indexPengajuan() 
    {
        $data = Pembelian::with('bahanBaku')
            // Logika urutan kustom SQL disamakan persis dengan logika if-else di Blade
            ->orderByRaw("
                CASE 
                    WHEN status_acc = 'menunggu_pembelian' THEN 2
                    WHEN status_acc = 'menunggu_barang' THEN 3 
                    WHEN status_acc = 'disetujui' THEN 4 
                    WHEN status_acc = 'ditolak' THEN 5 
                    ELSE 1 -- Tangkap nilai NULL, 'pending', atau kosong, lalu taruh paling atas!
                END
            ")
            // Kalau statusnya sama, urutin dari yang terbaru
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pembelian.index_pengajuan', compact('data'));
    }

    public function indexStok(Request $request) 
    {
        // 1. Ambil semua data pembelian yang disetujui (Urutin dari id terbaru biar data perwakilannya akurat)
        $pembelians = Pembelian::where('status_acc', 'disetujui')
            ->with('bahanBaku')
            ->orderBy('id', 'desc') 
            ->get();

        // 2. Kelompokkan berdasarkan bahan baku dan rekap jumlahnya
        $data = $pembelians->groupBy('bahan_baku_id')->map(function ($group) {
            $first = $group->first(); // Mengambil data perwakilan pertama (sekarang pasti ngambil yang terbaru)
            
            return (object) [
                'id' => $first->id, 
                'tanggal' => $group->max('tanggal'), 
                'tanggal_terima' => $group->max('updated_at'), // Ambil tanggal kapan barang diterima/disetujui
                'bahanBaku' => $first->bahanBaku,
                'keterangan' => $first->keterangan, 
                
                // Gunakan jumlah_konversi agar perhitungannya presisi 
                // (misal mencegah error gabungin 1 kg + 500 gram)
                'jumlah' => $group->sum('jumlah_konversi'), 
                'satuan_beli' => $first->bahanBaku->satuan, // Tampilkan dengan satuan dasar (gram, ml, pcs, dll)
                
                // Jumlahkan total sisa stok dari semua transaksi bahan ini
                'sisa_distribusi' => $group->sum('sisa_distribusi')
            ];
        });

        // 3. Sorting berdasarkan request user
        $sortBy = $request->query('sort', 'terbaru'); // Default terbaru
        
        if ($sortBy == 'nama') {
            $data = $data->sortBy(function($item) {
                return $item->bahanBaku->nama_bahan;
            });
        } elseif ($sortBy == 'kategori') {
            $data = $data->sortBy(function($item) {
                return ($item->bahanBaku->kategori ?? 'z') . '-' . $item->bahanBaku->nama_bahan;
            });
        } else {
            $data = $data->sortByDesc('tanggal_terima');
        }

        // 4. Fitur Search
        $search = $request->query('search');
        if (!empty($search)) {
            $data = $data->filter(function($item) use ($search) {
                return stripos($item->bahanBaku->nama_bahan, $search) !== false;
            });
        }

        $data = $data->values(); // Reset urutan index array

        return view('pembelian.index_stok', compact('data', 'sortBy', 'search'));
    }

    public function create()
    {
        $bahanBaku = BahanBaku::selectRaw('MIN(id) as id, nama_bahan, satuan')
            ->groupBy('nama_bahan', 'satuan')
            ->orderBy('nama_bahan')
            ->get();

        return view('pembelian.create', compact('bahanBaku'));
    }

    public function store(Request $request)
    {
        // 1. Validasi diubah jadi bentuk array (pakai .* biar ngecek tiap barisnya)
        $request->validate([
            'tanggal' => 'required|date',
            'bahan_baku_id' => 'required|array',
            'bahan_baku_id.*' => 'required|exists:bahan_bakus,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|numeric|min:1',
            'satuan_beli' => 'required|array',
            'satuan_beli.*' => 'required',
            'keterangan' => 'nullable|array',
        ]);

        // 2. Looping (diulang) sebanyak baris barang yang ditambahin di form
        foreach ($request->bahan_baku_id as $key => $bahan_id) {
            
            // Cari data bahan baku aslinya buat nyocokin satuan
            $bahanBaku = BahanBaku::findOrFail($bahan_id);

            // 3. Konversi satuan untuk masing-masing barang yang lagi di-loop
            $jumlahKonversi = $this->konversiSatuan(
                $request->jumlah[$key],
                $request->satuan_beli[$key],
                $bahanBaku->satuan
            );

            // 4. Simpan ke database satu-satu sesuai urutannya
            Pembelian::create([
                'tanggal' => $request->tanggal, // Tanggalnya sama semua untuk satu pengajuan ini
                'bahan_baku_id' => $bahan_id,
                'jumlah' => $request->jumlah[$key],
                'satuan_beli' => $request->satuan_beli[$key],
                'jumlah_konversi' => $jumlahKonversi,
                'sisa_distribusi' => $jumlahKonversi,
                'status_distribusi' => 'belum',
                'keterangan' => $request->keterangan[$key] ?? null,
            ]);
        }

        // 5. Kalau semua baris udah beres disimpan, balikin ke halaman pengajuan
        return redirect()->route('pembelian.pengajuan')
            ->with('success', 'Semua pengadaan bahan baku berhasil diajukan!');
    }

    private function konversiSatuan($jumlah, $satuanBeli, $satuanDasar)
    {
        // 1. Jika satuannya sama (misal beli ml, dasar ml), tidak ada konversi
        if (strtolower($satuanBeli) == strtolower($satuanDasar)) {
            return $jumlah;
        }

        // 2. Konversi Liter ke ML
        if (strtolower($satuanBeli) == 'liter' && strtolower($satuanDasar) == 'ml') {
            return $jumlah * 1000;
        }

        // 3. Konversi KG ke Gram
        if (strtolower($satuanBeli) == 'kg' && strtolower($satuanDasar) == 'gram') {
            return $jumlah * 1000;
        }

        // 4. [BARU] Konversi Botol ke ML (Khusus kasus seperti Soda Water)
        if (strtolower($satuanBeli) == 'botol' && strtolower($satuanDasar) == 'ml') {
            return $jumlah * 250; // 1 botol dikali 250 ml
        }

        // Jika tidak masuk kondisi di atas, kembalikan nilai aslinya
        return $jumlah;
    }

    public function show($id)
    {
        // Ambil data pembelian beserta relasi item detailnya biar gak null
        $pembelian = Pembelian::with('bahanBaku')->findOrFail($id);
        
        return view('pembelian.show', compact('pembelian'));
    }

    public function edit(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $bahanBaku = BahanBaku::selectRaw('MIN(id) as id, nama_bahan, satuan')
            ->groupBy('nama_bahan', 'satuan')
            ->orderBy('nama_bahan')
            ->get();

        return view('pembelian.edit', compact('pembelian', 'bahanBaku'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'jumlah' => 'required|numeric|min:1',
            'satuan_beli' => 'required',
            'keterangan' => 'nullable',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->status_distribusi != 'belum') {
            return redirect()->route('pembelian.pengajuan')
                ->with('success', 'Pembelian yang sudah didistribusikan tidak dapat diubah.');
        }

        $bahanBaku = BahanBaku::findOrFail($request->bahan_baku_id);

        $jumlahKonversi = $this->konversiSatuan(
            $request->jumlah,
            $request->satuan_beli,
            $bahanBaku->satuan
        );

        $pembelian->update([
            'tanggal' => $request->tanggal,
            'bahan_baku_id' => $request->bahan_baku_id,
            'jumlah' => $request->jumlah,
            'satuan_beli' => $request->satuan_beli,
            'jumlah_konversi' => $jumlahKonversi,
            'sisa_distribusi' => $jumlahKonversi,
            'status_distribusi' => 'belum',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pembelian.pengajuan')
            ->with('success', 'Pembelian berhasil diubah.');
    }

    public function destroy(string $id)
    {
        $data = Pembelian::findOrFail($id);

        if ($data->status_distribusi != 'belum') {
            return redirect()->route('pembelian.pengajuan')
                ->with('success', 'Pembelian yang sudah didistribusikan tidak dapat dihapus.');
        }

        $data->delete();

        return redirect()->route('pembelian.pengajuan')
            ->with('success', 'Data pembelian berhasil dihapus.');
    }

   // UPDATE FUNGSI ACC LAMA (Biar bisa nerima revisi satuan)
    public function accPembelian(Request $request, $id) 
    {
        $pembelian = Pembelian::findOrFail($id);
        
        // Kalau manajer ngubah Qty di halaman Detail
        if($request->has('jumlah_revisi') && $request->jumlah_revisi != $pembelian->jumlah) {
            $pembelian->jumlah = $request->jumlah_revisi;
            
            // Hitung ulang konversinya
            $pembelian->jumlah_konversi = $this->konversiSatuan($pembelian->jumlah, $pembelian->satuan_beli, $pembelian->bahanBaku->satuan);
            $pembelian->sisa_distribusi = $pembelian->jumlah_konversi;
        }
        
        $pembelian->status_acc = ($request->action == 'setujui') ? 'menunggu_pembelian' : 'ditolak';
        $pembelian->save();
        
        return redirect()->route('pembelian.pengajuan')
            ->with('success', 'Pengajuan di-ACC dengan jumlah final: ' . $pembelian->jumlah . ' ' . $pembelian->satuan_beli);
    }

    // FUNGSI BUAT NAMPILIN MENU ACC MASSAL
    public function reviewAcc()
    {
        // Cuma panggil barang yang statusnya Menunggu ACC
        $pengajuans = Pembelian::with('bahanBaku')
            ->whereNull('status_acc')
            ->orWhereNotIn('status_acc', ['disetujui', 'menunggu_barang', 'ditolak', 'menunggu_pembelian'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('pembelian.review_acc', compact('pengajuans'));
    }

    // FUNGSI BUAT NGE-PROSES ACC MASSAL & REVISI JUMLAH
    public function accMassal(Request $request)
    {
        $request->validate([
            'pembelian_ids' => 'required|array', // Harus ada kotak yang dicentang
            'action' => 'required|in:setujui,tolak'
        ]);

        $statusBaru = ($request->action == 'setujui') ? 'menunggu_pembelian' : 'ditolak';
        $count = 0;

        foreach($request->pembelian_ids as $id) {
            $pembelian = Pembelian::find($id);
            
            if($pembelian && !in_array($pembelian->status_acc, ['disetujui', 'menunggu_barang', 'ditolak', 'menunggu_pembelian'])) {
                
                // Kalau Qty-nya direvisi sama manager
                if(isset($request->jumlah[$id]) && $request->jumlah[$id] != $pembelian->jumlah) {
                    $pembelian->jumlah = $request->jumlah[$id];
                    // Hitung ulang konversinya
                    $pembelian->jumlah_konversi = $this->konversiSatuan($pembelian->jumlah, $pembelian->satuan_beli, $pembelian->bahanBaku->satuan);
                    $pembelian->sisa_distribusi = $pembelian->jumlah_konversi;
                }

                $pembelian->status_acc = $statusBaru;
                $pembelian->save();
                $count++;
            }
        }

        $pesan = ($request->action == 'setujui') ? "$count pengajuan berhasil disetujui!" : "$count pengajuan ditolak!";
        // Kalau udah kelar, balikin ke index
        return redirect()->route('pembelian.pengajuan')->with('success', $pesan); 
    }

    // FUNGSI BUKA MENU TERIMA BARANG MASSAL
    public function reviewTerima()
    {
        // Cuma panggil barang yang statusnya lagi di perjalanan (menunggu barang)
        $pengajuans = Pembelian::with('bahanBaku')
            ->where('status_acc', 'menunggu_barang')
            ->orderBy('tanggal', 'asc') // Yang paling lama dipesan taruh atas
            ->get();
            
        // Kelompokkan berdasarkan PO Number
        $groupedPengajuans = $pengajuans->groupBy(function ($item) {
            return $item->po_number ?: 'Tanpa PO / Manual';
        });

        return view('pembelian.review_terima', compact('groupedPengajuans'));
    }

    // FUNGSI EKSEKUSI TERIMA BARANG MASSAL
    public function terimaMassal(Request $request)
    {
        $request->validate([
            'po_numbers' => 'required|array'
        ]);

        $count = 0;
        foreach($request->po_numbers as $po) {
            if ($po == 'Tanpa PO / Manual') {
                $pembelians = Pembelian::where('status_acc', 'menunggu_barang')->whereNull('po_number')->get();
            } else {
                $pembelians = Pembelian::where('status_acc', 'menunggu_barang')->where('po_number', $po)->get();
            }

            foreach($pembelians as $pembelian) {
                $pembelian->status_acc = 'disetujui'; // Otomatis masuk gudang
                $pembelian->save();
                $count++;
            }
        }

        return redirect()->route('pembelian.pengajuan')
            ->with('success', "$count barang berhasil diterima dan stok masuk ke gudang!");
    }

    // 2. FUNGSI BARU: LOGISTIK TERIMA BARANG
    public function terimaBarang($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        
        if ($pembelian->status_acc != 'menunggu_barang') {
            return redirect()->route('pembelian.pengajuan')->withErrors(['Status tidak valid.']);
        }

        // Baru di titik ini statusnya jadi 'disetujui', 
        // sehingga otomatis masuk ke halaman "Daftar Pengadaan & Stok" lu!
        $pembelian->status_acc = 'disetujui';
        $pembelian->save();

        return redirect()->route('pembelian.pengajuan')
            ->with('success', 'Barang berhasil diterima! Stok sudah masuk ke gudang.');
    }

    // 3. FUNGSI BARU: CETAK PDF PURCHASE ORDER (PO)
    public function cetakPO($id)
    {
        $pembelian = Pembelian::with('bahanBaku')->findOrFail($id);
        
        // Cari urutan pengadaan ke berapa
        $pengadaanKe = Pembelian::select('created_at')
            ->where('created_at', '<=', $pembelian->created_at)
            ->groupBy('created_at')
            ->get()
            ->count();
        
        // Kita siapin file pdf_po.blade.php buat desain suratnya nanti
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pembelian.pdf_po', compact('pembelian', 'pengadaanKe'));
        
        return $pdf->download('PO_Pengadaan_Logistik_' . str_pad($pengadaanKe, 4, '0', STR_PAD_LEFT) . '.pdf');
    }

    // FUNGSI BUAT NAMPILIN HALAMAN PILIH BARANG PO
    public function pilihPO()
    {
        // Ambil HANYA barang yang statusnya nunggu pembelian (sudah ACC Manager)
        $pembelians = Pembelian::with('bahanBaku')
            ->where('status_acc', 'menunggu_pembelian')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('pembelian.pilih_po', compact('pembelians'));
    }

    // FUNGSI BUAT CETAK PDF MULTIPLE BARANG
    public function cetakPOMulti(Request $request)
    {
        $request->validate([
            'pembelian_ids' => 'required|array', // Harus ada barang yang dicentang
            'nama_supplier' => 'nullable|string'
        ]);

        $pembelians = Pembelian::with('bahanBaku')
            ->whereIn('id', $request->pembelian_ids)
            ->get();

        // Cari urutan pengadaan ke berapa
        $patokanCreatedAt = $pembelians->first()->created_at;
        $pengadaanKe = Pembelian::select('created_at')
            ->where('created_at', '<=', $patokanCreatedAt)
            ->groupBy('created_at')
            ->get()
            ->count();

        $nama_supplier = $request->nama_supplier ?? '.......................................';

        $po_number = 'PO_Logistik_' . date('Ymd') . '_' . str_pad($pengadaanKe, 4, '0', STR_PAD_LEFT);

        // Tandai bahwa barang-barang ini sudah dicetak PO-nya dan beri nomor PO
        Pembelian::whereIn('id', $request->pembelian_ids)->update([
            'is_po_dicetak' => true,
            'po_number' => $po_number
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pembelian.pdf_po_multi', compact('pembelians', 'nama_supplier', 'pengadaanKe'));
        
        return $pdf->download('PO_Logistik_' . date('Ymd') . '_' . str_pad($pengadaanKe, 4, '0', STR_PAD_LEFT) . '.pdf');
    }

    // FUNGSI BARU: LOGISTIK KLIK PROSES BELI MASSAL (Dari Halaman Cetak PO)
    public function prosesBeliMassal(Request $request)
    {
        // Update semua barang yang berstatus menunggu_pembelian menjadi menunggu_barang
        $count = Pembelian::where('status_acc', 'menunggu_pembelian')
            ->update(['status_acc' => 'menunggu_barang']);

        return redirect()->route('pembelian.pengajuan')->with('success', "$count barang sedang dipesan! Status berubah menjadi Menunggu Barang Datang.");
    }

    // FUNGSI BARU: RIWAYAT STOK MASUK DARI EVENT
    public function stokDariEvent(Request $request)
    {
        // 1. Ambil tanggal dari request, default ke bulan ini
        $tanggalMulai = $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai) : \Carbon\Carbon::now()->startOfMonth();
        $tanggalAkhir = $request->tanggal_akhir ? \Carbon\Carbon::parse($request->tanggal_akhir) : \Carbon\Carbon::now()->endOfMonth();

        // 2. Query tabel pembelians
        $data = Pembelian::with('bahanBaku')
            ->where('status_acc', 'disetujui')
            ->where('keterangan', 'like', '%Sisa fisik aktual Event%')
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 3. Ekstrak nama event dan total jumlah
        $totalMasuk = 0;
        foreach ($data as $item) {
            $totalMasuk += $item->jumlah_konversi;
            
            // Ekstrak nama event dari keterangan "Sisa fisik aktual Event: [Nama Event]"
            $item->nama_event = str_replace('Sisa fisik aktual Event: ', '', $item->keterangan);
        }

        return view('pembelian.stok_event', compact('data', 'tanggalMulai', 'tanggalAkhir', 'totalMasuk'));
    }
}