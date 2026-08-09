<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Distribusi Bahan Baku</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #003333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            color: #144238;
            letter-spacing: 1px;
        }

        .header p {
            margin: 6px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #144238;
            color: white;
            padding: 10px;
            border: 1px solid #777;
            text-align: center;
        }

        td {
            padding: 9px;
            border: 1px solid #999;
        }

        td.center {
            text-align: center;
        }

        .footer {
            margin-top: 35px;
            text-align: right;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>TERMINAL COFFEE</h1>
    <p>Laporan Distribusi Bahan Baku</p>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
</div>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Outlet</th>
        <th>Nama Bahan</th>
        <th>Jumlah Distribusi</th>
    </tr>

    @foreach($data as $item)
    <tr>
        <td class="center">{{ $loop->iteration }}</td>
        <td>{{ date('Y-m-d', strtotime($item->tanggal)) }}</td>

        <td>
            <span style="text-align: center; display: block;">
                @if($item->outlet == 'hasanuddin')
                    Hasanuddin
                @elseif($item->outlet == 'makmur')
                    Makmur
                @else
                    -
                @endif
            </span>
        </td>

        <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>

        <td>
            {{ $item->jumlah }}
            {{ $item->satuan }}
        </td>

    </tr>
    @endforeach
</table>

<div class="footer">
    Terminal Coffee POS System
</div>

</body>
</html>