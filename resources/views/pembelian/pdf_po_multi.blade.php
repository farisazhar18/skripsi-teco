<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #183f37; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #183f37; font-size: 26px; }
        .header p { margin: 5px 0 0; color: #6b6256; font-size: 15px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .item-table th, .item-table td { border: 1px solid #c9bca8; padding: 12px; text-align: left; }
        .item-table th { background-color: #183f37; color: white; font-weight: bold; }
        .signature { width: 100%; margin-top: 60px; }
        .signature td { width: 33%; text-align: center; vertical-align: bottom; height: 120px; }
        .line { border-bottom: 1px solid #333; width: 80%; margin: 0 auto; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PURCHASE ORDER (PO)</h1>
        <p>Formulir Pengadaan Bahan Baku Resmi</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>No. PO</strong></td>
            <td width="30%">: PO-{{ date('Ymd') }}-{{ str_pad($pembelians->first()->id ?? 1, 4, '0', STR_PAD_LEFT) }}</td>
            <td width="20%"><strong>Kepada Yth.</strong></td>
            <td width="30%">: <strong>{{ strtoupper($nama_supplier) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d-m-Y') }}</td>
            <td><strong>Pihak Supplier</strong></td>
            <td>: .......................................</td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="45%">Nama Barang (Bahan Baku)</th>
                <th width="15%" style="text-align: center;">Jumlah</th>
                <th width="15%" style="text-align: center;">Satuan</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelians as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td><strong>{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong></td>
                <td style="text-align: center; font-size: 16px; font-weight: bold;">{{ $item->jumlah }}</td>
                <td style="text-align: center;">{{ $item->satuan_beli }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 12px; color: #666; font-style: italic; background: #fdfaf5; padding: 10px; border-left: 3px solid #e67e22;">
        * Dokumen ini adalah surat jalan/pesanan resmi yang dikeluarkan oleh sistem setelah mendapat persetujuan (ACC) dari Operational Manager / Owner.<br>
        * Harap pihak logistik menyertakan dokumen ini saat melakukan pengambilan barang atau memberikannya kepada kurir supplier.
    </p>

    <table class="signature">
        <tr>
            <td>Dibuat Oleh (Logistik),<br><br><br><br><br><span class="line"></span></td>
            <td>Disetujui Oleh (Manager),<br><br><br><br><br><span class="line"></span></td>
            <td>Diterima Oleh (Supplier),<br><br><br><br><br><span class="line"></span></td>
        </tr>
    </table>

</body>
</html>