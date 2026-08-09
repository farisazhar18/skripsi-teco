<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->string('outlet')->after('bahan_baku_id');
            $table->integer('jumlah')->after('outlet');
            $table->string('satuan')->after('jumlah');
            $table->text('keterangan')->nullable()->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropColumn(['outlet', 'jumlah', 'satuan', 'keterangan']);
        });
    }
};