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

<!-- ACTION BAR: Judul Halaman & Tombol Aksi di Kanan -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <h1 class="page-title" style="margin-bottom: 0;">Detail Kebutuhan Event</h1>
    
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('event.index') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; padding: 0 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            ← Kembali
        </a>
        <a href="{{ route('event.pdf_manager', $event->id) }}" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; border-radius: 6px; text-decoration: none; font-weight: 600; background: #0284c7; border: 1px solid #0369a1;">
            📄 Download PDF
        </a>
    </div>
</div>

<!-- 🔥 BANNER NAMA EVENT 🔥 -->
<div class="card" style="margin-bottom: 25px; background-color: #183f37; color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #efe6d8; display: flex; align-items: center; gap: 10px;">
        🎉 {{ $event->nama_event }}
    </h2>
    <p style="margin: 8px 0 0 0; opacity: 0.8; font-size: 14px; color: #cbd5e1;">
        Rincian lengkap pesanan produk dan kebutuhan logistik bahan baku untuk kelancaran event.
    </p>
</div>

<!-- KOTAK 1: DAFTAR PESANAN PRODUK -->
<div class="card" style="margin-bottom: 25px; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <h3 style="color: #0f7a3a; margin-top: 0; margin-bottom: 15px; font-size: 18px; display: flex; align-items: center; gap: 8px;">
        📋 Daftar Produk Pesanan
    </h3>
    
    <div style="background: #f8fafc; border-left: 4px solid #0f7a3a; border-radius: 6px; padding: 20px; font-size: 15px; line-height: 1.7; color: #334155;">
        {!! $event->detail_pesanan ?? '<em style="color: #94a3b8;">Detail pesanan tidak tersedia (Event lama sebelum update sistem).</em>' !!}
    </div>
</div>

<!-- KOTAK 2: KEBUTUHAN BAHAN BAKU LOGISTIK -->
<div class="card" style="padding: 25px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <h3 style="color: #1e293b; margin-top: 0; margin-bottom: 15px; font-size: 18px; display: flex; align-items: center; gap: 8px;">
        📦 Kebutuhan Bahan Baku (Logistik)
    </h3>
    
    <div class="table-card" style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #245c50; color: white;">
                    <th style="padding: 15px; text-align: center; width: 10%;">No</th>
                    <th style="padding: 15px; text-align: left; width: 60%;">Nama Bahan Baku</th>
                    <th style="padding: 15px; text-align: center; width: 30%;">Jumlah Dibutuhkan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($event->eventDetails as $index => $detail)
                <tr class="row-item">
                    <td style="text-align: center; padding: 15px; color: #475569; vertical-align: middle;">
                        {{ $index + 1 }}
                    </td>
                    
                    <td style="text-align: left; padding: 15px; font-weight: 600; color: #1e293b; vertical-align: middle;">
                        {{ $detail->bahanBaku->nama_bahan ?? 'Bahan Tidak Ditemukan' }}
                    </td>
                    
                    <td style="text-align: center; padding: 15px; vertical-align: middle;">
                        <span class="badge-modern" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 14px;">
                            {{ $detail->jumlah_dibutuhkan }} <span style="font-size: 12px; font-weight: normal;">{{ $detail->bahanBaku->satuan ?? '-' }}</span>
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                        📁 Belum ada data bahan baku untuk event ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection