@extends('layouts.pos')

@section('content')
<h1 class="page-title">Tambah Paket Event Baru</h1>

<div class="form-card" style="margin: 0 auto; max-width: 600px;">
    <form action="{{ route('paket-event.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Nama Paket (Contoh: Paket A)</label>
            <input type="text" name="nama_paket" required>
        </div>

        <div class="form-group">
            <label>Deskripsi Singkat (Contoh: Banana Cake + 1 Minuman)</label>
            <input type="text" name="deskripsi" required>
        </div>

        <div class="form-group">
            <label>Pilih Makanan Tetap (Fixed Menu)</label>
            <select name="makanan_produk_id" required style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: white;">
                <option value="">-- Pilih Menu Makanan --</option>
                @foreach($makanans as $makanan)
                    <option value="{{ $makanan->id }}">{{ $makanan->nama_produk }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Harga Paket (Rp)</label>
            <input type="number" name="harga" min="0" placeholder="Contoh: 25000" required>
        </div>

        <div class="form-actions" style="margin-top: 30px; justify-content: flex-end;">
            <a href="{{ route('paket-event.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn">Simpan Paket</button>
        </div>
    </form>
</div>
@endsection