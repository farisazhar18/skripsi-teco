<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use App\Models\PengajuanStok;
use App\Models\RiwayatPenggunaanBahan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Distribusi;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = BahanBaku::where('is_active', true);
        
        // Buat narik daftar nama bahan baku (buat ngisi dropdown TomSelect)
        $listBahanQuery = BahanBaku::where('is_active', true)->select('nama_bahan')->distinct();

        // Filter Outlet
        if ($user->role == 'barista') {
            $query->where('outlet', session('outlet_aktif'));
            $listBahanQuery->where('outlet', session('outlet_aktif'));
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
            }
        }

        // Ambil list bahan baku yang udah di-filter buat dilempar ke dropdown
        $listBahan = $listBahanQuery->orderBy('nama_bahan', 'asc')->get();

        // Terapkan Filter Pencarian (TomSelect) jika user mengetik sesuatu
        if ($request->search_bahan) {
            $query->where('nama_bahan', $request->search_bahan);
        }

        // Eksekusi query untuk dapetin data tabel
        $data = $query->orderBy('outlet', 'asc')
            ->orderBy('kategori', 'asc') // <--- TAMBAHIN BARIS INI
            ->orderBy('nama_bahan', 'asc')
            ->get();

        $outlet = $user->role == 'barista'
            ? session('outlet_aktif')
            : $request->outlet;
            
        // Ambil kata pencarian biar nempel di dropdown (nggak ilang pas di-refresh)
        $search_bahan = $request->search_bahan;

        return view('bahan_baku.index', compact('data', 'outlet', 'listBahan', 'search_bahan'));
    }

    public function create()
    {
        $user = auth()->user();

        // KUNCI PINTU MASUK: Tolak selain Logistik, Manager, dan Owner
        if ($user->role !== 'logistik' && $user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Akses Ditolak: Hanya divisi Logistik dan Manager yang dapat menambah master data bahan baku baru.');
        }

        return view('bahan_baku.create');
    }

    public function store(Request $request)
    {

        $user = auth()->user();

        if ($user->role !== 'logistik' && $user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Akses Ditolak.');
        }

        // 1. Validasi Inputan
        $request->validate([
            'outlet' => 'required',
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'required',
            'satuan' => 'required',
            'stok' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
        ]);

        // 2. Cek apakah usernya milih "Semua Outlet"
        if ($request->outlet == 'semua') {
            
            // Simpan buat Hasanuddin
            BahanBaku::create([
                'outlet' => 'hasanuddin',
                'nama_bahan' => $request->nama_bahan,
                'kategori' => $request->kategori, // <--- TAMBAHIN INI
                'satuan' => $request->satuan,
                'stok' => $request->stok,
                'stok_minimum' => $request->stok_minimum,
            ]);

            // Simpan buat Makmur
            BahanBaku::create([
                'outlet' => 'makmur',
                'nama_bahan' => $request->nama_bahan,
                'kategori' => $request->kategori, // <--- TAMBAHIN INI
                'satuan' => $request->satuan,
                'stok' => $request->stok,
                'stok_minimum' => $request->stok_minimum,
            ]);

        } else {
            // 3. Kalau cuma milih salah satu
            BahanBaku::create([
                'outlet' => $request->outlet,
                'nama_bahan' => $request->nama_bahan,
                'kategori' => $request->kategori, // <--- TAMBAHIN INI
                'satuan' => $request->satuan,
                'stok' => $request->stok,
                'stok_minimum' => $request->stok_minimum,
            ]);
        }

        return redirect('/bahan-baku')->with('success', 'Bahan Baku berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = BahanBaku::findOrFail($id);

        if (auth()->user()->role == 'barista' && $data->outlet != session('outlet_aktif')) {
            abort(403);
        }

        return view('bahan_baku.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $user = auth()->user();

        // =========================================================
        // 1. JIKA YANG SUBMIT ADALAH BOS/LOGISTIK (FULL EDIT)
        // =========================================================
        if ($request->tipe_form == 'full_edit') {
            
            $request->validate([
                'nama_bahan' => 'required|string|max:255',
                'outlet' => 'required',
                'kategori' => 'required',
                'satuan' => 'required',
                'stok' => 'required|numeric|min:0',
                'stok_minimum' => 'required|numeric|min:0',
            ]);

            $bahanBaku->update([
                'nama_bahan' => $request->nama_bahan,
                'outlet' => $request->outlet,
                'kategori' => $request->kategori, // <--- TAMBAHIN INI JUGA
                'satuan' => $request->satuan,
                'stok' => $request->stok,
                'stok_minimum' => $request->stok_minimum,
            ]);

            return redirect('/bahan-baku')->with('success', 'Bahan Baku berhasil diupdate!');
        } 
        
        // =========================================================
        // 2. JIKA YANG SUBMIT ADALAH BARISTA (PENYESUAIAN STOK)
        // =========================================================
        else {
            // Validasi input dari form pengajuan (termasuk foto bukti)
            $request->validate([
                'stok_aktual' => 'required|integer|min:0',
                'alasan' => 'required|string',
                'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
            ]);

            // 🔥 LOGIKA UPLOAD FOTO BUKTI 🔥
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                // Foto disimpan ke folder: storage/app/public/bukti_stok
                $fotoPath = $request->file('foto_bukti')->store('bukti_stok', 'public');
            }

            // Simpan sebagai pengajuan baru ke tabel pengajuan_stoks
            PengajuanStok::create([
                'bahan_baku_id' => $bahanBaku->id,
                'outlet' => $bahanBaku->outlet,
                'stok_seharusnya' => $bahanBaku->stok,
                'stok_aktual' => $request->stok_aktual,
                'alasan' => $request->alasan,
                'foto_bukti' => $fotoPath, // <--- TAMPUNG PATH FOTO DI SINI
                'status' => 'pending',
                'user_id' => $user->id,
            ]);

            return redirect('/bahan-baku')->with('success', 'Pengajuan penyesuaian stok & bukti foto berhasil dikirim ke Manager.');
        }
    }

    public function destroy(string $id)
    {

        $user = auth()->user();

        // KUNCI PINTU HAPUS: Tolak selain Logistik, Manager, dan Owner
        if ($user->role !== 'logistik' && $user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Akses Ditolak: Hanya divisi Logistik dan Manajemen yang dapat menonaktifkan master data bahan baku.');
        }

        $data = BahanBaku::findOrFail($id);

        if (auth()->user()->role == 'barista' && $data->outlet != session('outlet_aktif')) {
            abort(403);
        }

        // $data->delete(); // SEKARANG KITA NONAKTIFKAN BUKAN HAPUS PERMANEN
        $data->is_active = false;
        $data->save();

        return redirect('/bahan-baku')->with('success', 'Bahan Baku berhasil dinonaktifkan.');
    }

    public function indexNonaktif(Request $request)
    {
        $user = auth()->user();

        // Hanya manajemen yang bisa lihat list nonaktif
        if ($user->role !== 'logistik' && $user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Akses Ditolak.');
        }

        $query = BahanBaku::where('is_active', false);
        
        if ($request->outlet) {
            $query->where('outlet', $request->outlet);
        }

        $data = $query->orderBy('outlet', 'asc')
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_bahan', 'asc')
            ->get();

        $outlet = $request->outlet;

        return view('bahan_baku.nonaktif', compact('data', 'outlet'));
    }

    public function aktifkan(string $id)
    {
        $user = auth()->user();

        // KUNCI PINTU AKTIFKAN: Hanya Logistik, Manager, dan Owner
        if ($user->role !== 'logistik' && $user->role !== 'operational_manager' && $user->role !== 'owner') {
            abort(403, 'Akses Ditolak.');
        }

        $data = BahanBaku::findOrFail($id);
        $data->is_active = true;
        $data->save();

        return redirect()->route('bahan-baku.nonaktif')->with('success', 'Bahan Baku berhasil diaktifkan kembali.');
    }

    public function rekapHarian(Request $request)
    {
        $user = auth()->user();
        $outletAktif = session('outlet_aktif');

        if (in_array($user->role, ['logistik', 'operational_manager', 'owner'])) {
            $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        } else {
            $tanggal = Carbon::today();
        }

        $query = RiwayatPenggunaanBahan::with('bahanBaku')
                    ->whereDate('created_at', $tanggal);

        $queryPengajuan = PengajuanStok::with('bahanBaku')
                    ->whereDate('created_at', $tanggal);

        // =========================================================
        // LOGIKA FILTER OUTLET
        // =========================================================
        if ($user->role == 'barista') {
            if (!$outletAktif) {
                abort(403, 'Pilih outlet terlebih dahulu.');
            }
            $query->where('outlet', $outletAktif);
            $queryPengajuan->where('outlet', $outletAktif);
        } else {
            // Kalau Manajemen milih outlet tertentu dari dropdown
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
                $queryPengajuan->where('outlet', $request->outlet);
            }
        }

        $riwayat = $query->latest()->get();
        $dataPengajuan = $queryPengajuan->latest()->get();

        $rekapMap = [];

        foreach ($riwayat as $item) {
            $id = $item->bahan_baku_id;
            if (!isset($rekapMap[$id])) {
                $rekapMap[$id] = [
                    'nama_bahan' => $item->bahanBaku->nama_bahan ?? 'Bahan Terhapus',
                    'satuan' => $item->bahanBaku->satuan ?? '',
                    'total_terpakai' => 0,
                    'outlet' => $item->outlet
                ];
            }
            $rekapMap[$id]['total_terpakai'] += $item->jumlah_terpakai;
        }

        foreach ($dataPengajuan as $item) {
            $id = $item->bahan_baku_id;
            if (!isset($rekapMap[$id])) {
                $rekapMap[$id] = [
                    'nama_bahan' => $item->bahanBaku->nama_bahan ?? 'Bahan Terhapus',
                    'satuan' => $item->bahanBaku->satuan ?? '',
                    'total_terpakai' => 0,
                    'outlet' => $item->outlet
                ];
            }
            $selisih = $item->stok_seharusnya - $item->stok_aktual;
            $rekapMap[$id]['total_terpakai'] += $selisih;
        }

        $rekapTotal = collect($rekapMap)->values();

        $tanggalString = $tanggal->translatedFormat('l, d F Y');
        $isHariIni = $tanggal->isToday();

        return view('bahan_baku.rekap_harian', compact('riwayat', 'rekapTotal', 'tanggalString', 'isHariIni', 'dataPengajuan'));
    }

    public function rekapHarianPdf(Request $request)
    {
        $user = auth()->user();
        $outletAktif = session('outlet_aktif');

        if (in_array($user->role, ['logistik', 'operational_manager', 'owner'])) {
            $tanggalTarget = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        } else {
            $tanggalTarget = Carbon::today();
        }

        $query = RiwayatPenggunaanBahan::with('bahanBaku')
                    ->whereDate('created_at', $tanggalTarget);
                    
        $queryPengajuan = PengajuanStok::with('bahanBaku')
                    ->whereDate('created_at', $tanggalTarget);

        // FILTER OUTLET BUAT PDF JUGA SAMA
        if ($user->role == 'barista') {
            if (!$outletAktif) {
                abort(403, 'Pilih outlet terlebih dahulu.');
            }
            $query->where('outlet', $outletAktif);
            $queryPengajuan->where('outlet', $outletAktif);
        } else {
            if ($request->outlet) {
                $query->where('outlet', $request->outlet);
                $queryPengajuan->where('outlet', $request->outlet);
            }
        }
        
        $riwayat = $query->get();
        $dataPengajuan = $queryPengajuan->get();

        $rekapMap = [];

        foreach ($riwayat as $item) {
            $id = $item->bahan_baku_id;
            if (!isset($rekapMap[$id])) {
                $rekapMap[$id] = [
                    'nama_bahan' => $item->bahanBaku->nama_bahan ?? 'Bahan Terhapus',
                    'satuan' => $item->bahanBaku->satuan ?? '',
                    'total_terpakai' => 0,
                    'outlet' => $item->outlet
                ];
            }
            $rekapMap[$id]['total_terpakai'] += $item->jumlah_terpakai;
        }

        foreach ($dataPengajuan as $item) {
            $id = $item->bahan_baku_id;
            if (!isset($rekapMap[$id])) {
                $rekapMap[$id] = [
                    'nama_bahan' => $item->bahanBaku->nama_bahan ?? 'Bahan Terhapus',
                    'satuan' => $item->bahanBaku->satuan ?? '',
                    'total_terpakai' => 0,
                    'outlet' => $item->outlet
                ];
            }
            $selisih = $item->stok_seharusnya - $item->stok_aktual;
            $rekapMap[$id]['total_terpakai'] += $selisih;
        }

        $rekapTotal = collect($rekapMap)->values();

        $tanggal = $tanggalTarget->translatedFormat('l, d F Y');
        $namaFileTanggal = $tanggalTarget->format('Y-m-d');

        $pdf = Pdf::loadView('bahan_baku.rekap_harian_pdf', compact('rekapTotal', 'tanggal'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Rekap_Pemakaian_Bahan_' . $namaFileTanggal . '.pdf');
    }

    public function rekapMasuk(Request $request)
    {
        // Cek outlet aktif
        $outlet = session('outlet_aktif'); 
        
        if (!$outlet && $request->has('outlet')) {
            $outlet = $request->outlet;
        }

        //  AMBIL TANGGAL DARI REQUEST, KALAU KOSONG DEFAULT HARI INI
        $tanggal = $request->tanggal ? \Carbon\Carbon::parse($request->tanggal) : \Carbon\Carbon::today();
        
        // Buat nampilin teks dinamis di view (Data Hari Ini vs Data Tanggal: ...)
        $isHariIni = $tanggal->isToday();
        $tanggalString = $tanggal->format('d M Y');

        // Tarik data berdasarkan tanggal yang dipilih
        $query = \App\Models\Distribusi::with('bahanBaku')
            ->whereDate('created_at', $tanggal);

        if ($outlet && $outlet !== 'semua') {
            $query->where('outlet', $outlet);
        }

        $barangMasuk = $query->latest()->get();

        // Lempar variabel tambahannya ke view
        return view('bahan_baku.masuk', compact('barangMasuk', 'isHariIni', 'tanggalString'));
    }

    // =========================================================
    // FUNGSI LAPOR SELISIH DISTRIBUSI (BARISTA)
    // =========================================================
    public function laporSelisih(Request $request, $distribusi_id)
    {
        $distribusi = \App\Models\Distribusi::with('bahanBaku')->findOrFail($distribusi_id);
        $bahanBaku = $distribusi->bahanBaku;
        
        // Pengecekan outlet agar barista tidak bisa lapor outlet lain
        if (auth()->user()->role == 'barista' && $distribusi->outlet != session('outlet_aktif')) {
            abort(403);
        }

        $dikirim = $distribusi->jumlah;
        $tanggal = $distribusi->created_at->format('Y-m-d');

        return view('bahan_baku.lapor_selisih', compact('bahanBaku', 'dikirim', 'tanggal', 'distribusi'));
    }

    public function storeSelisih(Request $request, $distribusi_id)
    {
        $distribusi = \App\Models\Distribusi::findOrFail($distribusi_id);
        $bahanBaku = BahanBaku::findOrFail($distribusi->bahan_baku_id);
        
        $request->validate([
            'fisik_diterima' => 'required|numeric|min:0',
            'alasan' => 'required|string',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dikirim = $distribusi->jumlah;
        $diterima = $request->fisik_diterima;

        // KALKULASI MATEMATIKA: 
        // Stok di sistem saat ini (sudah ditambah $dikirim oleh logistik)
        $stokSekarang = $bahanBaku->stok; 
        
        // Berapa barang yang hilang / kurang
        $selisihHilang = $dikirim - $diterima;

        // Stok yang benar-benar ada di toko (Stok Aktual)
        $stokAktual = $stokSekarang - $selisihHilang;

        // Upload Bukti
        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('bukti_stok', 'public');
        }

        // Buat Pengajuan Stok
        PengajuanStok::create([
            'bahan_baku_id' => $bahanBaku->id,
            'outlet' => $bahanBaku->outlet,
            'stok_seharusnya' => $stokSekarang, // Stok sebelum disesuaikan
            'stok_aktual' => $stokAktual, // Stok hasil pengurangan barang hilang
            'alasan' => "Selisih Distribusi tgl " . $distribusi->created_at->format('d M Y') . " (Dikirim: $dikirim, Diterima: $diterima). Keterangan: " . $request->alasan,
            'foto_bukti' => $fotoPath,
            'status' => 'pending',
            'user_id' => auth()->user()->id,
        ]);

        // Simpan jumlah_diterima di tabel distribusi agar ada indikator visual
        $distribusi->jumlah_diterima = $diterima;
        $distribusi->save();

        return redirect()->route('bahan-baku.masuk')->with('success', 'Laporan selisih distribusi berhasil dikirim ke Manager. Menunggu persetujuan.');
    }

    // =========================================================
    // FUNGSI ACC PENGADUAN STOK OLEH MANAGER
    // =========================================================
    public function approvePengajuan($id)
    {
        $pengajuan = PengajuanStok::findOrFail($id);
        $bahanBaku = BahanBaku::findOrFail($pengajuan->bahan_baku_id);

        // Update stok utama di master data bahan baku
        $bahanBaku->stok = $pengajuan->stok_aktual;
        $bahanBaku->save();

        // Ubah status pengajuan jadi disetujui
        $pengajuan->status = 'disetujui';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Pengajuan disetujui! Stok utama sistem berhasil diperbarui.');
    }

    // =========================================================
    // FUNGSI TOLAK PENGADUAN STOK OLEH MANAGER
    // =========================================================
    public function rejectPengajuan($id)
    {
        $pengajuan = PengajuanStok::findOrFail($id);
        
        // Stok utama dibiarin aja, cuma status pengajuannya yang ditolak
        $pengajuan->status = 'ditolak';
        $pengajuan->save();

        return redirect()->back()->with('error', 'Pengajuan ditolak! Barista diwajibkan bertanggung jawab atas selisih stok tersebut.');
    }

}