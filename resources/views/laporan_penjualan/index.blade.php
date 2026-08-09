@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 TAMBAHAN CSS BIAR UI MAKIN SMOOTH & MODERN */
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

<h1 class="page-title">Laporan Penjualan</h1>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="/laporan-penjualan">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 130px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 130px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Filter Outlet</label>
                <select name="outlet" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
                    <option value="">Semua Outlet</option>
                    <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                    <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Metode Bayar</label>
                <select name="metode_pembayaran" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
                    <option value="">Semua Metode</option>
                    <option value="Tunai" {{ request('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ request('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Cari Produk</label>
                <input type="text" name="search_produk" id="inputCariProduk" oninput="filterProduk()" value="{{ request('search_produk') }}" placeholder="Ketik nama produk..." style="width: 100%; height: 42px; padding: 0 12px; border-radius: 6px; border: 1px solid #d1d5db; outline: none;">
            </div>

            <div class="form-actions" style="margin-top: 0; margin-bottom: 0; display: flex; gap: 10px;">
                @if(request('tanggal_awal') || request('tanggal_akhir') || request('outlet') || request('metode_pembayaran') || request('search_produk'))
                    <a href="/laporan-penjualan" class="btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 6px; text-decoration: none;">Reset</a>
                @endif
            </div>

        </div>
    </form>
</div>

<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    
    <div style="background-color: #d1fae5; border: 1px solid #6ee7b7; padding: 10px 20px; border-radius: 8px; color: #065f46; font-weight: bold; font-size: 16px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        💰 Total Penjualan: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
    </div>

    <a href="/laporan-penjualan/pdf?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}&outlet={{ request('outlet') }}&metode_pembayaran={{ request('metode_pembayaran') }}&search_produk={{ request('search_produk') }}" target="_blank" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; background: #efe6d8; text-decoration: none; font-weight: 600; color: #183f37; border: 1px solid #d8cbb8; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: center; height: 42px;">
        📥 Export PDF
    </a>
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; overflow-x: auto;">
    <table style="border-collapse: collapse; width: 100%; min-width: 1100px;">

        <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #1e293b;">
            <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
            <th style="padding: 12px 15px; text-align: center; width: 10%;">Tanggal</th>
            <th style="padding: 12px 15px; text-align: center; width: 12%;">Outlet</th>
            <th style="padding: 12px 15px; text-align: left; width: 20%;">Produk</th>
            <th style="padding: 12px 15px; text-align: center; width: 8%;">Ukuran</th>
            <th style="padding: 12px 15px; text-align: center; width: 8%;">Tipe</th>
            <th style="padding: 12px 15px; text-align: center; width: 7%;">Jumlah</th>
            <th style="padding: 12px 15px; text-align: right; width: 10%;">Subtotal</th>
            <th style="padding: 12px 15px; text-align: center; width: 10%;">Metode Bayar</th>
            <th style="padding: 12px 15px; text-align: center; width: 10%;">Sumber Order</th>
        </tr>

        @forelse($data as $penjualan)
            @foreach($penjualan->detailPenjualans as $detail)
            <tr class="row-item" data-nama="{{ strtolower($detail->produk->nama_produk ?? '') }}">
                <td style="text-align: center; color: #475569; padding: 12px 15px; vertical-align: middle;">
                    {{ $loop->parent->iteration }}
                </td>

                <td style="text-align: center; color: #1e293b; font-weight: 500; padding: 12px 15px; vertical-align: middle;">
                    {{ date('d-m-Y', strtotime($penjualan->tanggal)) }}
                </td>

                <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                    @if($penjualan->outlet == 'hasanuddin')
                        <span style="font-weight: 600; color: #183f37;">Hasanuddin</span>
                    @elseif($penjualan->outlet == 'makmur')
                        <span style="font-weight: 600; color: #183f37;">Makmur</span>
                    @else
                        -
                    @endif
                </td>

                <td style="font-weight: 600; color: #1e293b; padding: 12px 15px; vertical-align: middle;">
                    {{ $detail->produk->nama_produk ?? '-' }}
                </td>

                <td style="text-align: center; padding: 12px 15px; vertical-align: middle; color: #475569;">
                    {{ ucfirst($detail->ukuran) }}
                </td>

                <td style="text-align: center; padding: 12px 15px; vertical-align: middle; color: #475569;">
                    {{ ucfirst($detail->tipe) }}
                </td>

                <td style="text-align: center; font-weight: bold; color: #0f172a; padding: 12px 15px; vertical-align: middle;">
                    {{ $detail->jumlah }}
                </td>

                <td style="text-align: right; font-weight: 600; color: #047857; padding: 12px 15px; vertical-align: middle;">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </td>

                <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                    @if($penjualan->metode_pembayaran == 'QRIS')
                        <span class="badge-modern" style="background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">QRIS</span>
                    @else
                        <span class="badge-modern" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Tunai</span>
                    @endif
                </td>

                <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                    @if($penjualan->sumber_order == 'customer_qr')
                        <span class="badge-modern" style="background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff;">🌐 Customer QR</span>
                    @else
                        <span class="badge-modern" style="background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">👨‍💼 Kasir</span>
                    @endif
                </td>
            </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="10" style="text-align:center; padding: 30px; color: #6b7280; font-style: italic;">
                    📁 Data penjualan tidak ditemukan.
                </td>
            </tr>
        @endforelse
    </table>
</div>

<script>
    function filterProduk() {
        let input = document.getElementById('inputCariProduk').value.toLowerCase();
        let rows = document.querySelectorAll('.row-item');
        
        rows.forEach(row => {
            let nama = row.getAttribute('data-nama');
            
            if (nama && nama.includes(input)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    }

    // Biar pas halaman diload, kalau inputannya ada isinya langsung nyaring otomatis
    window.onload = function() {
        if(document.getElementById('inputCariProduk').value !== '') {
            filterProduk();
        }
    };
</script>

@endsection