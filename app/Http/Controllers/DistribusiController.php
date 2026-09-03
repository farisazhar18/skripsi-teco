<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Pembelian;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $query = Distribusi::with(['pembelian', 'bahanBaku'])
            ->orderBy('created_at', 'desc'); 

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }
        if ($request->outlet) {
            $query->where('outlet', $request->outlet);
        }

        $rawData = $query->get();

        $dataGrouped = $rawData->groupBy(function($item) {
            return $item->outlet . '|' . $item->created_at->format('Y-m-d H:i');
        });

        return view('distribusi.index', compact('dataGrouped'));
    }

    public function printPdf(Request $request)
    {
        $outlet = $request->outlet;
        $waktu = $request->waktu; 

        $distribusi = Distribusi::with('bahanBaku')
            ->where('outlet', $outlet)
            ->where('created_at', 'like', $waktu . '%')
            ->get();

        if ($distribusi->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('distribusi.pdf', compact('distribusi', 'outlet', 'waktu'));
        
        $namaFile = 'Surat_Jalan_' . ucfirst($outlet) . '_' . date('Ymd_Hi', strtotime($waktu)) . '.pdf';
        
        return $pdf->download($namaFile);
    }

    public function create(Request $request)
    {
        $namaBahanDipilih = null;

        // Ambil nama bahan jika di-klik dari halaman Daftar Pengadaan
        if ($request->pembelian_id) {
            $pembelian = Pembelian::with('bahanBaku')->find($request->pembelian_id);
            if ($pembelian && $pembelian->bahanBaku) {
                $namaBahanDipilih = $pembelian->bahanBaku->nama_bahan;
            }
        }

        // Ambil semua data pembelian yang sudah di ACC dan masih ada sisanya
        $pembelians = Pembelian::with('bahanBaku')
            ->where('status_acc', 'disetujui')
            ->where('sisa_distribusi', '>', 0)
            ->get();

        // GABUNGKAN DATA BATCH BERDASARKAN NAMA BAHAN (Biar di dropdown rapi)
        $bahanTersedia = $pembelians->groupBy(function($item) {
            return $item->bahanBaku->nama_bahan;
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'nama_bahan' => $first->bahanBaku->nama_bahan,
                'satuan' => $first->bahanBaku->satuan,
                'total_sisa' => $group->sum('sisa_distribusi')
            ];
        })->values();

        // 🔥 TAMBAHAN BARU: Ambil stok outlet yang menipis (Stok <= Stok Minimum)
        $kebutuhanOutlet = \App\Models\BahanBaku::whereColumn('stok', '<', 'stok_minimum')
            ->orderBy('stok', 'asc') // Prioritaskan bahan yang habis duluan (stok 0)
            ->orderBy('outlet')
            ->orderBy('nama_bahan')
            ->get();

        // Urutkan ulang: Taruh "Gudang Kosong" di paling bawah
        $kebutuhanOutlet = $kebutuhanOutlet->map(function($butuh) use ($bahanTersedia) {
            $stokGudang = 0;
            foreach($bahanTersedia as $tersedia) {
                if (strtolower($tersedia->nama_bahan) == strtolower($butuh->nama_bahan)) {
                    $stokGudang = $tersedia->total_sisa;
                    break;
                }
            }
            $butuh->stok_gudang_temp = $stokGudang;
            return $butuh;
        })->sort(function($a, $b) {
            $aKosong = $a->stok_gudang_temp <= 0;
            $bKosong = $b->stok_gudang_temp <= 0;
            
            if ($aKosong !== $bKosong) {
                return $aKosong ? 1 : -1;
            }
            
            if ($a->stok != $b->stok) {
                return $a->stok <=> $b->stok;
            }
            
            if ($a->outlet != $b->outlet) {
                return $a->outlet <=> $b->outlet;
            }
            
            return $a->nama_bahan <=> $b->nama_bahan;
        })->values();

        return view('distribusi.create', compact('bahanTersedia', 'namaBahanDipilih', 'kebutuhanOutlet'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Sekarang kita menggunakan nama_bahan)
        $request->validate([
            'outlet' => 'required|array|min:1',
            'outlet.*' => 'in:hasanuddin,makmur',
            'nama_bahan' => 'required|array|min:1',
            'nama_bahan.*' => 'required|string',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'keterangan' => 'nullable',
        ]);

        $outlets = $request->outlet;
        $namaBahans = $request->nama_bahan;
        $jumlahs = $request->jumlah;

        // --- FASE 1: PRE-VALIDASI TOTAL STOK ---
        $totalRequested = [];
        foreach ($namaBahans as $index => $nama) {
            $qty = $jumlahs[$index] * count($outlets); 
            
            if (!isset($totalRequested[$nama])) {
                $totalRequested[$nama] = 0;
            }
            $totalRequested[$nama] += $qty;
        }

        foreach ($totalRequested as $nama => $totalQty) {
            // Hitung total sisa distribusi di database untuk bahan ini
            $totalSisaTersedia = Pembelian::whereHas('bahanBaku', function($q) use ($nama) {
                    $q->where('nama_bahan', $nama);
                })
                ->where('status_acc', 'disetujui')
                ->sum('sisa_distribusi');

            if ($totalQty > $totalSisaTersedia) {
                return back()
                    ->withErrors(['jumlah' => 'Total distribusi bahan "' . $nama . '" (' . $totalQty . ') melebihi total sisa pengadaan (' . $totalSisaTersedia . ').'])
                    ->withInput();
            }

            foreach ($outlets as $outlet) {
                $bahanOutlet = BahanBaku::where('nama_bahan', $nama)
                    ->where('outlet', $outlet)
                    ->first();

                if (!$bahanOutlet) {
                    $namaOutlet = $outlet == 'hasanuddin' ? 'Hasanuddin' : 'Makmur';
                    return back()
                        ->withErrors(['outlet' => 'Data bahan baku "' . $nama . '" untuk outlet ' . $namaOutlet . ' belum tersedia. Tambahkan dulu di master bahan baku.'])
                        ->withInput();
                }
            }
        }

        // --- FASE 2: EKSEKUSI PENYIMPANAN KE DATABASE (SISTEM FIFO) ---
        foreach ($outlets as $outlet) {
            foreach ($namaBahans as $index => $nama) {
                $qtyToDistribute = $jumlahs[$index];

                $bahanOutlet = BahanBaku::where('nama_bahan', $nama)
                    ->where('outlet', $outlet)
                    ->first();

                // Ambil batch Pembelian dengan sisa > 0, urutkan dari yg paling tua (FIFO)
                $batches = Pembelian::whereHas('bahanBaku', function($q) use ($nama) {
                        $q->where('nama_bahan', $nama);
                    })
                    ->where('status_acc', 'disetujui')
                    ->where('sisa_distribusi', '>', 0)
                    ->orderBy('tanggal', 'asc') // Yg paling dulu dibeli (Oldest)
                    ->orderBy('id', 'asc')
                    ->get();

                // LOOPING PEMOTONGAN BATCH
                foreach ($batches as $batch) {
                    if ($qtyToDistribute <= 0) {
                        break; // Selesai distribusi bahan ini untuk outlet ini
                    }

                    // Tentukan berapa yang bisa dipotong dari batch ini
                    $potong = min($qtyToDistribute, $batch->sisa_distribusi);

                    // 1. Simpan Riwayat Distribusi 
                    Distribusi::create([
                        'tanggal' => date('Y-m-d'), 
                        'pembelian_id' => $batch->id,
                        'bahan_baku_id' => $bahanOutlet->id,
                        'outlet' => $outlet,
                        'jumlah' => $potong,
                        'satuan' => $bahanOutlet->satuan,
                        'keterangan' => $request->keterangan,
                    ]);

                    // 2. Tambah Stok di Outlet
                    $bahanOutlet->stok += $potong;
                    $bahanOutlet->save();

                    // 3. Kurangi Sisa Distribusi di Pengadaan Utama
                    $batch->sisa_distribusi -= $potong;
                    
                    // 4. Update Status Distribusi langsung di batch ini
                    $batch->status_distribusi = $this->updateStatusDistribusi($batch->id);
                    $batch->save();

                    // Kurangi target jumlah yang harus dikirim
                    $qtyToDistribute -= $potong;
                }
            }
        }

        return redirect('/distribusi')->with('success', 'Distribusi sistem FIFO berhasil! Stok otomatis terpotong dari pengadaan paling awal.');
    }

    private function updateStatusDistribusi($pembelianId)
    {
        $outlets = Distribusi::where('pembelian_id', $pembelianId)
            ->pluck('outlet')
            ->unique()
            ->values()
            ->toArray();

        if (in_array('hasanuddin', $outlets) && in_array('makmur', $outlets)) {
            return 'keduanya';
        }

        if (in_array('hasanuddin', $outlets)) {
            return 'hasanuddin';
        }

        if (in_array('makmur', $outlets)) {
            return 'makmur';
        }

        return 'belum';
    }
}