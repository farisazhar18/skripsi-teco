@extends('layouts.pos')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
    <h1 class="page-title" style="margin-bottom: 0;">Rekap Pemakaian Bahan Baku</h1>
    <a href="/bahan-baku" class="btn-secondary">← Kembali ke Data Bahan Baku</a>
</div>

<div class="card" style="margin-bottom: 20px; background-color: #183f37; color: #efe6d8;">
    <h2 style="color: #efe6d8;">Data {{ $isHariIni ? 'Hari Ini' : 'Tanggal' }}: {{ $tanggalString }}</h2>
    <p style="margin-top: 5px; opacity: 0.8; font-size: 14px;">Menampilkan jumlah bahan baku yang terpotong secara otomatis oleh sistem kasir dan pesanan customer.</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        
        <!-- BAGIAN KIRI: JUDUL -->
        <h2 style="margin-bottom: 0; font-size: 18px; white-space: nowrap;">📊 Akumulasi Total Harian</h2>
        
        <!-- BAGIAN KANAN: FILTER & TOMBOL -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            
            <!-- FORM FILTER KHUSUS MANAJEMEN -->
            @if(in_array(auth()->user()->role, ['logistik', 'operational_manager', 'owner']))
                <form method="GET" action="{{ route('bahan-baku.rekap') }}" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                    
                    <!-- Dropdown Outlet -->
                    <select name="outlet" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; height: 42px; outline: none;">
                        <option value="">Semua Outlet</option>
                        <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                        <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                    </select>

                    <!-- Input Tanggal -->
                    <input type="date" name="tanggal" value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; height: 42px; outline: none;">
                    
                    <!-- Tombol Cari -->
                    <button type="submit" style="padding: 0 16px; height: 42px; border-radius: 6px; background-color: #f3f4f6; color: #183f37; border: 1px solid #d1d5db; font-weight: 600; cursor: pointer;">Cari</button>
                    
                    <!-- Tombol Reset -->
                    @if(request('tanggal') || request('outlet'))
                        <a href="{{ route('bahan-baku.rekap') }}" style="display: flex; align-items: center; justify-content: center; padding: 0 16px; height: 42px; border-radius: 6px; background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; text-decoration: none; font-weight: 600;">Reset</a>
                    @endif
                </form>
            @endif

            <!-- TOMBOL DOWNLOAD PDF -->
            <a href="{{ route('bahan-baku.rekap.pdf', ['tanggal' => request('tanggal'), 'outlet' => request('outlet')]) }}" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0;">
                📄 Download PDF
            </a>
            
        </div>
    </div>

    <div class="table-card">
        <table>
            <tr>
                <th>No</th>
                <th>Outlet</th>
                <th>Nama Bahan</th>
                <th>Total Terpakai Hari Ini</th>
            </tr>
            @forelse($rekapTotal as $rekap)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ ucfirst($rekap['outlet']) }}</td>
                <td>{{ $rekap['nama_bahan'] }}</td>
                <td style="font-weight: bold; color: #b91c1c; font-size: 16px;">
                    {{ $rekap['total_terpakai'] }} {{ $rekap['satuan'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; padding: 20px;">
                    Belum ada pemakaian bahan baku hari ini.
                </td>
            </tr>
            @endforelse
        </table>
    </div>
</div>

<div class="card" style="margin-bottom: 25px;">
    <h2 style="margin-bottom: 15px; font-size: 18px;">📝 Catatan Penyesuaian Stok</h2>
    <div class="table-card">
        <table>
            <tr>
                <th>Waktu</th>
                <th>Outlet</th>
                <th>Nama Bahan</th>
                <th>Selisih Stok</th>
                <th>Keterangan</th>
            </tr>
            @forelse($dataPengajuan as $p)
            <tr>
                <td style="font-weight: 600; color: #183f37;">{{ $p->created_at->format('H:i') }} WIB</td>
                <td>{{ ucfirst($p->outlet) }}</td>
                <td>{{ $p->bahanBaku->nama_bahan ?? 'Bahan Terhapus' }}</td>
                
                <!-- Ngitung selisih barang yang tumpah/hilang -->
                <td style="font-weight: bold; color: #b91c1c;">
                    -{{ $p->stok_seharusnya - $p->stok_aktual }} {{ $p->bahanBaku->satuan ?? '' }}
                </td>
                
                <td style="max-width: 250px; word-wrap: break-word;">{{ $p->alasan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding: 20px; color: #6b7280; font-style: italic;">
                    Tidak ada catatan penyesuaian stok hari ini.
                </td>
            </tr>
            @endforelse
        </table>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 15px; font-size: 18px;">🕰️ Rincian Transaksi</h2>
    <div class="table-card">
        <table>
            <tr>
                <th>Waktu</th>
                <th>Outlet</th>
                <th>Nama Bahan</th>
                <th>Jumlah Dipotong</th>
                <th>Keterangan</th>
            </tr>
            @forelse($riwayat as $item)
            <tr>
                <td style="font-weight: bold; color: #183f37;">{{ $item->created_at->format('H:i') }} WIB</td>
                <td>{{ ucfirst($item->outlet) }}</td>
                <td>{{ $item->bahanBaku->nama_bahan ?? 'Bahan Terhapus' }}</td>
                <td style="font-weight: bold; color: #b91c1c;">
                    -{{ $item->jumlah_terpakai }} {{ $item->bahanBaku->satuan ?? '' }}
                </td>
                <td style="max-width: 250px; word-wrap: break-word;">
                    {{ $item->keterangan }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding: 20px;">
                    Tidak ada riwayat transaksi masuk.
                </td>
            </tr>
            @endforelse
        </table>
    </div>
</div>

@endsection