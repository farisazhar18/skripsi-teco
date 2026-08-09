<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $outletFilter = $request->outlet; // Tangkap filter outlet dari dropdown (khusus owner)

        // Mulai query dasar
        $query = Penjualan::whereNotNull('no_hp');

        // LOGIKA FILTER OUTLET
        if (auth()->user()->role == 'kasir') {
            // Jika kasir, kunci datanya cuma buat outlet tempat dia login
            $query->where('outlet', session('outlet_aktif'));
        } else {
            // Jika owner dan dia milih filter outlet tertentu (bukan "semua")
            if ($outletFilter && $outletFilter != 'semua') {
                $query->where('outlet', $outletFilter);
            }
        }

        // LOGIKA PENCARIAN (NAMA / NO HP)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_hp', 'like', "%{$search}%")
                  ->orWhere('nama_customer', 'like', "%{$search}%");
            });
        }

        $pelanggans = $query->select(
                'no_hp',
                DB::raw('MAX(nama_customer) as nama_customer'),
                DB::raw('COUNT(id) as total_kunjungan'),
                DB::raw('SUM(total_harga) as total_belanja'),
                DB::raw('MAX(tanggal) as kunjungan_terakhir')
            )
            ->groupBy('no_hp')
            ->orderBy('kunjungan_terakhir', 'desc')
            ->get();

        return view('pelanggan.index', compact('pelanggans', 'search', 'outletFilter'));
    }

    public function show($no_hp)
    {
        // Query untuk ambil detail pelanggan
        $query = Penjualan::where('no_hp', $no_hp)->whereNotNull('nama_customer');

        // Query untuk ambil list transaksinya
        $historyQuery = Penjualan::with('detailPenjualans.produk')->where('no_hp', $no_hp);

        // Kunci history juga kalau yang login kasir
        if (auth()->user()->role == 'kasir') {
            $query->where('outlet', session('outlet_aktif'));
            $historyQuery->where('outlet', session('outlet_aktif'));
        }

        $nama_customer = $query->orderBy('created_at', 'desc')->value('nama_customer') ?? 'Pelanggan Tanpa Nama';
        $riwayat_transaksi = $historyQuery->orderBy('created_at', 'desc')->get();

        $total_belanja = $riwayat_transaksi->sum('total_harga');
        $total_kunjungan = $riwayat_transaksi->count();

        return view('pelanggan.show', compact('no_hp', 'nama_customer', 'riwayat_transaksi', 'total_belanja', 'total_kunjungan'));
    }
}