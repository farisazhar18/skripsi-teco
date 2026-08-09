@extends('layouts.pos')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<h1 class="page-title">Tambah Distribusi</h1>

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

    <!-- 🔥 TOMBOL TRIGGER MODAL CONTEKAN -->
    @if(isset($kebutuhanOutlet) && $kebutuhanOutlet->count() > 0)
    <div style="margin-bottom: 25px;">
        <button type="button" onclick="document.getElementById('modalKebutuhan').style.display='flex'" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; font-size: 15px; transition: 0.2s;">
            ⚠️ Cek Daftar Kebutuhan Outlet
        </button>
    </div>

    <!-- 🔥 MODAL POPUP MELAYANG -->
    <div id="modalKebutuhan" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(3px);">
        <div style="background-color: #fff; width: 90%; max-width: 800px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; max-height: 85vh;">
            
            <!-- HEADER MODAL & FILTER -->
            <div style="background-color: #fffbeb; padding: 20px; border-bottom: 1px solid #fde68a; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1;">
                    <h3 style="color: #b45309; margin: 0 0 8px 0; font-size: 18px;">⚠️ Info Kebutuhan Outlet</h3>
                    <p style="color: #92400e; font-size: 14px; margin: 0;">Berikut bahan baku yang menipis. Gunakan sebagai acuan distribusi.</p>
                </div>
                
                <!-- 🔥 DROPDOWN FILTER OUTLET DI DALAM MODAL 🔥 -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="font-size: 13px; color: #92400e; font-weight: bold;">Filter:</label>
                    <select id="filterOutletModal" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #fde68a; outline: none; background: white; color: #b45309; font-weight: bold; cursor: pointer;">
                        <option value="all">Semua Outlet</option>
                        <option value="hasanuddin">Hasanuddin</option>
                        <option value="makmur">Makmur</option>
                    </select>
                    
                    <button type="button" onclick="document.getElementById('modalKebutuhan').style.display='none'" style="background: none; border: none; font-size: 28px; color: #b45309; cursor: pointer; line-height: 1; padding: 0; margin-left: 10px;">&times;</button>
                </div>
            </div>

            <!-- ISI MODAL (TABEL SCROLLABLE) -->
            <div style="overflow-y: auto; padding: 0 20px 20px 20px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead style="position: sticky; top: 0; background-color: #fff; z-index: 1;">
                        <tr style="border-bottom: 2px solid #fcd34d; color: #b45309;">
                            <th style="padding: 15px 10px; text-align: left;">Outlet</th>
                            <th style="padding: 15px 10px; text-align: left;">Bahan Baku</th>
                            <th style="padding: 15px 10px; text-align: center;">Stok</th>
                            <th style="padding: 15px 10px; text-align: center;">Stok Minimum</th>
                            <th style="padding: 15px 10px; text-align: right;">Kekurangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kebutuhanOutlet as $butuh)
                        <!-- 🔥 Tambah class "row-kebutuhan" dan data-outlet buat di-filter pakai Javascript -->
                        <tr class="row-kebutuhan" data-outlet="{{ strtolower($butuh->outlet) }}" style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 10px; font-weight: bold; color: #92400e;">{{ ucfirst($butuh->outlet) }}</td>
                            <td style="padding: 12px 10px; color: #1e293b; font-weight: 500;">{{ $butuh->nama_bahan }}</td>
                            <td style="padding: 12px 10px; text-align: center; color: #dc2626; font-weight: bold;">{{ $butuh->stok }} {{ $butuh->satuan }}</td>
                            <td style="padding: 12px 10px; text-align: center; color: #64748b;">{{ $butuh->stok_minimum }} {{ $butuh->satuan }}</td>
                            <td style="padding: 12px 10px; text-align: right; font-weight: bold; color: #047857;">
                                @php 
                                    $kurang = $butuh->stok_minimum - $butuh->stok; 
                                    $saranKirim = $kurang > 0 ? $kurang : 1;
                                @endphp
                                +{{ $saranKirim }} {{ $butuh->satuan }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="padding: 15px 20px; border-top: 1px solid #e5e7eb; text-align: right; background-color: #f8fafc;">
                <button type="button" onclick="document.getElementById('modalKebutuhan').style.display='none'" style="background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT BUAT KLIK DI LUAR MODAL KETUTUP -->
    <script>
        window.onclick = function(event) {
            var modal = document.getElementById('modalKebutuhan');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    @endif
    <!-- 🔥 AKHIR MODAL CONTEKAN -->


    <form action="{{ url('/distribusi') }}" method="POST">
        @csrf

        <!-- 🔥 KEMBALI PAKAI CHECKBOX SESUAI ASLINYA -->
        <div class="form-group">
            <label>Outlet Tujuan</label>
            <div style="display: flex; gap: 30px; align-items: center; margin-top: 8px; margin-bottom: 5px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="outlet[]" value="hasanuddin" style="width: 20px; height: 20px; margin: 0;"> 
                    Hasanuddin
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="outlet[]" value="makmur" style="width: 20px; height: 20px; margin: 0;"> 
                    Makmur
                </label>
            </div>
            <small style="color: #666; font-style: italic;">*Centang keduanya jika ingin mendistribusikan ke dua outlet sekaligus.</small>
        </div>

        {{-- BAHAN BAKU TABEL --}}
        <div class="form-group" style="margin-top: 25px;">
            <label>Bahan Baku yang Akan Didistribusikan</label>
            <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-top: 10px;">
                <table style="width: 100%; border-collapse: collapse;" id="table-bahan">
                    <thead style="background-color: #183f37; color: white;">
                        <tr>
                            <th style="text-align: center; padding: 12px 15px;">Bahan Baku</th>
                            <th style="text-align: center; width: 200px; padding: 12px 15px;">Jumlah</th>
                            <th style="text-align: center; width: 80px; padding: 12px 15px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-bahan">
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px 15px;">
                                <select name="nama_bahan[]" class="select2-bahan" style="width: 100%;" required>
                                    <option value="">-- Cari / Pilih Bahan --</option>
                                    @foreach($bahanTersedia as $bahan)
                                        <option value="{{ $bahan->nama_bahan }}" {{ isset($namaBahanDipilih) && $namaBahanDipilih == $bahan->nama_bahan ? 'selected' : '' }}>
                                            {{ $bahan->nama_bahan }} (Total Sisa: {{ $bahan->total_sisa }} {{ $bahan->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding: 12px 15px;">
                                <input type="number" name="jumlah[]" min="1" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                            </td>
                            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                                <button type="button" class="btn btn-sm remove-row" title="Hapus baris" style="background-color: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-size: 18px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;">
                                    &times;
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <button type="button" class="btn btn-info btn-sm" id="btn-tambah-baris" style="margin-top: 15px; background-color: #183f37; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                + Tambah Bahan Lain
            </button>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" class="form-control" placeholder="Opsional" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-actions" style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn">
                Simpan Distribusi
            </button>
            <a href="{{ route('distribusi.index') }}" class="btn-secondary">
                ← Kembali
            </a>
        </div>

    </form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        function initSelect2() {
            $('.select2-bahan').select2({
                placeholder: "-- Cari / Pilih Bahan --",
                allowClear: true
            });
        }

        initSelect2();

        $('#btn-tambah-baris').click(function() {
            var newRow = `
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 15px;">
                        <select name="nama_bahan[]" class="select2-bahan" style="width: 100%;" required>
                            <option value="">-- Cari / Pilih Bahan --</option>
                            @foreach($bahanTersedia as $bahan)
                                <option value="{{ $bahan->nama_bahan }}">
                                    {{ $bahan->nama_bahan }} (Total Sisa: {{ $bahan->total_sisa }} {{ $bahan->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 12px 15px;">
                        <input type="number" name="jumlah[]" min="1" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    </td>
                    <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                        <button type="button" class="btn btn-sm remove-row" title="Hapus baris" style="background-color: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-size: 18px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
            $('#tbody-bahan').append(newRow);
            initSelect2();
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#tbody-bahan tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus mengirim 1 bahan baku!');
            }
        });

        // 🔥 LOGIKA JAVASCRIPT BUAT FILTER OUTLET DI MODAL 🔥
        $('#filterOutletModal').change(function() {
            var selectedOutlet = $(this).val();
            $('.row-kebutuhan').each(function() {
                var rowOutlet = $(this).data('outlet');
                if (selectedOutlet === 'all' || rowOutlet === selectedOutlet) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

@endsection