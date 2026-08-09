<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengadaan</title>
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
    <p>Laporan Pengadaan Bahan Baku</p>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
</div>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Bahan</th>
        <th>Jumlah Beli</th>
        <th>Jumlah Konversi</th>
        <th>Keterangan</th>
    </tr>

    @foreach($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->tanggal }}</td>
        <td>{{ $item->bahanBaku->nama_bahan }}</td>
        <td>{{ $item->jumlah }} {{ $item->satuan_beli }}</td>
        <td>{{ $item->jumlah_konversi }} {{ $item->bahanBaku->satuan }}</td>
        <td>{{ $item->keterangan }}</td>
    </tr>
    @endforeach
</table>

<div class="footer">
    Terminal Coffee POS System
</div>

</body>
</html>