<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Tambahkan kolom no_urut_bulanan setelah kolom id
            $table->integer('no_urut_bulanan')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('no_urut_bulanan');
        });
    }
};