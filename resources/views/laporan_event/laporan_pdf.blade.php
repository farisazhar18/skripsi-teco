<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Event</title>
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
            text-align: center;
        }

        td.left {
            text-align: left;
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
    <p>Laporan Rekapitulasi Event</p>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
    @if($bulan != 'semua' || $tahun != 'semua' || $outlet != 'semua')
        <p>Filter: 
            @if($bulan != 'semua') Bulan {{ $bulan }} @endif
            @if($tahun != 'semua') Tahun {{ $tahun }} @endif
            @if($outlet != 'semua') Outlet {{ ucfirst($outlet) }} @endif
        </p>
    @endif
</div>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal Pelaksanaan</th>
        <th>Nama Event</th>
        <th>Outlet</th>
        <th>Status</th>
    </tr>

    @foreach($events as $event)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ date('d-m-Y', strtotime($event->tanggal_pelaksanaan)) }}</td>
        <td class="left">{{ $event->nama_event }}</td>
        <td>{{ ucfirst($event->outlet) }}</td>
        <td>{{ str_replace('_', ' ', strtoupper($event->status)) }}</td>
    </tr>
    @endforeach

    @if(count($events) == 0)
    <tr>
        <td colspan="5">Data laporan rekapitulasi event tidak ditemukan.</td>
    </tr>
    @endif
</table>

<div class="footer">
    Terminal Coffee POS System
</div>

</body>
</html>