<!DOCTYPE html>
<html>
<head>
    <title>Rekap Perencanaan Event - {{ $event->nama_event }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        
        /* Warna hijau udah disamakan dengan tema Terminal Coffee (#183f37) */
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #183f37; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; color: #183f37; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #999; padding: 10px; text-align: left; }
        .data-table th { background-color: #183f37; color: white; font-weight: bold; text-align: center; }
        
        .signature { margin-top: 50px; text-align: right; padding-right: 50px; }
        
        /* Desain kotak untuk daftar menu pesanan */
        .produk-box { 
            background: #fdfaf5; 
            border: 1px dashed #183f37; 
            padding: 15px; 
            margin-bottom: 25px; 
            line-height: 1.6; 
            font-size: 14px;
        }
        .section-title {
            color: #183f37;
            margin-bottom: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Perencanaan Event</h2>
        <p style="margin: 0; font-size: 16px;"><b>Manajemen Terminal Coffee</b></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Nama Event</strong></td>
            <td width="3%">:</td>
            <td>{{ $event->nama_event }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Dibuat</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($event->created_at)->translatedFormat('d F Y') }}</td>
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

    <!-- ========================================== -->
    <!-- BAGIAN BARU: DAFTAR PRODUK PESANAN         -->
    <!-- ========================================== -->
    <h3 class="section-title">Daftar Produk Pesanan:</h3>
    <div class="produk-box">
        {!! $event->detail_pesanan ?? '<em style="color: #999;">Detail pesanan tidak tersedia.</em>' !!}
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN: RINCIAN BAHAN BAKU                 -->
    <!-- ========================================== -->
    <h3 class="section-title">Rincian Kebutuhan Bahan Baku Gudang:</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="50%">Nama Bahan Baku</th>
                <th width="20%">Total</th>
                <th width="20%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->eventDetails as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                <td style="text-align: center;"><b>{{ $detail->jumlah_dibutuhkan }}</b></td>
                <td style="text-align: center;">{{ $detail->bahanBaku->satuan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="signature">
        <p>Disetujui Oleh,</p>
        <br><br><br><br>
        <p><b>( Operational Manager )</b></p>
    </div>

</body>
</html>