@extends('layouts.pos')

@section('content')
    
    <div class="card" style="margin-top: 20px; padding: 30px;">
        <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #e5efe6; padding-bottom: 15px;">
            <div>
                <h2>Detail Pengadaan #{{ $pembelian->id }}</h2>
                <p style="color: #666; margin: 5px 0;">Diajukan oleh bagian: <strong>Logistik</strong></p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; color: #666;">Tanggal: {{ date('d-m-Y', strtotime($pembelian->tanggal)) }}</p>
                <p style="margin: 5px 0 0 0;">
                    @if($pembelian->status_acc == 'disetujui')
                        <span class="badge badge-success" style="background: #e5f5ec; color: #0f7a3a;">✅ Masuk Gudang</span>
                    @elseif($pembelian->status_acc == 'menunggu_pembelian')
                        <span class="badge" style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">🛒 Menunggu PO</span>
                    @elseif($pembelian->status_acc == 'menunggu_barang')
                        <span class="badge" style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">🚚 Menunggu Barang</span>
                    @elseif($pembelian->status_acc == 'ditolak')
                        <span class="badge badge-danger">❌ Ditolak</span>
                    @else
                        <span class="badge badge-warning">⏳ Menunggu ACC</span>
                    @endif
                </p>
            </div>
        </div>

        <div style="margin-top: 25px; line-height: 2;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 10px 0;">
                <span style="color: #666;">Nama Bahan Baku:</span>
                <strong>{{ $pembelian->bahanBaku->nama_bahan ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 10px 0;">
                <span style="color: #666;">Jumlah Pengadaan (Awal):</span>
                <strong>{{ $pembelian->jumlah }} {{ $pembelian->satuan_beli }}</strong>
            </div>
            @if($pembelian->keterangan)
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 10px 0;">
                <span style="color: #666;">Keterangan:</span>
                <strong style="color: #b45309;">{{ $pembelian->keterangan }}</strong>
            </div>
            @endif
        </div>

        @php $isMenunggu = !in_array($pembelian->status_acc, ['disetujui', 'menunggu_barang', 'ditolak', 'menunggu_pembelian']); @endphp

        @if(in_array(auth()->user()->role, ['owner', 'operational_manager']) && $isMenunggu)
            <div style="background: #f5efe6; padding: 25px; border-radius: 15px; margin-top: 40px; text-align: center; border: 2px dashed #dcd3c6;">
                <h4 style="margin: 0 0 5px 0; color: #183f37; font-size: 18px;">Lembar Persetujuan Manajemen</h4>
                <p style="font-size: 13px; color: #5b6256; margin-bottom: 20px;">Anda dapat merubah jumlah pengadaan sebelum melakukan ACC.</p>
                
                <form action="{{ route('pembelian.acc', $pembelian->id) }}" method="POST">
                    @csrf
                    
                    <!-- 🔥 KOTAK REVISI JUMLAH 🔥 -->
                    <div style="margin-bottom: 20px; display: inline-flex; align-items: center; gap: 10px; background: white; padding: 10px 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <label style="font-size: 14px; font-weight: bold; color: #1e293b;">Revisi Jumlah (Qty):</label>
                        <input type="number" name="jumlah_revisi" value="{{ $pembelian->jumlah }}" min="1" required style="width: 80px; padding: 8px; text-align: center; font-size: 16px; font-weight: bold; border: 2px solid #cbd5e1; border-radius: 8px; outline: none;">
                        <span style="font-size: 15px; font-weight: bold; color: #475569;">{{ $pembelian->satuan_beli }}</span>
                    </div>
                    <br>

                    <button type="submit" name="action" value="setujui" class="btn" style="background: #0f7a3a; padding: 11px 25px; margin-right: 10px;">
                        ✔️ Setujui Pengadaan
                    </button>
                    <button type="submit" name="action" value="tolak" class="btn btn-danger" style="padding: 11px 25px;">
                        ❌ Tolak Pengajuan
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="margin: 0;">
            ← Kembali ke Daftar
        </a>

       
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiTerima() {
            Swal.fire({
                title: 'Konfirmasi Terima Barang',
                text: "Apakah barang dari supplier sudah benar-benar sampai di gudang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0284c7', 
                cancelButtonColor: '#d33', 
                confirmButtonText: 'Ya, Barang Sudah Sampai!',
                cancelButtonText: 'Batal',
                background: '#f8f6f2',
                color: '#183f37', 
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-terima-barang').submit();
                }
            })
        }
    </script>
@endsection