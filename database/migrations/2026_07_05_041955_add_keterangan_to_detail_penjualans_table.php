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
        Schema::table('detail_penjualans', function (Blueprint $table) {
            // Menambahkan kolom keterangan setelah kolom subtotal
            $table->string('keterangan')->nullable()->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
