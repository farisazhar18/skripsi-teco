@extends('layouts.pos')

@section('content')
<h1 class="page-title">Transaksi Penjualan</h1>

@if(session('error'))
    <div style="background-color: #ffcccc; color: red; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        ❌ {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div style="background-color: #ffcccc; color: red; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        ❌ Error Validasi: Pastikan metode pembayaran sudah dipilih!
    </div>
@endif

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 300px;">
        <h3>Pilih Produk</h3>
        <div class="form-group">
            <label>Produk</label>
            <select id="produk_id" placeholder="Ketik nama produk...">
                <option value="">-- Pilih Produk --</option>
                @foreach($produks as $produk)
                    @php 
                        // Cek status otomatis berdasarkan outlet kasir yang sedang login
                        $outletAktif = session('outlet_aktif') ?? 'hasanuddin';
                        $statusBahan = $produk->statusOtomatis($outletAktif); 
                    @endphp
                    
                    <option value="{{ $produk->id }}" 
                            {{ $statusBahan != 'Aktif' ? 'disabled' : '' }}
                            data-nama="{{ $produk->nama_produk }}"
                            data-kategori="{{ strtolower($produk->kategori ?? '') }}"
                            data-harga-reguler="{{ $produk->harga_reguler ?? 0 }}"
                            data-harga-large="{{ $produk->harga_large ?? 0 }}"
                            data-hot="{{ $produk->tersedia_hot ?? 0 }}"
                            data-ice="{{ $produk->tersedia_ice ?? 0 }}"
                            data-bisa-syrup="{{ $produk->bisa_extra_syrup ? '1' : '0' }}">
                        {{ $produk->nama_produk }} {{ $statusBahan != 'Aktif' ? '(Sold Out)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="group-ukuran" style="display: none;">
            <label>Ukuran</label>
            <select id="ukuran">
                <option value="Reguler">Reguler</option>
                <option value="Large">Large</option>
            </select>
        </div>

        <div class="form-group" id="group-syrup" style="display: none;">
            <label>Extra Syrup (+ Rp 3.000)</label>
            <select id="extra_syrup">
                <option value="tidak">Tidak pakai</option>
                <option value="Butterscotch">Butterscotch</option>
                <option value="Caramel">Caramel</option>
                <option value="Hazelnut">Hazelnut</option>
            </select>
        </div>

        <div class="form-group" id="group-tipe" style="display: none;">
            <label>Penyajian</label>
            <select id="tipe">
                <option value="Hot">Hot</option>
                <option value="Ice">Ice</option>
            </select>
        </div>

        <div class="form-group">
            <label>Jumlah</label>
            <input type="number" id="jumlah" min="1" value="1">
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            
            <!-- Tambahin ID 'opsi-cepat' di div ini bang -->
            <div id="opsi-cepat" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-less-sugar" style="margin: 0; cursor: pointer;"> Less Sugar
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-no-sugar" style="margin: 0; cursor: pointer;"> No Sugar
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1d5db; user-select: none;">
                    <input type="checkbox" id="chk-less-ice" style="margin: 0; cursor: pointer;"> Less Ice
                </label>
            </div>
            <!-- Input Text Manual tetep disediain buat jaga-jaga -->
            <input type="text" id="keterangan" placeholder="Catatan lain (opsional)..." autocomplete="off">
        </div>

        <button type="button" class="btn btn-primary" onclick="tambahKeKeranjang()" style="width: 100%;">+ Tambah ke Keranjang</button>
    </div>

    <div class="card" style="flex: 2; min-width: 400px;">
        <h3>Keranjang Belanja</h3>
        <div class="table-card" style="overflow-x:auto;">
            <table style="width: 100%; table-layout: auto;">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                </tbody>
            </table>
        </div>
        
        <h2 style="text-align: right; margin-top: 20px;">Total: Rp <span id="total-harga">0</span></h2>

        <hr>
        
        <form action="{{ route('penjualan.store') }}" method="POST" id="form-checkout">
            @csrf
            <input type="hidden" name="cart_data" id="cart_data">
            
            <input type="hidden" name="metode_bayar" id="metode_bayar_hidden">
            
            <button type="button" class="btn btn-success" onclick="prosesCheckout()" style="width: 100%; font-size: 18px; padding: 15px;">Bayar Sekarang</button>
        </form>
    </div>
</div>

<div id="modal-checkout" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 15px; width: 400px; max-width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; text-align: center;">Konfirmasi Pembayaran</h3>
        <h1 style="text-align: center; color: #e67e22; font-size: 35px; margin: 10px 0;">Rp <span id="modal-total-display">0</span></h1>

        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select id="pilih-metode" onchange="ubahMetodeBayar()">
                <option value="Tunai">💵 Tunai</option>
                <option value="QRIS">📱 QRIS</option>
            </select>
        </div>

        <div id="area-tunai">
            <div class="form-group">
                <label>Uang Diterima (Rp)</label>
                <input type="number" id="uang-diterima" placeholder="Contoh: 50000" onkeyup="hitungKembalian()" style="font-size: 18px; font-weight: bold; width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                
                <div id="quick-cash-container" style="display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap;"></div>
                
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <p style="margin: 0; color: #666;">Kembalian:</p>
                <h2 style="margin: 0; color: #27ae60;">Rp <span id="kembalian-display">0</span></h2>
            </div>
        </div>

        <div id="area-qris" style="display: none; text-align: center; padding: 15px 0;">
            <p style="font-weight: bold; color: #183f37;">Layar QRIS akan muncul setelah Anda menekan tombol "Simpan & Cetak".</p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary" onclick="tutupModal()" style="flex: 1; background: #ccc; color: #333;">Batal</button>
            <button type="button" class="btn btn-success" onclick="submitFinal()" style="flex: 1;">✅ Simpan & Cetak</button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let tomSelectInstance = null;

    document.addEventListener("DOMContentLoaded", function() {
        if(document.getElementById("produk_id")) {
            tomSelectInstance = new TomSelect("#produk_id", { 
                create: false, 
                sortField: { field: "text", direction: "asc" } 
            });

            tomSelectInstance.on('change', function(value) {
                cekVarianProduk(value);
            });
        }
    });

    function cekVarianProduk(id) {
        if (!id) {
            document.getElementById('group-ukuran').style.display = 'none';
            document.getElementById('group-tipe').style.display = 'none';
            return;
        }

        let select = document.getElementById('produk_id');
        let selectedOption = select.querySelector(`option[value="${id}"]`);
        
        if (!selectedOption) return;

        let hargaLarge = parseFloat(selectedOption.getAttribute('data-harga-large')) || 0;
        let isHot = selectedOption.getAttribute('data-hot') == '1';
        let isIce = selectedOption.getAttribute('data-ice') == '1';

        if (hargaLarge > 0) {
            document.getElementById('group-ukuran').style.display = 'block';
        } else {
            document.getElementById('group-ukuran').style.display = 'none';
            document.getElementById('ukuran').value = 'Reguler';
        }

        if (isHot || isIce) {
            document.getElementById('group-tipe').style.display = 'block';
            if (!isHot) document.getElementById('tipe').value = 'Ice';
            if (!isIce) document.getElementById('tipe').value = 'Hot';
        } else {
            document.getElementById('group-tipe').style.display = 'none';
        }

        // LOGIKA MUNCULIN SIRUP
        let bisaSyrup = selectedOption.getAttribute('data-bisa-syrup') == '1';

        if (bisaSyrup) {
            document.getElementById('group-syrup').style.display = 'block';
        } else {
            document.getElementById('group-syrup').style.display = 'none';
            document.getElementById('extra_syrup').value = 'tidak'; // Reset kalau ganti produk
        }

        // LOGIKA MUNCULIN/SEMBUNYIIN OPSI REQUEST CEPAT BERALASAN KATEGORI
        let kategori = selectedOption.getAttribute('data-kategori');
        let opsiCepat = document.getElementById('opsi-cepat');

        // Kalau kategorinya ada unsur kata makanan/food/snack, maka sembunyikan
        if (kategori.includes('makanan') || kategori.includes('food') || kategori.includes('snack')) {
            opsiCepat.style.display = 'none';
            
            // Otomatis reset centangan biar ga kebawa-bawa ke keranjang
            if(document.getElementById('chk-less-sugar')) document.getElementById('chk-less-sugar').checked = false;
            if(document.getElementById('chk-no-sugar')) document.getElementById('chk-no-sugar').checked = false;
            if(document.getElementById('chk-less-ice')) document.getElementById('chk-less-ice').checked = false;
        } else {
            opsiCepat.style.display = 'flex'; // Munculin balik kalau yang dipilih minuman
        }
    }

    function tambahKeKeranjang() {
        let select = document.getElementById('produk_id');
        let id = select.value;
        
        if (!id) {
            alert('Pilih produk dulu!');
            return;
        }

        let selectedOption = select.querySelector(`option[value="${id}"]`);
        let nama = selectedOption.getAttribute('data-nama');
        
        let isSizeVisible = document.getElementById('group-ukuran').style.display !== 'none';
        let isTypeVisible = document.getElementById('group-tipe').style.display !== 'none';

        let ukuran = isSizeVisible ? document.getElementById('ukuran').value : '-';
        let tipe = isTypeVisible ? document.getElementById('tipe').value : '-';
        
        // === LOGIKA BARU KETERANGAN ===
        let ketManual = document.getElementById('keterangan').value.trim();
        let arrayKet = [];

        // Cek checkbox mana aja yang dicentang
        if(document.getElementById('chk-less-sugar') && document.getElementById('chk-less-sugar').checked) arrayKet.push("Less Sugar");
        if(document.getElementById('chk-no-sugar') && document.getElementById('chk-no-sugar').checked) arrayKet.push("No Sugar");
        if(document.getElementById('chk-less-ice') && document.getElementById('chk-less-ice').checked) arrayKet.push("Less Ice");

        // Masukin keterangan manual kalau barista ngetik sesuatu
        if(ketManual !== "") arrayKet.push(ketManual);

        // Gabungin semua request pakai koma
        let keterangan = arrayKet.join(', ');
        // ==============================
        let isSyrupVisible = document.getElementById('group-syrup').style.display !== 'none';
        let syrup = isSyrupVisible ? document.getElementById('extra_syrup').value : 'tidak';

        let hargaReguler = parseFloat(selectedOption.getAttribute('data-harga-reguler')) || 0;
        let hargaLarge = parseFloat(selectedOption.getAttribute('data-harga-large')) || 0;
        
        let hargaFix = hargaReguler;
        if (ukuran === 'Large' && hargaLarge > 0) {
            hargaFix = hargaLarge;
        }

        // --- TAMBAHAN LOGIKA NGE-CHARGE SIRUP ---
        if (syrup !== 'tidak') {
            hargaFix += 3000;
            let tambahanTulisan = "Extra Syrup: " + syrup;
            keterangan = keterangan ? keterangan + ' | ' + tambahanTulisan : tambahanTulisan;
        }

        let qty = parseInt(document.getElementById('jumlah').value) || 1;
        let subtotal = hargaFix * qty;
        
        let cartItemId = id + '_' + ukuran + '_' + tipe + '_' + keterangan;

        let existingIndex = cart.findIndex(item => item.cartItemId === cartItemId);
        if (existingIndex > -1) {
            cart[existingIndex].qty += qty;
            cart[existingIndex].subtotal = cart[existingIndex].qty * hargaFix;
        } else {
            let textVarian = [];
            if (ukuran !== '-') textVarian.push(ukuran);
            if (tipe !== '-') textVarian.push(tipe);
            let displayVarian = textVarian.length > 0 ? textVarian.join(' / ') : '-';

            cart.push({ 
                cartItemId: cartItemId, 
                id: id, 
                nama: nama, 
                varian: displayVarian, 
                harga: hargaFix, 
                qty: qty, 
                subtotal: subtotal,
                db_ukuran: ukuran, 
                db_tipe: tipe,
                keterangan: keterangan 
            });
        }

        renderCart();
        document.getElementById('keterangan').value = '';
        if(document.getElementById('chk-less-sugar')) document.getElementById('chk-less-sugar').checked = false;
        if(document.getElementById('chk-no-sugar')) document.getElementById('chk-no-sugar').checked = false;
        if(document.getElementById('chk-less-ice')) document.getElementById('chk-less-ice').checked = false;
    }

    function hapusItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;

        cart.forEach((item, index) => {
            total += item.subtotal;
            let badgeVarian = item.varian !== '-' ? `<span class="badge badge-warning" style="background:#f0ad4e; color:white; padding:3px 8px; border-radius:10px; font-size:12px;">${item.varian}</span>` : '-';
            
            let displayKeterangan = item.keterangan ? `<br><span style="font-size: 12px; color: #e67e22; font-style: italic; font-weight: normal;">📝 Req: "${item.keterangan}"</span>` : '';
            
            html += `
                <tr>
                    <td style="text-align: left; font-weight: bold;">
                        ${item.nama}
                        ${displayKeterangan}
                    </td>
                    <td>${badgeVarian}</td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td>${item.qty}</td>
                    <td style="font-weight: bold;">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${index})">X</button></td>
                </tr>
            `;
        });

        document.getElementById('cart-body').innerHTML = html;
        document.getElementById('total-harga').innerText = total.toLocaleString('id-ID');
    }

    let totalBelanja = 0;

    // Fungsi untuk membuat tombol nominal dinamis
    function renderQuickCash(total) {
        let container = document.getElementById('quick-cash-container');
        container.innerHTML = ''; // Kosongkan dulu setiap kali modal dibuka

        let options = [];
        
        // 1. Tombol Uang Pas
        options.push(total);

        // 2. Tombol Pembulatan 10.000 terdekat (Mendekati)
        let nextTenK = Math.ceil(total / 10000) * 10000;
        if (nextTenK > total) options.push(nextTenK);

        // 3. Tombol Pembulatan 50.000 terdekat (Sering dipakai)
        let nextFiftyK = Math.ceil(total / 50000) * 50000;
        if (nextFiftyK > total && !options.includes(nextFiftyK)) options.push(nextFiftyK);

        // 4. Tombol Pembulatan 100.000 terdekat (Sering dipakai)
        let nextHundredK = Math.ceil(total / 100000) * 100000;
        if (nextHundredK > total && !options.includes(nextHundredK)) options.push(nextHundredK);

        // Looping untuk memunculkan tombol ke HTML
        options.forEach(nominal => {
            let label = (nominal === total) ? 'Uang Pas' : nominal.toLocaleString('id-ID');
            // Bikin tombolnya dengan styling
            let btn = `
                <button type="button" onclick="setNominal(${nominal})" 
                    style="padding: 6px 12px; font-size: 14px; font-weight: bold; border: 1px solid #27ae60; background: #eafaf1; color: #27ae60; border-radius: 6px; cursor: pointer; transition: 0.2s;">
                    ${label}
                </button>
            `;
            container.innerHTML += btn;
        });
    }

    // Fungsi ketika tombol nominal ditekan
    function setNominal(jumlah) {
        document.getElementById('uang-diterima').value = jumlah;
        hitungKembalian(); // Otomatis hitung kembalian
    }

    function prosesCheckout() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong bang!');
            return;
        }
        
        totalBelanja = cart.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById('modal-total-display').innerText = totalBelanja.toLocaleString('id-ID');
        document.getElementById('uang-diterima').value = '';
        document.getElementById('kembalian-display').innerText = '0';
        
        // ---> TAMBAHKAN BARIS INI BANG <---
        renderQuickCash(totalBelanja); 
        
        document.getElementById('modal-checkout').style.display = 'flex';
        ubahMetodeBayar(); 
    }

    function tutupModal() {
        document.getElementById('modal-checkout').style.display = 'none';
    }

    function ubahMetodeBayar() {
        let metode = document.getElementById('pilih-metode').value;
        if (metode === 'Tunai') {
            document.getElementById('area-tunai').style.display = 'block';
            document.getElementById('area-qris').style.display = 'none';
        } else {
            document.getElementById('area-tunai').style.display = 'none';
            document.getElementById('area-qris').style.display = 'block';
        }
    }

    function hitungKembalian() {
        let uang = parseFloat(document.getElementById('uang-diterima').value) || 0;
        let kembalian = uang - totalBelanja;
        
        if (kembalian < 0) kembalian = 0; 
        document.getElementById('kembalian-display').innerText = kembalian.toLocaleString('id-ID');
    }

    function submitFinal() {
        let metode = document.getElementById('pilih-metode').value;
        let uang = parseFloat(document.getElementById('uang-diterima').value) || 0;
        
        if (metode === 'Tunai' && uang < totalBelanja) {
            alert('Duitnya kurang bang! Minta lagi ke customernya.');
            return;
        }

        // AMBIL DATA & TEMBAK KE FORM
        document.getElementById('metode_bayar_hidden').value = metode;
        document.getElementById('cart_data').value = JSON.stringify(cart);
        
        document.getElementById('form-checkout').submit();
    }
</script>

<div style="margin-bottom: 20px; display: flex; gap: 10px;">       
    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali ke Penjualan</a>
</div>
@endsection