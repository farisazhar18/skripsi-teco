@extends('layouts.pos')

@section('content')

<!-- Tambahin Select2 biar dropdown bahan bakunya cakep -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<h1 class="page-title" style="margin-bottom: 25px;">Tambah Pengajuan Pengadaan Bahan Baku</h1>

@if ($errors->any())
    <div class="card" style="margin-bottom: 20px;">
        <ul style="color:red; margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">

<form action="/pembelian" method="POST">
    @csrf

    <div class="form-group" style="margin-bottom: 25px;">
        <label style="font-weight: bold;">Tanggal Pengajuan</label>
        <input type="date"
               name="tanggal"
               value="{{ date('Y-m-d') }}"
               required
               style="width: 100%; max-width: 200px; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
    </div>

    {{-- TABEL MULTIPLE ITEM --}}
    <div class="form-group">
        <label style="font-weight: bold;">Daftar Bahan Baku</label>
        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-top: 10px;">
            <table style="width: 100%; border-collapse: collapse;" id="table-pengajuan">
                <thead style="background-color: #183f37; color: white;">
                    <tr>
                        <th style="text-align: left; padding: 12px 15px;">Bahan Baku</th>
                        <th style="text-align: center; width: 120px; padding: 12px 15px;">Jumlah</th>
                        <th style="text-align: left; width: 150px; padding: 12px 15px;">Satuan Beli</th>
                        <th style="text-align: left; padding: 12px 15px;">Keterangan</th>
                        <th style="text-align: center; width: 60px; padding: 12px 15px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-pengajuan">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 15px;">
                            <!-- 🔥 Ubah jadi array pake [] -->
                            <select name="bahan_baku_id[]" class="select2-bahan" style="width: 100%;" required>
                                <option value="">-- Pilih Bahan Baku --</option>
                                @foreach($bahanBaku as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->nama_bahan }} (Stok: {{ $item->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 12px 15px;">
                            <input type="number" name="jumlah[]" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: center;">
                        </td>
                        <td style="padding: 12px 15px;">
                            <select name="satuan_beli[]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">-- Satuan --</option>
                                <option value="ml">ml</option>
                                <option value="liter">liter</option>
                                <option value="gram">gram</option>
                                <option value="kg">kg</option>
                                <option value="pcs">pcs</option>
                                <option value="botol">botol</option>
                                <option value="pack">pack</option>
                            </select>
                        </td>
                        <td style="padding: 12px 15px;">
                            <input type="text" name="keterangan[]" placeholder="Opsional" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                        </td>
                        <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                            <button type="button" class="btn-sm remove-row" title="Hapus baris" style="background-color: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-size: 18px; font-weight: bold; transition: 0.2s;">
                                &times;
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <button type="button" id="btn-tambah-baris" style="margin-top: 15px; background-color: #f59e0b; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            + Tambah Bahan Lain
        </button>
    </div>

    <div class="form-actions" style="margin-top: 35px; display: flex; gap: 10px;">
        <button type="submit" style="background-color: #183f37; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Simpan Pengajuan
        </button>

        <a href="{{ route('pembelian.pengajuan') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 6px; text-decoration: none; border: 1px solid #ccc; color: #333;">
            ← Kembali
        </a>
    </div>

</form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi awal Dropdown Pencarian
        function initSelect2() {
            $('.select2-bahan').select2({
                placeholder: "-- Pilih Bahan Baku --",
                allowClear: true
            });
        }

        initSelect2();

        // Logika Tambah Baris Baru
        $('#btn-tambah-baris').click(function() {
            var newRow = `
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 15px;">
                        <select name="bahan_baku_id[]" class="select2-bahan" style="width: 100%;" required>
                            <option value="">-- Pilih Bahan Baku --</option>
                            @foreach($bahanBaku as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nama_bahan }} (Stok: {{ $item->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 12px 15px;">
                        <input type="number" name="jumlah[]" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: center;">
                    </td>
                    <td style="padding: 12px 15px;">
                        <select name="satuan_beli[]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="">-- Satuan --</option>
                            <option value="ml">ml</option>
                            <option value="liter">liter</option>
                            <option value="gram">gram</option>
                            <option value="kg">kg</option>
                            <option value="pcs">pcs</option>
                            <option value="botol">botol</option>
                            <option value="pack">pack</option>
                        </select>
                    </td>
                    <td style="padding: 12px 15px;">
                        <input type="text" name="keterangan[]" placeholder="Opsional" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    </td>
                    <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                        <button type="button" class="btn-sm remove-row" title="Hapus baris" style="background-color: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-size: 18px; font-weight: bold; transition: 0.2s;">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
            $('#tbody-pengajuan').append(newRow);
            initSelect2(); // Panggil lagi biar baris baru juga punya fitur search
        });

        // Logika Hapus Baris
        $(document).on('click', '.remove-row', function() {
            if ($('#tbody-pengajuan tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 pengajuan bahan baku!');
            }
        });
    });
</script>

@endsection