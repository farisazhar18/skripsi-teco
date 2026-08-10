@extends('layouts.pos')

@section('content')

<h1 class="page-title">Persetujuan Penyesuaian Stok</h1>

@if(session('success'))
    <div style="text-align:center; margin-bottom:20px;">
        <p class="success" style="color: green; font-weight: bold;">
            {{ session('success') }}
        </p>
    </div>
@endif

@if(session('error'))
    <div style="text-align:center; margin-bottom:20px;">
        <p style="color: red; font-weight: bold;">
            {{ session('error') }}
        </p>
    </div>
@endif

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
            <th style="padding: 12px 10px; text-align: center;">No</th>
            <th style="padding: 12px 10px; text-align: center;">Tanggal Pengajuan</th>
            <th style="padding: 12px 10px; text-align: center;">Outlet</th>
            <th style="padding: 12px 10px; text-align: left;">Bahan Baku</th>
            <th style="padding: 12px 10px; text-align: center;">Stok Sistem</th>
            <th style="padding: 12px 10px; text-align: center;">Stok Aktual</th>
            <th style="padding: 12px 10px; text-align: center;">Selisih</th>
            <th style="padding: 12px 10px; text-align: left;">Alasan / Ket</th>
            <th style="padding: 12px 10px; text-align: center;">Bukti Foto</th>
            <th style="padding: 12px 10px; text-align: center;">Aksi / Status</th>
        </tr>
    </thead>
    <tbody>
    @forelse($pengajuan as $item)
    <tr style="border-bottom: 1px solid #eee;">
        <td style="padding: 12px 10px; text-align: center; color: #64748b;">{{ $loop->iteration }}</td>
        
        <td style="padding: 12px 10px; text-align: center; font-size: 13px; color: #64748b;">
            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
        </td>
        
        <td style="padding: 12px 10px; text-align: center; font-weight: 600;">{{ ucfirst($item->outlet) }}</td>
        
        <td style="padding: 12px 10px;">
            <strong style="color: #1e293b;">{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong>
        </td>
        
        <td style="padding: 12px 10px; text-align: center;">{{ $item->stok_seharusnya }} {{ $item->bahanBaku->satuan ?? '' }}</td>
        
        <td style="padding: 12px 10px; text-align: center; font-weight: bold; color: #b91c1c;">
            {{ $item->stok_aktual }} {{ $item->bahanBaku->satuan ?? '' }}
        </td>
        
        <td style="padding: 12px 10px; text-align: center;">
            @php
                $selisih = $item->stok_aktual - $item->stok_seharusnya;
            @endphp
            @if($selisih < 0)
                <span style="color: #dc2626; font-weight: bold;">{{ $selisih }}</span> @elseif($selisih > 0)
                <span style="color: #059669; font-weight: bold;">+{{ $selisih }}</span> @else
                <span>0</span>
            @endif
        </td>

        <td style="padding: 12px 10px; max-width: 180px; word-wrap: break-word; font-style: italic; color: #475569;">
            "{{ $item->alasan }}"
        </td>

        <td style="padding: 12px 10px; text-align: center;">
            @if(!empty($item->foto_bukti))
                <a href="{{ url('foto-bukti/' . $item->foto_bukti) }}" target="_blank" title="Klik untuk memperbesar">
                    <img src="{{ url('foto-bukti/' . $item->foto_bukti) }}" alt="Bukti Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: pointer; transition: 0.2s;">
                </a>
            @else
                <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Tanpa Foto</span>
            @endif
        </td>

        <td style="padding: 12px 10px; text-align: center;">
            @if(isset($item->status) && $item->status == 'disetujui')
                <span style="background: #d1fae5; color: #047857; padding: 6px 12px; border-radius: 50px; font-weight: bold; font-size: 12px; border: 1px solid #6ee7b7; display: inline-block;">
                    ✅ Disetujui
                </span>
            @elseif(isset($item->status) && $item->status == 'ditolak')
                <span style="background: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 50px; font-weight: bold; font-size: 12px; border: 1px solid #fecaca; display: inline-block;">
                    ❌ Ditolak (Barista Bertanggung Jawab)
                </span>
            @else
                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                    <form action="/pengajuan-stok/{{ $item->id }}/approve" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn" style="background-color: #059669; border-color: #047857; padding: 6px 12px; font-size: 12px; font-weight: bold; cursor: pointer; border-radius: 6px;" onclick="return confirm('Yakin mau ACC? Data stok utama akan langsung berubah.')">
                            ✓ ACC
                        </button>
                    </form>

                    <form action="/pengajuan-stok/{{ $item->id }}/reject" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn" style="background-color: #dc2626; border-color: #b91c1c; color: white; padding: 6px 12px; font-size: 12px; font-weight: bold; cursor: pointer; border-radius: 6px;" onclick="return confirm('Yakin ingin TOLAK pengajuan ini? Barista harus bertanggung jawab atas selisih stok.')">
                            ❌ Tolak
                        </button>
                    </form>
                </div>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="10" style="text-align: center; padding: 30px; color: #64748b; font-style: italic;">
            Belum ada pengajuan penyesuaian stok.
        </td>
    </tr>
    @endforelse
    </tbody>
</table>
</div>

@endsection