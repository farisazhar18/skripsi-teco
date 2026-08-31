@extends('layouts.pos')
@section('content')

<h1 class="page-title">Pilih Barang untuk Cetak PO</h1>

<div class="card" style="padding: 20px; background: white; border-radius: 20px;">
    <form action="{{ route('pembelian.cetakPOMulti') }}" method="POST" target="_blank">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #183f37;">Tujuan Supplier (Opsional)</label><br>
            <input type="text" name="nama_supplier" placeholder="Contoh: Toko Makmur / PT Indofood" style="width: 100%; max-width: 400px; padding: 10px; border-radius: 8px; border: 1px solid #c9bca8; margin-top: 5px;">
        </div>

        <h3 style="color: #0f7a3a; margin-bottom: 10px;">Pilih Barang yang Mau Dibeli:</h3>
        
        @if($pembelians->count() > 0)
            <table class="table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background: #183f37; color: white;">
                        <th style="padding: 10px; width: 5%;">Pilih</th>
                        <th style="padding: 10px;">Tanggal ACC</th>
                        <th style="padding: 10px;">Nama Bahan</th>
                        <th style="padding: 10px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelians as $item)
                    <tr style="border-bottom: 1px solid #eae5dc; {{ $item->is_po_dicetak ? 'background-color: #f9fafb; opacity: 0.6;' : '' }}">
                        <td style="padding: 10px; text-align: center;">
                            @if($item->is_po_dicetak)
                                <input type="checkbox" disabled style="transform: scale(1.5);">
                            @else
                                <input type="checkbox" name="pembelian_ids[]" value="{{ $item->id }}" style="transform: scale(1.5);">
                            @endif
                        </td>
                        <td style="padding: 10px; {{ $item->is_po_dicetak ? 'text-decoration: line-through; color: #9ca3af;' : '' }}">
                            {{ date('d-m-Y', strtotime($item->updated_at ?? $item->tanggal)) }}
                            @if($item->is_po_dicetak)
                                <br><span style="font-size: 11px; font-weight: bold; color: #059669;">✅ PO Sudah Dicetak</span>
                            @endif
                        </td>
                        <td style="padding: 10px; font-weight: bold; {{ $item->is_po_dicetak ? 'text-decoration: line-through; color: #9ca3af;' : '' }}">
                            {{ $item->bahanBaku->nama_bahan ?? '-' }}
                        </td>
                        <td style="padding: 10px; {{ $item->is_po_dicetak ? 'text-decoration: line-through; color: #9ca3af;' : '' }}">
                            {{ $item->jumlah }} {{ $item->satuan_beli }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn" style="background: #e67e22;">📄 Generate PDF</button>
            </div>
        @else
            <div style="padding: 20px; background: #fff4e5; color: #b56a00; border-radius: 10px;">
                Belum ada pengajuan yang menunggu diproses pembelian.
            </div>
        @endif
    </form>

    @if($pembelians->count() > 0)
    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- FORM SELESAI (Ubah Status) -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="color: #1e293b; margin: 0 0 5px 0;">Sudah Selesai Mencetak Semua PO?</h3>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Klik tombol di samping untuk melanjutkan status barang menjadi Menunggu Barang Datang.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="padding: 12px 20px; border-radius: 8px; text-decoration: none;">← Kembali ke Daftar</a>
            
            <form action="{{ route('pembelian.prosesBeliMassal') }}" method="POST">
                @csrf
                <button class="btn" style="background: #0284c7; padding: 12px 24px; border-radius: 8px; border:none; color:white; cursor:pointer; font-size: 15px;">
                    🚚 Tandai Semua Sedang Dibeli
                </button>
            </form>
        </div>
    </div>
    @else
    <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="margin-top: 15px; display: inline-block;">Kembali</a>
    @endif
</div>
@endsection