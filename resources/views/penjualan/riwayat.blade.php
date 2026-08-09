@extends('layouts.pos')

@section('content')
<h1 class="page-title">Data Penjualan</h1>

<div class="table-card">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 15px; text-align: left;">No</th>
                <th style="padding: 15px; text-align: left;">Tanggal</th>
                <th style="padding: 15px; text-align: left;">Outlet</th>
                <th style="padding: 15px; text-align: left;">Produk</th>
                <th style="padding: 15px; text-align: center;">Jumlah</th>
                <th style="padding: 15px; text-align: right;">Total</th>
                <th style="padding: 15px; text-align: center;">Metode</th>
                <th style="padding: 15px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualans as $p)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 15px;">{{ $loop->iteration }}</td>
                <td style="padding: 15px;">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                <td style="padding: 15px;">{{ ucfirst($p->outlet) }}</td>
                <td style="padding: 15px;">
                    @foreach($p->detailPenjualans as $d)
                        <small>{{ $d->produk->nama_produk }} ({{ $d->jumlah }}x)</small><br>
                    @endforeach
                </td>
                <td style="padding: 15px; text-align: center;">{{ $p->detailPenjualans->sum('jumlah') }}</td>
                <td style="padding: 15px; text-align: right; font-weight: bold;">
                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                </td>
                <td style="padding: 15px; text-align: center;">
                    <span class="badge" style="background: {{ $p->metode_pembayaran == 'QRIS' ? '#dcfce7' : '#fef9c3' }}; 
                                               color: {{ $p->metode_pembayaran == 'QRIS' ? '#166534' : '#854d0e' }};">
                        {{ $p->metode_pembayaran }}
                    </span>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <a href="{{ route('penjualan.show', $p->id) }}" class="btn btn-sm" style="background:#64748b;">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection