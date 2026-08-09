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
        Schema::table('pembelians', function (Blueprint $table) {

            $table->integer('sisa_distribusi')
                ->default(0)
                ->after('jumlah_konversi');

        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {

            $table->dropColumn('sisa_distribusi');

        });
    }
};
