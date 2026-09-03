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

        <h3 style="color: #0f7a3a; margin-bottom: 10px;">📝 Langkah 1: Pilih Barang yang Mau Dibeli</h3>
        
        @if($belumDicetak->count() > 0)
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
                    @foreach($belumDicetak as $item)
                    <tr style="border-bottom: 1px solid #eae5dc;">
                        <td style="padding: 10px; text-align: center;">
                            <input type="checkbox" name="pembelian_ids[]" value="{{ $item->id }}" style="transform: scale(1.5);">
                        </td>
                        <td style="padding: 10px;">
                            {{ date('d-m-Y', strtotime($item->updated_at ?? $item->tanggal)) }}
                        </td>
                        <td style="padding: 10px; font-weight: bold;">
                            {{ $item->bahanBaku->nama_bahan ?? '-' }}
                        </td>
                        <td style="padding: 10px;">
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
                Semua barang sudah di-generate PO-nya (Silakan cek di tabel Riwayat PO di bawah).
            </div>
        @endif
    </form>

    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- TABEL 2: RIWAYAT PO SUDAH DICETAK -->
    <h3 style="color: #1e293b; margin-bottom: 10px;">🗂️ Langkah 2: Riwayat PO yang Sudah Dicetak</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: -5px; margin-bottom: 15px;">Daftar barang ini siap diproses beli. Jika ada kesalahan, Anda bisa membatalkannya di sini.</p>

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
                        {{ $item->jumlah }} {{ $item->satuan_beli }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        <form action="{{ route('pembelian.batalPO', $item->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" onclick="return confirm('Batalkan cetak PO untuk barang ini? Barang akan kembali ke daftar atas.');" class="btn-sm" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; cursor: pointer; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold;">
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
            Belum ada PO yang dicetak.
        </div>
    @endif

    <hr style="margin: 30px 0; border: none; border-top: 2px dashed #e2e8f0;">

    <!-- FORM SELESAI (Ubah Status) -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="color: #1e293b; margin: 0 0 5px 0;">Langkah 3: Proses Pengadaan</h3>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Jika semua PO di Riwayat sudah benar, klik tombol di samping untuk melanjutkan.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="padding: 12px 20px; border-radius: 8px; text-decoration: none;">← Kembali ke Daftar</a>
            
            @if($sudahDicetak->count() > 0)
                <form action="{{ route('pembelian.prosesBeliMassal') }}" method="POST">
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