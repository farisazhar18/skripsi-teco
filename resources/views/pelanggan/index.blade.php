@extends('layouts.pos')

@section('content')

<style>
    /* Efek hover baris tabel super smooth */
    .row-item:hover {
        background-color: #f0f5f3;
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb;
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
    }
</style>

<!-- JUDUL TENGAH -->
<h1 class="page-title">Customer Data</h1>

<!-- CARD FILTER -->
<div class="card" style="margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <form action="{{ route('pelanggan.index') }}" method="GET">
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            @if(auth()->user()->role == 'owner')
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #1e293b;">Filter Outlet</label>
                    <select name="outlet" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px;">
                        <option value="semua" {{ request('outlet') == 'semua' ? 'selected' : '' }}>Semua Outlet</option>
                        <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Outlet Hasanuddin</option>
                        <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Outlet Makmur</option>
                    </select>
                </div>
            @endif
            <div style="flex: 2; min-width: 250px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #1e293b;">Cari Pelanggan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No. HP..." style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 15px; margin: 0;">
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
            <button type="submit" class="btn" style="padding: 0 25px; height: 42px; width: auto; flex-shrink: 0; border-radius: 6px;">🔍 Cari</button>
            @if(request('search') || request('outlet'))
                <a href="{{ route('pelanggan.index') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; padding: 0 15px; width: auto; flex-shrink: 0; border-radius: 6px; text-decoration: none;">✖ Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- TABEL DATA -->
<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: left; width: 25%;">Nama Pelanggan</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">No. HP</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Total Order</th>
                <th style="padding: 12px 15px; text-align: right; width: 15%;">Total Belanja</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Terakhir Berkunjung</th>
                <th style="padding: 12px 15px; text-align: center; width: 10%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggans as $index => $p)
            <tr class="row-item">
                <td style="padding: 12px 15px; text-align: center; color: #475569; vertical-align: middle;">{{ $index + 1 }}</td>
                
                <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                    <strong style="color: #1e293b; font-size: 15px;">{{ $p->nama_customer ?: 'Tanpa Nama' }}</strong>
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle; color: #64748b;">
                    {{ $p->no_hp }}
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    <span class="badge-modern" style="background: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">{{ $p->total_kunjungan }} Kali</span>
                </td>
                
                <td style="padding: 12px 15px; text-align: right; vertical-align: middle;">
                    <strong style="color: #0f172a;">Rp {{ number_format($p->total_belanja, 0, ',', '.') }}</strong>
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle; color: #64748b; font-size: 13px;">
                    {{ date('d M Y', strtotime($p->kunjungan_terakhir)) }}
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    <a href="{{ route('pelanggan.show', $p->no_hp) }}" style="background: #e0e7ff; color: #3730a3; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #c7d2fe; display: inline-block;">
                        Lihat History
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                    📁 Data pelanggan tidak ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection