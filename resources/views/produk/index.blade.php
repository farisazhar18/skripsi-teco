@extends('layouts.pos')

@section('content')

 <!-- KEMBALI KE JUDUL ASLI -->
    <h1 class="page-title">Data Produk</h1>


@if(session('success'))
    <div class="alert-success">
        ✅ {{ session('success') }}
    </div>
@endif

<!-- HEADER -->
<div class="produk-header">
    
    <div class="produk-actions">
        @if(in_array(auth()->user()->role, ['owner', 'operational_manager']))
            <form action="{{ url('/produk') }}" method="GET" class="flex flex-wrap gap-md items-center m-0">
                <select name="outlet" onchange="this.form.submit()" class="outlet-select">
                    <option value="hasanuddin" {{ $outletDipilih == 'hasanuddin' ? 'selected' : '' }}>Outlet: Hasanuddin</option>
                    <option value="makmur" {{ $outletDipilih == 'makmur' ? 'selected' : '' }}>Outlet: Makmur</option>
                </select>
            </form>

            <a href="/produk/create" class="btn btn-add">
                + Tambah Produk
            </a>
        @else
            <span class="outlet-badge-inline">
                Outlet: {{ ucfirst($outletDipilih) }}
            </span>
        @endif
    </div>
</div>

<div class="table-card" style="overflow-x:auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center;">No</th>
                <th style="padding: 12px 15px;">Info Produk</th>
                <th style="padding: 12px 15px;">Harga</th>
                <th style="padding: 12px 15px;">Detail Penyajian</th>
                <th style="padding: 12px 15px; text-align: center;">Status</th>
                @if(in_array(auth()->user()->role, ['owner', 'operational_manager']))
                    <th style="padding: 12px 15px; text-align: center;">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">{{ $loop->iteration }}</td>
                
                <!-- KOLOM INFO PRODUK -->
                <td style="padding: 12px 15px; vertical-align: middle;">
                    <div class="flex items-center gap-lg text-left">
                        <div class="produk-foto-box">
                            @if($item->foto)
                                <img src="{{ asset($item->foto) }}" alt="foto">
                            @else
                                <span class="produk-foto-placeholder">☕</span>
                            @endif
                        </div>
                        <div>
                            <strong class="produk-nama">{{ $item->nama_produk }}</strong>
                            <span class="produk-kategori">{{ $item->kategori }}</span>
                        </div>
                    </div>
                </td>
                
                <!-- KOLOM HARGA -->
                <td style="padding: 12px 15px; vertical-align: middle;">
                    <div class="text-left">
                        <div class="harga-reguler">Reg: Rp {{ number_format($item->harga_reguler) }}</div>
                        @if($item->harga_large)
                            <div class="harga-large">Lrg: Rp {{ number_format($item->harga_large) }}</div>
                        @endif
                    </div>
                </td>
                
                <!-- KOLOM DETAIL PENYAJIAN -->
                <td style="padding: 12px 15px; vertical-align: middle;">
                    <div class="flex gap-xs flex-wrap items-center">
                        <span class="{{ $item->tipe_produk == 'racikan' ? 'badge-racikan' : 'badge-vendor' }}">
                            {{ ucfirst($item->tipe_produk) }}
                        </span>
                        
                        @if($item->tersedia_hot) 
                            <span class="badge-hot">Hot</span> 
                        @endif
                        
                        @if($item->tersedia_ice) 
                            <span class="badge-ice">Ice</span> 
                        @endif
                    </div>
                </td>
                
                <!-- KOLOM STATUS -->
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">
                    @php $statusToko = $item->statusOtomatis($outletDipilih); @endphp
                    @if($statusToko == 'Aktif')
                        <span class="badge-tersedia">Tersedia</span>
                    @elseif($statusToko == 'Tidak Aktif')
                        <span class="badge-kosong">Kosong</span>
                    @else
                        <span class="badge-no-resep">No Resep</span>
                    @endif
                </td>

                <!-- KOLOM AKSI -->
                @if(in_array(auth()->user()->role, ['owner', 'operational_manager']))
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">
                    <div class="flex gap-sm justify-center">
                        <a href="/produk/{{ $item->id }}/edit" class="btn-edit">Edit</a>
                        
                        <form action="/produk/{{ $item->id }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin mau hapus produk ini?')" class="btn-hapus">
                             Hapus
                            </button>
                        </form>
                    </div>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection