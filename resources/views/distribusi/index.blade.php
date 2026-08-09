@extends('layouts.pos')

@section('content')

<style>
    /* Efek hover baris tabel super smooth */
    .row-item:hover {
        background-color: #f0f5f3;
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb;
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
    }
</style>

<!-- JUDUL TENGAH -->
<h1 class="page-title">Data Distribusi</h1>

@if(session('success'))
    <div style="background-color: #d1fae5; color: #065f46; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #a7f3d0; text-align: center;">
        ✅ {{ session('success') }}
    </div>
@endif

<!-- ACTION BAR: TOMBOL TAMBAH DIBIKIN TINGGI 42px -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div>
        <a href="{{ url('/distribusi/create') }}" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px; text-decoration: none;">
            🚚 Tambah Distribusi
        </a>
    </div>
</div>

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: center; width: 20%;">Waktu Distribusi</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Outlet Tujuan</th>
                <th style="padding: 12px 15px; text-align: left; width: 45%;">Daftar Bahan Baku</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataGrouped as $batchKey => $items)
                @php 
                    // Memecah ID Grup menjadi nama outlet dan waktu
                    $parts = explode('|', $batchKey);
                    $outlet = $parts[0];
                    $waktu = $parts[1];
                @endphp
                <tr class="row-item">
                    <td style="text-align: center; vertical-align: middle; padding: 15px; color: #475569;">{{ $loop->iteration }}</td>
                    
                    <td style="text-align: center; vertical-align: middle; padding: 15px; font-size: 13px; color: #64748b;">
                        <strong style="color: #1e293b;">{{ date('d-m-Y', strtotime($waktu)) }}</strong><br>
                        <span style="font-size: 12px;">Jam: {{ date('H:i', strtotime($waktu)) }}</span>
                    </td>
                    
                    <td style="text-align: center; vertical-align: middle; padding: 15px;">
                        <span style="font-weight: 600; color: #334155;">{{ ucfirst($outlet) }}</span>
                    </td>
                    
                    <td style="padding: 15px; vertical-align: middle; text-align: left;">
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.6; color: #334155;">
                            @foreach($items as $item)
                                <li>{{ $item->bahanBaku->nama_bahan ?? '-' }} 
                                    <strong style="color: #0f172a;">({{ $item->jumlah }} {{ $item->satuan }})</strong>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    
                    <td style="text-align: center; vertical-align: middle; padding: 15px;">
                        <a href="{{ route('distribusi.print', ['outlet' => $outlet, 'waktu' => $waktu]) }}" style="background-color: #1a5c8b; border: none; padding: 6px 12px; border-radius: 6px; color: white; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-block;">
                            Print PDF
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                        📁 Belum ada data distribusi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('pembelian.stok') }}" class="btn-secondary" style="text-decoration: none; display: inline-block; padding: 10px 16px; border-radius: 8px;">
        ← Kembali ke Daftar Pengadaan
    </a>
</div>

@endsection