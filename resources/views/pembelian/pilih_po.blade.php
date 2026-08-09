@extends('layouts.pos')
@section('content')

<h1 class="page-title">Pilih Barang untuk Cetak PO</h1>

<div class="card" style="padding: 20px; background: white; border-radius: 20px;">
    <form action="{{ route('pembelian.cetakPOMulti') }}" method="POST">
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
                    <tr style="border-bottom: 1px solid #eae5dc;">
                        <td style="padding: 10px; text-align: center;">
                            <input type="checkbox" name="pembelian_ids[]" value="{{ $item->id }}" style="transform: scale(1.5);">
                        </td>
                        <td style="padding: 10px;">{{ date('d-m-Y', strtotime($item->updated_at ?? $item->tanggal)) }}</td>
                        <td style="padding: 10px; font-weight: bold;">{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
                        <td style="padding: 10px;">{{ $item->jumlah }} {{ $item->satuan_beli }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary">Kembali</a>
                <button type="submit" class="btn" style="background: #e67e22; margin-left: 10px;">📄 Generate PDF</button>
            </div>
        @else
            <div style="padding: 20px; background: #fff4e5; color: #b56a00; border-radius: 10px;">
                Belum ada pengajuan yang di-ACC (Menunggu Barang).
            </div>
            <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="margin-top: 15px; display: inline-block;">Kembali</a>
        @endif
    </form>
</div>
@endsection