@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 TAMBAHAN CSS BIAR UI MAKIN SMOOTH & MODERN */
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
        letter-spacing: 0.3px;
        display: inline-block;
        text-align: center;
    }
</style>

<h1 class="page-title">Laporan Rekapitulasi Event</h1>

<!-- CARD FILTER -->
<div class="card">
    <form action="{{ route('event.laporan') }}" method="GET">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- FILTER BULAN -->
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Bulan</label>
                <select name="bulan" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
                    <option value="semua" {{ $bulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- FILTER TAHUN -->
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Tahun</label>
                <select name="tahun" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
                    <option value="semua" {{ $tahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                    @php $tahunSekarang = date('Y'); @endphp
                    @for($t = $tahunSekarang; $t >= $tahunSekarang - 3; $t--)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endfor
                </select>
            </div>

            <!-- FILTER OUTLET -->
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #1e293b; display: block; margin-bottom: 8px;">Outlet</label>
                <select name="outlet" style="width: 100%; height: 42px; border-radius: 6px; border: 1px solid #d1d5db; padding: 0 12px; outline: none;">
                    <option value="semua" {{ $outlet == 'semua' ? 'selected' : '' }}>Semua Outlet</option>
                    <option value="hasanuddin" {{ $outlet == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                    <option value="makmur" {{ $outlet == 'makmur' ? 'selected' : '' }}>Makmur</option>
                    <option value="booth" {{ $outlet == 'booth' ? 'selected' : '' }}>Booth (Luar Outlet)</option>
                </select>
            </div>

            <!-- TOMBOL CARI & RESET -->
            <div class="form-actions" style="margin-top: 0; margin-bottom: 0; display: flex; gap: 10px;">
                <button type="submit" class="btn" style="height: 42px; padding: 0 20px; border-radius: 6px;">Filter</button>
                @if($bulan != 'semua' || $tahun != 'semua' || $outlet != 'semua')
                    <a href="{{ route('event.laporan') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 6px; text-decoration: none;">Reset</a>
                @endif
            </div>

        </div>
    </form>
</div>

<!-- 🎨 ACTION BAR: Tombol Export PDF -->
<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: flex-end;">
    <!-- Bagian Kanan: Tombol Export PDF -->
    <a href="{{ route('event.laporan_pdf') }}?bulan={{ $bulan }}&tahun={{ $tahun }}&outlet={{ $outlet }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; background: #efe6d8; text-decoration: none; font-weight: 600; color: #183f37; border: 1px solid #d8cbb8; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        📥 Export PDF
    </a>
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; overflow-x: auto;">
    <table style="border-collapse: collapse; width: 100%; min-width: 900px;">

        <!-- 🎨 HEADER TABEL MODERN -->
        <tr style="background-color: #245c50; color: white;">
            <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
            <th style="padding: 12px 15px; text-align: center; width: 20%;">Tanggal Pelaksanaan</th>
            <th style="padding: 12px 15px; text-align: left; width: 30%;">Nama Event</th>
            <th style="padding: 12px 15px; text-align: center; width: 15%;">Outlet</th>
            <th style="padding: 12px 15px; text-align: center; width: 15%;">Status Akhir</th>
            <th style="padding: 12px 15px; text-align: center; width: 15%;">Aksi</th>
        </tr>

        @forelse($events as $index => $event)
        <tr class="row-item">
            <td style="text-align: center; color: #475569; padding: 12px 15px; vertical-align: middle;">
                {{ $index + 1 }}
            </td>

            <td style="text-align: center; color: #1e293b; font-weight: 500; padding: 12px 15px; vertical-align: middle;">
                {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
            </td>

            <td style="font-weight: 600; color: #1e293b; padding: 12px 15px; vertical-align: middle;">
                {{ $event->nama_event }}
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                <span style="font-weight: 600; color: #334155;">{{ ucfirst($event->outlet) }}</span>
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                @if($event->status == 'selesai')
                    <span class="badge-modern" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">✅ Selesai</span>
                @else
                    <span class="badge-modern" style="background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">⏳ {{ str_replace('_', ' ', strtoupper($event->status)) }}</span>
                @endif
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                <a href="{{ route('event.show', $event->id) }}" style="background: #e0e7ff; color: #3730a3; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #c7d2fe; display: inline-block;">
                    Lihat Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; padding: 30px; color: #6b7280; font-style: italic;">
                📁 Tidak ada event yang ditemukan untuk filter tersebut.
            </td>
        </tr>
        @endforelse

    </table>
</div>

@endsection