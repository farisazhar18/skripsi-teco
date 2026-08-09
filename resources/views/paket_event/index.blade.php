@extends('layouts.pos')

@section('content')
<h1 class="page-title">Paket Event</h1>

@if(session('success'))
    <div style="margin-bottom: 20px;">
        <span class="success">✔️ {{ session('success') }}</span>
    </div>
@endif

<div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin: 0; color: #183f37; font-size: 20px;">Daftar Paket Bundling</h2>
        <small style="color: #6b6256;">Kelola paket event yang bisa dipilih oleh pelanggan</small>
    </div>
    <a href="{{ route('paket-event.create') }}" class="btn">➕ Tambah Paket Baru</a>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Paket</th>
                <th>Deskripsi</th>
                <th>Menu Makanan Utama</th>
                <th>Harga</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pakets as $index => $paket)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $paket->nama_paket }}</td>
                    <td>{{ $paket->deskripsi }}</td>
                    <td>{{ $paket->makanan->nama_produk ?? 'Data Terhapus' }}</td>
                    <td style="font-weight: bold; color: #0f7a3a;">Rp {{ number_format($paket->harga, 0, ',', '.') }}</td>
                    <td>
                        <form action="{{ route('paket-event.destroy', $paket->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" style="padding: 8px 12px; border-radius: 8px;">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b6256; padding: 30px;">Belum ada Master Paket Event.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection