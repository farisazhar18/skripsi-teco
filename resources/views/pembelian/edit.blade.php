@extends('layouts.pos')

@section('content')

<h1 class="page-title">Edit Pengadaan Bahan Baku</h1>

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

<form action="/pembelian/{{ $pembelian->id }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Tanggal</label>

        <input type="date"
               name="tanggal"
               value="{{ $pembelian->tanggal }}"
               required>
    </div>

    <div class="form-group">
        <label>Bahan Baku</label>

        <select name="bahan_baku_id" required>

            <option value="">
                -- Pilih Bahan Baku --
            </option>

            @foreach($bahanBaku as $item)

                <option value="{{ $item->id }}"
                    {{ $pembelian->bahan_baku_id == $item->id ? 'selected' : '' }}>

                    {{ $item->nama_bahan }} ({{ $item->satuan }})

                </option>

            @endforeach

        </select>
    </div>

    <div class="form-group">
        <label>Jumlah</label>

        <input type="number"
               name="jumlah"
               value="{{ $pembelian->jumlah }}"
               min="1"
               required>
    </div>

    <div class="form-group">
        <label>Satuan</label>

        <select name="satuan_beli" required>

            <option value="ml"
                {{ $pembelian->satuan_beli == 'ml' ? 'selected' : '' }}>
                ml
            </option>

            <option value="liter"
                {{ $pembelian->satuan_beli == 'liter' ? 'selected' : '' }}>
                liter
            </option>

            <option value="gram"
                {{ $pembelian->satuan_beli == 'gram' ? 'selected' : '' }}>
                gram
            </option>

            <option value="kg"
                {{ $pembelian->satuan_beli == 'kg' ? 'selected' : '' }}>
                kg
            </option>

            <option value="pcs"
                {{ $pembelian->satuan_beli == 'pcs' ? 'selected' : '' }}>
                pcs
            </option>

            <option value="botol"
                {{ $pembelian->satuan_beli == 'botol' ? 'selected' : '' }}>
                botol
            </option>

            <option value="pack"
                {{ $pembelian->satuan_beli == 'pack' ? 'selected' : '' }}>
                pack
            </option>

        </select>
    </div>

    <div class="form-group">
        <label>Keterangan</label>

        <textarea name="keterangan"
                  rows="4">{{ $pembelian->keterangan }}</textarea>
    </div>

    <div class="form-actions">

        <button type="submit">
            Update
        </button>

        <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary">
            ← Kembali
        </a>

    </div>

</form>

</div>

@endsection