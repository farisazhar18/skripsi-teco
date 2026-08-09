<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function pilih()
    {
        return view('outlet.pilih');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            // Tambahin kata 'event' di sini bang
            'outlet' => 'required|in:hasanuddin,makmur,event',
        ]);

        session(['outlet_aktif' => $request->outlet]);

        return redirect()->route('dashboard')
            ->with('success', 'Outlet berhasil dipilih.');
    }

    public function ganti()
    {
        session()->forget('outlet_aktif');

        return redirect()->route('outlet.pilih');
    }
}