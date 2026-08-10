<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cek Riwayat Pesanan - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-terminal.png') }}">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8f6f2; color: #183f37; margin: 0; padding: 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: white; border-radius: 24px; padding: 35px 25px; max-width: 400px; width: 100%; box-shadow: 0 10px 30px rgba(24,63,55,0.08); text-align: center; }
        
        h2 { margin: 0 0 10px; font-weight: 700; font-size: 22px; }
        p { color: #6b6256; font-size: 13px; margin-bottom: 24px; line-height: 1.5; }
        
        input { width: 100%; padding: 16px; border-radius: 14px; border: 2px solid #eae5dc; margin-bottom: 20px; font-size: 16px; text-align: center; color: #183f37; font-weight: 600; outline: none; transition: 0.3s; }
        input:focus { border-color: #183f37; box-shadow: 0 4px 15px rgba(24,63,55,0.1); }
        
        button { background: #183f37; color: #efe6d8; padding: 16px; border: none; border-radius: 14px; width: 100%; font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.2s; }
        button:hover { background: #2e5a4f; transform: translateY(-2px); }
        
        .back-link { display: block; margin-top: 20px; text-decoration: none; color: #6b6256; font-weight: 600; font-size: 14px; }
        .back-link:hover { color: #183f37; }
    </style>
</head>
<body>
<div class="card">
    <h2>Cek Riwayat 🔍</h2>
    <p>Masukkan Nomor WhatsApp Anda saat memesan untuk melihat daftar riwayat transaksi.</p>
    
    <form action="{{ route('customer.riwayat.cari', $outlet) }}" method="POST">
        @csrf
        <input type="tel" name="no_hp" placeholder="0812345678xx" required autocomplete="off">
        <button type="submit">Lihat Riwayat</button>
    </form>
    
    <a href="{{ route('customer.menu', $outlet) }}" class="back-link">← Kembali ke Menu Utama</a>
</div>
</body>
</html>