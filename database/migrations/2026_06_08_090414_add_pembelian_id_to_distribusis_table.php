<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->foreignId('pembelian_id')
                ->after('tanggal')
                ->nullable()
                ->constrained('pembelians')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropForeign(['pembelian_id']);
            $table->dropColumn('pembelian_id');
        });
    }
};