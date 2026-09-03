<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ResepProdukController;
use App\Http\Controllers\DetailResepProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\LaporanBahanBakuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\LaporanDistribusiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OutletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\PengajuanStokController;
use App\Http\Controllers\LaporanEventController;
use App\Http\Controllers\PollingController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/order/{outlet}', [CustomerOrderController::class, 'menu'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.menu');

Route::post('/order/{outlet}/tambah', [CustomerOrderController::class, 'tambahKeranjang'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.tambah');

Route::get('/order/{outlet}/keranjang', [CustomerOrderController::class, 'keranjang'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.keranjang');

Route::post('/order/{outlet}/update-keranjang', [\App\Http\Controllers\CustomerOrderController::class, 'updateKeranjang'])
    ->name('customer.updateKeranjang');

Route::post('/order/{outlet}/hapus', [CustomerOrderController::class, 'hapusKeranjang'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.hapus');

Route::post('/order/{outlet}/checkout', [CustomerOrderController::class, 'checkout'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.checkout');

Route::post('/midtrans/callback', [CustomerOrderController::class, 'callback'])
    ->name('midtrans.callback');

Route::get('/order/status/{id}', [CustomerOrderController::class, 'status'])
    ->name('customer.status');

Route::get('/order/bill/{id}', [CustomerOrderController::class, 'downloadBill'])
    ->name('customer.bill');

// Tambahin ini buat fitur cek riwayat customer
Route::get('/order/cek-customer/{no_hp}', [CustomerOrderController::class, 'cekCustomer'])
    ->name('customer.cek');

// Tambahkan di bawah route status yang sudah ada
Route::get('/cek-status/{id}', [CustomerOrderController::class, 'cekStatus'])
    ->name('customer.cekStatus');

// Pastikan di web.php ada {outlet}
Route::get('/order/{outlet}', [CustomerOrderController::class, 'menu'])
    ->where('outlet', 'hasanuddin|makmur')
    ->name('customer.menu');

// 1. Buat nampilin halaman ketik nomor HP (GET)
Route::get('/order/{outlet}/riwayat', [CustomerOrderController::class, 'formRiwayat'])
->name('customer.riwayat.form');

// 2. Buat nyari dan nampilin hasil riwayatnya pas tombol diklik (POST)
Route::post('/order/{outlet}/riwayat/cari', [CustomerOrderController::class, 'cariRiwayat'])
->name('customer.riwayat.cari');

// Route untuk halaman pilih metode pembayaran
Route::get('/order/{outlet}/pilih-pembayaran/{id}', [CustomerOrderController::class, 'pilihPembayaran'])
    ->name('customer.pilihPembayaran');

// Route untuk memproses pilihan pembayaran (QRIS atau Tunai)
Route::post('/order/{outlet}/proses-pembayaran/{id}', [CustomerOrderController::class, 'prosesPembayaran'])
    ->name('customer.prosesPembayaran');

Route::post('/order/simulate-success/{id}', [\App\Http\Controllers\CustomerOrderController::class, 'simulateSuccess'])
    ->name('customer.simulateSuccess');

Route::middleware(['auth'])->group(function () {
    Route::get('/pilih-outlet', [OutletController::class, 'pilih'])
        ->middleware('role:kasir,barista')
        ->name('outlet.pilih');

    Route::post('/pilih-outlet', [OutletController::class, 'simpan'])
        ->middleware('role:kasir,barista')
        ->name('outlet.simpan');

    Route::get('/ganti-outlet', [OutletController::class, 'ganti'])
        ->middleware('role:kasir,barista')
        ->name('outlet.ganti');
});

Route::middleware(['auth', 'check.outlet'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:owner,operational_manager,logistik,kasir,barista')
        ->name('dashboard');

    // =========================================================
    // POLLING API (Auto-refresh tanpa reload halaman)
    // =========================================================
    Route::get('/api/polling/pengajuan-pengadaan', [PollingController::class, 'pengajuanPengadaan'])->name('polling.pengadaan');
    Route::get('/api/polling/pengajuan-stok', [PollingController::class, 'pengajuanStok'])->name('polling.stok');
    Route::get('/api/polling/pesanan-baru', [PollingController::class, 'pesananBaru'])->name('polling.pesanan');

    Route::post('/penjualan/konfirmasi/{id}', [PenjualanController::class, 'konfirmasiPembayaran'])
        ->middleware('role:owner,kasir')
        ->name('admin.konfirmasi');

    Route::patch('/penjualan/{id}/update-status', [\App\Http\Controllers\PenjualanController::class, 'updateStatus'])
        ->name('penjualan.updateStatus');

    Route::get('/penjualan/{id}/cetak-struk', [\App\Http\Controllers\PenjualanController::class, 'cetakStruk'])
        ->name('penjualan.cetakStruk');

    Route::resource('penjualan', PenjualanController::class)
        ->middleware('role:owner,operational_manager,kasir,barista');

    Route::resource('produk', ProdukController::class)
        ->middleware('role:owner,operational_manager,kasir,barista');

    Route::resource('resep-produk', ResepProdukController::class)
        ->middleware('role:owner,operational_manager,barista');

    Route::resource('detail-resep-produk', DetailResepProdukController::class)
        ->middleware('role:owner,operational_manager,barista');

    Route::get('/bahan-baku/rekap-harian', [BahanBakuController::class, 'rekapHarian'])
        ->middleware('role:owner,operational_manager,logistik,barista')
        ->name('bahan-baku.rekap');

    // Route untuk melihat rekap barang masuk (distribusi) hari ini
    Route::get('/bahan-baku/masuk', [\App\Http\Controllers\BahanBakuController::class, 'rekapMasuk'])
        ->name('bahan-baku.masuk');

    Route::get('/bahan-baku/rekap-harian/pdf', [\App\Http\Controllers\BahanBakuController::class, 'rekapHarianPdf'])
        ->middleware('role:owner,operational_manager,logistik,barista')
        ->name('bahan-baku.rekap.pdf');

    // 🔥 FORM KHUSUS LAPOR SELISIH DISTRIBUSI (BARISTA & OWNER)
    Route::get('/bahan-baku/distribusi/{distribusi_id}/lapor', [\App\Http\Controllers\BahanBakuController::class, 'laporSelisih'])
        ->middleware('role:owner,barista')
        ->name('bahan-baku.lapor-selisih');
    
    Route::post('/bahan-baku/distribusi/{distribusi_id}/lapor', [\App\Http\Controllers\BahanBakuController::class, 'storeSelisih'])
        ->middleware('role:owner,barista')
        ->name('bahan-baku.store-selisih');
    Route::get('/bahan-baku/nonaktif', [BahanBakuController::class, 'indexNonaktif'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('bahan-baku.nonaktif');
        
    Route::post('/bahan-baku/{id}/aktifkan', [BahanBakuController::class, 'aktifkan'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('bahan-baku.aktifkan');


    Route::resource('bahan-baku', BahanBakuController::class)
        ->middleware('role:owner,operational_manager,logistik,barista');

    Route::get('/pengajuan-stok', [PengajuanStokController::class, 'index'])
        ->middleware('role:owner,operational_manager')
        ->name('pengajuan-stok.index');

    Route::post('/pengajuan-stok/{id}/approve', [App\Http\Controllers\BahanBakuController::class, 'approvePengajuan']);
    Route::post('/pengajuan-stok/{id}/reject', [App\Http\Controllers\BahanBakuController::class, 'rejectPengajuan']);

    Route::post('/pengajuan-stok/{id}/approve', [PengajuanStokController::class, 'approve'])
        ->middleware('role:owner,operational_manager')
        ->name('pengajuan-stok.approve');
    
    Route::get('/pengajuan-pengadaan', [PembelianController::class, 'indexPengajuan'])
        ->name('pembelian.pengajuan');

    Route::get('/pembelian/{id}/cetak-po', [App\Http\Controllers\PembelianController::class, 'cetakPO'])
        ->name('pembelian.cetakPO');
    Route::post('/pembelian/{id}/terima', [App\Http\Controllers\PembelianController::class, 'terimaBarang'])
        ->name('pembelian.terimaBarang');
    Route::post('/pembelian/proses-beli-massal', [App\Http\Controllers\PembelianController::class, 'prosesBeliMassal'])
        ->name('pembelian.prosesBeliMassal');

    Route::get('/pembelian/review-acc', [App\Http\Controllers\PembelianController::class, 'reviewAcc'])
        ->name('pembelian.reviewAcc');
    Route::post('/pembelian/acc-massal', [App\Http\Controllers\PembelianController::class, 'accMassal'])
        ->name('pembelian.accMassal');

    Route::get('/pembelian/review-terima', [App\Http\Controllers\PembelianController::class, 'reviewTerima'])
        ->name('pembelian.reviewTerima');
    Route::post('/pembelian/terima-massal', [App\Http\Controllers\PembelianController::class, 'terimaMassal'])
        ->name('pembelian.terimaMassal');

    Route::get('/pembelian/pilih-po', [App\Http\Controllers\PembelianController::class, 'pilihPO'])
        ->name('pembelian.pilihPO');
    Route::post('/pembelian/cetak-po-multi', [App\Http\Controllers\PembelianController::class, 'cetakPOMulti'])
        ->name('pembelian.cetakPOMulti');
    Route::post('/pembelian/{id}/batal-po', [App\Http\Controllers\PembelianController::class, 'batalPO'])
        ->name('pembelian.batalPO');

    // Menu Pengadaan/Stok (Untuk Distribusi ke Outlet)
    Route::get('/pengadaan-stok', [PembelianController::class, 'indexStok'])
        ->name('pembelian.stok');
    Route::get('/pengadaan-stok/dari-event', [PembelianController::class, 'stokDariEvent'])
        ->name('pembelian.stokEvent');

    Route::post('/pembelian/{id}/acc', [PembelianController::class, 'accPembelian'])
        ->name('pembelian.acc');

    Route::resource('pembelian', PembelianController::class)
        ->except(['index'])
        ->middleware('role:owner,operational_manager,logistik');

    Route::resource('distribusi', DistribusiController::class)
        ->only(['index', 'create', 'store'])
        ->middleware('role:owner,operational_manager,logistik');

    Route::get('/distribusi/print-pdf', [DistribusiController::class, 'printPdf'])
        ->name('distribusi.print');

    Route::get('/laporan-bahan-baku', [LaporanBahanBakuController::class, 'index'])
        ->middleware('role:owner,operational_manager,logistik,barista')
        ->name('laporan-bahan-baku.index');

    Route::get('/laporan-bahan-baku/pdf', [LaporanBahanBakuController::class, 'pdf'])
        ->middleware('role:owner,operational_manager,logistik,barista')
        ->name('laporan-bahan-baku.pdf');

    Route::get('/laporan-pembelian', [LaporanPembelianController::class, 'index'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('laporan-pembelian.index');

    Route::get('/laporan-pembelian/pdf', [LaporanPembelianController::class, 'pdf'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('laporan-pembelian.pdf');

    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])
        ->middleware('role:owner,operational_manager,kasir')
        ->name('laporan-penjualan.index');

    Route::get('/laporan-penjualan/pdf', [LaporanPenjualanController::class, 'pdf'])
        ->middleware('role:owner,operational_manager,kasir')
        ->name('laporan-penjualan.pdf');

    Route::get('/laporan-distribusi', [LaporanDistribusiController::class, 'index'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('laporan-distribusi.index');

    Route::get('/laporan-distribusi/pdf', [LaporanDistribusiController::class, 'pdf'])
        ->middleware('role:owner,operational_manager,logistik')
        ->name('laporan-distribusi.pdf');

    Route::resource('users', UserController::class)
        ->middleware('role:owner,operational_manager');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Rute Database Pelanggan (CRM)
    Route::get('/pelanggan', [App\Http\Controllers\PelangganController::class, 'index'])
        ->middleware('role:owner,kasir')
        ->name('pelanggan.index');

    Route::get('/pelanggan/{no_hp}', [App\Http\Controllers\PelangganController::class, 'show'])
        ->middleware('role:owner,kasir')
        ->name('pelanggan.show');

    Route::prefix('event')->group(function () {

        Route::get('/laporan', [App\Http\Controllers\LaporanEventController::class, 'laporan'])
            ->name('event.laporan')
            ->middleware('role:owner,operational_manager');
        Route::get('/laporan/pdf', [App\Http\Controllers\LaporanEventController::class, 'exportLaporanPdf'])
            ->name('event.laporan_pdf')
            ->middleware('role:owner,operational_manager');

        Route::get('/', [App\Http\Controllers\EventController::class, 'index'])
            ->name('event.index')
            ->middleware('role:owner,operational_manager,logistik,barista');

        // RUTE DETAIL & AKSI INTERNAL EVENT
        Route::get('/detail/{id}', [App\Http\Controllers\EventController::class, 'detailEvent'])
            ->name('event.detail');
        Route::post('/detail/{id}/ajukan-pengadaan', [App\Http\Controllers\EventController::class, 'ajukanPengadaan'])
            ->name('event.ajukanPengadaan');
        Route::post('/detail/{id}/acc-pengadaan', [App\Http\Controllers\EventController::class, 'accPengadaan'])
            ->name('event.accPengadaan');

        // CRUD Event
        Route::get('/create', [App\Http\Controllers\EventController::class, 'create'])
            ->name('event.create')->middleware('role:owner,operational_manager');
        Route::post('/store', [App\Http\Controllers\EventController::class, 'store'])
            ->name('event.store')->middleware('role:owner,operational_manager');

        Route::get('/{id}/show', [App\Http\Controllers\EventController::class, 'show'])
            ->name('event.show');
        Route::get('/{id}/pdf', [App\Http\Controllers\EventController::class, 'exportPdf'])
            ->name('event.pdf');
        Route::get('/{id}/pdf-manager', [App\Http\Controllers\EventController::class, 'exportPdfManager'])
            ->name('event.pdf_manager');

        // RUTE PAPAN TUGAS EKSEKUSI
        Route::get('/tugas', [App\Http\Controllers\EventController::class, 'tugas'])
            ->name('event.tugas')
            ->middleware('role:owner,logistik,barista,operational_manager');

        Route::get('/{id}/review-terima', [App\Http\Controllers\EventController::class, 'reviewTerima'])
            ->name('event.reviewTerima')->middleware('role:logistik,owner');
        Route::post('/{id}/terima-massal', [App\Http\Controllers\EventController::class, 'terimaMassal'])
            ->name('event.terimaMassal')->middleware('role:logistik,owner');

        Route::get('/{id}/form-po', [App\Http\Controllers\EventController::class, 'formPO'])
            ->name('event.formPO')->middleware('role:logistik,owner');
        Route::get('/{id}/cetak-po', [App\Http\Controllers\EventController::class, 'cetakPO'])
            ->name('event.cetakPO')->middleware('role:logistik,owner,operational_manager');
        Route::post('/{id}/batal-po', [App\Http\Controllers\EventController::class, 'batalPO'])
            ->name('event.batalPO')->middleware('role:logistik,owner');
        Route::post('/{id}/proses-beli', [App\Http\Controllers\EventController::class, 'prosesBeli'])
            ->name('event.prosesBeli')->middleware('role:logistik,owner');

        Route::post('/{id}/serahkan', [App\Http\Controllers\EventController::class, 'serahkanBarang'])
            ->name('event.serahkan')->middleware('role:logistik,owner');
        
        // 🔥 TAMBAHAN RUTE BARU BUAT FORM SISA FISIK 🔥
        Route::get('/{id}/lapor-sisa', [App\Http\Controllers\EventController::class, 'laporSisa'])
            ->name('event.laporSisa')->middleware('role:barista,owner,logistik');
        Route::post('/{id}/selesai', [App\Http\Controllers\EventController::class, 'selesaikanPesanan'])
            ->name('event.selesaikanPesanan')->middleware('role:barista,owner,logistik');
    });

    Route::prefix('paket-event')->middleware('role:owner,operational_manager')->group(function () {
        Route::get('/', [App\Http\Controllers\PaketEventController::class, 'index'])->name('paket-event.index');
        Route::get('/create', [App\Http\Controllers\PaketEventController::class, 'create'])->name('paket-event.create');
        Route::post('/store', [App\Http\Controllers\PaketEventController::class, 'store'])->name('paket-event.store');
        Route::delete('/{id}', [App\Http\Controllers\PaketEventController::class, 'destroy'])->name('paket-event.destroy');
    });
});

require __DIR__.'/auth.php';

Route::get('/foto-bukti/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!\Illuminate\Support\Facades\File::exists($filePath)) {
        abort(404);
    }
    
    $file = \Illuminate\Support\Facades\File::get($filePath);
    $type = \Illuminate\Support\Facades\File::mimeType($filePath);
    
    return \Illuminate\Support\Facades\Response::make($file, 200)->header("Content-Type", $type);
})->where('path', '.*');