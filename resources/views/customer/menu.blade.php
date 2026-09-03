<!DOCTYPE html>
<html lang="id">
<head>
    <title>Terminal Coffee - Menu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-terminal.png') }}">

    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background: #f8f6f2; /* Warna background lebih soft */
            color: #183f37;
        }

        /* --- HEADER & STICKY NAV --- */
        .header-wrapper {
            position: sticky;
            top: 0;
            z-index: 990;
            background: #f8f6f2;
        }

        .top-bar {
            background: #183f37; 
            padding: 12px 18px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: #efe6d8;
        }

        .top-bar-title {
            font-weight: 700; 
            font-size: 18px;
            letter-spacing: 0.5px;
        }

        .top-bar-actions {
            display: flex; 
            gap: 10px; 
            align-items: center;
        }

        .action-btn {
            color: #183f37; 
            background: #efe6d8;
            text-decoration: none; 
            font-size: 13px; 
            padding: 6px 12px; 
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .sticky-category {
            display: flex;
            gap: 12px;
            padding: 15px 18px;
            overflow-x: auto;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border-bottom: 1px solid #eae5dc;
        }
        
        /* Sembunyikan scrollbar di menu kategori */
        .sticky-category::-webkit-scrollbar { display: none; }

        .nav-pill {
            background: white;
            color: #6b6256;
            padding: 8px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #dcd3c6;
            transition: all 0.3s ease;
        }

        .nav-pill.active {
            background: #183f37;
            color: #efe6d8;
            border-color: #183f37;
        }

        /* --- CONTAINER UTAMA --- */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 24px 18px 110px;
        }

        .store-header {
            text-align: center;
            margin-bottom: 24px;
            margin-top: 10px;
        }

        .store-header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .store-header p { color: #5d6b66; margin-top: 5px; font-size: 15px; }

        .menu-category-wrapper {
            scroll-margin-top: 130px; /* Jarak aman biar judul gak ketutup header pas auto-scroll */
            margin-top: 35px;
        }

        .menu-section h2 {
            margin: 0 0 18px;
            font-size: 24px;
            color: #183f37;
            font-weight: 700;
            border-left: 4px solid #e67e22;
            padding-left: 10px;
        }

        /* --- GRID & CARDS (DESKTOP) --- */
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: stretch;
        }

        .product-card {
            background: white;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 8px 20px rgba(24,63,55,0.06);
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            height: 150px;
            border-radius: 14px;
            background: #e8e2d8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8a8073;
            font-weight: 600;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .product-card h3 {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 700;
            color: #183f37;
        }

        .product-desc {
            color: #7b8581;
            font-size: 12px;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Maksimal 2 baris di desktop */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .category {
            color: #8a8073;
            font-size: 13px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .price {
            font-weight: 700;
            margin-bottom: 12px;
            color: #e67e22;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            background: #183f37;
            color: #efe6d8;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn:hover { background: #2e5a4f; }

        /* --- ALERT BOXES --- */
        .alert-box {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-warning { background: #fff4e5; color: #b56a00; border: 1px solid #fce3b8; }
        .alert-success { background: #e5f5ec; color: #0f7a3a; border: 1px solid #c3e8d1; }
        .alert-error { background: #ffe1df; color: #c62828; border: 1px solid #fbc9c7; }

        /* --- MODAL --- */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .modal.active { display: flex; }
        .modal-box {
            background: white;
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-box h2 { margin-top: 0; font-size: 22px; color: #183f37; }
        input, select {
            width: 100%; padding: 12px; border-radius: 10px;
            border: 1px solid #dcd3c6; margin-bottom: 14px; font-size: 14px;
        }
        .close-btn { background: #efe6d8; color: #183f37; margin-top: 5px; }

        /* --- STEP GUIDE (CARA PESAN) --- */
        .step-guide { display: flex; flex-direction: column; gap: 15px; }
        .step-item { display: flex; gap: 12px; align-items: flex-start; background: #f8f6f2; padding: 12px; border-radius: 12px; border: 1px solid #eae5dc; }
        .step-icon {
            background: #183f37; color: #efe6d8; width: 28px; height: 28px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; flex-shrink: 0; font-size: 13px;
        }
        .step-text strong { display: block; color: #183f37; font-size: 14px; margin-bottom: 2px; }
        .step-text p { margin: 0; font-size: 12px; color: #5d6b66; line-height: 1.4; }

        /* --- CART FLOATING --- */
        .cart-floating {
            position: fixed;
            left: 50%;
            bottom: 20px;
            transform: translateX(-50%);
            width: calc(100% - 36px);
            max-width: 400px;
            background: #183f37;
            color: #efe6d8;
            padding: 14px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(24,63,55,0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 995;
            animation: popIn 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        }
        @keyframes popIn {
            from { transform: translate(-50%, 50px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        .cart-floating a {
            background: #efe6d8;
            color: #183f37;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        /* ========================================================
           RESPONSIVE DESIGN (THE MAGIC HAPPENS HERE)
           ======================================================== */
        @media (max-width: 1024px) {
            .grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 768px) {
            .grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 480px) {
            .store-header h1 { font-size: 26px; }
            
            .grid { 
                grid-template-columns: 1fr; 
                gap: 14px;
            }
            
            /* SIHIR CSS GRID: Mengubah Card Vertikal jadi Horizontal List Ala GoFood */
            .product-card {
                display: grid;
                grid-template-columns: 100px 1fr;
                grid-template-areas:
                    "img title"
                    "img desc"
                    "img price"
                    "img btn";
                gap: 2px 14px;
                padding: 12px;
                align-items: center;
            }

            .product-image { 
                grid-area: img; 
                height: 100px; 
                width: 100px; 
                margin: 0; 
                border-radius: 12px;
            }
            
            .product-card h3 { 
                grid-area: title; 
                font-size: 15px; 
                align-self: end; 
                margin-top: 4px;
            }
            
            .product-desc {
                grid-area: desc;
                font-size: 11px;
                margin: 0 0 2px 0;
                -webkit-line-clamp: 2; /* Maksimal 2 baris di HP */
            }
            
            .category { 
                display: none; /* Sembunyikan kategori di HP, karena udah ada judul section di atasnya */
            }
            
            .price { 
                grid-area: price; 
                font-size: 13px; 
                margin: 0; 
                line-height: 1.3;
            }
            
            .btn { 
                grid-area: btn; 
                width: max-content; 
                padding: 6px 14px; 
                font-size: 12px; 
                align-self: start;
                margin-top: 4px;
            }
        }
    </style>
</head>
<body>

<div class="header-wrapper">
    <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('logo-terminal.png') }}" alt="Logo" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
            <div class="top-bar-title">Terminal Coffee</div>
        </div>
        
        <div class="top-bar-actions">
            <button onclick="openBantuanModal()" class="action-btn" style="border: none; cursor: pointer;">
                📖 Cara Pesan
            </button>
            <a href="{{ route('customer.riwayat.form', $outlet) }}" class="action-btn">
               📜 Riwayat
            </a>
        </div>
    </div>

    <div class="sticky-category" id="categoryNav">
        <a href="#cat-coffee" class="nav-pill active">☕ Coffee</a>
        <a href="#cat-noncoffee" class="nav-pill">🍹 Non Coffee</a>
        <a href="#cat-food" class="nav-pill">🥐 Food</a>
    </div>
</div>

<div class="container">

    <div class="store-header">
        <h1>Outlet {{ ucfirst($outlet) }}</h1>
        <p>Pilih menu favoritmu dan nikmati kopi terbaik dari kami.</p>
    </div>

    @if(isset($pesananAktif) && $pesananAktif)
        <div class="alert-box alert-warning">
            <div>
                <strong style="display: block; font-size: 15px;">Pesanan sedang diproses! ☕</strong>
                <span style="font-size: 13px;">Status: <strong>{{ ucwords(str_replace('_', ' ', $pesananAktif->status)) }}</strong></span>
            </div>
            <a href="{{ route('customer.status', $pesananAktif->id) }}" class="action-btn" style="background: #b56a00; color: white;">
                Cek
            </a>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert-box alert-success">✅ {{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-box alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="cat-coffee" class="menu-category-wrapper">
        @include('customer.partials.produk-section', [
            'title' => 'Coffee',
            'items' => $coffee
        ])
    </div>

    <div id="cat-noncoffee" class="menu-category-wrapper">
        @include('customer.partials.produk-section', [
            'title' => 'Non Coffee',
            'items' => $nonCoffee
        ])
    </div>

    <div id="cat-food" class="menu-category-wrapper">
        @include('customer.partials.produk-section', [
            'title' => 'Food',
            'items' => $food
        ])
    </div>

</div>

@if($jumlahKeranjang > 0)
    <div class="cart-floating">
        <div>
            <div style="font-size: 13px; color: #c9bca8;">Total Belanja</div>
            <div style="font-weight: 700; font-size: 16px;">{{ $jumlahKeranjang }} item | Rp {{ number_format($totalKeranjang) }}</div>
        </div>
        <a href="{{ route('customer.keranjang', $outlet) }}">
            Checkout ➔
        </a>
    </div>
@endif

<div class="modal" id="orderModal">
    <div class="modal-box">
        <h2 id="modalNamaProduk" style="margin-bottom: 5px;">Pesan Menu</h2>
        <p id="modalDeskripsiProduk" style="font-size: 13px; color: #5d6b66; margin-top: 0; margin-bottom: 18px; line-height: 1.4;"></p>

        <form id="formModalOrder" method="POST">
            @csrf
            <input type="hidden" name="produk_id" id="modalProdukId">

            <div id="pilihanUkuran">
                <label style="font-size: 13px; font-weight: 600; color: #5d6b66;">Ukuran</label>
                <select name="ukuran" id="ukuranSelect">
                    <option value="reguler">Reguler</option>
                    <option value="large">Large</option>
                </select>
            </div>

            <div id="pilihanTipe">
                <label style="font-size: 13px; font-weight: 600; color: #5d6b66;">Tipe Penyajian</label>
                <select name="tipe" id="tipeSelect"></select>
            </div>

            <!-- Dropdown Sirup (Awalnya Disembunyikan) -->
            <div id="pilihanSyrup" style="display: none; margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 600; color: #5d6b66;">Extra Syrup (+ Rp 3.000)</label>
                <select name="extra_syrup" id="syrupSelect" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="tidak">Tidak pakai</option>
                    <option value="butterscotch">Butterscotch</option>
                    <option value="caramel">Caramel</option>
                    <option value="hazelnut">Hazelnut</option>
                </select>
            </div>

            <label style="font-size: 13px; font-weight: 600; color: #5d6b66;">Jumlah</label>
            <input type="number" name="jumlah" value="1" min="1" required>
            
            <!-- Opsi Cepat -->
            <div id="opsi-cepat-customer" style="display: none; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                <label id="label-with-sugar-cust" style="display: none; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-with-sugar-cust" style="margin: 0; cursor: pointer;"> With Sugar
                </label>
                <label id="label-less-sugar-cust" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-less-sugar-cust" style="margin: 0; cursor: pointer;"> Less Sugar
                </label>
                <label id="label-no-sugar-cust" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-no-sugar-cust" style="margin: 0; cursor: pointer;"> No Sugar
                </label>
                <label id="label-less-ice-cust" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-less-ice-cust" style="margin: 0; cursor: pointer;"> Less Ice
                </label>
            </div>
            
            <label style="font-size: 13px; font-weight: 600; color: #5d6b66;">Catatan Tambahan (Opsional)</label>
            <!-- 🔥 Kasih id="inputCatatan" biar bisa dikontrol Javascript -->
            <input type="text" id="inputCatatan" placeholder="Contoh: jangan di-press, dsb..." autocomplete="off">
            <input type="hidden" id="hiddenKeterangan" name="keterangan">

            <div id="warningStokModal" style="display: none; color: #c62828; font-size: 13px; font-weight: 700; margin-top: 10px; text-align: center;">⚠️ Varian ini sedang Sold Out!</div>
            <button type="submit" id="btnSubmitOrder" class="btn" style="margin-top: 10px;">+ Tambah ke Keranjang</button>
            <button type="button" class="btn close-btn" onclick="closeModal()">Batal</button>
        </form>
    </div>
</div>

<!-- Modal Cara Pesan -->
<div class="modal" id="bantuanModal">
    <div class="modal-box">
        <h2 style="margin-bottom: 18px; font-size: 20px; text-align: center;">Cara Pemesanan 💡</h2>
        
        <div class="step-guide">
            <div class="step-item">
                <div class="step-icon">1</div>
                <div class="step-text">
                    <strong>Pilih & Tambah Menu</strong>
                    <p>Pilih menu favoritmu, atur variannya, lalu klik "Tambah ke Keranjang".</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-icon">2</div>
                <div class="step-text">
                    <strong>Lengkapi Data & Checkout</strong>
                    <p>Buka keranjang, isi <b>Nama</b> dan <b>No. HP</b>, lalu pilih pembayaran (<b>Tunai/QRIS</b>).</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-icon">3</div>
                <div class="step-text">
                    <strong>Lakukan Pembayaran</strong>
                    <p>Jika <b>Tunai</b>, tunjukkan nomor pesanan ke kasir. Jika <b>QRIS</b>, langsung scan & bayar dari HP.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-icon">4</div>
                <div class="step-text">
                    <strong>Pantau & Tunggu</strong>
                    <p>Pantau pesanan di halaman status dan harap menunggu hingga <b>"Siap Diambil"</b>.</p>
                </div>
            </div>
        </div>

        <button type="button" class="btn" onclick="closeBantuanModal()" style="margin-top: 20px;">Paham, Terima Kasih!</button>
    </div>
</div>

<script>
    // FUNGSI MODAL
    let varianStokSaatIni = {}; 

    // 🔥 TAMBAHIN PARAMETER bisaSyrup & deskripsi DI PALING UJUNG SINI BANG 🔥
    function openModal(id, nama, tipeProduk, tersediaHot, tersediaIce, stokVarian, bisaSyrup, deskripsi) {
        document.getElementById('orderModal').classList.add('active');
        document.getElementById('modalProdukId').value = id;
        document.getElementById('modalNamaProduk').innerText = nama;
        
        const deskripsiEl = document.getElementById('modalDeskripsiProduk');
        if (deskripsi && deskripsi.trim() !== '') {
            deskripsiEl.innerText = deskripsi;
            deskripsiEl.style.display = 'block';
        } else {
            deskripsiEl.style.display = 'none';
        }

        const outletAktif = '{{ $outlet }}';
        document.getElementById('formModalOrder').action = `/order/${outletAktif}/tambah`;

        const pilihanUkuran = document.getElementById('pilihanUkuran');
        const pilihanTipe = document.getElementById('pilihanTipe');
        const tipeSelect = document.getElementById('tipeSelect');
        const inputCatatan = document.getElementById('inputCatatan'); // Ambil elemen catatan
        const opsiCepat = document.getElementById('opsi-cepat-customer');

        // Reset checkbox
        document.getElementById('chk-with-sugar-cust').checked = false;
        document.getElementById('chk-less-sugar-cust').checked = false;
        document.getElementById('chk-no-sugar-cust').checked = false;
        document.getElementById('chk-less-ice-cust').checked = false;

        // Tampilkan 'With Sugar' HANYA JIKA produknya Americano
        if (nama.toLowerCase().includes('americano')) {
            document.getElementById('label-with-sugar-cust').style.display = 'flex';
        } else {
            document.getElementById('label-with-sugar-cust').style.display = 'none';
        }

        tipeSelect.innerHTML = '';

        if (tipeProduk === 'vendor') {
            pilihanUkuran.style.display = 'none';
            pilihanTipe.style.display = 'none';
            opsiCepat.style.display = 'none'; // Sembunyikan opsi cepat untuk makanan
            // 🔥 Kalau makanan (vendor), ubah contoh catatannya!
            inputCatatan.placeholder = "Contoh: Dipanaskan, potong 2...";
        } else {
            pilihanUkuran.style.display = 'block';
            pilihanTipe.style.display = 'block';
            opsiCepat.style.display = 'flex'; // Tampilkan opsi cepat untuk minuman
            // 🔥 Kalau minuman, balikin contoh catatannya!
            inputCatatan.placeholder = "Contoh: Di-press yang kuat...";
            
            if (tersediaIce == 1) tipeSelect.innerHTML += '<option value="ice">Ice</option>';
            if (tersediaHot == 1) tipeSelect.innerHTML += '<option value="hot">Hot</option>';
        }

        // --- SAKLAR OTOMATIS SIRUP ---
        const pilihanSyrup = document.getElementById('pilihanSyrup');
        const syrupSelect = document.getElementById('syrupSelect');

        if (bisaSyrup == 1 || bisaSyrup == true) {
            pilihanSyrup.style.display = 'block'; // Munculin kalau bisa
        } else {
            pilihanSyrup.style.display = 'none';  // Ngumpet kalau nggak bisa
            syrupSelect.value = 'tidak';          // Reset biar nggak nyangkut
        }

        // Simpan json stok dan langsung cek ketersediaannya saat modal kebuka
        varianStokSaatIni = stokVarian;
        cekStokVarianModal(); 
        triggerTipeChange();
    }

    // Deteksi kalau pelanggan ganti-ganti dropdown ukuran / tipe
    document.getElementById('ukuranSelect').addEventListener('change', cekStokVarianModal);
    document.getElementById('tipeSelect').addEventListener('change', function() {
        cekStokVarianModal();
        let val = this.value;
        let labelIce = document.getElementById('label-less-ice-cust');
        let chkIce = document.getElementById('chk-less-ice-cust');
        if (val === 'hot') {
            labelIce.style.display = 'none';
            chkIce.checked = false;
        } else {
            labelIce.style.display = 'flex';
        }
    });

    // Jalankan satu kali event handler tipeSelect saat buka modal minuman
    function triggerTipeChange() {
        let ts = document.getElementById('tipeSelect');
        if(ts.options.length > 0) {
            let event = new Event('change');
            ts.dispatchEvent(event);
        }
    }

    function cekStokVarianModal() {
        const tipeProduk = document.getElementById('pilihanTipe').style.display === 'none' ? 'vendor' : 'racikan';
        const ukuran = document.getElementById('ukuranSelect').value;
        const tipe = document.getElementById('tipeSelect').value;
        
        const btnSubmit = document.getElementById('btnSubmitOrder');
        const warningTeks = document.getElementById('warningStokModal');

        let stokTersedia = false;

        if (tipeProduk === 'vendor') {
            // Sekarang Javascript akan baca hasil ASLI dari stok bahan baku di database
            stokTersedia = varianStokSaatIni['vendor_standar'] === true;
        } else {
            let key = tipe + '_' + ukuran; 
            stokTersedia = varianStokSaatIni[key] === true;
        }

        // Kalau bahannya ada, nyalain tombol. Kalau habis, matiin tombol!
        if (stokTersedia) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '+ Tambah ke Keranjang';
            btnSubmit.style.background = '#183f37';
            btnSubmit.style.cursor = 'pointer';
            warningTeks.style.display = 'none';
        } else {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Habis / Sold Out';
            btnSubmit.style.background = '#ccc';
            btnSubmit.style.cursor = 'not-allowed';
            warningTeks.style.display = 'block';
        }
    }

    function closeModal() {
        document.getElementById('orderModal').classList.remove('active');
    }

    function openBantuanModal() {
        document.getElementById('bantuanModal').classList.add('active');
    }

    function closeBantuanModal() {
        document.getElementById('bantuanModal').classList.remove('active');
    }

    // Intercept form submit untuk gabungkan Keterangan
    document.getElementById('formModalOrder').addEventListener('submit', function(e) {
        let ketManual = document.getElementById('inputCatatan').value.trim();
        let arrayKet = [];

        if (document.getElementById('chk-with-sugar-cust').checked) arrayKet.push("With Sugar");
        if (document.getElementById('chk-less-sugar-cust').checked) arrayKet.push("Less Sugar");
        if (document.getElementById('chk-no-sugar-cust').checked) arrayKet.push("No Sugar");
        if (document.getElementById('chk-less-ice-cust').checked) arrayKet.push("Less Ice");
        if (ketManual !== "") arrayKet.push(ketManual);

        document.getElementById('hiddenKeterangan').value = arrayKet.join(', ');
    });
    
    // SIHIR JAVASCRIPT: Auto ganti warna menu navigasi (pill) saat di scroll!
    document.addEventListener("DOMContentLoaded", function() {
        const sections = document.querySelectorAll(".menu-category-wrapper");
        const navLinks = document.querySelectorAll(".nav-pill");
        const navContainer = document.getElementById("categoryNav");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(link => {
                        link.classList.remove("active");
                        if (link.getAttribute("href").substring(1) === entry.target.id) {
                            link.classList.add("active");
                            // Bikin menu pill otomatis geser (scroll) ke posisi tombol yang aktif
                            link.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "center" });
                        }
                    });
                }
            });
        }, { threshold: 0.2, rootMargin: "-150px 0px -50% 0px" }); // Margin deteksi scroll

        sections.forEach(sec => observer.observe(sec));
    });
</script>

    @if(isset($pesananAktif) && $pesananAktif)
    <script>
        setInterval(function() {
            fetch("{{ route('customer.cekStatus', $pesananAktif->id) }}")
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'Siap diambil' || data.status === 'selesai') {
                        alert('Pesanan Anda sudah ' + data.status.replace('_', ' ') + '! Silakan ke kasir.');
                        window.location.href = "{{ route('customer.status', $pesananAktif->id) }}";
                    }
                });
        }, 10000); 
    </script>
    @endif

</body>
</html>