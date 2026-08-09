<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pilih Pembayaran - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8f6f2; color: #183f37; margin: 0; padding: 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: white; border-radius: 24px; padding: 35px 25px; max-width: 400px; width: 100%; box-shadow: 0 10px 30px rgba(24,63,55,0.08); text-align: center; }
        
        h2 { margin: 0 0 10px; font-weight: 600; font-size: 16px; color: #6b6256; }
        h1 { margin: 0 0 15px; font-weight: 700; font-size: 32px; color: #e67e22; }
        p { font-size: 13px; color: #183f37; margin-bottom: 25px; line-height: 1.5; }
        
        .btn-qris { width: 100%; padding: 16px; margin-bottom: 12px; background: #183f37; color: #efe6d8; border: none; border-radius: 14px; font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.2s; }
        .btn-qris:hover { background: #2e5a4f; transform: translateY(-2px); }
        .btn-tunai { width: 100%; padding: 16px; background: transparent; color: #183f37; border: 2px solid #183f37; border-radius: 14px; font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.2s; }
        .btn-tunai:hover { background: #f8f6f2; }
    </style>
</head>
<body>
<div class="card">
    <h2>Total Tagihan</h2>
    <h1>Rp {{ number_format($penjualan->total_harga) }}</h1>
    <p>Pilih metode pembayaran untuk pesanan <br><strong>#{{ $penjualan->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ str_pad($penjualan->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
    
    <form action="{{ route('customer.prosesPembayaran', [$outlet, $penjualan->id]) }}" method="POST">
        @csrf
        <button type="submit" name="metode" value="qris" class="btn-qris">📱 Bayar via QRIS / E-Wallet</button>
        <button type="submit" name="metode" value="tunai" class="btn-tunai">💵 Tunai (Bayar di Kasir)</button>
    </form>
</div>
</body>
</html>