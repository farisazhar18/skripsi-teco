<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan Logistik - {{ $event->nama_event }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 14px; 
            color: #333; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #183f37;
            padding-bottom: 10px;
        }
        .header h2 { 
            margin: 0 0 5px 0; 
            color: #183f37; 
            text-transform: uppercase;
        }
        .info-table { 
            width: 100%; 
            margin-bottom: 20px; 
        }
        .info-table td { 
            padding: 5px; 
            vertical-align: top;
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #999; 
            padding: 10px; 
            text-align: center; 
        }
        .data-table th { 
            background-color: #183f37; 
            color: white; 
            font-weight: bold;
        }
        .ttd-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .ttd-table td {
            width: 50%;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Daftar Kebutuhan Bahan Baku Event</h2>
        <p style="margin: 0; font-size: 16px;"><b>Terminal Coffee</b></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Nama Event</strong></td>
            <td width="3%">:</td>
            <td>{{ $event->nama_event }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pelaksanaan</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi Outlet</strong></td>
            <td>:</td>
            <td>{{ ucfirst($event->outlet) }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="45%">Nama Bahan Baku</th>
                <th width="25%">Total Kebutuhan</th>
                <th width="20%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->eventDetails as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                <td><b>{{ $detail->jumlah_dibutuhkan }}</b></td>
                <td>{{ $detail->bahanBaku->satuan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Kolom Tanda Tangan buat di Lapangan -->
    <table class="ttd-table">
        <tr>
            <td>
                Disiapkan Oleh (Logistik),
                <br><br><br><br><br>
                ( ............................................ )
            </td>
            <td>
                Diterima Oleh (Barista),
                <br><br><br><br><br>
                ( ............................................ )
            </td>
        </tr>
    </table>

</body>
</html>