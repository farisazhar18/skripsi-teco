@extends('layouts.pos')

@section('content')

<h1 class="page-title">Detail Resep Produk</h1>

<div class="card">
    <h2>{{ $resepProduk->produk->nama_produk }}</h2>

<br>

<span class="badge badge-warning">
    {{ ucfirst($resepProduk->ukuran) }}
</span>

<span class="badge badge-success">
    {{ ucfirst($resepProduk->tipe) }}
</span>
</div>

@if(session('success'))
    <p class="success">{{ session('success') }}</p>
@endif

<!-- Gembok Tombol Tambah Detail Resep -->
@if(in_array(auth()->user()->role, ['operational_manager', 'owner']))
    <a href="/detail-resep-produk/create?resep_produk_id={{ $resepProduk->id }}" class="btn">
        Tambah Detail Resep
    </a>
    <br><br>
@else
    <br> <!-- Jarak pengganti kalau tombolnya hilang buat Barista -->
@endif

<div class="table-card">
<table>
    <tr>
        <th>No</th>
        <th>Bahan Baku</th>
        <th>Jumlah</th>
        
        <!-- Sembunyikan Judul Kolom Aksi dari Barista -->
        @if(in_array(auth()->user()->role, ['operational_manager', 'owner']))
            <th>Aksi</th>
        @endif
    </tr>

    @foreach($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->bahanBaku->nama_bahan }}</td>
        <td>
            <span class="badge badge-warning">
                {{ $item->jumlah }} {{ $item->bahanBaku->satuan }}
            </span>
        </td>
        
        <!-- Sembunyikan Tombol Edit dan Hapus dari Barista -->
        @if(in_array(auth()->user()->role, ['operational_manager', 'owner']))
            <td>
                <div class="action-buttons">
                    <a href="/detail-resep-produk/{{ $item->id }}/edit" class="action-link">
                        Edit
                    </a>

                    <!-- Tambah margin 0 biar sejajar -->
                    <form action="/detail-resep-produk/{{ $item->id }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn-danger"
                                onclick="return confirm('Yakin mau hapus?')">
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        @endif
    </tr>
    @endforeach
</table>
</div>

<br>

<br><br>

<a href="/resep-produk" class="btn-secondary">
    ← Kembali ke Data Resep
</a>

@endsection