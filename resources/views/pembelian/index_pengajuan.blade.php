@extends('layouts.pos')

@section('content')

<style>
    .row-item:hover { background-color: #f0f5f3; transition: background-color 0.2s ease-in-out; }
    .row-item td { border-bottom: 1px solid #e5e7eb; }
    .badge-modern { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; text-align: center; }
</style>

<h1 class="page-title">Daftar Pengajuan Pengadaan</h1>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('pembelian.create') }}" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px;">
            ➕ Tambah Pengajuan
        </a>

        <!-- 🔥 TOMBOL KHUSUS MANAGER BUAT BUKA MENU ACC MASSAL 🔥 -->
        @if(in_array(auth()->user()->role, ['owner', 'operational_manager']))
        <a href="{{ route('pembelian.reviewAcc') }}" class="btn" style="background: #0f7a3a; height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px;">
            ✔️ Persetujuan Pengadaan
        </a>
        @endif
        
        <a href="{{ route('pembelian.pilihPO') }}" class="btn" style="background: #e67e22; height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px;">
            📄 Buat PO (Cetak PDF)
        </a>

        <!-- TOMBOL KHUSUS LOGISTIK/MANAGER BUAT TERIMA BARANG -->
        @if(in_array(auth()->user()->role, ['logistik', 'operational_manager', 'owner']))
        <a href="{{ route('pembelian.reviewTerima') }}" class="btn" style="background: #0284c7; height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px;">
            📦 Terima Barang
        </a>
        @endif
    </div>

    <div>
        <a href="{{ route('pembelian.stok') }}" class="btn" style="background: #183f37; height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px;">
            Lihat Stok Gudang
        </a>
    </div>

</div>

@if(session('success'))
    <div style="background: #e5f5ec; color: #0f7a3a; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e8d1; font-weight: 600;">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Tanggal</th>
                <th style="padding: 12px 15px; text-align: left; width: 35%;">Bahan</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Jumlah</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Status</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr class="row-item">   
                <td style="padding: 12px 15px; text-align: center; color: #475569; vertical-align: middle;">{{ $loop->iteration }}</td>
                
                <td style="padding: 12px 15px; text-align: center; font-size: 13px; color: #64748b; vertical-align: middle;">
                    {{ date('d-m-Y', strtotime($item->tanggal)) }}
                </td>
                
                <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                    <strong style="color: #1e293b; font-size: 14px;">{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong>
                </td>

                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    <strong style="color: #1e293b;">{{ $item->jumlah }}</strong> <span style="font-size: 13px; color: #64748b;">{{ $item->satuan_beli }}</span>
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    @if($item->status_acc == 'disetujui') 
                        <span class="badge-modern" style="background: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">✅ Masuk Gudang</span>
                    @elseif($item->status_acc == 'menunggu_pembelian') 
                        <span class="badge-modern" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047;">🛒 Menunggu PO</span>
                    @elseif($item->status_acc == 'menunggu_barang') 
                        <span class="badge-modern" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">🚚 Menunggu Barang</span>
                    @elseif($item->status_acc == 'ditolak') 
                        <span class="badge-modern" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">❌ Ditolak</span>
                    @else 
                        <span class="badge-modern" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">⏳ Menunggu ACC</span> 
                    @endif
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    <a href="{{ route('pembelian.show', $item->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                    Belum ada data pengajuan pengadaan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection