@extends('layouts.pos')

@section('content')

<h1 class="page-title">Tambah Resep Produk</h1>

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

<form action="/resep-produk" method="POST">

    @csrf

    <div class="form-group">

        <label>Produk</label>

        <select name="produk_id" id="produk_id" required>

            <option value="">
-- Pilih Produk --
            </option>

            @foreach($produks as $produk)

                <option
                    value="{{ $produk->id }}"
                    data-kategori="{{ $produk->kategori }}">

                    {{ $produk->nama_produk }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group" id="ukuranBox">

        <label>Ukuran</label>

        <select name="ukuran" id="ukuran">

            <option value="reguler">
                Reguler
            </option>

            <option value="large">
                Large
            </option>

            <option value="standar">
                Standar
            </option>

        </select>

    </div>

    <div class="form-group" id="tipeBox">

        <label>Tipe</label>

        <select name="tipe" id="tipe">

            <option value="ice">
                Ice
            </option>

            <option value="hot">
                Hot
            </option>

            <option value="food">
                Food
            </option>

        </select>

    </div>

    <div class="form-actions">

        <button type="submit">
            Simpan
        </button>

        <a href="/resep-produk"
           class="btn-secondary">
            ← Kembali
        </a>

    </div>

</form>

</div>

<!-- Library Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    const produkSelect = document.getElementById('produk_id');
    const ukuranSelect = document.getElementById('ukuran');
    const tipeSelect = document.getElementById('tipe');

    function updateForm() {
        const selected = produkSelect.options[produkSelect.selectedIndex];
        
        if (!selected || selected.value === "") return;

        const kategori = selected.dataset.kategori;

        if (kategori === 'Food') {
            ukuranSelect.innerHTML = `
                <option value="standar">Standar</option>
            `;
            tipeSelect.innerHTML = `
                <option value="food">Food</option>
            `;
        } else {
            ukuranSelect.innerHTML = `
                <option value="reguler">Reguler</option>
                <option value="large">Large</option>
            `;
            tipeSelect.innerHTML = `
                <option value="ice">Ice</option>
                <option value="hot">Hot</option>
            `;
        }
    }

    // Eksekusi Tom Select
    new TomSelect("#produk_id", {
        create: false,
        sortField: {field: "text", direction: "asc"},
        onChange: function(value) {
            updateForm(); // Otomatis ngubah dropdown di bawahnya tiap kali ngetik/milih
        }
    });
</script>

@endsection