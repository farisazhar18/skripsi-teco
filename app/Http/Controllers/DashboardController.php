<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Distribusi;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $outlet = $request->outlet;
        
        // Cek filter grafik dari request (Default: 7days)
        $chartFilter = $request->input('chart_filter', '7days');

        if (in_array($role, ['kasir', 'barista'])) {
            $outlet = session('outlet_aktif');
        }

        $totalProduk = Produk::count();

        $totalBahanBaku = BahanBaku::select('nama_bahan')
            ->distinct()
            ->count('nama_bahan');

        // =========================================================
        // QUERY PENDAPATAN & TRANSAKSI (Otomatis Filter Outlet)
        // =========================================================
        $penjualanQuery = Penjualan::where('status', 'selesai'); // Kita hitung yang selesai aja buat revenue

        if ($outlet) {
            $penjualanQuery->where('outlet', $outlet);
        }

        $totalPenjualan = (clone $penjualanQuery)->count();

        $pendapatanHariIni = (clone $penjualanQuery)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('total_harga');

        $pendapatanBulanIni = (clone $penjualanQuery)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_harga');

        // =========================================================
        // 1. LOGIKA GRAFIK DINAMIS 2 LINE (HASANUDDIN VS MAKMUR)
        // =========================================================
        $chartDates = [];
        $chartDataHasanuddin = [];
        $chartDataMakmur = [];

        if ($chartFilter === '30days') {
            // Kalender Bulan Ini (Tgl 1 s/d Akhir Bulan Ini)
            $tahunIni = now()->year;
            $bulanIni = now()->month;
            $jumlahHari = now()->daysInMonth;

            for ($i = 1; $i <= $jumlahHari; $i++) {
                $date = Carbon::createFromDate($tahunIni, $bulanIni, $i);
                $chartDates[] = $date->translatedFormat('d M');
                
                $chartDataHasanuddin[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'hasanuddin')
                    ->whereDate('tanggal', $date->toDateString())
                    ->sum('total_harga');

                $chartDataMakmur[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'makmur')
                    ->whereDate('tanggal', $date->toDateString())
                    ->sum('total_harga');
            }
        } elseif ($chartFilter === '1year') {
            // Kalender Tahun Ini (Bulan Januari s/d Desember)
            $tahunIni = now()->year;

            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::createFromDate($tahunIni, $i, 1);
                $chartDates[] = $date->translatedFormat('M Y');
                
                $chartDataHasanuddin[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'hasanuddin')
                    ->whereMonth('tanggal', $i)
                    ->whereYear('tanggal', $tahunIni)
                    ->sum('total_harga');

                $chartDataMakmur[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'makmur')
                    ->whereMonth('tanggal', $i)
                    ->whereYear('tanggal', $tahunIni)
                    ->sum('total_harga');
            }
        } else {
            // Default: Mundur 7 Hari 
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartDates[] = $date->translatedFormat('d M');
                
                $chartDataHasanuddin[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'hasanuddin')
                    ->whereDate('tanggal', $date->toDateString())
                    ->sum('total_harga');

                $chartDataMakmur[] = Penjualan::where('status', 'selesai')
                    ->where('outlet', 'makmur')
                    ->whereDate('tanggal', $date->toDateString())
                    ->sum('total_harga');
            }
        }

        // =========================================================
        // 2. LOGIKA TOP 5 MENU TERLARIS 
        // =========================================================
        $topProduk = DetailPenjualan::with('produk')
            ->select('produk_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('penjualan', function ($q) use ($outlet) {
                if ($outlet) {
                    $q->where('outlet', $outlet);
                }
                $q->where('status', 'selesai');
            })
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();


        // =========================================================
        // 3. LOGIKA INVENTORI & STOK
        // =========================================================
        $totalPembelian = Pembelian::count();
        $totalDistribusi = Distribusi::when($outlet, function ($query) use ($outlet) {
                $query->where('outlet', $outlet);
            })
            ->count();

        $bahanQuery = BahanBaku::query();

        if ($outlet) {
            $bahanQuery->where('outlet', $outlet);
        }

        $stokMenipis = (clone $bahanQuery)
            ->whereColumn('stok', '<', 'stok_minimum')
            ->where('stok', '>', 0)
            ->count();

        $stokHabis = (clone $bahanQuery)
            ->where('stok', 0)
            ->count();

        $stokMenipisList = (clone $bahanQuery)
            ->whereColumn('stok', '<', 'stok_minimum')
            ->where('stok', '>', 0)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

        $stokHabisList = (clone $bahanQuery)
            ->where('stok', 0)
            ->orderBy('nama_bahan', 'asc')
            ->take(5)
            ->get();


        // =========================================================
        // 4. LOGIKA PESANAN / ANTRIAN
        // =========================================================
        $pesananMenunggu = Penjualan::where('status', 'menunggu')
            ->when($outlet, function ($query) use ($outlet) {
                $query->where('outlet', $outlet);
            })
            ->count();

        $pesananDiproses = Penjualan::where('status', 'diproses')
            ->when($outlet, function ($query) use ($outlet) {
                $query->where('outlet', $outlet);
            })
            ->count();

        $pesananSiapDiambil = Penjualan::where('status', 'Siap diambil')
            ->when($outlet, function ($query) use ($outlet) {
                $query->where('outlet', $outlet);
            })
            ->count();

        $pesananSelesaiHariIni = Penjualan::where('status', 'selesai')
            ->whereDate('tanggal', now()->toDateString())
            ->when($outlet, function ($query) use ($outlet) {
                $query->where('outlet', $outlet);
            })
            ->count();

        // =========================================================
        // LEMPAR DATA KE VIEW
        // =========================================================
        return view('dashboard.index', compact(
            'role',
            'outlet',
            'totalProduk',
            'totalBahanBaku',
            'totalPenjualan',
            'totalPembelian',
            'totalDistribusi',
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'stokMenipis',
            'stokHabis',
            'stokMenipisList',
            'stokHabisList',
            'pesananMenunggu',
            'pesananDiproses',
            'pesananSiapDiambil',
            'pesananSelesaiHariIni',
            'chartDates',
            'chartDataHasanuddin', // <-- ARRAY BARU HASANUDDIN
            'chartDataMakmur',     // <-- ARRAY BARU MAKMUR
            'topProduk',
            'chartFilter' 
        ));
    }
}