<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $query = Distribusi::with(['pembelian', 'bahanBaku'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if ($request->outlet) {
            $query->where('outlet', $request->outlet);
        }

        $data = $query->get();

        return view('laporan_distribusi.index', compact('data'));
    }

    public function pdf(Request $request)
    {
        $query = Distribusi::with(['pembelian', 'bahanBaku'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        if ($request->outlet) {
            $query->where('outlet', $request->outlet);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('laporan_distribusi.pdf', compact('data'));

        return $pdf->download('laporan-distribusi.pdf');
    }
}