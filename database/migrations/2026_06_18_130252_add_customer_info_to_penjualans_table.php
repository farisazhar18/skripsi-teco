<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // nullable() artinya boleh kosong / opsional
            $table->string('nama_customer')->nullable()->after('outlet');
            $table->string('no_hp')->nullable()->after('nama_customer');
        });
    }

    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['nama_customer', 'no_hp']);
        });
    }
};
