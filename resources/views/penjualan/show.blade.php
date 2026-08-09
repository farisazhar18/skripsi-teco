@extends('layouts.pos')

@section('content')

<h1 class="page-title">Detail Transaksi Penjualan</h1>

@if(session('success'))
    <div style="text-align:center; margin-bottom:20px;">
        <span class="success">✅ {{ session('success') }}</span>
    </div>
@endif

<div style="margin-bottom: 20px; display: flex; gap: 10px;">
    <a href="{{ route('penjualan.cetakStruk', $penjualan->id) }}" target="_blank" class="btn" style="background-color: #3b82f6; color: white;">
        🖨️ Cetak Struk
    </a>
    <a href="{{ route('penjualan.create') }}" class="btn btn-secondary">Kembali ke Kasir</a>
</div>

{{-- JIKA ADA SNAP TOKEN (Artinya bayar via QRIS belum lunas) --}}
@if(!empty($snapToken))
    <div style="background-color: #fff3cd; padding: 20px; border-radius: 12px; border: 2px dashed #e67e22; text-align: center; margin-bottom: 20px;">
        <h3 style="color: #d35400; margin-top: 0;">⚠️ Pembayaran QRIS Belum Diselesaikan</h3>
        <p>Silakan tunjukkan QRIS kepada pelanggan untuk di-scan.</p>
        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
            <button id="pay-button" class="btn" style="background-color: #183f37; color: white; padding: 15px 30px; font-size: 18px; border-radius: 8px;">
                📱 Tampilkan Layar QRIS
            </button>
            <form action="{{ route('penjualan.updateStatus', $penjualan->id) }}" method="POST" class="m-0">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="menunggu">
                <button type="submit" class="btn" style="background-color: #10b981; color: white; padding: 15px 30px; font-size: 18px; border-radius: 8px;" onclick="return confirm('Yakin ingin menandai pesanan ini sudah dibayar? (Hanya untuk percobaan lokal)')">
                    ✅ Simulasi Lunas
                </button>
            </form>
        </div>
    </div>

    <form id="form-qris-success" action="{{ route('penjualan.updateStatus', $penjualan->id) }}" method="POST" style="display:none;">
        @csrf @method('PATCH')
        <input type="hidden" name="status" value="menunggu">
    </form>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    // Langsung update status dengan mensubmit form tersembunyi
                    document.getElementById('form-qris-success').submit();
                },
                onPending: function(result){
                    alert("Menunggu pembayaran dari customer!");
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                }
            });
        });
    </script>
@endif

<div class="card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px;">
    <div>
        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">No. Pesanan</strong>
        <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
            #{{ $penjualan->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ date('ym', strtotime($penjualan->tanggal)) }}-{{ str_pad($penjualan->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}
        </p>
    </div>
    
    <div>
        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Tanggal & Waktu</strong>
        <p style="margin: 5px 0 0 0; font-size: 15px; font-weight: 500;">
            {{ date('d M Y', strtotime($penjualan->tanggal)) }} - {{ date('H:i', strtotime($penjualan->created_at)) }}
        </p>
    </div>

    <div>
        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Outlet</strong>
        <p style="margin: 5px 0 0 0; font-size: 15px; font-weight: 500;">
            {{ $penjualan->outlet == 'hasanuddin' ? 'Hasanuddin' : 'Makmur' }}
        </p>
    </div>

    <div>
        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Metode Pembayaran</strong>
        <p style="margin: 5px 0 0 0;">
            @if($penjualan->metode_pembayaran == 'QRIS')
                <span class="badge badge-success" style="background-color: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 6px; font-weight: bold;">📱 QRIS</span>
            @else
                <span class="badge badge-warning" style="background-color: #fef9c3; color: #854d0e; padding: 5px 10px; border-radius: 6px; font-weight: bold;">💵 Tunai</span>
            @endif
        </p>
    </div>

    <div>
        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Kasir</strong>
        <p style="margin: 5px 0 0 0; font-size: 15px; font-weight: 500;">
            {{ ucfirst(auth()->user()->name ?? 'nama' ) }}
        </p>
    </div>
</div>

<div class="table-card" style="overflow-x:auto; margin-top: 20px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="text-align: left; padding: 15px;">Nama Produk</th>
                <th style="text-align: center; padding: 15px;">Ukuran</th>
                <th style="text-align: center; padding: 15px;">Tipe</th>
                <th style="text-align: left; padding: 15px;">Catatan Request</th>
                <th style="text-align: right; padding: 15px;">Harga</th>
                <th style="text-align: center; padding: 15px;">Qty</th>
                <th style="text-align: right; padding: 15px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; @endphp
            @foreach($penjualan->detailPenjualans as $detail)
                @php $totalQty += $detail->jumlah; @endphp
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: bold; color: #0f172a;">{{ $detail->produk->nama_produk }}</td>
                    <td style="text-align: center; padding: 15px;">
                        <span style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($detail->ukuran ?? '-') }}
                        </span>
                    </td>
                    <td style="text-align: center; padding: 15px;">
                        <span style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($detail->tipe ?? '-') }}
                        </span>
                    </td>
                    <td style="padding: 15px; font-style: italic; color: #d97706;">
                        {{ $detail->keterangan ?? '-' }}
                    </td>
                    <td style="text-align: right; padding: 15px;">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td style="text-align: center; padding: 15px; font-weight: bold;">{{ $detail->jumlah }}</td>
                    <td style="text-align: right; padding: 15px; font-weight: bold; color: #0f172a;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            <tr style="background-color: #f8fafc; font-weight: bold; font-size: 16px; border-top: 2px solid #e2e8f0;">
                <td colspan="5" style="text-align: right; padding: 20px; color: #475569;">TOTAL TRANSAKSI ({{ $totalQty }} Item):</td>
                <td colspan="2" style="text-align: right; padding: 20px; color: #10b981; font-size: 18px; font-weight: 800;">
                    Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
<br>
    <div style="margin-bottom: 20px; display: flex; gap: 10px;">       
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali ke Penjualan</a>
    </div>

@endsection