@extends('layouts.pos')

@section('content')

<h1 class="page-title">Edit Resep Produk</h1>

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

<form action="/resep-produk/{{ $resepProduk->id }}" method="POST">

    @csrf
    @method('PUT')

    <div class="form-group">

        <label>Produk</label>

        <select name="produk_id" id="produk_id" required>

            @foreach($produks as $produk)

                <option
                    value="{{ $produk->id }}"
                    data-kategori="{{ $produk->kategori }}"
                    {{ $resepProduk->produk_id == $produk->id ? 'selected' : '' }}>

                    {{ $produk->nama_produk }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group" id="ukuranBox">

        <label>Ukuran</label>

        <select name="ukuran" id="ukuran">

            <option value="reguler"
                {{ $resepProduk->ukuran == 'reguler' ? 'selected' : '' }}>
                Reguler
            </option>

            <option value="large"
                {{ $resepProduk->ukuran == 'large' ? 'selected' : '' }}>
                Large
            </option>

            <option value="standar"
                {{ $resepProduk->ukuran == 'standar' ? 'selected' : '' }}>
                Standar
            </option>

        </select>

    </div>

    <div class="form-group" id="tipeBox">

        <label>Tipe</label>

        <select name="tipe" id="tipe">

            <option value="ice"
                {{ $resepProduk->tipe == 'ice' ? 'selected' : '' }}>
                Ice
            </option>

            <option value="hot"
                {{ $resepProduk->tipe == 'hot' ? 'selected' : '' }}>
                Hot
            </option>

            <option value="food"
                {{ $resepProduk->tipe == 'food' ? 'selected' : '' }}>
                Food
            </option>

        </select>

    </div>

    <div class="form-actions">

        <button type="submit">
            Update
        </button>

        <a href="/resep-produk" class="btn-secondary">
            ← Kembali
        </a>

    </div>

</form>

</div>

<script>
    const produkSelect = document.getElementById('produk_id');
    const ukuranSelect = document.getElementById('ukuran');
    const tipeSelect = document.getElementById('tipe');

    const currentUkuran = "{{ $resepProduk->ukuran }}";
    const currentTipe = "{{ $resepProduk->tipe }}";

    function updateForm() {
        const selected = produkSelect.options[produkSelect.selectedIndex];
        const kategori = selected.dataset.kategori;

        if (kategori === 'Food') {
            ukuranSelect.innerHTML = `
                <option value="standar">
                    Standar
                </option>
            `;

            tipeSelect.innerHTML = `
                <option value="food">
                    Food
                </option>
            `;
        } else {
            ukuranSelect.innerHTML = `
                <option value="reguler">
                    Reguler
                </option>

                <option value="large">
                    Large
                </option>
            `;

            tipeSelect.innerHTML = `
                <option value="ice">
                    Ice
                </option>

                <option value="hot">
                    Hot
                </option>
            `;

            ukuranSelect.value = currentUkuran;
            tipeSelect.value = currentTipe;
        }
    }

    produkSelect.addEventListener('change', updateForm);
    updateForm();
</script>

@endsection