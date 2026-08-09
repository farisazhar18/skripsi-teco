<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::query();

        // 1. Filter Outlet
        if (auth()->user()->role == 'barista') {
            $query->where('outlet', session('outlet_aktif'));
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
            }
        }

        // 2. Filter Pencarian Nama Bahan
        if ($request->search_bahan) {
            $query->where('nama_bahan', 'LIKE', '%' . $request->search_bahan . '%');
        }

        // 3. Filter Status Stok
        if ($request->status_stok) {
            if ($request->status_stok == 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($request->status_stok == 'menipis') {
                $query->whereColumn('stok', '<', 'stok_minimum')->where('stok', '>', 0);
            } elseif ($request->status_stok == 'aman') {
                $query->whereColumn('stok', '>=', 'stok_minimum');
            } elseif ($request->status_stok == 'menipis_habis') {
                $query->whereColumn('stok', '<', 'stok_minimum');
            }
        }

        // 4. 🔥 URUTAN SAKTI: Habis (1) -> Menipis (2) -> Aman (3) 🔥
        $query->orderByRaw("
            CASE 
                WHEN stok <= 0 THEN 1
                WHEN stok < stok_minimum THEN 2
                ELSE 3
            END ASC
        ")
        ->orderBy('outlet', 'asc')
        ->orderBy('nama_bahan', 'asc');

        $data = $query->get();

        return view('laporan_bahan_baku.index', compact('data'));
    }

    public function pdf(Request $request)
    {
        $query = BahanBaku::query();

        // 1. Filter Outlet (Biar PDF yang dicetak sesuai filter)
        if (auth()->user()->role == 'barista') {
            $query->where('outlet', session('outlet_aktif'));
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
            }
        }

        // 2. Filter Pencarian Nama Bahan
        if ($request->search_bahan) {
            $query->where('nama_bahan', 'LIKE', '%' . $request->search_bahan . '%');
        }

        // 3. Filter Status Stok
        if ($request->status_stok) {
            if ($request->status_stok == 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($request->status_stok == 'menipis') {
                $query->whereColumn('stok', '<', 'stok_minimum')->where('stok', '>', 0);
            } elseif ($request->status_stok == 'aman') {
                $query->whereColumn('stok', '>=', 'stok_minimum');
            } elseif ($request->status_stok == 'menipis_habis') {
                $query->whereColumn('stok', '<', 'stok_minimum');
            }
        }

        // 4. Urutan di PDF juga disamain
        $query->orderByRaw("
            CASE 
                WHEN stok <= 0 THEN 1
                WHEN stok < stok_minimum THEN 2
                ELSE 3
            END ASC
        ")
        ->orderBy('outlet', 'asc')
        ->orderBy('nama_bahan', 'asc');

        $data = $query->get();

        // 🔥 LOGIKA RENDER PDF 🔥
        // Pastikan nama view PDF-nya disesuaikan dengan file lu (misal: laporan_bahan_baku.pdf)
        $pdf = Pdf::loadView('laporan_bahan_baku.pdf', compact('data'));
        
        // Atur ukuran kertas
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan_Stok_Bahan_Baku_' . date('Ymd') . '.pdf');
    }
}