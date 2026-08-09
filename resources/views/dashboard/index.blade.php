@extends('layouts.pos')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .dashboard-wrapper { font-family: 'Poppins', sans-serif; color: #183f37; }
    
    /* Header & Filter */
    .dash-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
    .dash-title { margin: 0; font-size: 28px; font-weight: 700; }
    .dash-subtitle { color: #6b6256; font-size: 14px; margin-top: 5px; }
    
    .filter-box { background: white; padding: 15px 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; gap: 10px; align-items: center; }
    .filter-box select { padding: 8px 12px; border-radius: 8px; border: 1px solid #dcd3c6; outline: none; font-family: 'Poppins'; }
    .btn-filter { background: #183f37; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .btn-reset { background: #efe6d8; color: #183f37; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; text-decoration: none; font-weight: 600; }
    
    .outlet-badge { background: #e5f5ec; color: #0f7a3a; padding: 8px 16px; border-radius: 12px; font-weight: 600; font-size: 14px; border: 1px solid #b7e4c7; }

    /* Section Titles */
    .section-title { font-size: 18px; font-weight: 700; margin: 30px 0 15px; padding-bottom: 8px; border-bottom: 2px solid #eae5dc; display: flex; align-items: center; gap: 8px; }

    /* Grid Layouts */
    .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; }

    /* Stat Cards */
    .stat-card { background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 20px rgba(24,63,55,0.04); display: flex; flex-direction: column; position: relative; overflow: hidden; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(24,63,55,0.08); }
    .stat-icon { position: absolute; right: -10px; bottom: -10px; font-size: 80px; opacity: 0.05; }
    .stat-label { color: #6b6256; font-size: 14px; font-weight: 600; margin-bottom: 8px; z-index: 1; }
    .stat-value { font-size: 32px; font-weight: 700; color: #183f37; margin: 0; z-index: 1; }
    .stat-value.money { color: #e67e22; font-size: 28px; }

    /* Alerts */
    .alert-card { background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 20px rgba(24,63,55,0.04); height: 100%; }
    .alert-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .alert-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
    .alert-count { font-size: 24px; font-weight: 700; }
    
    .list-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eae5dc; font-size: 14px; }
    .list-item:last-child { border: none; }
    .empty-state { text-align: center; color: #8a8073; font-style: italic; padding: 20px 0; font-size: 14px; }

    /* Quick Action Button */
    .btn-quick { display: inline-flex; align-items: center; justify-content: center; background: #e67e22; color: white; text-decoration: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; transition: 0.2s; gap: 8px; }
    .btn-quick:hover { background: #cf711f; }
    
    @media (max-width: 768px) {
        .filter-box { width: 100%; flex-wrap: wrap; }
        .filter-box select { flex: 1; }
    }
</style>

<div class="dashboard-wrapper">

    <div class="dash-header">
        <div>
            <h1 class="dash-title">Halo, {{ ucfirst(auth()->user()->name ?? $role) }}! 👋</h1>
            <div class="dash-subtitle">Pantau aktivitas Terminal Coffee hari ini.</div>
        </div>

        @if(in_array($role, ['owner', 'operational_manager']))
            <form method="GET" action="{{ route('dashboard') }}" class="filter-box">
                <span style="font-weight: 600; font-size: 14px;">Filter Outlet:</span>
                <select name="outlet" onchange="this.form.submit()">
                    <option value="">Semua Outlet</option>
                    <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                    <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                </select>
                @if(request('outlet'))
                    <a href="{{ route('dashboard') }}" class="btn-reset">Reset Filter</a>
                @endif
            </form>
        @endif

        @if(in_array($role, ['kasir', 'barista']) && $outlet)
            <div class="outlet-badge">
                🏪 Outlet Aktif: {{ ucfirst($outlet) }}
            </div>
        @endif
    </div>


    @if(in_array($role, ['owner', 'operational_manager']))
        
        <div class="section-title">📊 Ringkasan Finansial & Transaksi {{ request('outlet') ? '('.ucfirst(request('outlet')).')' : '(Seluruh Outlet)' }}</div>
        
        <div class="grid-stats">
            <div class="stat-card">
                <div class="stat-label">Pendapatan Hari Ini</div>
                <div class="stat-value money">Rp {{ number_format($pendapatanHariIni) }}</div>
                <div class="stat-icon">💵</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pendapatan Bulan Ini</div>
                <div class="stat-value money">Rp {{ number_format($pendapatanBulanIni) }}</div>
                <div class="stat-icon">💰</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ $totalPenjualan }} <span style="font-size: 14px; font-weight: normal; color: #6b6256;">Pesanan</span></div>
                <div class="stat-icon">🧾</div>
            </div>
        </div>

        <div class="grid-2" style="margin-bottom: 30px;">
            
            <div class="card" style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 20px rgba(24,63,55,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 700;">📈 Pendapatan Outlet</h3>
                        <small style="color: #64748b; font-size: 12px;">
                            {{ request('outlet') ? 'Menampilkan data '.ucfirst(request('outlet')) : 'Perbandingan Hasanuddin vs Makmur' }}
                        </small>
                    </div>
                    
                    <form method="GET" action="{{ route('dashboard') }}" id="formChartFilter">
                        @if(request('outlet')) <input type="hidden" name="outlet" value="{{ request('outlet') }}"> @endif
                        <select name="chart_filter" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #dcd3c6; font-size: 12px; font-family: 'Poppins'; font-weight: 600;">
                            <option value="7days" {{ ($chartFilter ?? '7days') == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="30days" {{ ($chartFilter ?? '') == '30days' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                            <option value="1year" {{ ($chartFilter ?? '') == '1year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                        </select>
                    </form>
                </div>
                <canvas id="revenueChart" height="160"></canvas>
            </div>

            <div class="card" style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 20px rgba(24,63,55,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700;">🏆 Top 5 Menu Terlaris</h3>
                    <span style="font-size: 12px; background: #fff3d8; color: #b56a00; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                        {{ request('outlet') ? ucfirst(request('outlet')) : 'Semua Outlet' }}
                    </span>
                </div>
                <div>
                    @if(isset($topProduk) && count($topProduk) > 0)
                        @foreach($topProduk as $index => $tp)
                            <div class="list-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #eae5dc;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #fff3d8; color: #b56a00; font-weight: 700; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">#{{ $index + 1 }}</div>
                                    <strong style="font-size: 14px; color: #183f37;">{{ $tp->produk->nama_produk ?? 'Produk Dihapus' }}</strong>
                                </div>
                                <div style="font-weight: 700; font-size: 14px; color: #e67e22;">{{ $tp->total_terjual }} Terjual</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">Belum ada data penjualan selesai.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="section-title">☕ Data & Inventori</div>
        <div class="grid-stats">
            <div class="stat-card">
                <div class="stat-label">Total Produk Menu</div>
                <div class="stat-value">{{ $totalProduk }}</div>
                <div class="stat-icon">🥤</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Jenis Bahan Baku</div>
                <div class="stat-value">{{ $totalBahanBaku }}</div>
                <div class="stat-icon">📦</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pengadaan</div>
                <div class="stat-value">{{ $totalPembelian }}</div>
                <div class="stat-icon">🛒</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Distribusi</div>
                <div class="stat-value">{{ $totalDistribusi }}</div>
                <div class="stat-icon">🚚</div>
            </div>
        </div>
    @endif


    @if($role == 'kasir')
        <div class="dash-header" style="margin-top: 10px;">
            <a href="{{ route('penjualan.create') }}" class="btn-quick">➕ Buat Pesanan Baru</a>
        </div>

        <div class="section-title">💸 Laporan Kasir Hari Ini</div>
        <div class="grid-stats">
            <div class="stat-card">
                <div class="stat-label">Pendapatan Masuk</div>
                <div class="stat-value money">Rp {{ number_format($pendapatanHariIni) }}</div>
                <div class="stat-icon">💵</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pesanan Selesai</div>
                <div class="stat-value">{{ $pesananSelesaiHariIni }}</div>
                <div class="stat-icon">🏁</div>
            </div>
        </div>

        <div class="section-title">📡 Radar Antrean</div>
        <div class="grid-stats">
            <div class="stat-card" style="border-bottom: 5px solid #f51f0b;">
                <div class="stat-label">Menunggu Pembayaran (QRIS/Tunai)</div>
                <div class="stat-value">{{ $pesananMenunggu }}</div>
                <div class="stat-icon">🕒</div>
            </div>
            <div class="stat-card" style="border-bottom: 5px solid #10b981;">
                <div class="stat-label">Siap Diambil Customer</div>
                <div class="stat-value">{{ $pesananSiapDiambil }}</div>
                <div class="stat-icon">📢</div>
            </div>
        </div>
    @endif


    @if($role == 'barista')
        @if(session('outlet_aktif') == 'event')
            <div class="section-title">🎪 Ringkasan Tugas Event</div>
            <div class="grid-stats">
                <div class="stat-card" style="background: #e0f2fe; border: 1px solid #bae6fd;">
                    <div class="stat-label" style="color: #0284c7;">Tugas Event (Menunggu Eksekusi)</div>
                    @php 
                        $tugasBarista = \App\Models\Event::where('status', 'diserahkan')->count(); 
                    @endphp
                    <div class="stat-value" style="color: #0284c7;">{{ $tugasBarista }}</div>
                    <div class="stat-icon">🎪</div>
                </div>
            </div>
            <div style="margin-top: 5px; margin-bottom: 30px;">
                <a href="{{ route('event.tugas') }}" class="btn-quick" style="background: #0284c7;">🚀 Buka Papan Tugas Event</a>
            </div>
        @else
            <div class="section-title">📡 Live Radar Dapur</div>
            <div class="grid-stats">
                <div class="stat-card" style="background: #fff3d8; border: 1px solid #fce3b8;">
                    <div class="stat-label" style="color: #b56a00;">Antrean Masuk (Menunggu)</div>
                    <div class="stat-value" style="color: #b56a00;">{{ $pesananMenunggu }}</div>
                    <div class="stat-icon">📝</div>
                </div>
                <div class="stat-card" style="background: #e0f2fe; border: 1px solid #bae6fd;">
                    <div class="stat-label" style="color: #0284c7;">Sedang Diracik (Diproses)</div>
                    <div class="stat-value" style="color: #0284c7;">{{ $pesananDiproses }}</div>
                    <div class="stat-icon">☕</div>
                </div>
                <div class="stat-card" style="background: #e5f5ec; border: 1px solid #b7e4c7;">
                    <div class="stat-label" style="color: #0f7a3a;">Siap Diambil</div>
                    <div class="stat-value" style="color: #0f7a3a;">{{ $pesananSiapDiambil }}</div>
                    <div class="stat-icon">✅</div>
                </div>
            </div>
        @endif
    @endif


    @if($role == 'logistik')
        <div class="section-title">📦 Aktivitas Gudang</div>
        <div class="grid-stats">
            <div class="stat-card">
                <div class="stat-label">Jenis Bahan Baku</div>
                <div class="stat-value">{{ $totalBahanBaku }}</div>
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pengadaan ke Vendor</div>
                <div class="stat-value">{{ $totalPembelian }}</div>
                <div class="stat-icon">🛒</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Distribusi ke Outlet</div>
                <div class="stat-value">{{ $totalDistribusi }}</div>
                <div class="stat-icon">🚚</div>
            </div>
        </div>
    @endif


    @if(in_array($role, ['owner', 'operational_manager', 'logistik', 'barista']) && session('outlet_aktif') != 'event')
        <div class="section-title">⚠️ Pantauan Stok Inventori</div>
        <div class="grid-2">
            
            <div class="alert-card" style="border-top: 5px solid #f59e0b;">
                <div class="alert-header">
                    <h3 style="color: #d97706;">Stok Menipis (Hampir Habis)</h3>
                    <div class="alert-count" style="color: #d97706;">{{ $stokMenipis }}</div>
                </div>
                <div>
                    @forelse($stokMenipisList as $item)
                        <div class="list-item">
                            <div>
                                <strong>{{ $item->nama_bahan }}</strong><br>
                                <span style="color: #6b6256; font-size: 12px;">{{ ucfirst($item->outlet) }}</span>
                            </div>
                            <div style="font-weight: 600; color: #d97706;">
                                Sisa: {{ $item->stok }} {{ $item->satuan }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">✅ Stok aman, tidak ada yang menipis.</div>
                    @endforelse
                </div>
            </div>

            <div class="alert-card" style="border-top: 5px solid #ef4444;">
                <div class="alert-header">
                    <h3 style="color: #dc2626;">Stok Kritis (Habis Total)</h3>
                    <div class="alert-count" style="color: #dc2626;">{{ $stokHabis }}</div>
                </div>
                <div>
                    @forelse($stokHabisList as $item)
                        <div class="list-item">
                            <div>
                                <strong>{{ $item->nama_bahan }}</strong><br>
                                <span style="color: #6b6256; font-size: 12px;">{{ ucfirst($item->outlet) }}</span>
                            </div>
                            <div style="font-weight: 600; color: #dc2626;">
                                KOSONG!
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">✅ Tidak ada stok yang habis.</div>
                    @endforelse
                </div>
            </div>

        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(in_array($role, ['owner', 'operational_manager']) && isset($chartDates))
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const pointSize = "{{ $chartFilter ?? '7days' }}" === '30days' ? 1 : 4;

    const datasets = [];

    // 🔥 LINE 1: HASANUDDIN (Warna Hijau #183f37)
    @if(!request('outlet') || request('outlet') == 'hasanuddin')
    datasets.push({
        label: 'Outlet Hasanuddin',
        data: {!! json_encode($chartDataHasanuddin ?? []) !!},
        borderColor: '#183f37',
        backgroundColor: 'rgba(24, 63, 55, 0.05)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#183f37',
        pointRadius: pointSize
    });
    @endif

    // 🔥 LINE 2: MAKMUR (Warna Oranye #e67e22)
    @if(!request('outlet') || request('outlet') == 'makmur')
    datasets.push({
        label: 'Outlet Makmur',
        data: {!! json_encode($chartDataMakmur ?? []) !!},
        borderColor: '#e67e22',
        backgroundColor: 'rgba(230, 126, 34, 0.05)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#e67e22',
        pointRadius: pointSize
    });
    @endif

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDates) !!},
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { 
                    display: true,
                    position: 'top',
                    labels: {
                        font: { family: 'Poppins', weight: '600', size: 12 },
                        usePointStyle: true,
                        boxWidth: 8
                    }
                } 
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        font: { family: 'Poppins' },
                        callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } 
                    } 
                },
                x: {
                    ticks: { font: { family: 'Poppins' } }
                }
            }
        }
    });
    @endif
</script>

@endsection