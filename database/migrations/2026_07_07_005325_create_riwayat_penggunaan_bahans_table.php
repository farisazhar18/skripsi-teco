<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penggunaan_bahans', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel bahan baku
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->string('outlet');
            
            // Jumlah yang berkurang/terpakai
            $table->integer('jumlah_terpakai');
            
            // Keterangan (Misal: "Terjual dari Order #INV-123", atau "Dibuang/Rusak")
            $table->string('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penggunaan_bahans');
    }
};