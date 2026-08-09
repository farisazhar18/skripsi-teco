<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Deteksi Outlet untuk filter tampilan
        if (in_array($user->role, ['owner', 'operational_manager'])) {
            $outletDipilih = $request->input('outlet', 'hasanuddin');
        } else {
            $outletDipilih = session('outlet_aktif', 'hasanuddin'); 
        }

        $data = Produk::orderByRaw("
            CASE
                WHEN kategori = 'Coffee' THEN 1
                WHEN kategori = 'Non Coffee' THEN 2
                WHEN kategori = 'Food' THEN 3
                ELSE 4
            END
            ")
            ->orderBy('nama_produk')
            ->get();
            
        return view('produk.index', compact('data', 'outletDipilih'));
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'is_event' => 'nullable', // <-- UDAH DIBENERIN
            'harga_reguler' => 'nullable|numeric',
            'harga_large' => 'nullable|numeric',
            'tipe_produk' => 'required',
            'stok_produk' => 'nullable|numeric',
            'foto' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $namaFoto = null;
        if ($request->hasFile('foto')) {
            // 1. Bikin nama file unik
            $namaFotoBaru = time() . '.' . $request->foto->extension();
            
            // 2. JALUR BELAKANG: Pindahin file LANGSUNG ke folder 'public/foto_produk'
            $request->foto->move(public_path('foto_produk'), $namaFotoBaru);
            
            // 3. Siapin teks buat disimpen ke database
            $namaFoto = 'foto_produk/' . $namaFotoBaru;
        }

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'foto' => $namaFoto, // <-- UDAH DIBENERIN BIAR GAK DOBEL
            'kategori' => $request->kategori,
            'is_event' => $request->has('is_event'), // <-- INI YANG KETINGGALAN TADI
            'bisa_extra_syrup' => $request->has('bisa_extra_syrup'),
            'harga_reguler' => $request->harga_reguler,
            'harga_large' => $request->kategori == 'Food' ? null : $request->harga_large,
            'tersedia_hot' => $request->kategori == 'Food' ? false : $request->has('tersedia_hot'),
            'tersedia_ice' => $request->kategori == 'Food' ? false : $request->has('tersedia_ice'),
            'tipe_produk' => $request->kategori == 'Food' ? 'vendor' : $request->tipe_produk,
            'stok_produk' => $request->stok_produk,
            'status' => 'Otomatis', 
        ]);

        return redirect('/produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'is_event' => 'nullable', // <-- UDAH DIBENERIN JUGA
            'harga_reguler' => 'nullable|numeric',
            'harga_large' => 'nullable|numeric',
            'tipe_produk' => 'required',
            'stok_produk' => 'nullable|numeric',
            'foto' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $namaFoto = $produk->foto; // Ambil nama foto lama
        if ($request->hasFile('foto')) {
            // 1. Bikin nama file unik
            $namaFotoBaru = time() . '.' . $request->foto->extension();
            
            // 2. JALUR BELAKANG: Pindahin file LANGSUNG ke folder 'public/foto_produk'
            $request->foto->move(public_path('foto_produk'), $namaFotoBaru);
            
            // 3. Siapin teks buat disimpen ke database
            $namaFoto = 'foto_produk/' . $namaFotoBaru;
        }

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'foto' => $namaFoto, 
            'kategori' => $request->kategori,
            'is_event' => $request->has('is_event'), // <-- INI YANG KETINGGALAN TADI
            'bisa_extra_syrup' => $request->has('bisa_extra_syrup'),
            'harga_reguler' => $request->harga_reguler,
            'harga_large' => $request->kategori == 'Food' ? null : $request->harga_large,
            'tersedia_hot' => $request->kategori == 'Food' ? false : $request->has('tersedia_hot'),
            'tersedia_ice' => $request->kategori == 'Food' ? false : $request->has('tersedia_ice'),
            'tipe_produk' => $request->kategori == 'Food' ? 'vendor' : $request->tipe_produk,
            'stok_produk' => $request->stok_produk,
            'status' => 'Otomatis',
        ]);

        return redirect('/produk')->with('success', 'Produk berhasil diubah.');
    }

    public function show(Produk $produk) {}

    public function edit(Produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect('/produk')->with('success', 'Produk berhasil dihapus.');
    }
}