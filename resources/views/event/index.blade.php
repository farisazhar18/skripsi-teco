@extends('layouts.pos')

@section('content')
<h1 class="page-title">Pengelolaan Pemesanan Event</h1>

@if(session('success'))
    <div style="margin-bottom: 20px;">
        <span class="success">✔️ {{ session('success') }}</span>
    </div>
@endif

<div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin: 0; color: #183f37; font-size: 20px; font-weight: 600;">Daftar Event Outlet</h2>
        <small style="color: #6b6256;">Pemantauan dan eksekusi bahan baku event</small>
    </div>
    
    <!-- Tombol Tambah HANYA muncul buat Operational Manager & Owner -->
    @if(in_array(auth()->user()->role, ['owner', 'operational_manager']))
        <a href="{{ route('event.create') }}" class="btn">➕ Tambah Event Baru</a>
    @endif
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Event</th>
                <th>Tanggal Pelaksanaan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $index => $event)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $event->nama_event }}</td>
                    <td>{{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y') }}</td>
                    <td>
                        @if($event->status == 'menunggu_logistik')
                            <span class="badge badge-warning">⏳ Menunggu Logistik</span>
                        @elseif($event->status == 'bahan_ready')
                            <span class="badge badge-success" style="background: #e0f2fe; color: #0284c7;">📦 Bahan Ready</span>
                        @elseif($event->status == 'diserahkan')
                            <!-- Tambahin badge baru ini bang -->
                            <span class="badge badge-warning" style="background: #fef08a; color: #854d0e;">🧑‍🍳 Sedang Diproses Barista</span>
                        @elseif($event->status == 'selesai')
                            <span class="badge badge-success">✅ Selesai</span>
                        @endif
                    </td>
                    
                    <td>
                        <div class="action-buttons">
                            <!-- TOMBOL DETAIL BISA DIAKSES SEMUA ROLE BUAT PANTAUAN -->
                            <a href="{{ route('event.show', $event->id) }}" class="btn-secondary" style="padding: 5px 10px; font-size: 13px; text-decoration: none; border-radius: 6px;">Lihat Detail</a>
                        </div>
                    </td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b6256; padding: 30px;">
                        Belum ada data event saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection