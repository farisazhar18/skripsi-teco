@extends('layouts.pos')

@section('content')
<h1 class="page-title">Tambah Detail Resep</h1>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="card">
    <h2>{{ $resepProduk->produk->nama_produk }}</h2>
    <br>
    <span class="badge badge-warning">{{ ucfirst($resepProduk->ukuran) }}</span>
    <span class="badge badge-success">{{ ucfirst($resepProduk->tipe) }}</span>
</div>

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
    <form action="/detail-resep-produk" method="POST">
        @csrf
        <input type="hidden" name="resep_produk_id" value="{{ $resepProduk->id }}">

        <div class="form-group">
            <label>Bahan Baku</label>
            <select id="select-bahan" name="bahan_baku_id" required>
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach($bahanBakus as $bahan)
                    <option value="{{ $bahan->id }}">
                        {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Jumlah Pemakaian</label>
            <input type="number" name="jumlah" min="1" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Simpan</button>
            <a href="/detail-resep-produk?resep_produk_id={{ $resepProduk->id }}" class="btn-secondary">← Kembali</a>
        </div>
    </form>
</div>

<script>
    new TomSelect("#select-bahan", {
        create: false,
        sortField: {field: "text", direction: "asc"}
    });
</script>

@endsection