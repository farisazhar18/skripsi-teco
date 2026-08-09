<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('pembelians', function (Blueprint $table) {
        if (!Schema::hasColumn('pembelians', 'satuan_beli')) {
            $table->string('satuan_beli')->after('jumlah');
        }

        if (!Schema::hasColumn('pembelians', 'jumlah_konversi')) {
            $table->integer('jumlah_konversi')->default(0)->after('satuan_beli');
        }
    });
}

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn(['satuan_beli', 'jumlah_konversi']);
        });
    }
};