<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #183f37;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 4px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #183f37;
            color: white;
            padding: 7px;
            border: 1px solid #183f37;
        }

        td {
            padding: 7px;
            border: 1px solid #999;
        }

        .total {
            margin-top: 15px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>TERMINAL COFFEE</h2>
    <p>Laporan Penjualan</p>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
    @if(request('outlet'))
        <p>
            Outlet:
            {{ request('outlet') == 'hasanuddin' ? 'Hasanuddin' : 'Makmur' }}
        </p>
    @endif
</div>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Outlet</th>
        <th>Produk</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
        <th>Metode</th>
        <th>Sumber Order</th>
    </tr>

    @foreach($data as $penjualan)
        @foreach($penjualan->detailPenjualans as $detail)
        <tr>
            <td>{{ $loop->parent->iteration }}</td>

            <td>
                {{ date('d-m-Y', strtotime($penjualan->tanggal)) }}
            </td>

            <td>
                @if($penjualan->outlet == 'hasanuddin')
                    Hasanuddin
                @elseif($penjualan->outlet == 'makmur')
                    Makmur
                @else
                    -
                @endif
            </td>

            <td>{{ $detail->produk->nama_produk }}</td>

            <td>{{ $detail->jumlah }}</td>

            <td>
                Rp {{ number_format($detail->subtotal) }}
            </td>

            <td>
                {{ $penjualan->metode_pembayaran }}
            </td>

            <td>
                @if($penjualan->sumber_order == 'customer_qr')
                    🌐 Customer QR
                @else
                    👨‍💼 Kasir
                @endif
            </td>
        </tr>
        @endforeach
    @endforeach
</table>

<div class="total">
    Total Penjualan: Rp {{ number_format($totalPenjualan) }}
</div>

<div class="footer">
    Terminal Coffee POS System
</div>

</body>
</html>