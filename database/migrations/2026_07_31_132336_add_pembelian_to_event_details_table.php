<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('event_details', function (Blueprint $table) {
            $table->decimal('jumlah_beli', 10, 2)->nullable()->after('jumlah_dibutuhkan');
            $table->string('satuan_beli')->nullable()->after('jumlah_beli');
        });
    }
    public function down(): void {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn(['jumlah_beli', 'satuan_beli']);
        });
    }
};