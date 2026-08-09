<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti 'nama_tabel_bahan_baku_lu' sesuai nama asli di database (biasanya 'bahan_bakus' atau 'bahan_baku')
        Schema::table('bahan_bakus', function (Blueprint $table) {
            // Nambahin kolom stok khusus event setelah kolom stok harian
            $table->integer('stok_event')->default(0)->after('stok');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn('stok_event');
        });
    }
};