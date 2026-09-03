@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 TAMBAHAN CSS BIAR UI MAKIN SMOOTH & MODERN */
    .row-item:hover {
        background-color: #f0f5f3; /* Highlight hijau super pudar pas disorot mouse */
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb; /* Garis tipis pembatas antar baris data */
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px; /* Bikin ujungnya membulat kayak kapsul (Pill) */
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        display: inline-block;
        text-align: center;
    }
</style>

<h1 class="page-title">Data Bahan Baku</h1>

@if(session('success'))
    <div class="text-center mb-md">
        <p class="success">
            {{ session('success') }}
        </p>
    </div>
@endif

<div class="card">
    <form method="GET" action="{{ url('/bahan-baku') }}">
        <div class="flex gap-xl flex-wrap" style="align-items: flex-end;">
            
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <label>Filter Outlet</label>
                <select name="outlet">
                    <option value="">Semua Outlet</option>
                    <option value="hasanuddin" {{ $outlet == 'hasanuddin' ? 'selected' : '' }}>
                        Hasanuddin
                    </option>
                    <option value="makmur" {{ $outlet == 'makmur' ? 'selected' : '' }}>
                        Makmur
                    </option>
                </select>
            </div>

            <div class="form-group mb-0" style="flex: 2; min-width: 250px;">
                <label>Cari Bahan Baku</label>
                <select id="filter-bahan" name="search_bahan" placeholder="Ketik nama bahan...">
                    <option value="">Semua Bahan Baku</option>
                    @foreach($listBahan as $b)
                        <option value="{{ $b->nama_bahan }}" {{ $search_bahan == $b->nama_bahan ? 'selected' : '' }}>
                            {{ $b->nama_bahan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions" style="margin-top: 0; margin-bottom: 0;">
                <button type="submit" class="btn">Cari</button>
                @if($outlet || $search_bahan)
                    <a href="{{ url('/bahan-baku') }}" class="btn-secondary">Reset</a>
                @endif
            </div>

        </div>
    </form>
</div>

<!-- 🎨 ACTION BAR: Tombol-tombol dibikin sejajar rapi di atas tabel -->
<div class="flex gap-lg mb-md items-center flex-wrap">
    @if(auth()->user()->role == 'logistik' || auth()->user()->role == 'operational_manager' || auth()->user()->role == 'owner')
        <a href="/bahan-baku/create" class="btn btn-action-bar">
            ➕ Tambah Bahan Baku
        </a>
        <a href="{{ route('bahan-baku.nonaktif') }}" class="btn" style="background: #e2e8f0; color: #475569;">
            🗑️ Bahan Baku Nonaktif
        </a>
    @endif

    <!-- TOMBOL REKAP KELUAR (YANG LAMA) -->
    <a href="{{ route('bahan-baku.rekap') }}" class="btn btn-rekap-keluar">
        📤 Rekap Pemakaian (Keluar)
    </a>

    <!-- 🔥 TOMBOL BARU: REKAP BARANG MASUK (DISTRIBUSI) 🔥 -->
    <a href="{{ route('bahan-baku.masuk') }}" class="btn btn-rekap-masuk">
        📥 Rekap Barang Masuk
    </a>

    @if(auth()->user()->role == 'logistik' || auth()->user()->role == 'operational_manager' || auth()->user()->role == 'owner')
        <a href="{{ route('laporan-bahan-baku.index') }}" class="btn btn-laporan">
            📋 Laporan Bahan Baku
        </a>
    @endif
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
<table style="border-collapse: collapse; width: 100%;">
    <!-- 🎨 HEADER TABEL: Warnanya dibedain dikit biar kontras sama Outlet -->
    <tr style="background-color: #245c50; color: white;">
        <th class="th-bahan">No</th>
        <th class="th-bahan">Outlet</th>
        <th class="th-bahan">Nama Bahan</th>
        <th class="th-bahan">Satuan</th>
        <th class="th-bahan">Stok</th>
        <th class="th-bahan">Stok Minimum</th>
        <th class="th-bahan text-center">Keterangan</th>
        <th class="th-bahan text-center">Aksi</th>
    </tr>

    <!-- LEVEL 1: GROUPING BERDASARKAN OUTLET -->
    @forelse($data->groupBy('outlet') as $outletName => $itemsPerOutlet)
        
        <!-- BARIS JUDUL OUTLET -->
        <tr class="outlet-row">
            <td colspan="8">
                🏬 Outlet: {{ $outletName == 'hasanuddin' ? 'Hasanuddin' : ($outletName == 'makmur' ? 'Makmur' : $outletName) }}
            </td>
        </tr>

        @php $no = 1; @endphp

        <!-- LEVEL 2: GROUPING BERDASARKAN KATEGORI DI DALAM OUTLET -->
        @foreach($itemsPerOutlet->groupBy('kategori') as $kategori => $items)
            
           <!-- BARIS JUDUL KATEGORI -->
            <tr class="kategori-row">
                <td colspan="8">
                    {{ $kategori }}
                </td>
            </tr>

            <!-- LEVEL 3: TAMPILIN DATA BAHAN BAKUNYA -->
            @foreach($items as $item)
            <tr class="row-item">
                <td class="text-center">{{ $no++ }}</td>

                <td>
                    @if($item->outlet == 'hasanuddin')
                        Hasanuddin
                    @elseif($item->outlet == 'makmur')
                        Makmur
                    @else
                        -
                    @endif
                </td>

                <td class="font-medium">{{ $item->nama_bahan }}</td>
                <td>{{ $item->satuan }}</td>
                <td class="stok-value">{{ $item->stok }} <span class="stok-unit">{{ $item->satuan }}</span></td>
                <td>{{ $item->stok_minimum }} <span class="stok-unit">{{ $item->satuan }}</span></td>

                <td class="text-center">
                    @if($item->stok <= 0)
                        <span class="badge-modern badge-stok-habis">Stok Habis</span>
                    @elseif($item->stok < $item->stok_minimum)
                        <span class="badge-modern badge-stok-menipis">Stok Menipis</span>
                    @else
                        <span class="badge-modern badge-stok-tersedia">Stok Tersedia</span>
                    @endif
                </td>

                <td>
                    <div class="flex gap-sm items-center justify-center">
                        
                        <!-- Tombol Edit: Cuma buat Owner (full edit) dan Barista (buat lapor selisih stok) -->
                        @if(auth()->user()->role == 'owner' || auth()->user()->role == 'barista')
                        <a href="/bahan-baku/{{ $item->id }}/edit" class="action-link" style="text-decoration: none; padding: 6px 12px; font-size: 13px; border-radius: 6px;">
                            Edit
                        </a>
                        @endif

                        <!-- Tombol Nonaktifkan: Bisa diakses Logistik, Manager, Owner -->
                        @if(auth()->user()->role == 'logistik' || auth()->user()->role == 'operational_manager' || auth()->user()->role == 'owner')
                            <form action="{{ route('bahan-baku.destroy', $item->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link" onclick="return confirm('Yakin ingin menonaktifkan bahan baku ini?')" style="color: #c62828; border: none; cursor: pointer; padding: 6px 12px; font-size: 13px; border-radius: 6px;">
                                    Nonaktifkan
                                </button>
                            </form>
                        @endif
                        
                    </div>
                </td>
            </tr>
            @endforeach

        @endforeach
        
    @empty
        <tr>
            <td colspan="8" class="empty-row">
                📁 Data bahan baku tidak ditemukan.
            </td>
        </tr>
    @endforelse
</table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#filter-bahan", {
            create: false,
            sortField: {field: "text", direction: "asc"},
            maxOptions: 8 
        });
    });
</script>

@endsection