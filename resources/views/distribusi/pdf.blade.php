<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan Distribusi</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #183f37; padding-bottom: 10px; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 3px 0; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 15px; } /* Margin bawah dikurangi biar nyambung ke keterangan */
        .table-data th, .table-data td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table-data th { background-color: #183f37; color: white; }
        
        /* CSS Baru untuk Kotak Keterangan */
        .keterangan-box { 
            border: 1px solid #ddd; 
            padding: 12px; 
            margin-bottom: 30px; 
            min-height: 40px; 
            background-color: #fafafa;
            border-radius: 4px;
        }
        
        .signature { width: 100%; margin-top: 30px; }
        .signature td { text-align: center; width: 50%; padding-top: 70px; }
    </style>
</head>
<body>

    <div class="header">
        <h2 style="margin: 0; color: #183f37;">SURAT JALAN DISTRIBUSI</h2>
        <p style="margin: 5px 0 0 0;">Dokumen Pemindahan Bahan Baku Internal</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 15%;"><strong>Outlet Tujuan</strong></td>
                <td style="width: 35%;">: {{ ucfirst($outlet) }}</td>
                <td style="width: 15%;"><strong>Tanggal</strong></td>
                <td style="width: 35%;">: {{ date('d F Y', strtotime($waktu)) }}</td>
            </tr>
            <tr>
                <td><strong>Bagian</strong></td>
                <td>: Logistik</td>
                <td><strong>Waktu Cetak</strong></td>
                <td>: {{ date('H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th style="text-align: center; width: 10%;">No</th>
                <th>Nama Bahan Baku</th>
                <th style="width: 25%; text-align: center;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribusi as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->jumlah }} {{ $item->satuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="keterangan-box">
        <strong style="color: #183f37;">Keterangan:</strong><br>
        <span style="display: block; margin-top: 5px;">
            {{ $distribusi->first()->keterangan ?? '-' }}
        </span>
    </div>

    <table class="signature">
        <tr>
            <td>
                ( ..................................... )<br>
                <strong>Tim Logistik Pengirim</strong>
            </td>
            <td>
                ( ..................................... )<br>
                <strong>Penerima Outlet</strong>
            </td>
        </tr>
    </table>

</body>
</html>