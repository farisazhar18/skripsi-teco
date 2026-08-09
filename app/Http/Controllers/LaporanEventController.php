<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // Wajib dipanggil biar model Event-nya kebaca

class LaporanEventController extends Controller
{
    // 1. TAMPILAN LAPORAN & FILTER
    public function laporan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $outlet = $request->input('outlet', 'semua');

        $query = Event::with('eventDetails.bahanBaku');

        if ($bulan != 'semua') { $query->whereMonth('tanggal_pelaksanaan', $bulan); }
        if ($tahun != 'semua') { $query->whereYear('tanggal_pelaksanaan', $tahun); }
        if ($outlet != 'semua') { $query->where('outlet', $outlet); }

        $events = $query->orderBy('tanggal_pelaksanaan', 'desc')->get();

        // Ngambil view dari folder baru lu
        return view('laporan_event.laporan', compact('events', 'bulan', 'tahun', 'outlet'));
    }

    // 2. EXPORT PDF
    public function exportLaporanPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $outlet = $request->input('outlet', 'semua');

        $query = Event::with('eventDetails.bahanBaku');

        if ($bulan != 'semua') { $query->whereMonth('tanggal_pelaksanaan', $bulan); }
        if ($tahun != 'semua') { $query->whereYear('tanggal_pelaksanaan', $tahun); }
        if ($outlet != 'semua') { $query->where('outlet', $outlet); }

        $events = $query->orderBy('tanggal_pelaksanaan', 'desc')->get();

        // Ngambil view PDF dari folder baru lu
        $pdf = \PDF::loadView('laporan_event.laporan_pdf', compact('events', 'bulan', 'tahun', 'outlet'));
        
        return $pdf->download('Laporan_Event_' . $bulan . '_' . $tahun . '.pdf');
    }
}