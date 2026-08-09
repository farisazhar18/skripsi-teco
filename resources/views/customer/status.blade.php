<!DOCTYPE html>
<html lang="id">
<head>
    <title>Status Pesanan - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; background: #f8f6f2; color: #183f37; padding: 20px; }
        .container { max-width: 500px; margin: auto; }
        .card { background: white; border-radius: 20px; padding: 24px; margin-bottom: 16px; box-shadow: 0 8px 20px rgba(24,63,55,0.06); }
        
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 10px; display: block; object-fit: cover; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: #6b6256; margin: 5px 0 0; font-size: 14px; }

        .order-number { font-size: 26px; font-weight: 700; margin: 10px 0; color: #183f37; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 999px; font-weight: 600; font-size: 14px; }
        
        .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .info-row span { font-weight: 600; color: #183f37; }
        
        .item { border-top: 1px dashed #eae5dc; padding-top: 12px; margin-top: 12px; font-size: 14px; }
        .item-title { font-weight: 600; margin-bottom: 4px; }
        
        .timeline { display: flex; flex-direction: column; gap: 12px; margin-top: 10px; }
        .timeline-step { display: flex; gap: 14px; padding: 14px; border-radius: 16px; background: #f5efe6; border: 2px solid transparent; transition: 0.3s; }
        .timeline-step.done { background: #e5f5ec; border-color: #b7e4c7; }
        .timeline-step.current { background: #fff3d8; border-color: #f0c36a; box-shadow: 0 4px 10px rgba(240,195,106,0.2); }
        .timeline-icon { width: 36px; height: 36px; border-radius: 50%; background: #d8cbb8; color: #183f37; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }
        .timeline-step.done .timeline-icon { background: #0f7a3a; color: white; }
        .timeline-step.current .timeline-icon { background: #b56a00; color: white; }
        
        .timeline-text strong { display: block; font-size: 14px; margin-bottom: 2px; }
        .timeline-text span { color: #6b6256; font-size: 12px; }

        .btn { display: block; width: 100%; text-align: center; background: #183f37; color: #efe6d8; padding: 14px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.2s; margin-top: 12px; border: none; cursor: pointer; }
        .btn-secondary { background: #efe6d8; color: #183f37; }
        .note { text-align: center; color: #6b6256; font-size: 13px; margin: 15px 0; }
    </style>
</head>
<body onclick="aktifkanAudio()">

@php
    $statusUrutan = ['menunggu', 'diproses', 'Siap diambil', 'selesai'];
    $statusIndex = array_search($penjualan->status, $statusUrutan);
    $prefix = ($penjualan->outlet == 'hasanuddin') ? 'TCH' : 'TCM';
    
    // Penentuan warna badge
    $badgeBg = '#fff3d8'; $badgeColor = '#b56a00';
    if(in_array($penjualan->status, ['Siap diambil', 'selesai'])) { $badgeBg = '#e5f5ec'; $badgeColor = '#0f7a3a'; }
@endphp

<div class="container">
    <div class="header">
        <img src="{{ asset('logo-terminal.png') }}" class="logo" alt="Terminal Coffee">
        <h1>Terminal Coffee</h1>
        <p>{{ $penjualan->status == 'menunggu_pembayaran' ? 'Menunggu Konfirmasi Kasir...' : 'Pesanan sedang diproses!' }}</p>
    </div>

    @if($penjualan->status === 'menunggu_pembayaran' && $penjualan->snap_token)
        <div class="card" style="text-align:center; background-color: #fff3cd; border: 2px dashed #e67e22;">
            <h3 style="color: #d35400; margin-top: 0;">Menunggu Pembayaran</h3>
            <p style="font-size: 13px; color: #856404;">Selesaikan pembayaran agar pesananmu segera diproses.</p>
            <button id="pay-button" class="btn">📱 Bayar Sekarang via QRIS</button>
            <button id="bypass-button" class="btn" style="background-color: #10b981; margin-top: 10px;">✅ Bypass Pembayaran (Testing Lokal)</button>
        </div>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            document.getElementById('bypass-button').addEventListener('click', function () {
                fetch("{{ route('customer.simulateSuccess', $penjualan->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    window.location.reload(); 
                });
            });

            document.getElementById('pay-button').addEventListener('click', function () {
                window.snap.pay('{{ $penjualan->snap_token }}', {
                    onSuccess: function(result){ 
                        fetch("{{ route('customer.simulateSuccess', $penjualan->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(() => {
                            window.location.reload(); 
                        });
                    },
                    onPending: function(result){ alert("Menunggu konfirmasi pembayaran!"); },
                    onError: function(result){ alert("Pembayaran gagal atau dibatalkan!"); }
                });
            });
        </script>
    @endif

    <div class="card" style="text-align:center;">
        <p style="margin:0 0 5px; font-size:13px; color:#6b6256;">Nomor Pesanan</p>
        <div class="order-number">#{{ $prefix }}-{{ date('ym', strtotime($penjualan->tanggal)) }}-{{ str_pad($penjualan->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}</div>
        <span class="status-badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">
            {{ ucwords(str_replace('_', ' ', $penjualan->status)) }}
        </span>
    </div>

    <div class="card">
        <div class="info-row"><div>Outlet</div><span>{{ ucfirst($penjualan->outlet) }}</span></div>
        <div class="info-row"><div>Pembayaran</div><span>{{ $penjualan->metode_pembayaran }}</span></div>
        <div class="info-row" style="margin-bottom:0; padding-top:10px; border-top:1px dashed #eae5dc;"><div>Total Tagihan</div><span style="color:#e67e22;">Rp {{ number_format($penjualan->total_harga) }}</span></div>
    </div>

    <div class="card">
        <h3 style="margin:0 0 10px; font-size:16px;">Detail Pesanan</h3>
        @foreach($penjualan->detailPenjualans as $detail)
            <div class="item">
                <div class="item-title">{{ $detail->jumlah }}x {{ $detail->produk->nama_produk }}</div>
                <div style="display:flex; justify-content:space-between; color:#6b6256;">
                    <span>{{ $detail->tipe == 'food' ? 'Food' : ucfirst($detail->ukuran) . ' - ' . ucfirst($detail->tipe) }}</span>
                    <span style="font-weight:600; color:#183f37;">Rp {{ number_format($detail->subtotal) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h3 style="margin:0 0 10px; font-size:16px;">Progress Pesanan</h3>
        @php
            $timeline = [
                'menunggu' => ['icon' => '✓', 'title' => 'Pesanan Diterima', 'desc' => 'Sudah masuk ke sistem kasir.'],
                'diproses' => ['icon' => '☕', 'title' => 'Sedang Disiapkan', 'desc' => 'Barista sedang meracik pesananmu.'],
                'Siap diambil' => ['icon' => '📢', 'title' => 'Siap Diambil', 'desc' => 'Hore! Silakan ambil di pick-up bar.'],
                'selesai' => ['icon' => '🎉', 'title' => 'Selesai', 'desc' => 'Terima kasih, selamat menikmati!'],
            ];
        @endphp
        <div class="timeline">
            @foreach($statusUrutan as $index => $status)
                <!-- 🔥 LOGIKA BARU: Cuma dirender JIKA index statusnya belum kelewatan/masih sama -->
                @if($index <= $statusIndex)
                    @php
                        $class = '';
                        if ($index < $statusIndex) $class = 'done';
                        elseif ($index == $statusIndex) $class = 'current';
                    @endphp
                    <div class="timeline-step {{ $class }}">
                        <div class="timeline-icon">{{ $timeline[$status]['icon'] }}</div>
                        <div class="timeline-text">
                            <strong>{{ $timeline[$status]['title'] }}</strong>
                            <span>{{ $timeline[$status]['desc'] }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div style="background-color: #e5f5ec; padding: 12px; border-radius: 12px; text-align: center; margin-bottom: 15px; border: 1px solid #b7e4c7;">
        <p style="margin: 0; font-size: 13px; font-weight: 500; color: #0f7a3a;">
            🕒 <strong>Harap tetap di halaman ini.</strong><br>
            Jangan tutup layar hingga terdengar bunyi notifikasi pesanan siap diambil ya!
        </p>
    </div>

    <p class="note" style="margin-top: 0;">Halaman ini akan me-refresh otomatis setiap 10 detik.<br>
    <span style="font-size: 11px;">(Pastikan volume HP nyala & ketuk layar sekali agar suara aktif)</span></p>

    <a href="{{ url()->current() }}" class="btn">↻ Refresh Manual</a>
    <a href="{{ route('customer.bill', $penjualan->id) }}" class="btn btn-secondary">📥 Download Struk PDF</a>
    <a href="{{ route('customer.menu', $penjualan->outlet) }}" class="btn" style="background:transparent; color:#183f37; border: 2px solid #183f37;">← Menu Utama</a>
</div>

<!-- 🔥 LOGIKA JAVASCRIPT NOTIFIKASI SUARA -->
<script>
    // Variabel buat nampung engine suara
    let suaraCustomer = new Audio("{{ asset('audio/ting-tiong.mp3') }}");
    let audioDiizinkan = false;

    // Fungsi biar browser ngasih izin muter suara setelah user nyentuh layar
    function aktifkanAudio() {
        if (!audioDiizinkan) {
            suaraCustomer.play().then(() => {
                suaraCustomer.pause();
                suaraCustomer.currentTime = 0;
                audioDiizinkan = true;
            }).catch(e => console.log(e));
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        let statusPesanan = "{{ $penjualan->status }}";
        let orderId = "{{ $penjualan->id }}";
        let storageKey = "notif_customer_" + orderId;

        // Cek kalau statusnya udah 'Siap diambil' DAN belum pernah dibunyiin
        if (statusPesanan === 'Siap diambil' && localStorage.getItem(storageKey) !== 'sudah_bunyi') {
            
            suaraCustomer.play().then(() => {
                // Tandain di memory HP customer kalau orderan ini udah dibunyiin
                localStorage.setItem(storageKey, 'sudah_bunyi');
            }).catch((err) => {
                console.log("Tertahan Autoplay. Customer belum nyentuh layar.");
            });
        }
    });
</script>

</body>
</html>