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

        @php
            $belumDicetak = $event->eventDetails->where('is_po_dicetak', false)->where('jumlah_beli', '>', 0);
            $sudahDicetak = $event->eventDetails->where('is_po_dicetak', true)->where('jumlah_beli', '>', 0);
        @endphp

        <h3 style="color: #0f7a3a; margin-bottom: 10px;">📝 Langkah 1: Pilih Barang yang Mau Dibeli</h3>

        @if($belumDicetak->count() > 0)
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
                    @foreach($belumDicetak as $detail)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; text-align: center;">
                                <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" style="transform: scale(1.5); cursor: pointer;">
                            </td>
                            <td style="padding: 12px; font-weight: 600; font-size: 15px;">
                                {{ $detail->bahanBaku->nama_bahan ?? '-' }}
                            </td>
                            <td style="padding: 12px; text-align: center; color: #047857; font-weight: bold; font-size: 15px;">
                                {{ $detail->jumlah_beli }} {{ $detail->satuan_beli }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <button type="submit" class="btn" style="background: #e67e22; padding: 12px 24px; border-radius: 8px; font-size: 15px;">
                📄 Cetak PO Terpilih
            </button>
        </div>
        @else
        <div style="padding: 20px; background: #fff4e5; color: #b56a00; border-radius: 10px;">
            Semua barang untuk event ini sudah di-generate PO-nya (Silakan cek di tabel Riwayat PO di bawah).
        </div>
        @endif
    </form>

    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- TABEL 2: RIWAYAT PO SUDAH DICETAK -->
    <h3 style="color: #1e293b; margin-bottom: 10px;">🗂️ Langkah 2: Riwayat PO yang Sudah Dicetak</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: -5px; margin-bottom: 15px;">Daftar barang event ini siap diproses beli. Jika ada kesalahan, Anda bisa membatalkannya di sini.</p>

    @if($sudahDicetak->count() > 0)
        <table class="table" style="width: 100%; text-align: left; border-collapse: collapse; background: #f8fafc;">
            <thead>
                <tr style="background: #475569; color: white;">
                    <th style="padding: 10px;">Supplier</th>
                    <th style="padding: 10px;">Bahan Baku</th>
                    <th style="padding: 10px;">Jumlah</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sudahDicetak as $item)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px; font-weight: bold; color: #0f7a3a;">
                        {{ $item->nama_supplier ?? 'Tanpa Nama' }}
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #334155;">
                        {{ $item->bahanBaku->nama_bahan ?? '-' }}
                    </td>
                    <td style="padding: 10px; color: #475569;">
                        {{ $item->jumlah_beli }} {{ $item->satuan_beli }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        <form action="{{ route('event.batalPO', $item->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" onclick="return confirm('Batalkan cetak PO Event untuk barang ini? Barang akan kembali ke daftar atas.');" class="btn-sm" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; cursor: pointer; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                                ↩️ Batalkan & Edit
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 20px; background: #f1f5f9; color: #64748b; border-radius: 10px;">
            Belum ada PO Event yang dicetak.
        </div>
    @endif

    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- FORM SELESAI (Ubah Status) -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="color: #1e293b; margin: 0 0 5px 0;">Langkah 3: Proses Pengadaan</h3>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Jika semua PO Event di Riwayat sudah benar, klik tombol di samping untuk melanjutkan status event.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('event.detail', $event->id) }}" class="btn-secondary" style="padding: 12px 20px; border-radius: 8px; text-decoration: none;">← Kembali ke Detail</a>
            
            @if($sudahDicetak->count() > 0)
                <form action="{{ route('event.prosesBeli', $event->id) }}" method="POST">
                    @csrf
                    <button class="btn" style="background: #0284c7; padding: 12px 24px; border-radius: 8px; border:none; color:white; cursor:pointer; font-size: 15px;">
                        🚚 Proses PO di Langkah 2 Saja (Menunggu Barang Datang)
                    </button>
                </form>
            @else
                <button class="btn" disabled style="background: #94a3b8; padding: 12px 24px; border-radius: 8px; border:none; color:white; cursor:not-allowed; font-size: 15px;">
                    🚚 Proses PO di Langkah 2 Saja (Menunggu Barang Datang)
                </button>
            @endif
        </div>
    </div>
</div>
@endsection