@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 CSS BIAR UI MAKIN SMOOTH & MODERN */
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
        letter-spacing: 0.3px;
        display: inline-block;
        text-align: center;
    }
</style>

<h1 class="page-title">Laporan Pengadaan</h1>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="/laporan-pembelian">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 180px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Filter Bahan Baku</label>
                <select name="nama_bahan" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
                    <option value="">Semua Bahan</option>
                    @foreach($bahanBakus as $bahan)
                        <option value="{{ $bahan->nama_bahan }}" {{ request('nama_bahan') == $bahan->nama_bahan ? 'selected' : '' }}>
                            {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 220px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Cari Bahan Baku</label>
                <input type="text" name="search_bahan" id="inputCariBahan" oninput="filterBahan()" value="{{ request('search_bahan') }}" placeholder="Ketik nama bahan..." style="width: 100%; height: 42px; padding: 0 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            </div>

            <div style="display: flex; gap: 8px; align-items: flex-end;">
                @if(request('tanggal_awal') || request('tanggal_akhir') || request('nama_bahan') || request('search_bahan'))
                    <a href="/laporan-pembelian" class="btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 8px; border: 1px solid #cbd5e1; text-decoration: none; color: #475569; font-weight: 600;">
                        Reset
                    </a>
                @endif
            </div>

        </div>
    </form>
</div>

<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: flex-end;">
    <a href="/laporan-pembelian/pdf?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}&nama_bahan={{ request('nama_bahan') }}&search_bahan={{ request('search_bahan') }}" target="_blank" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; background: #efe6d8; text-decoration: none; font-weight: 600; color: #183f37; border: 1px solid #d8cbb8; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        📥 Export PDF
    </a>
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; overflow-x: auto;">
    <table style="border-collapse: collapse; width: 100%; min-width: 900px;">

        <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #1e293b;">
            <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
            <th style="padding: 12px 15px; text-align: center; width: 15%;">Tanggal</th>
            <th style="padding: 12px 15px; text-align: left; width: 35%;">Nama Bahan</th>
            <th style="padding: 12px 15px; text-align: center; width: 25%;">Jumlah Beli</th>
            <th style="padding: 12px 15px; text-align: center; width: 20%;">Sisa Stok</th>
        </tr>

        @forelse($data as $item)
        <tr class="row-item" data-nama="{{ strtolower($item->bahanBaku->nama_bahan ?? '') }}">
            <td style="text-align: center; color: #475569; padding: 12px 15px; vertical-align: middle;">
                {{ $loop->iteration }}
            </td>

            <td style="text-align: center; color: #1e293b; font-weight: 500; padding: 12px 15px; vertical-align: middle;">
                {{ date('d-m-Y', strtotime($item->tanggal)) }}
            </td>

            <td style="font-weight: 600; color: #1e293b; padding: 12px 15px; vertical-align: middle;">
                {{ $item->bahanBaku->nama_bahan ?? '-' }}
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                <span class="badge-modern" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7; font-size: 14px;">
                    +{{ $item->jumlah }} <span style="font-size: 12px; font-weight: normal;">{{ $item->satuan_beli }}</span>
                </span>
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle; font-weight: 600; color: #b45309;">
                {{ $item->sisa_stok ?? ($item->sisa_distribusi ?? ($item->bahanBaku->stok ?? 0)) }} <span style="font-size: 12px; color: #6b7280; font-weight: normal;">{{ $item->bahanBaku->satuan ?? '' }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; padding: 30px; color: #6b7280; font-style: italic;">
                📦 Data laporan pengadaan tidak ditemukan.
            </td>
        </tr>
        @endforelse

    </table>
</div>

<script>
    function filterBahan() {
        let input = document.getElementById('inputCariBahan').value.toLowerCase();
        let rows = document.querySelectorAll('.row-item');
        
        rows.forEach(row => {
            let nama = row.getAttribute('data-nama');
            
            if (nama.includes(input)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    }

    // Biar pas halaman diload, kalau inputannya ada isinya langsung nyaring otomatis
    window.onload = function() {
        if(document.getElementById('inputCariBahan').value !== '') {
            filterBahan();
        }
    };
</script>

@endsection