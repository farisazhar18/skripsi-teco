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
<h1 class="page-title">Daftar Pengadaan & Stok Gudang</h1>

<div class="flex flex-wrap gap-md mb-md items-center">
    <a href="{{ route('pembelian.stokEvent') }}" class="btn btn-rekap-masuk" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
        🎪 Riwayat Stok dari Event
    </a>
</div>

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 8%;">No</th>
                <th style="padding: 12px 15px; text-align: left; width: 50%;">Nama Bahan</th>
                <th style="padding: 12px 15px; text-align: center; width: 25%;">Sisa Stok Tersedia</th>
                <th style="padding: 12px 15px; text-align: center; width: 17%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp <!-- Bikin nomor urut manual -->
            
            @foreach($data as $item)
                <!-- Cuma nampilin barang yang sisa distribusinya lebih dari 0 -->
                @if($item->sisa_distribusi > 0)
                <tr class="row-item">
                    <td style="padding: 12px 15px; text-align: center; color: #475569; vertical-align: middle;">{{ $no++ }}</td>
                    
                    <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                        <strong style="color: #1e293b; font-size: 15px; display: block; margin-bottom: 4px;">{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong>
                        
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            {{-- 🔥 PERBAIKAN: Pakai kolom 'tanggal_terima' biar akurat ngecek kapan barangnya masuk gudang 🔥 --}}
                            @if(isset($item->tanggal_terima) && \Carbon\Carbon::parse($item->tanggal_terima)->isToday())
                                <span style="background: #22c55e; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">
                                    ✨ BARU MASUK HARI INI
                                </span>
                            @endif


                        </div>
                    </td>
                    
                    <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                        <span class="badge-modern" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">
                            {{ intval($item->sisa_distribusi) }} {{ $item->bahanBaku->satuan ?? '' }}
                        </span>
                    </td>
                    
                    <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                        <a href="{{ route('distribusi.create', ['pembelian_id' => $item->id]) }}" class="btn" style="background: #0284c7; padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 6px; display: inline-block; color: white;">
                            Distribusi
                        </a>
                    </td>
                </tr>
                @endif
            @endforeach

            <!-- Kalau kebetulan semua stoknya udah 0 (habis) -->
            @if(collect($data)->where('sisa_distribusi', '>', 0)->count() == 0)
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                        📁 Semua stok bahan dari pengadaan saat ini sudah habis didistribusikan.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="text-decoration: none; display: inline-block; padding: 10px 16px; border-radius: 8px;">
        ← Kembali ke Pengajuan Pengadaan
    </a>
</div>

@endsection