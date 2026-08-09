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
        Schema::table('pembelians', function (Blueprint $table) {
            // Menambahkan kolom status_acc dengan nilai default 'Menunggu ACC'
            $table->string('status_acc')->default('Menunggu ACC')->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('status_acc');
        });
    }
};
