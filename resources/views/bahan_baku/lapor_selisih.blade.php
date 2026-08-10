@extends('layouts.pos')

@section('content')

<h1 class="page-title">Form Lapor Selisih Distribusi</h1>

@if ($errors->any())
    <div class="card" style="margin-bottom: 20px; background-color: #fee2e2; border: 1px solid #fca5a5;">
        <ul style="color:#b91c1c; margin: 0; padding-left: 20px; font-weight: 500;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card" style="max-width: 800px; margin: 0 auto; background-color: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
    
    <div style="background-color: #f8fafc; padding: 20px 25px; border-bottom: 1px solid #e2e8f0;">
        <h2 style="margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            ⚠️ Pelaporan Barang Kurang / Rusak
        </h2>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Laporkan jika fisik barang yang diterima tidak sesuai dengan data pengiriman dari Logistik.</p>
    </div>

    <div style="padding: 25px;">
        <form action="{{ route('bahan-baku.store-selisih', $distribusi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="tgl" value="{{ $tanggal }}">
            <input type="hidden" name="dikirim" value="{{ $dikirim }}">

            <div class="form-group">
                <label style="font-weight: 600; color: #334155;">Nama Bahan Baku</label>
                <input type="text" value="{{ $bahanBaku->nama_bahan }} (Outlet: {{ ucfirst($bahanBaku->outlet) }})" disabled style="background-color: #f1f5f9; width: 100%; font-weight: bold; color: #475569; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 6px; cursor: not-allowed;" class="form-control">
            </div>

            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px;">
                <!-- Kotak Info Logistik -->
                <div style="flex: 1; min-width: 250px; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px;">
                    <label style="font-size: 13px; font-weight: 600; color: #166534; display: block; margin-bottom: 5px;">📦 Data Pengiriman Logistik</label>
                    <div style="font-size: 24px; font-weight: bold; color: #15803d;">
                        {{ $dikirim }} <span style="font-size: 16px; font-weight: 600;">{{ $bahanBaku->satuan }}</span>
                    </div>
                    <div style="font-size: 12px; color: #166534; margin-top: 4px;">Tgl: {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</div>
                </div>

                <!-- Input Fisik Diterima -->
                <div style="flex: 1; min-width: 250px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 15px;">
                    <label style="font-size: 13px; font-weight: 600; color: #991b1b; display: block; margin-bottom: 5px;">✅ Fisik Benar-Benar Diterima <span style="color:red;">*</span></label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="number" name="fisik_diterima" min="0" max="{{ $dikirim - 1 }}" required placeholder="Ketik angka..." style="flex: 1; width: 100%; padding: 8px 12px; border: 1px solid #f87171; border-radius: 6px; font-size: 16px; font-weight: bold; color: #7f1d1d; outline: none;" autofocus>
                        <span style="font-weight: bold; color: #991b1b;">
                            {{ $bahanBaku->satuan }}
                        </span>
                    </div>
                    <div style="font-size: 12px; color: #991b1b; margin-top: 4px;">Harus lebih kecil dari data logistik.</div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label style="font-weight: 600; color: #334155;">Keterangan Kenapa Berkurang? <span style="color:red;">*</span></label>
                <textarea name="alasan" rows="3" required placeholder="Contoh: 1 dus susu bocor di jalan, atau dihitung ternyata kurang 1 renceng..." style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; transition: 0.2s;" class="form-control" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label style="font-weight: 600; color: #334155;">Foto Bukti Fisik / Barang Rusak <span style="color:red;">*</span></label>
                <div style="border: 2px dashed #cbd5e1; padding: 20px; text-align: center; border-radius: 8px; background-color: #f8fafc; transition: 0.2s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#cbd5e1'">
                    <input type="file" name="foto_bukti" accept="image/*" required style="width: 100%; cursor: pointer;">
                    <small style="color: #64748b; font-style: italic; display: block; margin-top: 8px;">
                        📌 Wajib lampirkan foto barang / kardus / kondisi paket sebagai bukti untuk diajukan ke Manager.
                    </small>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <a href="{{ route('bahan-baku.masuk') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; transition: 0.2s; background-color: #f1f5f9;">
                    Batal
                </a>
                <button type="submit" class="btn" style="background-color: #dc2626; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
                    Kirim Laporan Selisih
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
