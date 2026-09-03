<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\RiwayatPenggunaanBahan;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;

class CleanupAbandonedCarts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Temukan semua riwayat stok yang "Ditahan" dan umurnya lebih dari 15 menit
        $expiredReservations = RiwayatPenggunaanBahan::where('keterangan', 'LIKE', 'Ditahan%')
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        if ($expiredReservations->count() > 0) {
            DB::beginTransaction();
            try {
                foreach ($expiredReservations as $riwayat) {
                    // Kembalikan stok ke BahanBaku
                    $bahan = BahanBaku::find($riwayat->bahan_baku_id);
                    if ($bahan) {
                        $bahan->stok += $riwayat->jumlah_terpakai;
                        $bahan->save();
                    }
                    
                    // Hapus riwayat penahanan
                    $riwayat->delete();
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Abaikan error pada background cleanup agar request utama tidak terganggu
            }
        }

        return $next($request);
    }
}
