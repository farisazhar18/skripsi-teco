<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembelian - Terminal Coffee</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            background: #ffffff;
        }
        /* Bungkus area cetak agar pas ukuran kertas struk kasir 80mm */
        #area-cetak-bill {
            width: 74mm;
            padding: 10px;
            background: #ffffff;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { vertical-align: top; padding: 2px 0; font-family: 'Courier New', Courier, monospace; }
        .header-title { font-size: 16px; font-weight: bold; margin: 0 0 3px 0; }
        .header-address { margin: 0 0 5px 0; padding: 0 5px; font-size: 11px; }
        .receipt-copy-text { font-size: 11px; letter-spacing: 1px; color: #444; margin: 2px 0 0 0; }
        .info-receipt { margin-bottom: 5px; }
        .item-name { padding-top: 6px; font-weight: bold; }
        .item-detail { color: #333; padding-bottom: 6px; }
        .variant-text { color: #555; display: block; font-weight: normal; font-size: 11px; }
    </style>
</head>
<body>

    <div id="area-cetak-bill">
        <div class="text-center">
            <p class="header-title">TERMINAL COFFEE</p>
            
            @if($penjualan->outlet == 'hasanuddin')
                <p class="header-address">Jl. Hasanudin No.3, Lebakgede, Kecamatan Coblong, Kota Bandung</p>
            @else
                <p class="header-address">Jl. Makmur No.2, Pasteur, Kec. Sukajadi, Kota Bandung</p>
            @endif
            <div class="divider"></div>
            <p class="receipt-copy-text">- RECEIPT COPY -</p>
        </div>

        <div class="divider"></div>

        <div class="info-receipt">
            <table class="table">
                <tr>
                    <td>No. Pesanan</td>
                    <td class="text-right">#{{ $penjualan->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ date('ym', strtotime($penjualan->tanggal)) }}-{{ str_pad($penjualan->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td class="text-right">{{ date('d M Y', strtotime($penjualan->tanggal)) }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td class="text-right">{{ ucfirst(auth()->user()->name ?? 'nama' ) }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <table class="table">
            @php $totalQty = 0; @endphp
            @foreach($penjualan->detailPenjualans as $detail)
                @php $totalQty += $detail->jumlah; @endphp
                <tr>
                    <td colspan="2" class="item-name">
                        {{ $detail->produk->nama_produk }}
                        @if(!empty($detail->keterangan))
                            <br><span style="font-weight:normal; font-style:italic; font-size:11px;">Req: "{{ $detail->keterangan }}"</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="item-detail">
                        {{ $detail->jumlah }} x Rp {{ number_format($detail->harga) }} 
                        <span class="variant-text">({{ ucfirst($detail->ukuran ?? '-') }} - {{ ucfirst($detail->tipe ?? '-') }})</span>
                    </td>
                    <td class="text-right item-detail" style="vertical-align: bottom;">
                        Rp {{ number_format($detail->subtotal) }}
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table class="table">
            <tr>
                <td>Jumlah Item</td>
                <td class="text-right">{{ $totalQty }}</td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td class="text-right">{{ $penjualan->metode_pembayaran }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="table">
            <tr style="font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($penjualan->total_harga) }}</td>
            </tr>
            @if($penjualan->metode_pembayaran == 'Tunai' && !empty($penjualan->uang_diterima))
                <tr>
                    <td>Tunai</td>
                    <td class="text-right">Rp {{ number_format($penjualan->uang_diterima) }}</td>
                </tr>
                <tr>
                    <td>Kembalian</td>
                    <td class="text-right">Rp {{ number_format($penjualan->uang_diterima - $penjualan->total_harga) }}</td>
                </tr>
            @endif
        </table>

        <div class="divider"></div>

        <div class="text-center" style="margin-top: 15px;">
            <p style="margin: 2px 0;">Terima Kasih Atas Kunjungan Anda!</p>
            <p style="margin: 2px 0;">Silakan Datang Kembali</p>
            
            <div class="divider"></div>
            <span>Wi-Fi: TERMINAL COFFEE 5G</span><br>
            <span>Pass: ResetIndonesia</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const element = document.getElementById('area-cetak-bill');
            
            // Trik Sakti: hitung tinggi asli konten struknya dalam pixel
            const elementHeightPx = element.offsetHeight;
            // Konversi dari pixel ke milimeter (mm) untuk setup jsPDF
            const elementHeightMm = Math.ceil(elementHeightPx * 0.264583) + 10; // Ditambah 10mm buat margin aman bawah

            const opt = {
                margin:       [0, 0, 0, 0], // Setel margin nol total
                filename:     'Struk-{{ $penjualan->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ $penjualan->id }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 3, useCORS: true, logging: false }, 
                // KUNCI UTAMA: tinggi kertas ([76, elementHeightMm]) sekarang otomatis elastis ngikutin panjang struk!
                jsPDF:        { unit: 'mm', format: [76, elementHeightMm], orientation: 'portrait' } 
            };
            
            // Proses download PDF berjalan, lalu tab otomatis ditutup
            html2pdf().set(opt).from(element).save().then(() => {
                setTimeout(() => {
                    window.close(); 
                }, 600);
            });
        });
    </script>
</body>
</html>