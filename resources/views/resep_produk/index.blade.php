@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 CSS BIAR TABELNYA SMOOTH KAYAK BAHAN BAKU */
    .row-item:hover {
        background-color: #f0f5f3; /* Highlight hijau pudar pas disorot mouse */
        transition: background-color 0.2s ease-in-out;
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px; /* Bikin ujungnya melengkung kayak kapsul */
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
    }
</style>

<!-- JUDUL KEMBALI KE TENGAH -->
<h1 class="page-title">Data Resep Produk</h1>

@if(session('success'))
    <div style="background-color: #d1fae5; color: #065f46; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #a7f3d0;">
        {{ session('success') }}
    </div>
@endif

<!-- ========================================== -->
<!-- HEADER: TAMBAH DI KIRI, CARI DI KANAN      -->
<!-- ========================================== -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    
    <!-- KIRI: Tombol Tambah Resep -->
    <div>
        @if(in_array(auth()->user()->role, ['operational_manager', 'owner']))
            <a href="/resep-produk/create" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px; white-space: nowrap;">
                + Tambah Resep
            </a>
        @endif
    </div>

    <!-- KANAN: Input Pencarian -->
    <div>
        <input type="text" id="searchInput" onkeyup="cariResep()" placeholder="Cari nama produk..." style="height: 42px; padding: 0 15px; width: 100%; min-width: 250px; border-radius: 6px; border: 1px solid #d1d5db; outline: none; background: white; color: #1e293b; font-size: 14px;">
    </div>

</div>

<div class="table-card" style="overflow-x:auto;">
    <table id="tabelResep" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; background-color: #f8fafc;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: left; width: 35%;">Produk</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Ukuran</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Tipe</th>
                <th style="padding: 12px 15px; text-align: center; width: 30%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <!-- 🔥 TAMBAHIN CLASS row-item BIAR EFEK HOVER NYALA -->
            <tr class="baris-resep row-item" style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center; color: #475569;">{{ $loop->iteration }}</td>
                
                <!-- NAMA PRODUK -->
                <td style="padding: 12px 15px; vertical-align: middle; text-align: left;">
                    <strong class="nama-produk" style="color: #1e293b; font-size: 15px;">{{ $item->produk->nama_produk }}</strong>
                </td>

                <!-- UKURAN DENGAN BADGE KAPSUL -->
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">
                    @if($item->ukuran == 'reguler')
                        <span class="badge-modern" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">Reguler</span>
                    @elseif($item->ukuran == 'large')
                        <span class="badge-modern" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Large</span>
                    @else
                        <span class="badge-modern" style="background: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">Standar</span>
                    @endif
                </td>

                <!-- TIPE DENGAN BADGE KAPSUL -->
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">
                    @if($item->tipe == 'ice')
                        <span class="badge-modern" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">Ice</span>
                    @elseif($item->tipe == 'hot')
                        <span class="badge-modern" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">Hot</span>
                    @else
                        <span class="badge-modern" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">Food</span>
                    @endif
                </td>

                <!-- AKSI -->
                <td style="padding: 12px 15px; vertical-align: middle; text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        
                        {{-- Tombol Detail --}}
                        <a href="/detail-resep-produk?resep_produk_id={{ $item->id }}" style="background: #e0e7ff; color: #3730a3; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #c7d2fe;">
                            Detail
                        </a>

                        @if(in_array(auth()->user()->role, ['operational_manager', 'owner']))
                            <a href="/resep-produk/{{ $item->id }}/edit" style="background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1;">
                                Edit
                            </a>

                            <form action="/resep-produk/{{ $item->id }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin mau hapus resep ini?')" style="background: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 6px; border: 1px solid #fecaca; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- SCRIPT UNTUK PENCARIAN INSTAN              -->
<!-- ========================================== -->
<script>
function cariResep() {
    let input = document.getElementById('searchInput');
    let filter = input.value.toLowerCase();
    let barisTabel = document.querySelectorAll('.baris-resep');

    barisTabel.forEach(function(row) {
        let namaProduk = row.querySelector('.nama-produk').textContent.toLowerCase();
        
        if (namaProduk.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

@endsection