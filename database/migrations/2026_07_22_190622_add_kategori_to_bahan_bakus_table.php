<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            // Kita kasih default 'Lainnya' biar data bahan baku yang lama nggak error
            $table->string('kategori')->default('Lainnya')->after('nama_bahan');
        });
    }

    public function down()
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
