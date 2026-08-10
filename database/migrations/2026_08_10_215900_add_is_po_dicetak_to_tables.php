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
            $table->boolean('is_po_dicetak')->default(false)->after('status_acc');
        });
        
        Schema::table('event_details', function (Blueprint $table) {
            $table->boolean('is_po_dicetak')->default(false)->after('sisa_bahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('is_po_dicetak');
        });
        
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('is_po_dicetak');
        });
    }
};
