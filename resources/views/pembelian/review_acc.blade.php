@extends('layouts.pos')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h1 class="page-title" style="margin: 0;">Persetujuan Pengajuan Stok Bahan Baku</h1>
        <p style="color: #64748b; margin-top: 5px;">Review, revisi jumlah (jika perlu), lalu centang data untuk disetujui atau ditolak.</p>
    </div>
    <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; border-radius: 6px;">
        ← Kembali
    </a>
</div>

<form action="{{ route('pembelian.accMassal') }}" method="POST" id="form-acc-massal">
    @csrf
    <!-- Input hidden untuk menampung jenis aksi (setujui / tolak) -->
    <input type="hidden" name="action" id="action-type" value="setujui">

    <div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #0f7a3a; color: white;">
                    <th style="padding: 12px; text-align: center; width: 5%;">
                        <input type="checkbox" id="checkAll" style="cursor: pointer; transform: scale(1.2);">
                    </th>
                    <th style="padding: 12px; text-align: center; width: 15%;">Tanggal Pengajuan</th>
                    <th style="padding: 12px; text-align: left; width: 40%;">Bahan Baku</th>
                    <th style="padding: 12px; text-align: center; width: 20%;">Revisi Jumlah (Qty)</th>
                    <th style="padding: 12px; text-align: center; width: 20%;">Status Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $item)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 15px 12px; text-align: center;">
                        <input type="checkbox" name="pembelian_ids[]" value="{{ $item->id }}" class="check-item" style="cursor: pointer; transform: scale(1.2);">
                    </td>
                    <td style="padding: 15px 12px; text-align: center; color: #64748b; font-size: 13px;">
                        {{ date('d M Y', strtotime($item->tanggal)) }}
                    </td>
                    <td style="padding: 15px 12px; font-weight: 600; color: #1e293b;">
                        {{ $item->bahanBaku->nama_bahan ?? '-' }}
                    </td>
                    <td style="padding: 15px 12px; text-align: center;">
                        <!-- 🔥 Input untuk fitur revisi jumlah oleh Manager 🔥 -->
                        <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                            <input type="number" name="jumlah[{{ $item->id }}]" value="{{ $item->jumlah }}" min="1" style="width: 70px; text-align: center; padding: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            <span style="color: #64748b; font-size: 13px; font-weight: 600;">{{ $item->satuan_beli }}</span>
                        </div>
                    </td>
                    <td style="padding: 15px 12px; text-align: center;">
                        <span style="padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                            ⏳ Menunggu ACC
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                        ✅ Tidak ada pengajuan yang sedang menunggu persetujuan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuans->count() > 0)
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <button type="button" onclick="konfirmasiAccMassal('setujui')" class="btn" style="background: #0f7a3a; padding: 12px 24px; border-radius: 8px; font-size: 15px; color: white;">
            ✔️ Setujui
        </button>
        <button type="button" onclick="konfirmasiAccMassal('tolak')" class="btn" style="background: #dc2626; border-color: #dc2626; padding: 12px 24px; border-radius: 8px; font-size: 15px; color: white;">
            ❌ Tolak 
        </button>
    </div>
    @endif
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('checkAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.check-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function konfirmasiAccMassal(actionType) {
        let selected = document.querySelectorAll('.check-item:checked');
        if(selected.length === 0) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih minimal satu pengajuan terlebih dahulu!' });
            return;
        }

        // Penyesuaian teks SweetAlert berdasarkan tombol yang ditekan
        let titleTxt = actionType === 'setujui' ? 'Setujui Pengajuan?' : 'Tolak Pengajuan?';
        let textTxt = actionType === 'setujui' ? 'Barang yang dicentang akan dilanjutkan ke proses pencetakan PO.' : 'Pengajuan yang dicentang akan dibatalkan/ditolak.';
        let confirmBtnColor = actionType === 'setujui' ? '#0f7a3a' : '#dc2626';
        let confirmBtnTxt = actionType === 'setujui' ? 'Ya, Setujui!' : 'Ya, Tolak!';

        Swal.fire({
            title: titleTxt,
            text: textTxt,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: confirmBtnTxt,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set nilai input hidden 'action' sesuai tombol yang diklik
                document.getElementById('action-type').value = actionType;
                // Eksekusi submit
                document.getElementById('form-acc-massal').submit();
            }
        });
    }
</script>
@endsection