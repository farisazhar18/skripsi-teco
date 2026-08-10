<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-terminal.png') }}">

    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8f6f2; color: #183f37; margin: 0; padding: 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: white; border-radius: 24px; padding: 30px; max-width: 450px; width: 100%; box-shadow: 0 10px 30px rgba(24,63,55,0.08); text-align: center; }
        
        h2 { margin: 0 0 5px; font-size: 22px; font-weight: 700; }
        .total { font-size: 26px; font-weight: 700; color: #e67e22; margin: 10px 0 20px; }
        
        .alert-box { background: #fff3cd; color: #856404; padding: 18px; border-radius: 16px; margin: 20px 0; border: 1px solid #ffeeba; }
        .alert-box p { margin: 0 0 8px; font-size: 13px; font-weight: 600; }
        .alert-box h1 { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 1px; }

        .btn { width: 100%; padding: 16px; background: #183f37; color: #efe6d8; border: none; border-radius: 14px; font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.2s; text-decoration: none; display: block; }
        .btn:hover { background: #2e5a4f; }
        .btn-secondary { background: #efe6d8; color: #183f37; margin-top: 15px; }
        
        .detail-box { text-align: left; margin-top: 25px; border-top: 2px dashed #eae5dc; padding-top: 20px; }
        .detail-title { font-weight: 600; font-size: 14px; margin-bottom: 12px; }
        .detail-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: #6b6256; }
        .detail-row span:last-child { font-weight: 600; color: #183f37; }
    </style>
</head>
<body>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
<div class="card">
    @if($penjualan->metode_pembayaran == 'QRIS')
        <h2>Bayar Pesanan</h2>
        <div class="total">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</div>

        @if(!empty($snapToken))
            <button id="pay-button" class="btn">📱 Bayar Sekarang via QRIS</button>
            
            <!-- TOMBOL BYPASS UNTUK TESTING LOKAL -->
            <button id="bypass-button" class="btn" style="background-color: #10b981; margin-top: 10px;">✅ Bypass Pembayaran (Testing Lokal)</button>
            <!-- ================================== -->

            <script>
                // Bypass action
                document.getElementById('bypass-button').addEventListener('click', function () {
                    fetch("{{ route('customer.simulateSuccess', $penjualan->id) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.href = "{{ route('customer.status', $penjualan->id) }}"; 
                    });
                });

                document.getElementById('pay-button').addEventListener('click', function () {
                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function(result){ 
                            // Hit mock API dulu biar lunas, baru redirect
                            fetch("{{ route('customer.simulateSuccess', $penjualan->id) }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            }).then(() => {
                                window.location.href = "{{ route('customer.status', $penjualan->id) }}"; 
                            });
                        },
                        onPending: function(result){ alert("Menunggu konfirmasi pembayaran!"); },
                        onError: function(result){ alert("Pembayaran gagal atau dibatalkan!"); }
                    });
                });

                // Otomatis munculin pop-up pas halaman kebuka
                window.onload = function() {
                    setTimeout(function() {
                        document.getElementById('pay-button').click();
                    }, 500); // Jeda setengah detik biar nunggu halamannya bener-bener beres dimuat
                };
            </script>
        @else
            <p style="color: #c62828; font-weight: 600; font-size: 13px; background: #ffe1df; padding: 10px; border-radius: 10px;">Maaf, sistem QRIS sedang sibuk. Silakan lapor ke kasir.</p>
        @endif

    @else
        <h2>Pembayaran Tunai 💵</h2>
        <div class="alert-box">
            <p>Tunjukkan nomor pesanan ini ke kasir:</p>
            @php $prefix = ($penjualan->outlet == 'hasanuddin') ? 'TCH' : 'TCM'; @endphp
            <h1>#{{ $prefix }}-{{ date('ym', strtotime($penjualan->tanggal)) }}-{{ str_pad($penjualan->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}</h1>
        </div>
    @endif

    <div class="detail-box">
        <div class="detail-title">Ringkasan Pesanan</div>
        @foreach($penjualan->detailPenjualans as $d)
            <div class="detail-row">
                <span>{{ $d->jumlah }}x {{ $d->produk->nama_produk }}</span>
                <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <a href="{{ route('customer.status', $penjualan->id) }}" class="btn btn-secondary">
        Lewati & Lihat Status ➔
    </a>
</div>

</body>
</html>