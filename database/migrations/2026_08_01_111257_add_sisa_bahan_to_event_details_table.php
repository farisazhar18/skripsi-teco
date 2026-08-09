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
        Schema::table('event_details', function (Blueprint $table) {
            // Nambahin kolom sisa_bahan
            $table->double('sisa_bahan')->nullable()->after('satuan_beli');
        });
    }

    public function down()
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('sisa_bahan');
        });
    }
};
