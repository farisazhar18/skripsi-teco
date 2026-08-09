@extends('layouts.pos')

@section('content')

<style>
    .row-item:hover {
        background-color: #f0f5f3;
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb;
    }
</style>

<div class="flex justify-between items-center mb-md flex-wrap gap-md">
    <h1 class="page-title m-0">Riwayat Stok dari Event</h1>
    <a href="{{ route('pembelian.stok') }}" class="btn-secondary">
        ← Kembali ke Stok Gudang
    </a>
</div>

<!-- KARTU RINGKASAN & FILTER -->
<div class="card mb-md" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
    <div class="flex justify-between items-center flex-wrap gap-md">
        
        <!-- Info Total -->
        <div>
            <p class="text-muted m-0" style="font-size: 14px;">Total Barang Masuk dari Event</p>
            <h2 class="m-0" style="color: #059669;">{{ number_format($totalMasuk) }} <span style="font-size: 16px; font-weight: normal; color: #64748b;">Item / Pcs / Gram</span></h2>
        </div>

        <!-- Filter Tanggal -->
        <form action="{{ route('pembelian.stokEvent') }}" method="GET" class="flex gap-sm items-center m-0 flex-wrap">
            <div class="flex items-center gap-xs">
                <label style="font-size: 13px; color: #475569;">Mulai:</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <div class="flex items-center gap-xs">
                <label style="font-size: 13px; color: #475569;">Sampai:</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir->format('Y-m-d') }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <button type="submit" class="btn" style="height: 38px; margin: 0; padding: 0 16px;">Filter</button>
            <a href="{{ route('pembelian.stokEvent') }}" class="btn-secondary" style="height: 38px; display: flex; align-items: center; padding: 0 16px;">Reset</a>
        </form>

    </div>
</div>

<!-- TABEL DATA -->
<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #0f7a3a; color: white;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Tanggal</th>
                <th style="padding: 12px 15px; text-align: left; width: 35%;">Nama Event</th>
                <th style="padding: 12px 15px; text-align: left; width: 25%;">Bahan Baku yang Dikembalikan</th>
                <th style="padding: 12px 15px; text-align: center; width: 20%;">Jumlah Sisa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr class="row-item">
                    <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                    
                    <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                        <span style="font-weight: 600; color: #334155;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                    </td>
                    
                    <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                        <strong style="color: #1e293b; font-size: 15px;">🎪 {{ $item->nama_event }}</strong>
                    </td>
                    
                    <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                        {{ $item->bahanBaku->nama_bahan ?? 'Data Dihapus' }}
                    </td>
                    
                    <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                        <span style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 13px;">
                            +{{ intval($item->jumlah_konversi) }} {{ $item->bahanBaku->satuan ?? '' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #6b7280; font-style: italic;">
                        <div style="font-size: 24px; margin-bottom: 10px;">📦</div>
                        Tidak ada riwayat sisa bahan baku event pada periode tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
