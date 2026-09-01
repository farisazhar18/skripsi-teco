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
            $table->string('po_number')->nullable()->after('is_po_dicetak');
        });
        
        Schema::table('event_pengadaans', function (Blueprint $table) {
            $table->string('po_number')->nullable()->after('is_po_dicetak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('po_number');
        });
        
        Schema::table('event_pengadaans', function (Blueprint $table) {
            $table->dropColumn('po_number');
        });
    }
};
