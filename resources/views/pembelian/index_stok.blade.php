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

<div class="flex flex-wrap gap-md mb-md items-center" style="justify-content: space-between;">
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('pembelian.stokEvent') }}" class="btn btn-rekap-masuk" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
            🎪 Riwayat Stok dari Event
        </a>
        
        <!-- FORM PENCARIAN -->
        <form action="{{ route('pembelian.stok') }}" method="GET" style="margin: 0; display: flex; gap: 5px;">
            <input type="hidden" name="sort" value="{{ $sortBy ?? 'terbaru' }}">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama bahan..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; width: 220px;">
            <button type="submit" class="btn" style="background: #0284c7; padding: 8px 12px; border-radius: 6px; font-size: 13px; border: none; color: white;">🔍 Cari</button>
            @if(!empty($search))
                <a href="{{ route('pembelian.stok', ['sort' => $sortBy ?? 'terbaru']) }}" class="btn-secondary" style="padding: 8px 12px; border-radius: 6px; font-size: 13px; text-decoration: none;">Reset</a>
            @endif
        </form>
    </div>
    
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <span style="color: #64748b; font-size: 14px; font-weight: 600;">Urutkan:</span>
        <a href="{{ route('pembelian.stok', ['sort' => 'terbaru', 'search' => $search ?? '']) }}" class="btn-secondary" style="{{ ($sortBy ?? 'terbaru') == 'terbaru' ? 'background: #0f7a3a; color: white; border-color: #0f7a3a;' : '' }} padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 6px;">Waktu Masuk</a>
        <a href="{{ route('pembelian.stok', ['sort' => 'nama', 'search' => $search ?? '']) }}" class="btn-secondary" style="{{ ($sortBy ?? '') == 'nama' ? 'background: #0f7a3a; color: white; border-color: #0f7a3a;' : '' }} padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 6px;">Nama Bahan</a>
        <a href="{{ route('pembelian.stok', ['sort' => 'kategori', 'search' => $search ?? '']) }}" class="btn-secondary" style="{{ ($sortBy ?? '') == 'kategori' ? 'background: #0f7a3a; color: white; border-color: #0f7a3a;' : '' }} padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 6px;">Kategori</a>
    </div>
</div>

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: left; width: 15%;">Kategori</th>
                <th style="padding: 12px 15px; text-align: left; width: 35%;">Nama Bahan</th>
                <th style="padding: 12px 15px; text-align: center; width: 25%;">Sisa Stok Tersedia</th>
                <th style="padding: 12px 15px; text-align: center; width: 20%;">Aksi</th>
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
                        @if(!empty($item->bahanBaku->kategori))
                            <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                {{ $item->bahanBaku->kategori }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 12px; font-style: italic;">-</span>
                        @endif
                    </td>
                    
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
                    <td colspan="5" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                        @if(!empty($search))
                            🔍 Tidak ditemukan stok dengan nama bahan "{{ $search }}"
                        @else
                            📁 Semua stok bahan dari pengadaan saat ini sudah habis didistribusikan.
                        @endif
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