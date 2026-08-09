<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // Nambahin kolom boolean (True/False). Default-nya 0 (False/Bukan menu paket)
            $table->boolean('is_menu_paket')->default(false)->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('is_menu_paket');
        });
    }
};