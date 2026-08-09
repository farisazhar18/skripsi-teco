@extends('layouts.pos')

@section('content')
<div class="card" style="margin-top: 20px; padding: 30px; border-radius: 20px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <h1 style="color: #183f37; font-size: 24px; margin-bottom: 20px;">Laporan Sisa Bahan Fisik</h1>
    <p style="color: #64748b; margin-bottom: 30px;">Event: <strong>{{ $event->nama_event }}</strong></p>

    <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #b45309;">
        <strong>⚠️ Perhatian Barista / Logistik:</strong><br>
        Berikut adalah estimasi sisa bahan menurut sistem. Silakan sesuaikan angkanya dengan <strong>sisa fisik aktual</strong> yang benar-benar layak dikembalikan ke Gudang Utama. (Jika ada yang basi/tumpah, kurangi angkanya!).
    </div>

    <!-- 🔥 INI DIA TRIK JAVASCRIPT & TARGET BLANK-NYA 🔥 -->
    <form action="{{ route('event.selesaikanPesanan', $event->id) }}" method="POST" target="_blank" onsubmit="setTimeout(function(){ window.location.href = '{{ route('event.tugas') }}'; }, 1500);">
        @csrf
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 10px;">Bahan Baku</th>
                        <th style="padding: 10px; text-align: center;">Estimasi Sisa (Sistem)</th>
                        <th style="padding: 10px; text-align: center; width: 250px;">Sisa Fisik Aktual <span style="color:red">*</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->eventDetails as $detail)
                        @if(isset($sisaSistem[$detail->id]) && $sisaSistem[$detail->id] > 0)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 15px 10px; font-weight: 600;">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                            <td style="padding: 15px 10px; text-align: center; color: #64748b; font-style: italic;">
                                {{ $sisaSistem[$detail->id] }} {{ $detail->bahanBaku->satuan ?? '' }}
                            </td>
                            <td style="padding: 15px 10px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    {{-- Value bawaannya adalah sisa sistem, jadi Barista tinggal ngedit kalau beda --}}
                                    <input type="number" name="sisa_fisik[{{ $detail->id }}]" 
                                           value="{{ $sisaSistem[$detail->id] }}" 
                                           min="0" step="0.01" required 
                                           style="width: 100px; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; text-align: center;">
                                    <span style="font-weight: bold; color: #183f37;">{{ $detail->bahanBaku->satuan ?? '' }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('event.detail', $event->id) }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none;">Batal</a>
            <!-- 🔥 TEKS TOMBOL DIUBAH BIAR JELAS 🔥 -->
            <button type="submit" class="btn" style="background: #10b981; padding: 10px 20px; border-radius: 8px; border: none; color: white; font-weight: bold; cursor: pointer;">
                🏁 Selesai & Unduh Laporan PDF
            </button>
        </div>
    </form>
</div>
@endsection