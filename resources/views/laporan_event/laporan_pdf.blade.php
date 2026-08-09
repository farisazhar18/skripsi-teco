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

<h1 class="page-title">Laporan Rekapitulasi Event</h1>

<div class="card" style="margin-bottom: 20px;">
    <form action="{{ route('event.laporan') }}" method="GET">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Bulan</label>
                <select name="bulan" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 120px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Tahun</label>
                <select name="tahun" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Filter Outlet</label>
                <select name="outlet" onchange="this.form.submit()" style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; outline: none;">
                    <option value="">Semua Outlet</option>
                    <option value="hasanuddin" {{ request('outlet') == 'hasanuddin' ? 'selected' : '' }}>Hasanuddin</option>
                    <option value="makmur" {{ request('outlet') == 'makmur' ? 'selected' : '' }}>Makmur</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">Cari Nama Event</label>
                <input type="text" name="search_event" id="inputCariEvent" oninput="filterEvent()" value="{{ request('search_event') }}" placeholder="Ketik nama event..." style="width: 100%; height: 42px; padding: 0 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            </div>

            <div style="display: flex; gap: 8px; align-items: flex-end;">
                @if(request('bulan') || request('tahun') || request('outlet') || request('search_event'))
                    <a href="{{ route('event.laporan') }}" class="btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 8px; border: 1px solid #cbd5e1; text-decoration: none; color: #475569; font-weight: 600;">
                        Reset
                    </a>
                @endif
            </div>

        </div>
    </form>
</div>

<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: flex-end;">
    <a href="/event/laporan/pdf?bulan={{ request('bulan') }}&tahun={{ request('tahun') }}&outlet={{ request('outlet') }}&search_event={{ request('search_event') }}" target="_blank" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; background: #efe6d8; text-decoration: none; font-weight: 600; color: #183f37; border: 1px solid #d8cbb8; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: center; height: 42px;">
        📥 Export PDF
    </a>
</div>

<div class="table-card" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; overflow-x: auto;">
    <table style="border-collapse: collapse; width: 100%; min-width: 950px;">

        <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #1e293b;">
            <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
            <th style="padding: 12px 15px; text-align: center; width: 15%;">Tanggal Event</th>
            <th style="padding: 12px 15px; text-align: left; width: 25%;">Nama Event</th>
            <th style="padding: 12px 15px; text-align: left; width: 20%;">Penyelenggara</th>
            <th style="padding: 12px 15px; text-align: center; width: 12%;">Outlet</th>
            <th style="padding: 12px 15px; text-align: center; width: 13%;">Status</th>
            <th style="padding: 12px 15px; text-align: center; width: 10%;">Aksi</th>
        </tr>

        @forelse($events as $event)
        <tr class="row-item" data-nama="{{ strtolower($event->nama_event ?? '') }} {{ strtolower($event->penyelenggara ?? '') }}">
            <td style="text-align: center; color: #475569; padding: 12px 15px; vertical-align: middle;">
                {{ $loop->iteration }}
            </td>

            <td style="text-align: center; color: #1e293b; font-weight: 500; padding: 12px 15px; vertical-align: middle;">
                {{ date('d-m-Y', strtotime($event->tanggal_event)) }}
            </td>

            <td style="font-weight: 600; color: #1e293b; padding: 12px 15px; vertical-align: middle;">
                {{ $event->nama_event }}
            </td>

            <td style="color: #475569; padding: 12px 15px; vertical-align: middle;">
                {{ $event->penyelenggara ?? '-' }}
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                <span style="font-weight: 600; color: #183f37;">{{ ucfirst($event->outlet) }}</span>
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                @if($event->status == 'selesai')
                    <span class="badge-modern" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">✅ Selesai</span>
                @else
                    <span class="badge-modern" style="background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">⏳ {{ str_replace('_', ' ', strtoupper($event->status)) }}</span>
                @endif
            </td>

            <td style="text-align: center; padding: 12px 15px; vertical-align: middle;">
                <a href="{{ route('event.show', $event->id) }}" style="background: #e0e7ff; color: #3730a3; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #c7d2fe; display: inline-block;">
                    Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding: 30px; color: #6b7280; font-style: italic;">
                🎪 Data laporan rekapitulasi event tidak ditemukan.
            </td>
        </tr>
        @endforelse

    </table>
</div>

<script>
    function filterEvent() {
        let input = document.getElementById('inputCariEvent').value.toLowerCase();
        let rows = document.querySelectorAll('.row-item');
        
        rows.forEach(row => {
            let nama = row.getAttribute('data-nama');
            
            if (nama && nama.includes(input)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    }

    // Biar pas halaman diload, kalau inputannya ada isinya langsung nyaring otomatis
    window.onload = function() {
        if(document.getElementById('inputCariEvent').value !== '') {
            filterEvent();
        }
    };
</script>

@endsection