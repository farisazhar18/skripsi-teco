@extends('layouts.pos')

@section('content')
    <style>
        /* 🎨 TAMBAHAN CSS BIAR UI MAKIN SMOOTH & MODERN */
        .row-item:hover {
            background-color: #f0f5f3; 
            transition: background-color 0.2s ease-in-out;
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

    <!-- ACTION BAR -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h1 class="page-title" style="margin-bottom: 0;">Riwayat Pesanan</h1>
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; padding: 0 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            ← Kembali
        </a>
    </div>

    <!-- 🔥 KOTAK PROFIL PELANGGAN (DIBIKIN DARK MODE BIAR EKSKLUSIF) 🔥 -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; background-color: #183f37; color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px;">
        <div>
            <h2 style="font-size: 26px; margin-bottom: 6px; color: #efe6d8;">{{ $nama_customer }}</h2>
            <div style="color: #cbd5e1; font-weight: 500; font-size: 15px;">📱 {{ $no_hp }}</div>
        </div>
        <div style="text-align: right; background: rgba(255,255,255,0.1); padding: 15px 20px; border-radius: 10px;">
            <div style="color: #cbd5e1; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Total Kontribusi Belanja</div>
            <div style="font-size: 26px; font-weight: bold; color: #d1fae5;">
                Rp {{ number_format($total_belanja, 0, ',', '.') }}
            </div>
            <div style="color: #cbd5e1; font-size: 14px; margin-top: 5px;">
                Dari <strong>{{ $total_kunjungan }}</strong> Transaksi
            </div>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; margin: 30px 0 20px;">
        <h2 style="color: #1e293b; margin: 0; font-size: 20px;">🛍️ Daftar Transaksi</h2>
    </div>

    <!-- LOOPING STRUK TRANSAKSI -->
    @forelse($riwayat_transaksi as $transaksi)
    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        
        <!-- HEADER STRUK -->
        <div style="background: #f8fafc; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="font-size: 16px; color: #0f172a;">
                    #{{ $transaksi->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ date('ym', strtotime($transaksi->tanggal)) }}-{{ str_pad($transaksi->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}
                </strong>
                <span style="color: #64748b; margin-left: 10px; font-size: 14px;">
                    | {{ date('d M Y • H:i', strtotime($transaksi->created_at)) }}
                </span>
            </div>
            <div style="color: #475569; font-size: 14px;">
                Outlet: <strong style="color: #1e293b;">{{ ucfirst($transaksi->outlet) }}</strong>
            </div>
        </div>

        <!-- TABEL BELANJAAN -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin: 0;">
                <thead>
                    <!-- 🔥 BAGIAN INI YANG GUA REVISI BANG, WARNA TEKS JADI PUTIH 🔥 -->
                    <tr style="background-color: #245c50; color: white;">
                        <th style="border-bottom: 1px solid #183f37; text-align: left; padding: 12px 24px; font-size: 14px;">Menu</th>
                        <th style="border-bottom: 1px solid #183f37; text-align: center; padding: 12px 24px; font-size: 14px;">Varian</th>
                        <th style="border-bottom: 1px solid #183f37; text-align: center; padding: 12px 24px; font-size: 14px;">Jumlah</th>
                        <th style="border-bottom: 1px solid #183f37; text-align: right; padding: 12px 24px; font-size: 14px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->detailPenjualans as $detail)
                    <tr class="row-item">
                        <td style="text-align: left; padding: 14px 24px;">
                            <strong style="color: #1e293b;">{{ $detail->produk->nama_produk }}</strong>
                        </td>
                        <td style="text-align: center; padding: 14px 24px; color: #475569; font-size: 14px;">
                            {{ strtoupper($detail->ukuran) }} - {{ strtoupper($detail->tipe) }}
                        </td>
                        <td style="text-align: center; padding: 14px 24px; font-weight: bold; color: #0f172a;">
                            {{ $detail->jumlah }}
                        </td>
                        <td style="text-align: right; padding: 14px 24px; font-weight: bold; color: #047857;">
                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- FOOTER STRUK -->
        <div style="background: #f8fafc; padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="color: #475569; display: flex; align-items: center; gap: 8px;">
                Metode Pembayaran: 
                @if(strtolower($transaksi->metode_pembayaran) == 'qris')
                    <span class="badge-modern" style="background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11px;">QRIS</span>
                @else
                    <span class="badge-modern" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 11px;">TUNAI</span>
                @endif
            </div>
            <div style="font-size: 18px; font-weight: bold; color: #0f172a;">
                Total: <span style="color: #047857;">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>
    
    @empty
    <!-- JAGA-JAGA KALAU PELANGGAN BARU DAFTAR TAPI BELUM ADA TRANSAKSI -->
    <div class="card" style="text-align: center; padding: 40px 20px; color: #64748b; font-style: italic;">
        🛍️ Pelanggan ini belum memiliki riwayat transaksi.
    </div>
    @endforelse
@endsection