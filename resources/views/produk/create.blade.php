@extends('layouts.pos')

@section('content')

<h1 class="page-title">Tambah Produk</h1>

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

<form action="/produk" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Foto Produk (Opsional)</label>
        <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
        <small class="text-muted">*Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
    </div>

    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" required>
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <select name="kategori" id="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="Coffee">Coffee</option>
            <option value="Non Coffee">Non Coffee</option>
            <option value="Food">Food</option>
        </select>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_event" value="1" class="checkbox-inline">
            <span>Khusus / Bisa untuk Paket Event</span>
        </label>
    </div>

    <!-- INI CHECKBOX EXTRA SYRUP YANG BARU -->
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="bisa_extra_syrup" value="1" class="checkbox-inline" {{ old('bisa_extra_syrup') ? 'checked' : '' }}>
            <span>Menu ini bisa tambah Extra Syrup (+ Rp 3.000)</span>
        </label>
    </div>

    <div class="form-group" id="hargaRegulerBox">
        <label>Harga Reguler</label>
        <input type="number" name="harga_reguler" required>
    </div>

    <div class="form-group" id="hargaLargeBox">
        <label>Harga Large</label>
        <input type="number" name="harga_large">
    </div>

    <div class="form-group" id="tersediaBox">
        <label>Tersedia</label>

        <div class="checkbox-row">
            <label>
                <input type="checkbox" name="tersedia_hot" class="checkbox-inline">
                Hot
            </label>

            <label>
                <input type="checkbox" name="tersedia_ice" class="checkbox-inline">
                Ice
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Tipe Produk</label>
        <select name="tipe_produk" id="tipe_produk" required>
            <option value="racikan">Racikan</option>
            <option value="vendor">Vendor</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit">
            Simpan
        </button>

        <a href="/produk" class="btn-secondary">
            ← Kembali
        </a>
    </div>

</form>

</div>

<script>
    const kategori = document.getElementById('kategori');
    const hargaLargeBox = document.getElementById('hargaLargeBox');
    const tersediaBox = document.getElementById('tersediaBox');
    const tipeProduk = document.getElementById('tipe_produk');
    

    function toggleFieldProduk() {
        if (kategori.value === 'Food') {
            hargaLargeBox.style.display = 'none';
            tersediaBox.style.display = 'none';
            stokBox.style.display = 'block'; // Tampilkan form stok
            tipeProduk.value = 'vendor';
        } else {
            hargaLargeBox.style.display = 'block';
            tersediaBox.style.display = 'block';
            stokBox.style.display = 'none'; // Sembunyikan form stok
            tipeProduk.value = 'racikan';
        }
    }

    kategori.addEventListener('change', toggleFieldProduk);
    toggleFieldProduk();
</script>

@endsection