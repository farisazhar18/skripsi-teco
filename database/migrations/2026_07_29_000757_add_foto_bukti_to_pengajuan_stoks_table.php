<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_stoks', function (Blueprint $table) {
            // Menambahkan kolom foto_bukti bertipe string (varchar) yang boleh kosong (nullable)
            // after('alasan') fungsinya biar kolom baru ini ditaruh berjejer setelah kolom alasan
            $table->string('foto_bukti')->nullable()->after('alasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_stoks', function (Blueprint $table) {
            // Menghapus kolom foto_bukti kalau database di-rollback (dibatalkan)
            $table->dropColumn('foto_bukti');
        });
    }
};