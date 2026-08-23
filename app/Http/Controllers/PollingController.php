<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PengajuanStok;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PollingController extends Controller
{
    /**
     * Cek jumlah pengajuan pengadaan yang masih pending (belum di-ACC).
     * Untuk: Owner & Operational Manager
     */
    public function pengajuanPengadaan()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['owner', 'operational_manager', 'logistik'])) {
            return response()->json(['count' => 0]);
        }

        $count = Pembelian::whereNull('status_acc')
            ->orWhere('status_acc', 'pending')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Cek jumlah pengajuan penyesuaian stok yang masih pending.
     * Untuk: Owner & Operational Manager
     */
    public function pengajuanStok()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['owner', 'operational_manager'])) {
            return response()->json(['count' => 0]);
        }

        $count = PengajuanStok::where('status', 'pending')->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Cek jumlah pesanan baru yang statusnya 'menunggu'.
     * Untuk: Kasir & Barista (filter per outlet)
     */
    public function pesananBaru()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['owner', 'operational_manager', 'kasir', 'barista'])) {
            return response()->json(['count' => 0]);
        }

        $query = Penjualan::where('status', 'menunggu');

        // Barista & Kasir cuma lihat outlet mereka sendiri
        if (in_array($user->role, ['barista', 'kasir'])) {
            $outlet = session('outlet_aktif');
            if ($outlet) {
                $query->where('outlet', $outlet);
            }
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}
