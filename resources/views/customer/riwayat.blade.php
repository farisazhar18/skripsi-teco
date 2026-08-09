<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pesanan - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8f6f2; color: #183f37; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        h2 { text-align: center; margin-top: 0; font-weight: 700; }
        .user-greeting { text-align: center; color: #6b6256; font-size: 14px; margin-bottom: 24px; }
        
        .card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 16px; box-shadow: 0 8px 20px rgba(24,63,55,0.06); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .order-id { font-weight: 700; font-size: 16px; }
        .status-badge { font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 8px; background: #e5f5ec; color: #0f7a3a; }
        
        .order-meta { font-size: 13px; color: #6b6256; margin: 0 0 12px; }
        .item-list { border-top: 1px dashed #eae5dc; padding-top: 12px; margin-bottom: 12px; }
        .item-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; }
        
        .order-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 15px; border-top: 2px solid #183f37; padding-top: 12px; color: #e67e22; }
        
        .btn { background: #183f37; color: #efe6d8; border: none; padding: 14px; border-radius: 12px; text-decoration: none; font-weight: 600; display: block; text-align: center; width: 100%; margin-bottom: 10px; font-size: 14px; }
        .btn-secondary { background: #efe6d8; color: #183f37; }
    </style>
</head>
<body>
<div class="container">
    <h2>📜 Riwayat Pesanan</h2>
    <div class="user-greeting">
        Halo, <strong>{{ $riwayat->isNotEmpty() ? ($riwayat->first()->nama_customer ?? 'Pelanggan') : 'Pelanggan' }}</strong> ({{ $no_hp }})
    </div>
    
    @if($riwayat->isNotEmpty())
        <div class="card" style="text-align: center; background-color: #e5f5ec; border: 1px solid #b7e4c7;">
            <h3 style="margin: 0; color: #0f7a3a; font-size: 28px;">{{ $riwayat->count() }}</h3>
            <p style="margin: 5px 0 0; color: #183f37; font-weight: 500;">Total Pesanan Anda</p>
        </div>
        
        <button id="toggle-history" class="btn" style="background-color: #3b82f6; margin-bottom: 20px;">
            👀 Lihat Semua Pesanan
        </button>

        <div id="history-list" style="display: none;">
            @foreach($riwayat as $order)
                <div class="card">
                    <div class="card-header">
                        <div class="order-id">#{{ $order->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ date('ym', strtotime($order->tanggal)) }}-{{ str_pad($order->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}</div>
                        <div class="status-badge">{{ ucwords(str_replace('_', ' ', $order->status)) }}</div>
                    </div>
                    <div class="order-meta">
                        Outlet: <strong>{{ ucfirst($order->outlet) }}</strong> <br>
                        {{ $order->created_at->addHours(7)->format('d M Y, H:i') }}
                    </div>
                    
                    <div class="item-list">
                        @foreach($order->detailPenjualans as $detail)
                            <div class="item-row">
                                <span>{{ $detail->jumlah }}x {{ $detail->produk ? $detail->produk->nama_produk : 'Produk Dihapus' }}</span>
                                <span style="font-weight:600;">Rp {{ number_format($detail->subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="order-total">
                        <span>Total Belanja</span>
                        <span>Rp {{ number_format($order->total_harga) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <script>
            document.getElementById('toggle-history').addEventListener('click', function() {
                var list = document.getElementById('history-list');
                if (list.style.display === 'none') {
                    list.style.display = 'block';
                    this.innerHTML = 'Sembunyikan Pesanan';
                } else {
                    list.style.display = 'none';
                    this.innerHTML = 'Lihat Semua Pesanan';
                }
            });
        </script>
    @else
        <div class="card" style="text-align:center; padding: 40px 20px;">
            <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
            <p style="color:#6b6256; font-weight:500;">Belum ada riwayat pesanan.</p>
        </div>
    @endif
    
    <div style="margin-top: 30px;">
        <a href="{{ route('customer.riwayat.form', $outlet) }}" class="btn btn-secondary">🔍 Cari Nomor Lain</a>
        <a href="{{ route('customer.menu', $outlet) }}" class="btn">← Kembali ke Menu</a>
    </div>
</div>
</body>
</html>