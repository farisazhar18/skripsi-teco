<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\PengajuanStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanStokController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Pengecekan aman buat Owner dan Operational Manager
        if ($user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Anda tidak memiliki akses ke halaman persetujuan stok.');
        }

        $pengajuan = PengajuanStok::with(['bahanBaku', 'user'])
                        ->where('status', 'pending')
                        ->latest()
                        ->get();

        return view('pengajuan_stok.index', compact('pengajuan'));
    }

    public function approve($id)
    {
        $user = auth()->user();

        // Pengecekan aman buat Owner dan Operational Manager
        if ($user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Hanya Operational Manager atau Owner yang dapat menyetujui pengajuan.');
        }

        $pengajuan = PengajuanStok::findOrFail($id);
        
        DB::beginTransaction();

        try {
            $bahanBaku = BahanBaku::findOrFail($pengajuan->bahan_baku_id);
            $bahanBaku->stok = $pengajuan->stok_aktual; 
            $bahanBaku->save();

            $pengajuan->status = 'disetujui';
            $pengajuan->save();

            DB::commit();

            return redirect('/pengajuan-stok')->with('success', 'Pengajuan berhasil di-ACC. Stok bahan baku (' . $bahanBaku->nama_bahan . ') telah terupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/pengajuan-stok')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}