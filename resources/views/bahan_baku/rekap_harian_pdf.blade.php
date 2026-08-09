<!DOCTYPE html>
<html>
<head>
    <title>Rekap Harian Bahan Baku</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; color: #183f37; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        hr { border: 0; border-top: 2px solid #183f37; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #183f37; color: white; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-danger { color: #b91c1c; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI PEMAKAIAN BAHAN BAKU HARIAN</h2>
        <h2>TERMINAL COFFEE</h2>
        <p>Tanggal: {{ $tanggal }}</p>
    </div>
    
    <hr>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Outlet</th>
                <th width="45%">Nama Bahan Baku</th>
                <th width="30%">Total Terpakai Hari Ini</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapTotal as $rekap)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ ucfirst($rekap['outlet']) }}</td>
                <td>{{ $rekap['nama_bahan'] }}</td>
                <td class="text-center text-bold text-danger">
                    {{ $rekap['total_terpakai'] }} {{ $rekap['satuan'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">
                    Belum ada pemakaian bahan baku hari ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>