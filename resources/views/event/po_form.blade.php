@extends('layouts.pos')

@section('content')
<h1 class="page-title">Buat Purchase Order (PO)</h1>

<div class="card" style="padding: 30px; background: white; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <div style="margin-bottom: 20px;">
        <h2 style="color: #183f37; font-size: 20px;">Event: {{ $event->nama_event }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Pilih bahan baku yang akan dibeli pada supplier yang sama, lalu cetak PO.</p>
    </div>

    <!-- FORM CETAK PO (Buka di Tab Baru) -->
    <form action="{{ route('event.cetakPO', $event->id) }}" method="GET" target="_blank">
        <div style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 15px; border-radius: 10px; margin-bottom: 25px; color: #0284c7;">
            <strong>ℹ️ Cara Kerja:</strong> Centang barang, ketik nama supplier tujuan, lalu klik <strong>Cetak PO Terpilih</strong>. Anda bisa mengulanginya berkali-kali untuk supplier yang berbeda.
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #183f37; display: block; margin-bottom: 8px;">Nama Supplier Tujuan:</label>
            <input type="text" name="supplier" placeholder="Contoh: Toko Plastik Makmur / Agen Susu" required style="width: 100%; max-width: 450px; padding: 12px; border-radius: 8px; border: 1px solid #c9bca8;">
        </div>

        <div style="overflow-x: auto; margin-bottom: 30px;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background: #183f37; color: white; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 12px; text-align: center; width: 5%;">Pilih</th>
                        <th style="padding: 12px;">Bahan Baku</th>
                        <th style="padding: 12px; text-align: center;">Jumlah Dibeli</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->eventDetails as $detail)
                        @if($detail->jumlah_beli > 0)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; text-align: center;">
                                <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" style="transform: scale(1.5); cursor: pointer;">
                            </td>
                            <td style="padding: 12px; font-weight: 600; font-size: 15px;">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center; color: #047857; font-weight: bold; font-size: 15px;">
                                {{ $detail->jumlah_beli }} {{ $detail->satuan_beli }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div>
            <button type="submit" class="btn" style="background: #e67e22; padding: 12px 24px; border-radius: 8px; font-size: 15px;">
                📄 Cetak PO Terpilih
            </button>
        </div>
    </form>

    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- FORM SELESAI (Ubah Status) -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="color: #1e293b; margin: 0 0 5px 0;">Sudah Selesai Mencetak Semua PO?</h3>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Klik tombol di samping untuk melanjutkan status event.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('event.detail', $event->id) }}" class="btn-secondary" style="padding: 12px 20px; border-radius: 8px; text-decoration: none;">← Kembali ke Detail</a>
            
            <form action="{{ route('event.prosesBeli', $event->id) }}" method="POST">
                @csrf
                <button class="btn" style="background: #0284c7; padding: 12px 24px; border-radius: 8px; border:none; color:white; cursor:pointer; font-size: 15px;">
                    🚚 Tandai Semua Sedang Dibeli
                </button>
            </form>
        </div>
    </div>
</div>
@endsection