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

<h1 class="page-title">Bahan Baku Nonaktif</h1>
<p style="color: #64748b; margin-bottom: 20px;">Daftar bahan baku yang dinonaktifkan dari sistem. Anda dapat mengaktifkannya kembali jika diperlukan.</p>

@if(session('success'))
    <div class="text-center mb-md">
        <p class="success" style="background: #e5f5ec; padding: 10px; border-radius: 6px; color: #0f7a3a;">
            {{ session('success') }}
        </p>
    </div>
@endif

<div class="card mb-md">
    <div class="flex gap-lg items-center">
        <a href="{{ route('bahan-baku.index') }}" class="btn-secondary">
            ← Kembali ke Daftar Aktif
        </a>
    </div>
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
<table style="border-collapse: collapse; width: 100%;">
<!-- 🎨 HEADER TABEL: Warnanya dibedain dikit biar kontras sama Outlet -->
    <tr style="background-color: #64748b; color: white;">
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
                        
                        <!-- Tombol Aktifkan -->
                        <form action="{{ route('bahan-baku.aktifkan', $item->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn-success" onclick="return confirm('Yakin ingin mengaktifkan kembali bahan baku ini?')" style="padding: 6px 12px; font-size: 13px; border-radius: 6px; border: none; cursor: pointer;">
                                ✅ Aktifkan
                            </button>
                        </form>
                        
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



@endsection