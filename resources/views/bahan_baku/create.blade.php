@extends('layouts.pos')

@section('content')

<h1 class="page-title">Tambah Bahan Baku</h1>

@if ($errors->any())
    <div class="card">
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">

<form action="/bahan-baku" method="POST">
    @csrf

    <div class="form-group">
        <label>Outlet</label>
        <select name="outlet" required>
            <option value="semua">Semua Outlet (Hasanuddin & Makmur)</option>
            <option value="hasanuddin">Hasanuddin</option>
            <option value="makmur">Makmur</option>
        </select>
    </div>

    <div class="form-group">
        <label>Nama Bahan</label>
        <input type="text"
               name="nama_bahan"
               required>
    </div>

    <div class="form-group">
        <label>Kategori Bahan</label>
        <select name="kategori" class="form-control" required>
            <!-- Biar pas di halaman edit otomatis kepilih kategori lamanya -->
            @php $kat = old('kategori'); @endphp
            
            <option value="">-- Pilih Kategori --</option>
            <option value="Kopi & Roastery" {{ $kat == 'Kopi & Roastery' ? 'selected' : '' }}>☕ Kopi & Roastery</option>
            <option value="Powder & Sirup" {{ $kat == 'Powder & Sirup' ? 'selected' : '' }}>🍯 Powder & Sirup</option>
            <option value="Susu & Cairan" {{ $kat == 'Susu & Cairan' ? 'selected' : '' }}>🥛 Susu & Cairan</option>
            <option value="Bahan Makanan" {{ $kat == 'Bahan Makanan' ? 'selected' : '' }}>🥐 Bahan Makanan</option>
            <option value="Packaging" {{ $kat == 'Packaging' ? 'selected' : '' }}>📦 Packaging (Cup, Lid, Sedotan)</option>
            <option value="Lainnya" {{ $kat == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>

    <div class="form-group">
        <label>Satuan</label>

        <select name="satuan" required>
            <option value="">-- Pilih Satuan --</option>
            <option value="ml">ml</option>
            <option value="gram">gram</option>
            <option value="pcs">pcs</option>
            <option value="pack">pack</option>
            <option value="botol">botol</option>
        </select>
    </div>

    <div class="form-group">
        <label>Stok Awal</label>
        <input type="number"
               name="stok"
               min="0"
               required>
    </div>

    <div class="form-group">
        <label>Stok Minimum</label>
        <input type="number"
               name="stok_minimum"
               min="0"
               required>
    </div>

    <div class="form-actions">
        <button type="submit">
            Simpan
        </button>

        <a href="/bahan-baku" class="btn-secondary">
            ← Kembali
        </a>
    </div>

</form>

</div>

@endsection