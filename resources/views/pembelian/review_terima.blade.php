@extends('layouts.pos')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h1 class="page-title" style="margin: 0;">Terima Barang Massal</h1>
        <p style="color: #64748b; margin-top: 5px;">Centang barang yang sudah benar-benar sampai di gudang.</p>
    </div>
    <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; border-radius: 6px;">
        ← Kembali
    </a>
</div>

<form action="{{ route('pembelian.terimaMassal') }}" method="POST" id="form-terima-massal">
    @csrf

    <div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #0284c7; color: white;">
                    <th style="padding: 12px; text-align: center; width: 5%;">
                        <input type="checkbox" id="checkAll" style="cursor: pointer; transform: scale(1.2);">
                    </th>
                    <th style="padding: 12px; text-align: center; width: 15%;">Tanggal Order</th>
                    <th style="padding: 12px; text-align: left; width: 40%;">Bahan Baku</th>
                    <th style="padding: 12px; text-align: center; width: 20%;">Jumlah (Qty)</th>
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
                        <strong style="color: #0284c7; font-size: 16px;">{{ $item->jumlah }}</strong> 
                        <span style="color: #64748b; font-size: 13px; font-weight: 600;">{{ $item->satuan_beli }}</span>
                    </td>
                    <td style="padding: 15px 12px; text-align: center;">
                        <span style="padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                            🚚 Menunggu Barang
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                        📦 Tidak ada pengadaan barang yang sedang ditunggu.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuans->count() > 0)
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <button type="button" onclick="konfirmasiTerimaMassal()" class="btn" style="background: #0284c7; padding: 12px 24px; border-radius: 8px; font-size: 15px;">
            📦 Terima Barang Terpilih
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

    function konfirmasiTerimaMassal() {
        let selected = document.querySelectorAll('.check-item:checked');
        if(selected.length === 0) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih minimal satu barang yang sudah sampai!' });
            return;
        }

        Swal.fire({
            title: 'Terima Barang?',
            text: "Barang yang dicentang akan ditambahkan ke stok gudang.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Masukkan Gudang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-terima-massal').submit();
            }
        });
    }
</script>
@endsection