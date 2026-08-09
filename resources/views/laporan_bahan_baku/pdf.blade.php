<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bahan Baku</title>
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

        .status-aman {
            color: #0f7a3a;
            font-weight: bold;
        }

        .status-menipis {
            color: #b56a00;
            font-weight: bold;
        }

        .status-habis {
            color: #c62828;
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
    <p>Laporan Bahan Baku</p>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
</div>

<table>
    <tr>
        <th>No</th>
        <th>Nama Bahan</th>
        <th>Stok</th>
        <th>Stok Minimum</th>
        <th>Status</th>
    </tr>

    @foreach($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_bahan }}</td>
        <td>{{ $item->stok }} {{ $item->satuan }}</td>
        <td>{{ $item->stok_minimum }} {{ $item->satuan }}</td>
        <td>
            @if($item->stok == 0)
                <span class="status-habis">Stok Habis</span>
            @elseif($item->stok < $item->stok_minimum)
                <span class="status-menipis">Stok Menipis</span>
            @else
                <span class="status-aman">Stok Aman</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>

<div class="footer">
    Terminal Coffee POS System
</div>

</body>
</html>