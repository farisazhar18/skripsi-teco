@extends('layouts.pos')

@section('content')

<h1 class="page-title">Edit Produk</h1>

@if ($errors->any())
    <div class="card">
        <ul class="error-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">

<form action="/produk/{{ $produk->id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Foto Produk Saat Ini</label>
        <br>
        @if($produk->foto)
            <img src="{{ asset($produk->foto) }}" alt="Foto Produk" class="product-img-preview">
        @else
            <div class="product-img-empty">Belum ada foto</div>
        @endif
        
        <label>Ganti Foto Produk</label>
        <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
        <small class="text-muted">*Biarkan kosong jika tidak ingin mengganti foto saat ini.</small>
    </div>

    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text"
               name="nama_produk"
               value="{{ $produk->nama_produk }}"
               required>
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <select name="kategori" id="kategori" required>
            <option value="Coffee" {{ $produk->kategori == 'Coffee' ? 'selected' : '' }}>Coffee</option>
            <option value="Non Coffee" {{ $produk->kategori == 'Non Coffee' ? 'selected' : '' }}>Non Coffee</option>
            <option value="Food" {{ $produk->kategori == 'Food' ? 'selected' : '' }}>Food</option>
        </select>
    </div>

    <!-- INI CHECKBOX PAKET EVENT -->
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_event" value="1" class="checkbox-inline" {{ $produk->is_event ? 'checked' : '' }}>
            <span>Khusus / Bisa untuk Paket Event</span>
        </label>
    </div>

    <!-- INI CHECKBOX EXTRA SYRUP YANG BARU -->
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="bisa_extra_syrup" value="1" class="checkbox-inline" {{ $produk->bisa_extra_syrup ? 'checked' : '' }}>
            <span>Menu ini bisa tambah Extra Syrup (+ Rp 3.000)</span>
        </label>
    </div>

    <div class="form-group">
        <label>Harga Reguler</label>
        <input type="number"
               name="harga_reguler"
               value="{{ $produk->harga_reguler }}"
               required>
    </div>

    <div class="form-group" id="hargaLargeBox">
        <label>Harga Large</label>
        <input type="number"
               name="harga_large"
               value="{{ $produk->harga_large }}">
    </div>

    <div class="form-group" id="tersediaBox">
        <label>Tersedia</label>
        <div class="checkbox-row">
            <label>
                <input type="checkbox"
                       name="tersedia_hot"
                       class="checkbox-inline"
                       {{ $produk->tersedia_hot ? 'checked' : '' }}>
                Hot
            </label>

            <label>
                <input type="checkbox"
                       name="tersedia_ice"
                       class="checkbox-inline"
                       {{ $produk->tersedia_ice ? 'checked' : '' }}>
                Ice
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Tipe Produk</label>
        <select name="tipe_produk" id="tipe_produk" required>
            <option value="racikan" {{ $produk->tipe_produk == 'racikan' ? 'selected' : '' }}>Racikan</option>
            <option value="vendor" {{ $produk->tipe_produk == 'vendor' ? 'selected' : '' }}>Vendor</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit">Update</button>
        <a href="/produk" class="btn-secondary">← Kembali</a>
    </div>

</form>

</div>

@endsection