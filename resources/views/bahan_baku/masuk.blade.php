@extends('layouts.pos')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
    <h1 class="page-title" style="margin-bottom: 0;">Rekap Barang Masuk (Distribusi)</h1>
    <a href="/bahan-baku" class="btn-secondary" style="height: 42px; display: flex; align-items: center; padding: 0 16px; border-radius: 6px; text-decoration: none;">
        ← Kembali ke Data Bahan Baku
    </a>
</div>

<div class="card" style="margin-bottom: 20px; background-color: #0284c7; color: white;">
    <!-- 🔥 TEKS DINAMIS SESUAI TANGGAL -->
    <h2 style="color: white; margin-bottom: 5px;">Data {{ $isHariIni ? 'Hari Ini' : 'Tanggal' }}: {{ $tanggalString }}</h2>
    <p style="margin-top: 0; opacity: 0.9; font-size: 14px;">Menampilkan daftar bahan baku yang dikirim dari Logistik ke Outlet pada tanggal tersebut.</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin-bottom: 0; font-size: 18px;">📥 Rincian Barang Masuk</h2>
        
        <!-- 🔥 FORM FILTER TANGGAL (DAN OUTLET BUAT MANAGER/OWNER) -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="{{ route('bahan-baku.masuk') }}" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                
                <!-- Filter Outlet (Cuma buat peran tertentu) -->
                @if(in_array(auth()->user()->role, ['logistik', 'operational_manager', 'owner']))
                    <select name="outlet" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; height: 42px; outline: none;">
                        <option value="">Semua Outlet</option>
                        <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                        <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                    </select>
                @endif

                <!-- Input Kalender / Tanggal -->
                <input type="date" name="tanggal" value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; height: 42px; outline: none;">
                
                <button type="submit" style="padding: 0 16px; height: 42px; border-radius: 6px; background-color: #f3f4f6; color: #183f37; border: 1px solid #d1d5db; font-weight: 600; cursor: pointer;">Cari</button>
                
                @if(request('tanggal') || request('outlet'))
                    <a href="{{ route('bahan-baku.masuk') }}" style="display: flex; align-items: center; justify-content: center; padding: 0 16px; height: 42px; border-radius: 6px; background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; text-decoration: none; font-weight: 600;">Reset</a>
                @endif
            </form>
        </div>
        
    </div>

    <div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px 15px; text-align: center; width: 20%;">Waktu Kirim</th>
                    <th style="padding: 12px 15px; text-align: center; width: 20%;">Outlet</th>
                    <th style="padding: 12px 15px; text-align: left; width: 35%;">Nama Bahan Baku</th>
                    <th style="padding: 12px 15px; text-align: center; width: 25%;">Jumlah Diterima</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangMasuk as $item)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 15px; text-align: center; font-weight: 600; color: #0284c7;">
                        {{ $item->created_at->format('H:i') }} WIB
                    </td>
                    <td style="padding: 12px 15px; text-align: center; font-weight: 600; color: #334155;">
                        {{ ucfirst($item->outlet) }}
                    </td>
                    <td style="padding: 12px 15px; text-align: left; font-weight: 500; color: #1e293b;">
                        {{ $item->bahanBaku->nama_bahan ?? 'Data Terhapus' }}
                    </td>
                    <td style="padding: 12px 15px; text-align: center; font-weight: bold; color: #059669; font-size: 15px;">
                        +{{ $item->jumlah }} {{ $item->satuan }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding: 30px; color: #6b7280; font-style: italic;">
                        🚚 Belum ada barang masuk dari logistik pada tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection