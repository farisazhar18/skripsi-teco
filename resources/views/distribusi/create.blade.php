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
                            <th style="padding: 15px 10px; text-align: center;">Pilih</th>
                            <th style="padding: 15px 10px; text-align: left;">Outlet</th>
                            <th style="padding: 15px 10px; text-align: left;">Bahan Baku</th>
                            <th style="padding: 15px 10px; text-align: right;">Kekurangan</th>
                            <th style="padding: 15px 10px; text-align: center;">Stok Gudang</th>
                            <th style="padding: 15px 10px; text-align: center;">Kirim Berapa?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kebutuhanOutlet as $butuh)
                        @php 
                            $kurang = $butuh->stok_minimum - $butuh->stok; 
                            $saranKirim = $kurang > 0 ? $kurang : 1;
                            
                            $stokGudang = 0;
                            foreach($bahanTersedia as $tersedia) {
                                if (strtolower($tersedia->nama_bahan) == strtolower($butuh->nama_bahan)) {
                                    $stokGudang = $tersedia->total_sisa;
                                    break;
                                }
                            }

                            $isOutletHabis = $butuh->stok <= 0;
                            $rowBg = $isOutletHabis ? '#fef2f2' : '#fefce8'; // Merah muda jika habis, kuning muda jika menipis
                            $statusOutlet = $isOutletHabis ? '🚨 HABIS' : '⚠️ MENIPIS';
                            $statusColor = $isOutletHabis ? '#dc2626' : '#b45309';
                        @endphp
                        <!-- 🔥 Tambah class "row-kebutuhan" dan data-outlet buat di-filter pakai Javascript -->
                        <tr class="row-kebutuhan" data-outlet="{{ strtolower($butuh->outlet) }}" data-bahan="{{ $butuh->nama_bahan }}" data-stok="{{ $stokGudang }}" style="border-bottom: 1px solid #e5e7eb; background-color: {{ $rowBg }}; {{ $stokGudang <= 0 ? 'opacity: 0.75;' : '' }}">
                            <td style="padding: 12px 10px; text-align: center;">
                                @if($stokGudang <= 0)
                                    <span style="font-size: 10px; font-weight: bold; color: #6b7280; border: 1px solid #d1d5db; padding: 2px 4px; border-radius: 4px; background: #f3f4f6;">GUDANG KOSONG</span>
                                @else
                                    <input type="checkbox" class="chk-modal-bahan" style="transform: scale(1.3); cursor: pointer;">
                                @endif
                            </td>
                            <td style="padding: 12px 10px; font-weight: bold; color: #92400e;">
                                {{ ucfirst($butuh->outlet) }}
                            </td>
                            <td style="padding: 12px 10px; color: #1e293b; font-weight: 500;">
                                {{ $butuh->nama_bahan }}<br>
                                <span style="font-size: 10px; font-weight: bold; color: {{ $statusColor }};">{{ $statusOutlet }}</span>
                            </td>
                            <td style="padding: 12px 10px; text-align: right;">
                                <div style="font-size: 11px; color: #64748b; margin-bottom: 3px;">Stok Outlet: {{ $butuh->stok }}/{{ $butuh->stok_minimum }}</div>
                                <div style="font-weight: bold; color: #dc2626;">+{{ $saranKirim }} {{ $butuh->satuan }}</div>
                            </td>
                            <td style="padding: 12px 10px; text-align: center; font-weight: bold; color: {{ $stokGudang >= $saranKirim ? '#059669' : '#dc2626' }};">
                                {{ $stokGudang }} {{ $butuh->satuan }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center;">
                                @if($stokGudang <= 0)
                                    <span style="color: #6b7280; font-size: 12px; font-weight: bold;">-</span>
                                @else
                                    <input type="number" class="input-modal-jumlah" value="{{ $saranKirim > $stokGudang ? $stokGudang : $saranKirim }}" min="1" max="{{ $stokGudang }}" style="width: 70px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 5px;">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="padding: 15px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; background-color: #f8fafc;">
                <button type="button" id="btnIntegrate" style="background-color: #183f37; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Pilih & Terapkan ke Form Utama</button>
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
                <label class="label-outlet-utama" style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="outlet[]" value="hasanuddin" style="width: 20px; height: 20px; margin: 0;"> 
                    Hasanuddin
                </label>
                <label class="label-outlet-utama" style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="outlet[]" value="makmur" style="width: 20px; height: 20px; margin: 0;"> 
                    Makmur
                </label>
            </div>
            <small style="color: #666; font-style: italic;" id="hint-outlet-normal">*Centang keduanya jika ingin mendistribusikan ke dua outlet sekaligus dengan jumlah bahan baku yang persis sama.</small>
            <div id="hint-outlet-terkunci" style="display: none; align-items: center; gap: 10px; margin-top: 5px;">
                <small style="color: #dc2626; font-weight: bold; font-style: italic;">🔒 Pilihan outlet dikunci otomatis karena Anda mengambil data dari Daftar Kebutuhan Outlet.</small>
                <button type="button" id="btnResetIntegrasi" style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 4px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: bold;">❌ Batalkan Integrasi</button>
            </div>
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

        // 🔥 LOGIKA CEGAH PILIH BEDA OUTLET BERSAMAAN 🔥
        $('.chk-modal-bahan').change(function() {
            if ($(this).is(':checked')) {
                var currentOutlet = $(this).closest('tr').data('outlet');
                
                var hasDifferentOutlet = false;
                $('.chk-modal-bahan:checked').each(function() {
                    if ($(this).closest('tr').data('outlet') !== currentOutlet) {
                        hasDifferentOutlet = true;
                    }
                });

                if (hasDifferentOutlet) {
                    alert('Maaf, Anda hanya bisa memproses distribusi untuk SATU outlet dalam satu formulir pengiriman agar sistem tidak menduplikasi jumlah bahan baku. Silakan pilih barang untuk satu outlet saja, lalu selesaikan.');
                    $(this).prop('checked', false);
                }
            }
        });

        // 🔥 LOGIKA INTEGRASI MODAL KE FORM UTAMA 🔥
        $('#btnIntegrate').click(function() {
            var selectedOutlets = new Set();
            var itemsToAdd = [];
            var hasError = false;

            $('.chk-modal-bahan:checked').each(function() {
                var row = $(this).closest('tr');
                var outlet = row.data('outlet'); 
                var bahan = row.data('bahan');
                var stokGudang = parseFloat(row.data('stok'));
                var jumlah = parseFloat(row.find('.input-modal-jumlah').val());

                if (isNaN(jumlah) || jumlah <= 0) {
                    alert('Error: Jumlah pengiriman untuk "' + bahan + '" harus lebih dari 0.');
                    hasError = true;
                    return false; // break loop
                }

                if (stokGudang <= 0) {
                    alert('Error: Stok gudang untuk bahan "' + bahan + '" sedang kosong. Tidak dapat didistribusikan.');
                    hasError = true;
                    return false;
                }

                if (jumlah > stokGudang) {
                    alert('Error: Jumlah yang dikirim untuk "' + bahan + '" (' + jumlah + ') melebihi stok gudang (' + stokGudang + ').');
                    hasError = true;
                    return false;
                }

                selectedOutlets.add(outlet);
                itemsToAdd.push({ bahan: bahan, jumlah: jumlah });
            });

            if (hasError) return;

            if (itemsToAdd.length === 0) {
                alert('Pilih minimal 1 bahan baku untuk diintegrasikan.');
                return;
            }

            if (selectedOutlets.size > 1) {
                alert('Error: Sistem mendeteksi barang dari outlet berbeda. Harap hanya memproses 1 outlet pada satu waktu.');
                return;
            }

            // Centang outlet di form utama sesuai pilihan di modal
            $('input[name="outlet[]"]').prop('checked', false);
            selectedOutlets.forEach(function(outlet) {
                $('input[name="outlet[]"][value="' + outlet + '"]').prop('checked', true);
            });

            // 🔥 KUNCI CHECKBOX OUTLET UTAMA AGAR TIDAK BISA DIUBAH 🔥
            $('.label-outlet-utama').css({
                'pointer-events': 'none',
                'opacity': '0.6'
            });
            $('#hint-outlet-normal').hide();
            $('#hint-outlet-terkunci').css('display', 'flex');

            // Kosongkan tabel utama
            $('#tbody-bahan').empty();

            // Masukkan data terpilih ke tabel utama
            itemsToAdd.forEach(function(item) {
                var newRow = `
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 15px;">
                            <select name="nama_bahan[]" class="select2-bahan" style="width: 100%;" required>
                                <option value="">-- Cari / Pilih Bahan --</option>
                                @foreach($bahanTersedia as $bahan)
                                    <option value="{{ $bahan->nama_bahan }}" ${item.bahan === '{{ $bahan->nama_bahan }}' ? 'selected' : ''}>
                                        {{ $bahan->nama_bahan }} (Total Sisa: {{ $bahan->total_sisa }} {{ $bahan->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 12px 15px;">
                            <input type="number" name="jumlah[]" min="1" value="${item.jumlah}" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                        </td>
                        <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                            <button type="button" class="btn btn-sm remove-row" title="Hapus baris" style="background-color: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-size: 18px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;">
                                &times;
                            </button>
                        </td>
                    </tr>
                `;
                $('#tbody-bahan').append(newRow);
            });

            // Re-inisialisasi Select2 untuk baris yang baru ditambahkan
            initSelect2();
            
            // Tutup modal otomatis setelah apply
            document.getElementById('modalKebutuhan').style.display = 'none';
        });

        // 🔥 LOGIKA RESET / BATALKAN INTEGRASI 🔥
        $('#btnResetIntegrasi').click(function() {
            // Buka kunci checkbox outlet
            $('.label-outlet-utama').css({
                'pointer-events': 'auto',
                'opacity': '1'
            });
            $('input[name="outlet[]"]').prop('checked', false);
            
            // Sembunyikan tombol reset dan tampilkan hint normal
            $('#hint-outlet-terkunci').hide();
            $('#hint-outlet-normal').show();

            // Uncheck semua pilihan di modal
            $('.chk-modal-bahan').prop('checked', false);

            // Reset tabel bahan ke 1 baris kosong
            $('#tbody-bahan').empty();
            $('#btn-tambah-baris').click(); // Tambahkan 1 baris kosong bawaan
        });
    });
</script>

@endsection