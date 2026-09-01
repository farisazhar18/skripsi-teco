@extends('layouts.pos')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h1 class="page-title" style="margin: 0;">Terima Barang Event: {{ $event->nama_event }}</h1>
        <p style="color: #64748b; margin-top: 5px;">Centang PO / Barang yang sudah benar-benar sampai.</p>
    </div>
    <a href="{{ route('event.detail', $event->id) }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; border-radius: 6px;">
        ← Kembali
    </a>
</div>

<form action="{{ route('event.terimaMassal', $event->id) }}" method="POST" id="form-terima-massal">
    @csrf

    <div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #0284c7; color: white;">
                    <th style="padding: 12px; text-align: center; width: 5%;">
                        <input type="checkbox" id="checkAll" style="cursor: pointer; transform: scale(1.2);">
                    </th>
                    <th style="padding: 12px; text-align: left; width: 25%;">Nomor PO / Batch</th>
                    <th style="padding: 12px; text-align: left; width: 50%;">Daftar Barang</th>
                    <th style="padding: 12px; text-align: center; width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedDetails as $po => $items)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 15px 12px; text-align: center; vertical-align: middle;">
                        <input type="checkbox" name="po_numbers[]" value="{{ $po }}" class="check-item" style="cursor: pointer; transform: scale(1.2);">
                    </td>
                    <td style="padding: 15px 12px; font-weight: bold; color: #1e293b; vertical-align: middle;">
                        <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px;">
                            {{ $po }}
                        </span>
                    </td>
                    <td style="padding: 15px 12px;">
                        <ul style="margin: 0; padding-left: 20px; color: #475569; font-size: 14px;">
                            @foreach($items as $item)
                                <li>
                                    <strong>{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong> - 
                                    <span style="color: #0284c7; font-weight: bold;">{{ $item->jumlah_beli }} {{ $item->satuan_beli }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="padding: 15px 12px; text-align: center; vertical-align: middle;">
                        <span style="padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                            🚚 Menunggu Barang
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                        📦 Tidak ada barang yang menunggu diterima.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($groupedDetails->count() > 0)
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <button type="button" onclick="konfirmasiTerimaMassal()" class="btn" style="background: #0284c7; padding: 12px 24px; border-radius: 8px; font-size: 15px;">
            📦 Terima PO Terpilih
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
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih minimal satu batch PO yang sudah sampai!' });
            return;
        }

        Swal.fire({
            title: 'Terima Barang?',
            text: "Barang pada PO yang dicentang akan ditambahkan ke stok event.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-terima-massal').submit();
            }
        });
    }
</script>
@endsection
