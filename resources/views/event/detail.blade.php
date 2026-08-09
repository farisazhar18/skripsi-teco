@extends('layouts.pos')

@section('content')
<div class="card" style="margin-top: 20px; padding: 30px; border-radius: 20px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <h1 style="color: #183f37; font-size: 24px; margin-bottom: 20px;">Detail Eksekusi: {{ $event->nama_event }}</h1>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div style="background: #f8f6f2; padding: 15px; border-radius: 10px;">
            <p style="margin: 0; color: #666; font-size: 13px;">Tanggal Pelaksanaan</p>
            <strong>{{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y') }}</strong>
        </div>
        <div style="background: #f8f6f2; padding: 15px; border-radius: 10px;">
            <p style="margin: 0; color: #666; font-size: 13px;">Status Saat Ini</p>
            <strong>
                @if($event->status == 'menunggu_logistik')
                    ⏳ Menunggu Pengajuan Logistik
                @elseif($event->status == 'menunggu_acc_pengadaan')
                    ⚠️ Menunggu ACC Manager
                @elseif($event->status == 'menunggu_pembelian')
                    🛒 Menunggu Proses Pembelian (PO)
                @elseif($event->status == 'menunggu_barang_event')
                    🚚 Menunggu Barang Datang
                @elseif($event->status == 'bahan_ready')
                    ✅ Bahan Ready (Siap Diserahkan)
                @elseif($event->status == 'diserahkan')
                    ☕ Sedang Diproses Barista
                @else
                    🏁 {{ ucwords(str_replace('_', ' ', $event->status)) }}
                @endif
            </strong>
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; margin-bottom: 10px; color: #1e293b;">Catatan Kebutuhan Pemesanan</h3>
        <div style="background: #fff; border: 1px solid #eae5dc; padding: 15px; border-radius: 10px;">
            {!! $event->detail_pesanan ?? '<em>Detail pesanan tidak tersedia.</em>' !!}
        </div>
    </div>

    {{-- DAFTAR KEBUTUHAN BAHAN BAKU & FORM PENGAJUAN --}}
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; margin-bottom: 10px; color: #1e293b;">Daftar Kebutuhan Bahan Baku</h3>

        @if($event->status == 'menunggu_logistik' && in_array(auth()->user()->role, ['logistik', 'owner']))
            <!-- FORM 1: LOGISTIK MENGAJUKAN PENGADAAN -->
            <form action="{{ route('event.ajukanPengadaan', $event->id) }}" method="POST">
                @csrf
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                <th style="padding: 10px;">Bahan Baku</th>
                                <th style="padding: 10px; text-align: center;">Kebutuhan Event</th>
                                <th style="padding: 10px; text-align: center;">Jumlah Beli <span style="color:red">*</span></th>
                                <th style="padding: 10px; text-align: center;">Satuan Beli <span style="color:red">*</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->eventDetails as $detail)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 10px; font-weight: 600;">
                                    {{ $detail->bahanBaku->nama_bahan ?? '-' }}
                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                </td>
                                <td style="padding: 10px; text-align: center; color: #d97706; font-weight: bold;">
                                    {{ $detail->jumlah_dibutuhkan }} {{ $detail->bahanBaku->satuan ?? '' }}
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <input type="number" name="jumlah_beli[]" min="0.01" step="0.01" required style="width: 100px; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; text-align: center;">
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <select name="satuan_beli[]" required style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                        <option value="{{ $detail->bahanBaku->satuan ?? '' }}">{{ $detail->bahanBaku->satuan ?? '' }}</option>
                                        <option value="Liter">Liter</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Botol">Botol</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn" style="background: #0284c7; padding: 10px 20px; border-radius: 8px;">🛒 Ajukan Pengadaan ke Manager</button>
                </div>
            </form>

        @elseif($event->status == 'menunggu_acc_pengadaan' && in_array(auth()->user()->role, ['owner', 'operational_manager']))
            <!-- FORM 2: MANAGER MEREVISI & ACC PENGADAAN -->
            <form action="{{ route('event.accPengadaan', $event->id) }}" method="POST">
                @csrf
                <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #b45309;">
                    <strong>⚠️ Validasi Pembelian:</strong> Silakan sesuaikan kolom <strong>ACC Manager</strong> di bawah ini jika Anda ingin mengubah jumlah pembelian yang diajukan oleh Logistik.
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                <th style="padding: 10px;">Bahan Baku</th>
                                <th style="padding: 10px; text-align: center;">Kebutuhan Event</th>
                                <th style="padding: 10px; text-align: center; color: #64748b;">Pengajuan Logistik</th>
                                <th style="padding: 10px; text-align: center;">ACC Manager (Beli Final) <span style="color:red">*</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->eventDetails as $detail)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 10px; font-weight: 600;">
                                    {{ $detail->bahanBaku->nama_bahan ?? '-' }}
                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                </td>
                                <td style="padding: 10px; text-align: center; color: #d97706; font-weight: bold;">
                                    {{ $detail->jumlah_dibutuhkan }} {{ $detail->bahanBaku->satuan ?? '' }}
                                </td>
                                <td style="padding: 10px; text-align: center; color: #64748b; text-decoration: underline dotted;">
                                    {{ $detail->jumlah_beli }} {{ $detail->satuan_beli }}
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                        <input type="number" name="jumlah_beli[]" value="{{ $detail->jumlah_beli }}" min="0.01" step="0.01" required style="width: 100px; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; text-align: center;">
                                        <input type="hidden" name="satuan_beli[]" value="{{ $detail->satuan_beli }}">
                                        <span style="font-weight: bold; color: #183f37;">{{ $detail->satuan_beli }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- 🔥 Tombol ACC dimasukin lagi ke dalam form biar datanya kekirim 🔥 -->
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn" style="background: #10b981; padding: 10px 20px; border-radius: 8px;">✅ ACC & Tetapkan Pembelian</button>
                </div>
            </form>

        @else
            <!-- TABEL 3: TAMPILAN BACA SAJA KESELURUHAN (Barista, atau status lain) -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                            <th style="padding: 10px;">Bahan Baku</th>
                            <th style="padding: 10px; text-align: center;">Kebutuhan Event</th>
                            <th style="padding: 10px; text-align: center;">Pembelian Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->eventDetails as $detail)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 10px; font-weight: 600;">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                            <td style="padding: 10px; text-align: center; color: #d97706; font-weight: bold;">
                                {{ $detail->jumlah_dibutuhkan }} {{ $detail->bahanBaku->satuan ?? '' }}
                            </td>
                            <td style="padding: 10px; text-align: center; color: #047857; font-weight: bold;">
                                @if($detail->jumlah_beli)
                                    {{ $detail->jumlah_beli }} {{ $detail->satuan_beli }}
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-weight: normal;">Belum diajukan</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- TOMBOL EKSEKUSI SESUAI ALUR BERSAMA --}}
    <div style="display: flex; gap: 10px; border-top: 2px solid #f8f6f2; padding-top: 20px; flex-wrap: wrap;">
        <a href="{{ route('event.tugas') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none;">← Kembali</a>

        @if(in_array(auth()->user()->role, ['logistik', 'owner']))
            
            @if($event->status == 'menunggu_pembelian')
                <!-- 🔥 LINK MENUJU HALAMAN KHUSUS PO 🔥 -->
                <a href="{{ route('event.formPO', $event->id) }}" class="btn" style="background: #e67e22; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
                    📄 Buka Halaman Cetak PO
                </a>
            
            @elseif($event->status == 'menunggu_barang_event')
                <form action="{{ route('event.terima', $event->id) }}" method="POST">
                    @csrf
                    <button class="btn" style="background: #0284c7; padding: 10px 20px; border-radius: 8px;">📦 Terima Barang Event</button>
                </form>
            @elseif($event->status == 'bahan_ready')
                <form action="{{ route('event.serahkan', $event->id) }}" method="POST">
                    @csrf
                    <button class="btn" style="background: #d97706; padding: 10px 20px; border-radius: 8px;">📤 Serahkan ke Barista</button>
                </form>
            @endif
        @endif

        @if(in_array(auth()->user()->role, ['barista', 'owner']) && $event->status == 'diserahkan')
            <a href="{{ route('event.laporSisa', $event->id) }}" class="btn" style="background: #10b981; padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none;">
                🏁 Selesaikan & Lapor Sisa Bahan
            </a>
        @endif
    </div>
</div>
@endsection