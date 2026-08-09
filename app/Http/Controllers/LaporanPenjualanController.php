<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('detailPenjualans.produk')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if (auth()->user()->role == 'kasir') {
            $query->where('outlet', session('outlet_aktif'));
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
            }
        }

        if ($request->metode_pembayaran) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $data = $query->get();

        $totalPenjualan = $data->sum('total_harga');

        return view('laporan_penjualan.index', compact(
            'data',
            'totalPenjualan'
        ));
    }

    public function pdf(Request $request)
    {
        $query = Penjualan::with('detailPenjualans.produk')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if (auth()->user()->role == 'kasir') {
            $query->where('outlet', session('outlet_aktif'));
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
            }
        }

        if ($request->metode_pembayaran) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $data = $query->get();

        $totalPenjualan = $data->sum('total_harga');

        $pdf = Pdf::loadView(
            'laporan_penjualan.pdf',
            compact('data', 'totalPenjualan')
        );

        return $pdf->download('laporan-penjualan.pdf');
    }
}