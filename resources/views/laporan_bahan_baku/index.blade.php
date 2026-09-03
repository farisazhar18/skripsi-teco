@extends('layouts.pos')

@section('content')

<style>
    /* 🎨 CSS BIAR UI MAKIN SMOOTH & MODERN */
    .row-item:hover {
        background-color: #f0f5f3; 
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb; 
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px; 
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        display: inline-block;
        text-align: center;
    }
</style>

<h1 class="page-title">Laporan Bahan Baku</h1>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="/laporan-bahan-baku">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 180px;">
                <label style="font-weight: 600; color: #334155;">Filter Outlet</label>
                <select name="outlet" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 9px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">Semua Outlet</option>
                    <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                    <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-weight: 600; color: #334155;">Filter Status Stok</label>
                <select name="status_stok" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 9px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">Semua Status</option>
                    <option value="menipis_habis" {{ request('status_stok') == 'menipis_habis' ? 'selected' : '' }}>⚠️ Menipis & Habis</option>
                    <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>🔴 Stok Habis</option>
                    <option value="menipis" {{ request('status_stok') == 'menipis' ? 'selected' : '' }}>🟡 Stok Menipis</option>
                    <option value="aman" {{ request('status_stok') == 'aman' ? 'selected' : '' }}>🟢 Stok Aman</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 220px;">
                <label style="font-weight: 600; color: #334155;">Cari Bahan Baku</label>
                <input type="text" name="search_bahan" id="inputCariBahan" oninput="filterBahan()" value="{{ request('search_bahan') }}" placeholder="Ketik nama bahan..." style="width: 100%; padding: 9px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div style="display: flex; gap: 8px;">
                @if(request('outlet') || request('status_stok') || request('search_bahan'))
                    <a href="/laporan-bahan-baku" class="btn-secondary" style="padding: 9px 15px; border-radius: 8px; border: 1px solid #cbd5e1; text-decoration: none; color: #475569; font-weight: 600; display: inline-block;">
                        Reset
                    </a>
                @endif

            </div>

        </div>
    </form>
</div>

<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: flex-end;">
    <!-- Bagian Kanan: Tombol Export PDF -->
    <a href="/laporan-bahan-baku/pdf?outlet={{ request('outlet') }}&status_stok={{ request('status_stok') }}&search_bahan={{ request('search_bahan') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; background: #efe6d8; text-decoration: none; font-weight: 600; color: #183f37; border: 1px solid #d8cbb8; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        📥 Export PDF
    </a>
</div>

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Outlet</th>
                <th style="padding: 12px 15px; text-align: left; width: 35%;">Nama Bahan Baku</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Stok Sekarang</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Stok Minimum</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr class="row-item" data-nama="{{ strtolower($item->nama_bahan) }}">
                <td style="padding: 12px 15px; text-align: center; color: #64748b;">{{ $loop->iteration }}</td>

                <td style="padding: 12px 15px; text-align: center; font-weight: 600;">
                    @if($item->outlet == 'hasanuddin')
                        <span style="color: #b45309;">Hasanuddin</span>
                    @elseif($item->outlet == 'makmur')
                        <span style="color: #183f37;">Makmur</span>
                    @else
                        -
                    @endif
                </td>

                <td style="padding: 12px 15px; font-weight: 600; color: #1e293b;">
                    {{ $item->nama_bahan }}
                </td>

                <td style="padding: 12px 15px; text-align: center; font-weight: bold;">
                    <span style="{{ $item->stok <= 0 ? 'color: #dc2626;' : ($item->stok < $item->stok_minimum ? 'color: #d97706;' : 'color: #0f7a3a;') }}">
                        {{ $item->stok }}
                    </span>
                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">{{ $item->satuan }}</span>
                </td>

                <td style="padding: 12px 15px; text-align: center; color: #64748b;">
                    {{ $item->stok_minimum }} <span style="font-size: 12px;">{{ $item->satuan }}</span>
                </td>

                <td style="padding: 12px 15px; text-align: center;">
                    @if($item->stok <= 0)
                        <span class="badge-modern" style="background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                            Stok Habis
                        </span>
                    @elseif($item->stok < $item->stok_minimum)
                        <span class="badge-modern" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fcd34d;">
                            Stok Menipis
                        </span>
                    @else
                        <span class="badge-modern" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">
                            Stok Aman
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #64748b; font-style: italic;">
                    Tidak ada data bahan baku yang sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function filterBahan() {
        let input = document.getElementById('inputCariBahan').value.toLowerCase();
        let rows = document.querySelectorAll('.row-item');
        
        rows.forEach(row => {
            // Ngambil data nama bahan baku dari elemen <tr> yang udah kita setting
            let nama = row.getAttribute('data-nama');
            
            if (nama.includes(input)) {
                row.style.display = ''; // Munculin kalau hurufnya cocok
            } else {
                row.style.display = 'none'; // Sembunyiin kalau beda
            }
        });
    }

    // Biar pas halaman diload (misal pas baru reset), kalau kotak pencarian ada isinya, dia otomatis nyesuain
    window.onload = function() {
        if(document.getElementById('inputCariBahan').value !== '') {
            filterBahan();
        }
    };
</script>

@endsection