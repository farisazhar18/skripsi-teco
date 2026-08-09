@extends('layouts.pos')

@section('content')

{{-- Ubah Judul Halaman Sesuai Hak Akses --}}
@if(in_array(auth()->user()->role, ['owner', 'operational_manager', 'logistik']))
    <h1 class="page-title">Edit Master Bahan Baku</h1>
@else
    <h1 class="page-title">Pengajuan Penyesuaian Stok</h1>
@endif

@if ($errors->any())
    <div class="card" style="margin-bottom: 20px;">
        <ul style="color:red; margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">

<form action="/bahan-baku/{{ $data->id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ======================================================= --}}
    {{-- TAMPILAN FULL EDIT (UNTUK BOS & LOGISTIK)               --}}
    {{-- ======================================================= --}}
    @if(in_array(auth()->user()->role, ['owner', 'operational_manager', 'logistik']))
        
        <div class="form-group">
            <label>Nama Bahan</label>
            <input type="text" name="nama_bahan" value="{{ $data->nama_bahan }}" required style="width: 100%;" class="form-control">
        </div>

        <div class="form-group">
            <label>Satuan Dasarnya</label>
            <input type="text" name="satuan" value="{{ $data->satuan }}" required style="width: 100%;" class="form-control">
        </div>

        <div class="form-group">
            <label>Outlet</label>
            <select name="outlet" class="form-control" required style="width: 100%;">
                <option value="hasanuddin" {{ $data->outlet == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                <option value="makmur" {{ $data->outlet == 'makmur' ? 'selected' : '' }}>Makmur</option>
            </select>
        </div>

        <div class="form-group">
            <label>Batas Stok Minimum</label>
            <input type="number" name="stok_minimum" value="{{ $data->stok_minimum }}" min="0" required style="width: 100%;" class="form-control">
        </div>

        <div class="form-group">
            <label>Stok Sekarang</label>
            <input type="number" name="stok" value="{{ $data->stok }}" min="0" required style="width: 100%;" class="form-control">
        </div>

        <input type="hidden" name="tipe_form" value="full_edit">

    {{-- ======================================================= --}}
    {{-- TAMPILAN KHUSUS BARISTA / KASIR (PENYESUAIAN STOK)      --}}
    {{-- ======================================================= --}}
    @else

        <div class="form-group">
            <label>Nama Bahan Baku</label>
            <input type="text" value="{{ $data->nama_bahan }} (Outlet: {{ ucfirst($data->outlet) }})" disabled style="background-color: #f3f4f6; width: 100%; font-weight: bold;" class="form-control">
        </div>

        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Stok di Sistem Saat Ini</label>
                <input type="text" value="{{ $data->stok }} {{ $data->satuan }}" readonly style="background-color: #f3f4f6; width: 100%;" class="form-control">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Stok Aktual (Fisik) <span style="color:red;">*</span></label>
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" name="stok_aktual" min="0" required placeholder="Jumlah fisik..." style="flex: 1; width: 100%;" class="form-control">
                    <span style="background-color: #e5e7eb; padding: 8px 12px; border-radius: 4px; font-weight: bold; color: #374151; border: 1px solid #d1d5db;">
                        {{ $data->satuan }}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Alasan Perbedaan Stok <span style="color:red;">*</span></label>
            <textarea name="alasan" rows="3" required placeholder="Misal: Basi, kadaluwarsa, tumpah, kemasan rusak, atau produk gagal..." style="width: 100%;" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label style="font-weight: bold; color: #1e293b;">Foto Bukti Fisik / Barang Rusak <span style="color:red;">*</span></label>
            <input type="file" name="foto_bukti" accept="image/*" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
            <small style="color: #64748b; font-style: italic; display: block; margin-top: 4px;">
                📌 Wajib lampirkan foto barang (misal: foto kemasan rusak/tanggal kadaluwarsa) sebagai bukti pertanggungjawaban.
            </small>
        </div>

        <input type="hidden" name="tipe_form" value="penyesuaian">

    @endif

    <div class="form-actions" style="margin-top: 25px; display: flex; gap: 10px;">
        <button type="submit" class="btn" style="background-color: #183f37; color: white;">
            Simpan / Ajukan
        </button>
        <a href="/bahan-baku" class="btn-secondary" style="padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid #ccc; color: #333;">
            ← Batal
        </a>
    </div>

</form>

</div>

@endsection