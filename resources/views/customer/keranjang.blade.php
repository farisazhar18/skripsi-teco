<!DOCTYPE html>
<html lang="id">
<head>
    <title>Keranjang - Terminal Coffee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-terminal.png') }}">

    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8f6f2; color: #183f37; margin: 0; padding: 20px 20px 80px; }
        .container { max-width: 600px; margin: auto; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { margin: 0 0 5px; font-size: 26px; font-weight: 700; }
        .header p { color: #6b6256; margin: 0; font-size: 14px; }
        
        .card { background: white; padding: 20px; border-radius: 20px; margin-bottom: 16px; box-shadow: 0 8px 20px rgba(24,63,55,0.06); }
        .cart-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; }
        .cart-item h3 { margin: 0 0 4px; font-size: 16px; font-weight: 600; }
        .item-meta { color: #6b6256; font-size: 13px; line-height: 1.5; }
        .item-price { font-size: 16px; font-weight: 700; color: #e67e22; white-space: nowrap; text-align: right; }
        
        .btn { background: #183f37; color: #efe6d8; border: none; padding: 14px; border-radius: 12px; text-decoration: none; font-weight: 600; display: block; text-align: center; width: 100%; font-size: 14px; cursor: pointer; transition: 0.2s; }
        .btn:hover { background: #2e5a4f; }
        
        /* 🔥 STYLE TOMBOL AKSI BARU */
        .action-buttons { display: flex; gap: 8px; margin-top: 10px; }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
        .btn-edit { background: #e0f2fe; color: #0284c7; }
        .btn-danger { background: #ffe1df; color: #c62828; }
        
        .btn-secondary { background: #efe6d8; color: #183f37; }
        
        input, select { width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #dcd3c6; margin: 6px 0 16px; font-size: 14px; outline: none; transition: 0.2s; background: white; }
        input:focus, select:focus { border-color: #183f37; box-shadow: 0 0 0 3px rgba(24,63,55,0.1); }
        
        .total-row { display: flex; justify-content: space-between; align-items: center; font-size: 20px; font-weight: 700; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 2px dashed #eae5dc; }
        .error-box { background: #ffe1df; color: #c62828; padding: 14px; border-radius: 14px; margin-bottom: 18px; font-size: 13px; font-weight: 500; }
        .actions { display: flex; gap: 10px; flex-direction: column; }
        
        /* 🔥 STYLE MODAL EDIT */
        .modal-overlay { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .modal-content { background: white; width: 90%; max-width: 400px; border-radius: 20px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto; }
        .modal-title { font-size: 24px; font-weight: 700; color: #183f37; margin: 0 0 20px; }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 4px; display: block; color: #6b6256; }
        
        @media (min-width: 480px) { .actions { flex-direction: row; } .actions .btn { flex: 1; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🛒 Keranjang</h1>
        <p>Outlet: <strong>{{ ucfirst($outlet) }}</strong></p>
    </div>

    @if ($errors->any())
        <div class="error-box">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    @forelse($keranjang as $index => $item)
        <div class="card">
            <div class="cart-item">
                <div style="flex: 1;">
                    <h3>{{ $item['nama_produk'] }}</h3>
                    <div class="item-meta">
                        {{ $item['tipe'] == 'food' ? 'Food' : ucfirst($item['ukuran']) . ' - ' . ucfirst($item['tipe']) }}
                        @if(!empty($item['keterangan']))
                            <br><span style="color: #e67e22; font-style: italic;">📝 Req: {{ $item['keterangan'] }}</span>
                        @endif
                        <br>Qty: <strong>{{ $item['jumlah'] }}</strong>
                    </div>
                    
                    <!-- 🔥 TOMBOL AKSI (EDIT & HAPUS) 🔥 -->
                    <div class="action-buttons">
                        <!-- Tombol Edit -->
                        <button type="button" class="btn-action btn-edit" onclick="bukaModalEdit({{ $index }}, '{{ addslashes($item['nama_produk']) }}', '{{ strtolower($item['ukuran'] ?? 'reguler') }}', '{{ strtolower($item['tipe']) }}', {{ $item['jumlah'] }}, '{{ addslashes($item['keterangan'] ?? '') }}')">
                            ✏️ Edit
                        </button>
                        
                        <!-- Tombol Hapus -->
                        <form action="{{ route('customer.hapus', $outlet) }}" method="POST" style="margin: 0;">
                            @csrf <input type="hidden" name="index" value="{{ $index }}">
                            <button class="btn-action btn-danger" type="submit">🗑️ Hapus</button>
                        </form>
                    </div>
                </div>
                <div class="item-price">Rp {{ number_format($item['subtotal']) }}</div>
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center; padding: 40px 20px;">
            <div style="font-size: 40px; margin-bottom: 10px;">🛍️</div>
            <h3 style="margin:0 0 5px;">Keranjang Kosong</h3>
            <p style="color:#6b6256; font-size:14px; margin:0;">Silakan pilih menu favoritmu dulu.</p>
        </div>
    @endforelse

    @if(count($keranjang) > 0)
        <div class="card">
            <div class="total-row">
                <span>Total Belanja</span>
                <span style="color:#e67e22;">Rp {{ number_format($total) }}</span>
            </div>
            <form action="{{ route('customer.checkout', $outlet) }}" method="POST">
                @csrf
                <label style="font-size: 13px; font-weight: 600;">No. WhatsApp (Opsional)</label>
                <input type="text" name="no_hp" id="inputNoHp" placeholder="Contoh: 081234567890" onkeyup="cekRiwayatCustomer()" style="margin-bottom: 5px;">
                <small style="color: #6b6256; font-style: italic; display: block; margin-bottom: 15px; font-size: 11px;">
                    💡 Isi nomor HP untuk melihat riwayat pemesanan kamu.
                </small>
                
                <small id="pesanPelanggan" style="color: #0f7a3a; font-weight: bold; display: none; margin-bottom: 12px;"></small>

                <div id="grupNama">
                    <label style="font-size: 13px; font-weight: 600;">Nama Pemesan <span style="color: #c62828;">*</span></label>
                    <input type="text" name="nama_customer" id="inputNama" placeholder="Masukkan nama Anda..." required>
                </div>

                <label style="font-size: 13px; font-weight: 600;">Metode Pembayaran</label>
                <select name="metode_pembayaran" required>
                    <option value="Tunai">💵 Tunai (Bayar di Kasir)</option>
                    <option value="QRIS">📱 QRIS</option>
                </select>

                <div class="actions">
                    <a href="{{ route('customer.menu', $outlet) }}" class="btn btn-secondary">← Tambah Menu</a>
                    <button class="btn" type="submit">Checkout ➔</button>
                </div>
            </form>
        </div>
    @else
        <a href="{{ route('customer.menu', $outlet) }}" class="btn">← Kembali ke Menu</a>
    @endif

    @if(session('order_id'))
        <a href="{{ route('customer.status', session('order_id')) }}" style="display: block; text-align: center; color: #183f37; font-weight: 600; font-size: 13px; margin-top: 20px; text-decoration: underline;">
            Lihat status pesanan terakhirmu di sini ➔
        </a>
    @endif
</div>

<!-- ======================================================== -->
<!-- 💡 MODAL POPUP EDIT ITEM -->
<!-- ======================================================== -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <h3 id="editNamaProduk" class="modal-title">Nama Menu</h3>
        
        <!-- 🔥 Pastiin backend lu ada route 'customer.updateKeranjang' ya bang 🔥 -->
        <form action="{{ route('customer.updateKeranjang', $outlet) }}" method="POST">
            @csrf
            <input type="hidden" name="index" id="editIndex">
            <!-- Buat jaga-jaga ngelempar tipe bawaan kalau dia "food" -->
            <input type="hidden" name="tipe_bawaan" id="editTipeBawaan"> 

            <div id="areaUkuranTipe">
                <label class="form-label">Ukuran</label>
                <select name="ukuran" id="editUkuran" style="padding: 12px; margin-top: 0; margin-bottom: 16px;">
                    <option value="reguler">Reguler</option>
                    <option value="large">Large</option>
                </select>

                <label class="form-label">Tipe Penyajian</label>
                <select name="tipe" id="editTipe" style="padding: 12px; margin-top: 0; margin-bottom: 16px;">
                    <option value="ice">Ice</option>
                    <option value="hot">Hot</option>
                </select>
            </div>

            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" id="editJumlah" min="1" required style="padding: 12px; margin-top: 0; margin-bottom: 16px;">

            <label class="form-label">Catatan Tambahan (Opsional)</label>
            <input type="text" name="keterangan" id="editKeterangan" placeholder="Contoh: Less ice, less sugar..." style="padding: 12px; margin-top: 0; margin-bottom: 24px;">

            <button type="submit" class="btn" style="margin-bottom: 10px;">Simpan Perubahan</button>
            <button type="button" class="btn btn-secondary" onclick="tutupModalEdit()">Batal</button>
        </form>
    </div>
</div>

<script>
    function cekRiwayatCustomer() {
        let noHp = document.getElementById('inputNoHp').value;
        let pesan = document.getElementById('pesanPelanggan');
        let inputNama = document.getElementById('inputNama');
        if (noHp.length > 8) {
            fetch(`/order/cek-customer/${noHp}`)
                .then(res => res.json())
                .then(data => {
                    if (data.ditemukan) {
                        inputNama.value = data.nama;
                        pesan.innerHTML = "✨ Selamat datang kembali, <strong>" + data.nama + "</strong>!";
                        pesan.style.display = "block";
                    } else { pesan.style.display = "none"; }
                }).catch(err => console.error('Error:', err));
        } else { pesan.style.display = "none"; }
    }

    // 🔥 FUNGSI JAVASCRIPT BUAT MODAL EDIT 🔥
    function bukaModalEdit(index, nama, ukuran, tipe, jumlah, keterangan) {
        document.getElementById('editIndex').value = index;
        document.getElementById('editNamaProduk').innerText = nama;
        document.getElementById('editJumlah').value = jumlah;
        document.getElementById('editKeterangan').value = keterangan;
        document.getElementById('editTipeBawaan').value = tipe;
        
        let areaUkuranTipe = document.getElementById('areaUkuranTipe');
        
        // Kalau yang diedit tipe makan (food), umpetin pilihan ukuran & es/panas
        if (tipe === 'food') {
            areaUkuranTipe.style.display = 'none';
        } else {
            areaUkuranTipe.style.display = 'block';
            document.getElementById('editUkuran').value = ukuran;
            document.getElementById('editTipe').value = tipe;
        }
        
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function tutupModalEdit() {
        document.getElementById('modalEdit').style.display = 'none';
    }
    
    // Biar kalau klik di luar modal bisa nutup
    window.onclick = function(event) {
        var modal = document.getElementById('modalEdit');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>