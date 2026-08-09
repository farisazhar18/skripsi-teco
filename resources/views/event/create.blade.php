@extends('layouts.pos')

@section('content')
<h1 class="page-title">Buat Perencanaan Event Baru</h1>

<div class="form-card" style="margin: 0 auto; padding: 32px; max-width: 1000px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(24,63,55,0.10);">
    
    <!-- Tampilkan Error dari Backend kalau ada -->
    @if($errors->any())
        <div style="background: #ffe1df; color: #c62828; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('event.store') }}" method="POST">
        @csrf

        <!-- INFORMASI DASAR EVENT -->
        <!-- Gua ubah grid-nya jadi 1fr 1fr 1fr biar 3 kotak ini jejeran rapi bang -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="form-group">
                <label>Nama Event</label>
                <input type="text" name="nama_event" placeholder="Contoh: Turnamen ML Simicup 2026" required style="width: 100%; max-width: 100%;">
            </div>
            <div class="form-group">
                <label>Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal_pelaksanaan" required style="width: 100%; max-width: 100%;">
            </div>
            
            <!-- INI TAMBAHAN DROPDOWN OUTLETNYA -->
            <div class="form-group">
                <label>Lokasi Pelaksanaan</label>
                <select name="outlet" class="form-control" required style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: white;">
                    <option value="" disabled selected>-- Pilih Lokasi Event --</option>
                    <option value="hasanuddin">Hasanuddin</option>
                    <option value="makmur">Makmur</option>
                    <option value="booth">Booth (Luar Outlet)</option>
                </select>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 1: INPUT PESANAN PAKET (BUNDLING)   -->
        <!-- ========================================== -->
        <div style="background: #fdfaf5; border: 1px solid #e5e0d8; border-radius: 15px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0; color: #183f37;">🎁 Daftar Pesanan Paket Event</h3>
                    <small style="color: #6b6256;">Pilih paket bundling dan tentukan varian minumannya</small>
                </div>
                <button type="button" class="btn" onclick="tambahBarisPaket()" style="background: #d97706; padding: 8px 12px; font-size: 13px;">➕ Tambah Paket</button>
            </div>

            <table style="width: 100%; margin-bottom: 10px;" id="tabel-paket">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 25%;">Pilih Paket</th>
                        <th style="text-align: left; width: 25%;">Pilih Minuman</th>
                        <th style="text-align: left; width: 15%;">Ukuran</th>
                        <th style="text-align: left; width: 15%;">Tipe</th>
                        <th style="text-align: left; width: 10%;">Pax</th>
                        <th style="text-align: center; width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-paket">
                    <!-- Default kosong, user klik tambah jika mau pesan paket -->
                </tbody>
            </table>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 2: INPUT PESANAN PRODUK SATUAN      -->
        <!-- ========================================== -->
        <div style="background: #f4fbf7; border: 1px solid #d1fae5; border-radius: 15px; padding: 20px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0; color: #183f37;">☕ Daftar Pesanan Produk Satuan</h3>
                    <small style="color: #6b6256;">Pesan produk tambahan di luar paket</small>
                </div>
                <button type="button" class="btn" onclick="tambahBarisSatuan()" style="background: #0f7a3a; padding: 8px 12px; font-size: 13px;">➕ Tambah Produk</button>
            </div>

            <table style="width: 100%; margin-bottom: 10px;" id="tabel-satuan">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 35%;">Pilih Produk</th>
                        <th style="text-align: left; width: 20%;">Ukuran</th>
                        <th style="text-align: left; width: 20%;">Tipe</th>
                        <th style="text-align: left; width: 15%;">Qty</th>
                        <th style="text-align: center; width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-satuan">
                    <!-- Default 1 baris untuk satuan -->
                    <tr>
                        <td style="padding: 10px 10px 10px 0; border-right: none;">
                            <select name="produk_id[]" class="searchable-select produk-select" onchange="updateDropdownPilihan(this, this.value)" placeholder="-- Ketik Produk --" style="width: 100%; max-width: 100%;">
                                <option value="">-- Ketik/Pilih Produk --</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 10px 10px 10px 0; border-right: none;">
                            <select name="ukuran[]" class="select-ukuran" style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: #faf7f0; color: #183f37;">
                                <option value="">-- Pilih --</option>
                            </select>
                        </td>
                        <td style="padding: 10px 10px 10px 0; border-right: none;">
                            <select name="tipe[]" class="select-tipe" style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: #faf7f0; color: #183f37;">
                                <option value="">-- Pilih --</option>
                            </select>
                        </td>
                        <td style="padding: 10px; border-right: none;">
                            <input type="number" name="jumlah_pesanan[]" min="1" placeholder="0" style="width: 100%; max-width: 100%;">
                        </td>
                        <td style="padding: 10px 0 10px 10px; text-align: center; border-right: none;">
                            <button type="button" class="btn-danger" style="padding: 10px; border-radius: 8px;" onclick="hapusBaris(this)">🗑️</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-actions" style="justify-content: flex-end; padding-top: 20px; border-top: 1px solid #e5e0d8;">
            <a href="{{ route('event.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn" style="padding: 12px 24px; font-size: 16px;">💾 Simpan & Hitung Bahan</button>
        </div>
    </form>
</div>

<script>
    // ==========================================
    // 1. DATA ATURAN PRODUK DARI BACKEND
    // ==========================================
    const aturanProduk = {
        @foreach($produks as $produk)
        "{{ $produk->id }}": {
            kategori: "{{ strtolower($produk->kategori) }}",
            tersedia_hot: {{ $produk->tersedia_hot ? 'true' : 'false' }},
            tersedia_ice: {{ $produk->tersedia_ice ? 'true' : 'false' }}
        },
        @endforeach
    };

    // ==========================================
    // 2. FUNGSI FILTER DROPDOWN OTOMATIS
    // ==========================================
    function updateDropdownPilihan(selectElement, produkId) {
        const tr = selectElement.closest('tr');
        if (!tr) return; // Mencegah error
        
        const selectUkuran = tr.querySelector('.select-ukuran');
        const selectTipe = tr.querySelector('.select-tipe');

        if (!selectUkuran || !selectTipe) return; // Abaikan jika bukan di tabel satuan

        selectUkuran.innerHTML = '';
        selectTipe.innerHTML = '';

        if (!produkId || !aturanProduk[produkId]) {
            selectUkuran.innerHTML = '<option value="">-- Pilih --</option>';
            selectTipe.innerHTML = '<option value="">-- Pilih --</option>';
            selectUkuran.style.background = '#faf7f0';
            selectTipe.style.background = '#faf7f0';
            return;
        }

        const data = aturanProduk[produkId];
        selectUkuran.style.background = 'white';
        selectTipe.style.background = 'white';

        if (data.kategori === 'makanan' || data.kategori === 'food' || data.kategori === 'snack') {
            selectUkuran.innerHTML = '<option value="standar">Standar (Food)</option>';
            selectTipe.innerHTML = '<option value="food">Food</option>';
        } else {
            selectUkuran.innerHTML = `
                <option value="reguler">Reguler</option>
                <option value="large">Large</option>
            `;
            
            let htmlTipe = '';
            if (data.tersedia_hot) htmlTipe += '<option value="hot">Hot</option>';
            if (data.tersedia_ice) htmlTipe += '<option value="ice">Ice</option>';
            if (htmlTipe === '') htmlTipe = '<option value="">-- Kosong --</option>';
            
            selectTipe.innerHTML = htmlTipe;
        }
    }

    // ==========================================
    // 3. FUNGSI KHUSUS TEMBAK PLUGIN TOMSELECT
    // ==========================================
    function attachTomSelect(el) {
        if (el.tomselect) return; // Cegah double install

        new TomSelect(el, {
            create: false,
            sortField: { field: "text", direction: "asc" },
            onChange: function(value) {
                // Biar otomatis jalankan filter kalo yang diklik itu produk minuman/makanan
                if (el.classList.contains('produk-select')) {
                    updateDropdownPilihan(el, value);
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Tembak plugin ke form default pas halaman pertama kali dibuka
        document.querySelectorAll('.searchable-select').forEach(attachTomSelect);
    });

    // ==========================================
    // 4. FUNGSI TAMBAH BARIS SATUAN & PAKET
    // ==========================================
    function hapusBaris(btn) {
        const tr = btn.closest('tr');
        tr.remove();
    }

    function tambahBarisSatuan() {
        const tbody = document.getElementById('body-satuan');
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <select name="produk_id[]" class="searchable-select produk-select" placeholder="-- Ketik Produk --" style="width: 100%; max-width: 100%;">
                    <option value="">-- Ketik/Pilih Produk --</option>
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <select name="ukuran[]" class="select-ukuran" style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: #faf7f0; color: #183f37;">
                    <option value="">-- Pilih --</option>
                </select>
            </td>
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <select name="tipe[]" class="select-tipe" style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #c9bca8; background: #faf7f0; color: #183f37;">
                    <option value="">-- Pilih --</option>
                </select>
            </td>
            <td style="padding: 10px; border-right: none;">
                <input type="number" name="jumlah_pesanan[]" min="1" placeholder="0" style="width: 100%; max-width: 100%;">
            </td>
            <td style="padding: 10px 0 10px 10px; text-align: center; border-right: none;">
                <button type="button" class="btn-danger" style="padding: 10px; border-radius: 8px;" onclick="hapusBaris(this)">🗑️</button>
            </td>
        `;
        tbody.appendChild(tr);
        
        // 🔥 Langsung tembak plugin HANYA ke baris baru ini 🔥
        tr.querySelectorAll('.searchable-select').forEach(attachTomSelect);
    }

    function tambahBarisPaket() {
        const tbody = document.getElementById('body-paket');
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <select name="paket_id[]" class="searchable-select" placeholder="-- Pilih Paket --" required style="width: 100%; max-width: 100%;">
                    <option value="">-- Pilih Paket --</option>
                    @if(isset($pakets))
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}">{{ $paket->nama_paket }} ({{ $paket->makanan->nama_produk ?? 'Menu' }})</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <select name="minuman_id[]" class="searchable-select produk-select" placeholder="-- Pilih Minuman --" required style="width: 100%; max-width: 100%;">
                    <option value="">-- Pilih Minuman --</option>
                    @if(isset($minumanPakets))
                        @foreach($minumanPakets as $minuman)
                            <option value="{{ $minuman->id }}">{{ $minuman->nama_produk }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <input type="text" value="Reguler" readonly style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #d1d5db; background: #f3f4f6; color: #6b7280; cursor: not-allowed; text-align: center; font-weight: 600; outline: none;">
                <input type="hidden" name="minuman_ukuran[]" value="reguler">
            </td>
            <td style="padding: 10px 10px 10px 0; border-right: none;">
                <input type="text" value="Ice" readonly style="width: 100%; padding: 12px; border-radius: 11px; border: 1px solid #d1d5db; background: #f3f4f6; color: #6b7280; cursor: not-allowed; text-align: center; font-weight: 600; outline: none;">
                <input type="hidden" name="minuman_tipe[]" value="ice">
            </td>
            <td style="padding: 10px; border-right: none;">
                <input type="number" name="jumlah_paket[]" min="1" placeholder="0" required style="width: 100%; max-width: 100%;">
            </td>
            <td style="padding: 10px 0 10px 10px; text-align: center; border-right: none;">
                <button type="button" class="btn-danger" style="padding: 10px; border-radius: 8px;" onclick="hapusBaris(this)">🗑️</button>
            </td>
        `;
        tbody.appendChild(tr);
        
        // 🔥 Langsung tembak plugin HANYA ke baris baru ini 🔥
        tr.querySelectorAll('.searchable-select').forEach(attachTomSelect);
    }
</script>
@endsection