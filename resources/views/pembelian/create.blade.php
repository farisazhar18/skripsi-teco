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

    <!-- 🔥 TOMBOL TRIGGER MODAL CEK STOK GUDANG -->
    @if(isset($stokGudang) && $stokGudang->count() > 0)
    <div style="margin-bottom: 25px;">
        <button type="button" onclick="document.getElementById('modalStokGudang').style.display='flex'" style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; font-size: 15px; transition: 0.2s;">
            📦 Cek Stok Gudang
            @php
                $habisCount = $stokGudang->where('stok', '<=', 0)->count();
                $menipisCount = $stokGudang->filter(function($b){ return $b->stok > 0 && $b->stok <= $b->stok_minimum; })->count();
            @endphp
            @if($habisCount > 0)
                <span style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 50px; font-size: 12px;">{{ $habisCount }} Habis</span>
            @endif
            @if($menipisCount > 0)
                <span style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 50px; font-size: 12px;">{{ $menipisCount }} Menipis</span>
            @endif
        </button>
    </div>

    <!-- 🔥 MODAL POPUP CEK STOK GUDANG -->
    <div id="modalStokGudang" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(3px);">
        <div style="background-color: #fff; width: 90%; max-width: 800px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; max-height: 85vh;">
            
            <!-- HEADER MODAL & FILTER -->
            <div style="background-color: #eff6ff; padding: 20px; border-bottom: 1px solid #bfdbfe; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1;">
                    <h3 style="color: #1d4ed8; margin: 0 0 8px 0; font-size: 18px;">📦 Stok Bahan Baku Gudang</h3>
                    <p style="color: #1e40af; font-size: 14px; margin: 0;">Cek bahan baku yang perlu diajukan pengadaan. Bahan yang habis dan menipis ditampilkan di atas.</p>
                </div>
                
                <!-- DROPDOWN FILTER -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <select id="filterOutletStok" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #bfdbfe; outline: none; background: white; color: #1d4ed8; font-weight: bold; cursor: pointer;">
                        <option value="all">Semua Outlet</option>
                        <option value="hasanuddin">Hasanuddin</option>
                        <option value="makmur">Makmur</option>
                    </select>

                    <select id="filterStatusStok" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #bfdbfe; outline: none; background: white; color: #1d4ed8; font-weight: bold; cursor: pointer;">
                        <option value="all">Semua Status</option>
                        <option value="habis">🚨 Habis</option>
                        <option value="menipis">⚠️ Menipis</option>
                        <option value="aman">✅ Aman</option>
                    </select>
                    
                    <select id="filterKategoriStok" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #bfdbfe; outline: none; background: white; color: #1d4ed8; font-weight: bold; cursor: pointer;">
                        <option value="all">Semua Kategori</option>
                        @php $kategoris = $stokGudang->pluck('kategori')->unique()->filter()->sort(); @endphp
                        @foreach($kategoris as $kat)
                            <option value="{{ strtolower($kat) }}">{{ ucfirst($kat) }}</option>
                        @endforeach
                    </select>
                    
                    <button type="button" onclick="document.getElementById('modalStokGudang').style.display='none'" style="background: none; border: none; font-size: 28px; color: #1d4ed8; cursor: pointer; line-height: 1; padding: 0; margin-left: 10px;">&times;</button>
                </div>
            </div>

            <!-- SEARCH BOX -->
            <div style="padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <input type="text" id="searchStokGudang" placeholder="🔍 Cari nama bahan baku..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 14px;">
            </div>

            <!-- ISI MODAL (TABEL SCROLLABLE) -->
            <div style="overflow-y: auto; padding: 0 20px 20px 20px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead style="position: sticky; top: 0; background-color: #fff; z-index: 1;">
                        <tr style="border-bottom: 2px solid #bfdbfe; color: #1d4ed8;">
                            <th style="padding: 15px 10px; text-align: center;">Pilih</th>
                            <th style="padding: 15px 10px; text-align: left;">Outlet</th>
                            <th style="padding: 15px 10px; text-align: left;">Bahan Baku</th>
                            <th style="padding: 15px 10px; text-align: center;">Kategori</th>
                            <th style="padding: 15px 10px; text-align: center;">Stok Sekarang</th>
                            <th style="padding: 15px 10px; text-align: center;">Stok Minimum</th>
                            <th style="padding: 15px 10px; text-align: center;">Status</th>
                            <th style="padding: 15px 10px; text-align: center;">Beli Berapa?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stokGudang as $bahan)
                        @php
                            $isHabis = $bahan->stok <= 0;
                            $isMenipis = !$isHabis && $bahan->stok <= $bahan->stok_minimum;
                            $isAman = !$isHabis && !$isMenipis;
                            
                            if ($isHabis) {
                                $rowBg = '#fef2f2';
                                $statusLabel = '🚨 HABIS';
                                $statusColor = '#dc2626';
                                $statusKey = 'habis';
                            } elseif ($isMenipis) {
                                $rowBg = '#fffbeb';
                                $statusLabel = '⚠️ MENIPIS';
                                $statusColor = '#d97706';
                                $statusKey = 'menipis';
                            } else {
                                $rowBg = '#f0fdf4';
                                $statusLabel = '✅ Aman';
                                $statusColor = '#16a34a';
                                $statusKey = 'aman';
                            }
                            
                            $saranBeli = $bahan->stok_minimum > $bahan->stok ? ($bahan->stok_minimum - $bahan->stok) * 2 : 1;
                            if($saranBeli <= 0) $saranBeli = 1;
                        @endphp
                        <tr class="row-stok-gudang" data-status="{{ $statusKey }}" data-outlet="{{ strtolower($bahan->outlet ?? '') }}" data-kategori="{{ strtolower($bahan->kategori ?? '') }}" data-nama="{{ strtolower($bahan->nama_bahan) }}" data-id="{{ $bahan->id }}" style="border-bottom: 1px solid #e5e7eb; background-color: {{ $rowBg }};">
                            <td style="padding: 12px 10px; text-align: center;">
                                <input type="checkbox" class="chk-modal-bahan" style="transform: scale(1.3); cursor: pointer;">
                            </td>
                            <td style="padding: 12px 10px; font-weight: 600; color: #92400e;">
                                {{ ucfirst($bahan->outlet ?? '-') }}
                            </td>
                            <td style="padding: 12px 10px; font-weight: 600; color: #1e293b;">
                                {{ $bahan->nama_bahan }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center; color: #64748b; font-size: 13px;">
                                {{ ucfirst($bahan->kategori ?? '-') }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center; font-weight: bold; color: {{ $isHabis ? '#dc2626' : ($isMenipis ? '#d97706' : '#16a34a') }};">
                                {{ $bahan->stok }} {{ $bahan->satuan }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center; color: #64748b;">
                                {{ $bahan->stok_minimum }} {{ $bahan->satuan }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center;">
                                <span style="font-size: 12px; font-weight: bold; color: {{ $statusColor }}; padding: 3px 8px; border-radius: 50px; background: {{ $isHabis ? '#fee2e2' : ($isMenipis ? '#fef3c7' : '#dcfce7') }};">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="padding: 12px 10px; text-align: center;">
                                <input type="number" class="input-modal-jumlah" value="{{ $saranBeli }}" min="1" style="width: 70px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 5px;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="padding: 15px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; background-color: #f8fafc;">
                <button type="button" id="btnIntegrate" style="background-color: #1d4ed8; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Pilih & Terapkan ke Form Utama</button>
                <button type="button" onclick="document.getElementById('modalStokGudang').style.display='none'" style="background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT KLIK DI LUAR MODAL KETUTUP -->
    <script>
        window.addEventListener('click', function(event) {
            var modal = document.getElementById('modalStokGudang');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    </script>
    @endif
    <!-- 🔥 AKHIR MODAL CEK STOK GUDANG -->

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
                                    <option value="{{ $item->id }}" data-satuan="{{ strtolower($item->satuan) }}" data-nama="{{ strtolower($item->nama_bahan) }}">
                                        {{ $item->nama_bahan }} (Stok: {{ $item->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 12px 15px;">
                            <input type="number" name="jumlah[]" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: center;">
                        </td>
                        <td style="padding: 12px 15px;">
                            <select name="satuan_beli[]" class="satuan-beli" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">-- Pilih Bahan Baku Dulu --</option>
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
                                <option value="{{ $item->id }}" data-satuan="{{ strtolower($item->satuan) }}" data-nama="{{ strtolower($item->nama_bahan) }}">
                                    {{ $item->nama_bahan }} (Stok: {{ $item->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 12px 15px;">
                        <input type="number" name="jumlah[]" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: center;">
                    </td>
                    <td style="padding: 12px 15px;">
                        <select name="satuan_beli[]" class="satuan-beli" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="">-- Pilih Bahan Baku Dulu --</option>
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

        // Event Listener untuk update satuan beli
        $(document).on('change', '.select2-bahan', function() {
            var selectedOption = $(this).find(':selected');
            var satuan = selectedOption.data('satuan');
            var nama = selectedOption.data('nama');
            var satuanBeliSelect = $(this).closest('tr').find('.satuan-beli');
            
            satuanBeliSelect.empty();
            
            if (!satuan) {
                satuanBeliSelect.append('<option value="">-- Pilih Bahan Baku Dulu --</option>');
                return;
            }

            satuanBeliSelect.append('<option value="">-- Satuan --</option>');

            if (nama && nama.includes('soda water')) {
                satuanBeliSelect.append('<option value="botol">botol</option>');
                satuanBeliSelect.append('<option value="ml">ml</option>');
                satuanBeliSelect.append('<option value="liter">liter</option>');
            } else if (satuan === 'ml') {
                satuanBeliSelect.append('<option value="ml">ml</option>');
                satuanBeliSelect.append('<option value="liter">liter</option>');
            } else if (satuan === 'gram') {
                satuanBeliSelect.append('<option value="gram">gram</option>');
                satuanBeliSelect.append('<option value="kg">kg</option>');
            } else if (satuan === 'pcs') {
                satuanBeliSelect.append('<option value="pcs">pcs</option>');
            } else {
                satuanBeliSelect.append('<option value="' + satuan + '">' + satuan + '</option>');
            }
        });

        // 🔥 LOGIKA INTEGRASI MODAL KE FORM UTAMA 🔥
        $('#btnIntegrate').click(function() {
            var itemsToAdd = [];
            var hasError = false;

            $('.chk-modal-bahan:checked').each(function() {
                var row = $(this).closest('tr');
                var id = row.data('id'); 
                var nama = row.data('nama');
                var jumlah = parseFloat(row.find('.input-modal-jumlah').val());

                if (isNaN(jumlah) || jumlah <= 0) {
                    alert('Error: Jumlah pembelian untuk "' + nama + '" harus lebih dari 0.');
                    hasError = true;
                    return false; // break loop
                }

                itemsToAdd.push({ id: id, jumlah: jumlah });
            });

            if (hasError) return;

            if (itemsToAdd.length === 0) {
                alert('Pilih minimal 1 bahan baku untuk diintegrasikan.');
                return;
            }

            // Kosongkan tabel utama
            $('#tbody-pengajuan').empty();

            // Masukkan data terpilih ke tabel utama
            itemsToAdd.forEach(function(item) {
                var newRow = `
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 15px;">
                            <select name="bahan_baku_id[]" class="select2-bahan" style="width: 100%;" required>
                                <option value="">-- Pilih Bahan Baku --</option>
                                @foreach($bahanBaku as $bahanItem)
                                    <option value="{{ $bahanItem->id }}" data-satuan="{{ strtolower($bahanItem->satuan) }}" data-nama="{{ strtolower($bahanItem->nama_bahan) }}" ${item.nama === '{{ strtolower($bahanItem->nama_bahan) }}' ? 'selected' : ''}>
                                        {{ $bahanItem->nama_bahan }} (Stok: {{ $bahanItem->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 12px 15px;">
                            <input type="number" name="jumlah[]" min="1" value="${item.jumlah}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: center;">
                        </td>
                        <td style="padding: 12px 15px;">
                            <select name="satuan_beli[]" class="satuan-beli" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">-- Pilih Bahan Baku Dulu --</option>
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
            });

            // Re-inisialisasi Select2 dan trigger change untuk ngisi dropdown satuan beli
            initSelect2();
            $('.select2-bahan').trigger('change');
            
            // Tutup modal otomatis setelah apply
            document.getElementById('modalStokGudang').style.display = 'none';
        });

        // 🔥 FILTER & SEARCH STOK GUDANG MODAL
        function filterStokGudang() {
            var outlet = $('#filterOutletStok').val();
            var status = $('#filterStatusStok').val();
            var kategori = $('#filterKategoriStok').val();
            var search = $('#searchStokGudang').val().toLowerCase();

            $('.row-stok-gudang').each(function() {
                var rowOutlet = $(this).data('outlet');
                var rowStatus = $(this).data('status');
                var rowKategori = $(this).data('kategori');
                var rowNama = $(this).data('nama');

                var matchOutlet = (outlet === 'all' || rowOutlet === outlet);
                var matchStatus = (status === 'all' || rowStatus === status);
                var matchKategori = (kategori === 'all' || rowKategori === kategori);
                var matchSearch = (search === '' || rowNama.includes(search));

                if (matchOutlet && matchStatus && matchKategori && matchSearch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $('#filterOutletStok, #filterStatusStok, #filterKategoriStok').change(filterStokGudang);
        $('#searchStokGudang').on('input', filterStokGudang);

    });
</script>

@endsection