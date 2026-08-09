<?php

namespace App\Http\Controllers;

use App\Models\ResepProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class ResepProdukController extends Controller
{
    public function index()
    {
        $data = ResepProduk::with('produk')
            ->orderByRaw("
            CASE
                WHEN tipe = 'food' THEN 1
                WHEN tipe = 'ice' THEN 2
                WHEN tipe = 'hot' THEN 3
                ELSE 4
            END
            ")
            ->orderBy('produk_id')
            ->orderByRaw("
            CASE
                WHEN ukuran = 'standar' THEN 1
                WHEN ukuran = 'reguler' THEN 2
                WHEN ukuran = 'large' THEN 3
                ELSE 4
            END
            ")
            ->get();

        return view('resep_produk.index', compact('data'));
    }

    public function create()
    {
        // Gembok keamanan buat OP Manager dan Owner
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $produks = Produk::orderBy('nama_produk', 'asc')->get();

        return view('resep_produk.create', compact('produks'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'ukuran' => 'required',
            'tipe' => 'required',
        ]);

        ResepProduk::create([
            'produk_id' => $request->produk_id,
            'ukuran' => $request->ukuran,
            'tipe' => $request->tipe,
        ]);

        return redirect('/resep-produk')->with('success', 'Resep produk berhasil ditambahkan.');
    }

    public function edit(ResepProduk $resepProduk)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $produks = Produk::all();

        return view('resep_produk.edit', compact('resepProduk', 'produks'));
    }

    public function update(Request $request, ResepProduk $resepProduk)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'ukuran' => 'required',
            'tipe' => 'required',
        ]);

        $resepProduk->update([
            'produk_id' => $request->produk_id,
            'ukuran' => $request->ukuran,
            'tipe' => $request->tipe,
        ]);

        return redirect('/resep-produk')->with('success', 'Resep produk berhasil diubah.');
    }

    public function destroy(ResepProduk $resepProduk)
    {
        if (!in_array(auth()->user()->role, ['operational_manager', 'owner'])) {
            abort(403, 'Akses Ditolak!');
        }

        $resepProduk->delete();

        return redirect('/resep-produk')->with('success', 'Resep produk berhasil dihapus.');
    }
}