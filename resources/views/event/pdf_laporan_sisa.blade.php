<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sisa Bahan Event</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #183f37; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #183f37; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #6b6256; font-size: 14px; }
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .item-table th, .item-table td { border: 1px solid #c9bca8; padding: 10px; text-align: left; }
        .item-table th { background-color: #183f37; color: white; font-weight: bold; font-size: 13px; text-align: center;}
        .signature { width: 100%; margin-top: 50px; }
        .signature td { width: 50%; text-align: center; vertical-align: bottom; height: 100px; }
        .line { border-bottom: 1px solid #333; width: 70%; margin: 0 auto; display: inline-block; }
        .highlight { color: #047857; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN SISA BAHAN & PENYELESAIAN EVENT</h1>
        <p>Event: <strong>{{ strtoupper($event->nama_event) }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Tanggal Pelaksanaan</strong></td>
            <td width="30%">: {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d-m-Y') }}</td>
            <td width="20%"><strong>Tgl. Pelaporan</strong></td>
            <td width="30%">: {{ date('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi / Outlet</strong></td>
            <td>: {{ ucwords($event->outlet) }}</td>
            <td><strong>Status</strong></td>
            <td>: <span style="color: #047857; font-weight:bold;">SELESAI</span></td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%" style="text-align: left;">Nama Bahan Baku</th>
                <th width="15%">Total Dibeli</th>
                <th width="15%">Terpakai</th>
                <th width="15%">Sisa Bahan</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->eventDetails as $item)
                @if($item->jumlah_beli > 0)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong></td>
                    <td style="text-align: center;">{{ $item->jumlah_beli }} {{ $item->satuan_beli }}</td>
                    <td style="text-align: center;">
                        <!-- Hitung jumlah terpakai: Beli - Sisa -->
                        {{ $item->jumlah_beli - ($item->sisa_bahan ?? 0) }} {{ $item->satuan_beli }}
                    </td>
                    <td style="text-align: center; background-color: #f0fdf4;" class="highlight">
                        {{ $item->sisa_bahan ?? 0 }} {{ $item->satuan_beli }}
                    </td>
                    <td style="text-align: center; font-size: 11px;">
                        @if(($item->sisa_bahan ?? 0) > 0)
                            Dikembalikan ke Gudang
                        @else
                            Habis Terpakai
                        @endif
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 12px; color: #666; font-style: italic; background: #fdfaf5; padding: 10px; border-left: 3px solid #10b981;">
        * Dokumen ini menyatakan bahwa event telah selesai. Sisa bahan yang tercatat di atas (kolom hijau) telah otomatis ditambahkan kembali ke stok sistem (Inventory Gudang).
    </p>

    <table class="signature">
        <tr>
            <td>Dilaporkan Oleh (Barista),<br><br><br><br><br><span class="line"></span></td>
            <td>Diterima Oleh (Logistik / Manager),<br><br><br><br><br><span class="line"></span></td>
        </tr>
    </table>

</body>
</html>