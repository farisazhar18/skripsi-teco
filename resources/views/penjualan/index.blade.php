@extends('layouts.pos')

@section('content')
<!-- Pindahin definisi role ke paling atas biar bisa dipakai di seluruh halaman -->
@php $role = auth()->user()->role; @endphp

<h1 class="page-title mb-lg">Penjualan</h1>

<!-- 🔥 BAGIAN ACTION BAR & PENCARIAN REAL-TIME -->
<div class="action-bar">
    
    <!-- Cuma Owner dan Kasir yang bisa lihat tombol ini -->
    @if(in_array($role, ['owner', 'kasir']))
        <a href="{{ route('penjualan.create') }}" class="btn">Tambah Pesanan</a>
    @else
        <div></div> <!-- Pengganjal biar form pencarian tetap di kanan -->
    @endif
    
    <!-- Filter Nama Real-time (Tanpa Tombol Cari) -->
    <div class="search-box">
        <input type="text" id="inputCariNama" oninput="filterNama()" placeholder="Cari nama pelanggan..." class="search-input">
    </div>
</div>

<div class="order-grid">
    @foreach($penjualans as $p)
    @php
        // 🎨 Nentuin warna border dan badge
        if($p->status == 'menunggu_pembayaran') {
            $borderColor = '#f51f0b'; $badgeBg = '#fee2e2'; $badgeText = '#b91c1c';
        } elseif($p->status == 'menunggu') {
            $borderColor = '#f59e0b'; $badgeBg = '#fef3c7'; $badgeText = '#b45309';
        } elseif($p->status == 'diproses') {
            $borderColor = '#3b82f6'; $badgeBg = '#dbeafe'; $badgeText = '#1d4ed8';
        } else {
            $borderColor = '#10b981'; $badgeBg = '#d1fae5'; $badgeText = '#047857';
        }
    @endphp

    <!-- 🔥 TAMBAHAN CLASS 'order-card' & 'data-nama' BUAT SASARAN JAVASCRIPT -->
    <div class="card order-card" data-nama="{{ strtolower($p->nama_customer ?? 'tanpa nama') }}" style="border-left: 8px solid {{ $borderColor }};">
        
        <div class="flex justify-between items-start mb-sm">
            <div>
                <h3 class="order-id">#{{ $p->outlet == 'hasanuddin' ? 'TCH' : 'TCM' }}-{{ date('ym', strtotime($p->tanggal)) }}-{{ str_pad($p->no_urut_bulanan, 4, '0', STR_PAD_LEFT) }}</h3>
                <div class="order-customer">
                    👤 {{ $p->nama_customer ?? 'Tanpa Nama' }}
                </div>
            </div>
            <span class="badge badge-status" style="background: {{ $badgeBg }}; color: {{ $badgeText }};">
                {{ ucwords(str_replace('_', ' ', $p->status)) }}
            </span>
        </div>
        
        <p class="order-meta">
            Metode: <strong class="order-meta-method">{{ strtoupper($p->metode_pembayaran) }}</strong> <br>
            <strong class="order-meta-total">
                Total: Rp {{ number_format($p->total_harga, 0, ',', '.') }}
            </strong>
        </p>
        <hr class="order-divider">
        
        <ul class="order-items">
            @foreach($p->detailPenjualans as $d)
            <li class="order-item">
                <strong class="order-item-name">{{ $d->jumlah }}x {{ $d->produk->nama_produk }}</strong><br>
                <small class="order-item-detail">{{ ucfirst($d->ukuran) }} - {{ ucfirst($d->tipe) }}</small>
                @if($d->keterangan) 
                    <br><span class="order-item-note">📌 Req: {{ $d->keterangan }}</span> 
                @endif
            </li>
            @endforeach
        </ul>

        <div class="order-actions">
            
            @if($p->status == 'menunggu_pembayaran' && in_array($role, ['owner', 'kasir']))
                @if(strtolower($p->metode_pembayaran) == 'tunai')
                    <button type="button" class="btn btn-sm btn-success m-0" onclick="bukaModalKembalian('{{ route('admin.konfirmasi', $p->id) }}', {{ $p->total_harga }})">
                        Acc Tunai
                    </button>
                @endif
            @endif

            @if(in_array($role, ['owner', 'barista']))
                @if($p->status == 'menunggu')
                    <form action="{{ route('penjualan.updateStatus', $p->id) }}" method="POST" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="diproses">
                        <button type="submit" class="btn btn-sm btn-proses">Proses</button>
                    </form>
                @elseif($p->status == 'diproses')
                    <form action="{{ route('penjualan.updateStatus', $p->id) }}" method="POST" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="Siap diambil">
                        <button type="submit" class="btn btn-sm btn-siap">Siap Ambil</button>
                    </form>
                @endif
            @endif

            @if($p->status == 'Siap diambil' && in_array($role, ['owner', 'kasir']))
                <form action="{{ route('penjualan.updateStatus', $p->id) }}" method="POST" class="m-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="btn btn-sm btn-selesai">Selesai</button>
                </form>
            @endif

            <a href="{{ route('penjualan.show', $p->id) }}" class="btn btn-sm btn-detail">Detail</a>
            
        </div>
    </div>
    @endforeach
</div>

<!-- ======================================================== -->
<!-- 💡 MODAL POPUP HITUNG KEMBALIAN -->
<!-- ======================================================== -->
<div id="modalKembalian" class="modal-overlay">
    <div class="modal-container">
        
        <div class="modal-header">
            <h3>Hitung Kembalian</h3>
            <button onclick="tutupModal()" class="modal-close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="modal-tagihan">
                <p>Total Tagihan Pesanan:</p>
                <h2>Rp <span id="displayTotalTagihan">0</span></h2>
            </div>
            <hr class="modal-divider">
            <div id="area-tunai">
                <div class="form-group mb-md">
                    <label class="modal-input-label">Uang Diterima (Rp)</label>
                    <input type="number" id="uang-diterima" placeholder="Contoh: 50000" onkeyup="hitungKembalian()" class="modal-input-uang">
                    <div id="quick-cash-container" class="quick-cash-container"></div>
                </div>
                <div class="modal-kembalian-box">
                    <p>Uang Kembalian:</p>
                    <h2>Rp <span id="kembalian-display">0</span></h2>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="tutupModal()" class="btn-batal">Batal</button>
            <form id="formAccTunai" method="POST" class="m-0">
                @csrf
                <button type="submit" id="btnKonfirmasi" class="btn-konfirmasi" disabled>Selesai & ACC</button>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Notif Suara
    let latestOrderId = "{{ $penjualans->max('id') ?? 0 }}";
    let previousOrderId = localStorage.getItem('latestOrderId_POS');
    if (previousOrderId !== null && latestOrderId !== "0" && parseInt(latestOrderId) > parseInt(previousOrderId)) {
        let notifSound = new Audio("{{ asset('audio/ting-tiong.mp3') }}");
        notifSound.play().catch(function(error) { console.log("Autoplay ditahan."); });
    }
    localStorage.setItem('latestOrderId_POS', latestOrderId);

    // 2. LOGIKA FILTER REAL-TIME JAVASCRIPT 🔥
    function filterNama() {
        let input = document.getElementById('inputCariNama').value.toLowerCase();
        let cards = document.querySelectorAll('.order-card');
        
        cards.forEach(card => {
            // Ambil data nama dari atribut HTML yang udah disiapin
            let nama = card.getAttribute('data-nama');
            
            if (nama.includes(input)) {
                card.style.display = ''; // Munculin card
            } else {
                card.style.display = 'none'; // Sembunyiin card
            }
        });
    }

    // 3. Auto Refresh Ditahan Kalau Lagi Ngetik Filter
    let autoRefreshTimer = setInterval(function() { 
        let searchInput = document.getElementById('inputCariNama');
        // Jangan refresh kalau inputan lagi diklik, ATAU ada teks pencariannya
        if(document.activeElement !== searchInput && searchInput.value.trim() === '') {
            window.location.reload(); 
        }
    }, 15000);

    // 4. Modal Kembalian
    let totalTagihanSekarang = 0;
    function bukaModalKembalian(urlKonfirmasi, totalHarga) {
        clearInterval(autoRefreshTimer);
        totalTagihanSekarang = totalHarga;
        
        document.getElementById('displayTotalTagihan').innerText = totalHarga.toLocaleString('id-ID');
        document.getElementById('uang-diterima').value = '';
        document.getElementById('kembalian-display').innerText = '0';
        document.getElementById('kembalian-display').style.color = '#059669';
        document.getElementById('btnKonfirmasi').disabled = true; 
        document.getElementById('formAccTunai').action = urlKonfirmasi; 
        
        buatTombolQuickCash(totalHarga);
        document.getElementById('modalKembalian').style.display = 'flex';
        setTimeout(() => document.getElementById('uang-diterima').focus(), 100);
    }

    function tutupModal() {
        document.getElementById('modalKembalian').style.display = 'none';
        autoRefreshTimer = setInterval(function() { 
            let searchInput = document.getElementById('inputCariNama');
            if(document.activeElement !== searchInput && searchInput.value.trim() === '') {
                window.location.reload(); 
            }
        }, 15000);
    }

    function buatTombolQuickCash(totalHarga) {
        const container = document.getElementById('quick-cash-container');
        container.innerHTML = ''; 
        let btnUangPas = `<button type="button" onclick="setUangDiterima(${totalHarga})" style="padding: 6px 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 20px; font-size: 13px; cursor: pointer;">Uang Pas</button>`;
        container.innerHTML += btnUangPas;

        const pecahan = [20000, 50000, 100000];
        pecahan.forEach(nominal => {
            if (nominal > totalHarga) {
                let btnPecahan = `<button type="button" onclick="setUangDiterima(${nominal})" style="padding: 6px 12px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 20px; font-size: 13px; cursor: pointer;">${nominal.toLocaleString('id-ID')}</button>`;
                container.innerHTML += btnPecahan;
            }
        });
    }

    function setUangDiterima(nominal) {
        document.getElementById('uang-diterima').value = nominal;
        hitungKembalian(); 
    }

    function hitungKembalian() {
        let inputDuit = parseInt(document.getElementById('uang-diterima').value) || 0;
        let sisa = inputDuit - totalTagihanSekarang;
        let displayKembalian = document.getElementById('kembalian-display');
        let btnKonfirmasi = document.getElementById('btnKonfirmasi');

        if (sisa < 0) {
            displayKembalian.innerText = "Uang Kurang!";
            displayKembalian.style.color = "#dc2626";
            btnKonfirmasi.disabled = true;
        } else {
            displayKembalian.innerText = sisa.toLocaleString('id-ID');
            displayKembalian.style.color = "#059669";
            btnKonfirmasi.disabled = false;
        }
    }
</script>

@endsection