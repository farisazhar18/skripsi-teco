@extends('layouts.pos')

@section('content')
<h1 class="page-title">Edit Detail Resep</h1>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="card">
    <h2>{{ $detail->resepProduk->produk->nama_produk }}</h2>
    <br>
    <span class="badge badge-warning">{{ ucfirst($detail->resepProduk->ukuran) }}</span>
    <span class="badge badge-success">{{ ucfirst($detail->resepProduk->tipe) }}</span>
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
    <form action="/detail-resep-produk/{{ $detail->id }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Bahan Baku</label>
            <select id="select-bahan-edit" name="bahan_baku_id" required>
                @foreach($bahanBakus as $bahan)
                    <option value="{{ $bahan->id }}" 
                        {{ $detail->bahan_baku_id == $bahan->id ? 'selected' : '' }}>
                        {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Jumlah Pemakaian</label>
            <input type="number" name="jumlah" 
                   value="{{ $detail->jumlah }}" 
                   min="1" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Update</button>
            <a href="/detail-resep-produk?resep_produk_id={{ $detail->resep_produk_id }}" class="btn-secondary">
                ← Kembali
            </a>
        </div>
    </form>
</div>

<script>
    new TomSelect("#select-bahan-edit", {
        create: false,
        sortField: {field: "text", direction: "asc"}
    });
</script>

@endsection