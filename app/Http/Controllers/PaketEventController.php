<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaketEvent;
use App\Models\Produk;

class PaketEventController extends Controller
{
    // Nampilin daftar paket
    public function index()
    {
        $pakets = PaketEvent::with('makanan')->get();
        return view('paket_event.index', compact('pakets'));
    }

    // Form tambah paket baru
    public function create()
    {
        $makanans = Produk::where('status', 'Otomatis')
                          ->where('kategori', 'Food')
                          ->where('is_event', true) // <--- Ini kunci utamanya bang!
                          ->get();
        
        return view('paket_event.create', compact('makanans'));
    }

    // Simpan paket ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'makanan_produk_id' => 'required|exists:produks,id',
            'harga' => 'required|numeric',
        ]);

        PaketEvent::create($request->all());

        return redirect()->route('paket-event.index')->with('success', 'Master Paket Event berhasil ditambahkan!');
    }

    // Hapus paket
    public function destroy($id)
    {
        $paket = PaketEvent::findOrFail($id);
        $paket->delete();

        return redirect()->back()->with('success', 'Paket Event berhasil dihapus!');
    }
}