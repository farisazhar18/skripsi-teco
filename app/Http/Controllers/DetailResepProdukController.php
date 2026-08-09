<?php

namespace App\Http\Controllers;

use App\Models\DetailResepProduk;
use App\Models\ResepProduk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class DetailResepProdukController extends Controller
{
    public function index(Request $request)
    {
        $resepProdukId = $request->resep_produk_id;

        $resepProduk = ResepProduk::with('produk')->findOrFail($resepProdukId);

        $data = DetailResepProduk::with('bahanBaku')
            ->where('resep_produk_id', $resepProdukId)
            ->get();

        return view('detail_resep_produk.index', compact('data', 'resepProduk'));
    }

    public function create(Request $request)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $resepProdukId = $request->resep_produk_id;

        $resepProduk = ResepProduk::with('produk')->findOrFail($resepProdukId);
        // Coba ini sementara untuk tes
        $bahanBakus = BahanBaku::all()->unique('nama_bahan');

        return view('detail_resep_produk.create', compact('resepProduk', 'bahanBakus'));
    }

    public function store(Request $request)
    {

        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
                abort(403, 'Akses Ditolak!');
        }

        $request->validate([
            'resep_produk_id' => 'required|exists:resep_produks,id',
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        DetailResepProduk::create([
            'resep_produk_id' => $request->resep_produk_id,
            'bahan_baku_id' => $request->bahan_baku_id,
            'jumlah' => $request->jumlah,
        ]);

        return redirect('/detail-resep-produk?resep_produk_id=' . $request->resep_produk_id)
            ->with('success', 'Detail resep berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $detail = DetailResepProduk::with('resepProduk.produk', 'bahanBaku')->findOrFail($id);
        $bahanBakus = BahanBaku::all()->unique('nama_bahan');

        return view('detail_resep_produk.edit', compact('detail', 'bahanBakus'));
    }

    public function update(Request $request, string $id)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $detail = DetailResepProduk::findOrFail($id);

        $detail->update([
            'bahan_baku_id' => $request->bahan_baku_id,
            'jumlah' => $request->jumlah,
        ]);

        return redirect('/detail-resep-produk?resep_produk_id=' . $detail->resep_produk_id)
            ->with('success', 'Detail resep berhasil diubah.');
    }

    public function destroy(string $id)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }
        
        $detail = DetailResepProduk::findOrFail($id);
        $resepProdukId = $detail->resep_produk_id;

        $detail->delete();

        return redirect('/detail-resep-produk?resep_produk_id=' . $resepProdukId)
            ->with('success', 'Detail resep berhasil dihapus.');
    }
}