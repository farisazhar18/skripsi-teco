<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with('bahanBaku')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if ($request->nama_bahan) {
            $query->whereHas('bahanBaku', function ($q) use ($request) {
                $q->where('nama_bahan', $request->nama_bahan);
            });
        }

        $data = $query->get();

        $bahanBakus = BahanBaku::select('nama_bahan', 'satuan')
            ->groupBy('nama_bahan', 'satuan')
            ->orderBy('nama_bahan', 'asc')
            ->get();

        return view('laporan_pembelian.index', compact(
            'data',
            'bahanBakus'
        ));
    }

    public function pdf(Request $request)
    {
        $query = Pembelian::with('bahanBaku')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if ($request->nama_bahan) {
            $query->whereHas('bahanBaku', function ($q) use ($request) {
                $q->where('nama_bahan', $request->nama_bahan);
            });
        }

        $data = $query->get();

        $pdf = Pdf::loadView(
            'laporan_pembelian.pdf',
            compact('data')
        );

        return $pdf->download('laporan-pengadaan.pdf');
    }
}