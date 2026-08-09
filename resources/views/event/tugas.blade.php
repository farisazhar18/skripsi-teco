@extends('layouts.pos')

@section('content')
<h1 class="page-title">
    @if(auth()->user()->role == 'logistik')
        Pengadaan Bahan Baku Event
    @elseif(auth()->user()->role == 'barista')
        Tugas Produksi Event
    @elseif(in_array(auth()->user()->role, ['owner', 'operational_manager']))
        Persetujuan Pengadaan Event
    @else
        Papan Tugas Eksekusi Event
    @endif
</h1>

@if(session('success'))
    <div style="text-align:center; margin-bottom:20px;">
        <p class="success">{{ session('success') }}</p>
    </div>
@endif

<div class="table-card">
    <table style="width: 100%; table-layout: auto;">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="35%">Nama Event</th>
                <th width="20%">Tanggal Pelaksanaan</th>
                <th width="20%" style="text-align: center;">Status Saat Ini</th>
                <th width="20%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $index => $event)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    
                    {{-- NAMA EVENT (Sudah dibersihkan dari daftar pesanan) --}}
                    <td style="font-weight: bold;">
                        <span style="font-size: 15px; color: #183f37;">{{ $event->nama_event }}</span>
                    </td>
                    
                    <td>{{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y') }}</td>
                    
                    <td style="text-align: center;">
                        @if($event->status == 'menunggu_logistik')
                            <span class="badge" style="background: #fff3d8; color: #b56a00; border: 1px solid #fde68a;">⏳ Menunggu Logistik</span>
                        @elseif($event->status == 'menunggu_acc_pengadaan')
                            <span class="badge" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">⚠️ Menunggu ACC Manager</span>
                        @elseif($event->status == 'menunggu_pembelian')
                            <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fbd38d;">🛒 Menunggu Proses PO</span>
                        @elseif($event->status == 'menunggu_barang_event')
                            <span class="badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">🚚 Menunggu Barang</span>
                        @elseif($event->status == 'bahan_ready')
                            <span class="badge" style="background: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">✅ Bahan Ready</span>
                        @elseif($event->status == 'diserahkan')
                            <span class="badge" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047;">☕ Diproses Barista</span>
                        @elseif($event->status == 'selesai')
                            <span class="badge" style="background: #10b981; color: white;">🏁 Selesai</span>
                        @else
                            <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">{{ ucwords(str_replace('_', ' ', $event->status)) }}</span>
                        @endif
                    </td>
                    
                    <td style="text-align: center;">
                        {{-- Tombol Tunggal Buat Eksekusi (Masuk ke halaman detail) --}}
                        <a href="{{ route('event.detail', $event->id) }}" class="btn" style="background: #183f37; padding: 6px 12px; font-size: 13px; text-decoration: none;">
                            🔍 Lihat Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b6256; padding: 30px;">Yeay! Tidak ada tugas event yang harus dieksekusi saat ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection