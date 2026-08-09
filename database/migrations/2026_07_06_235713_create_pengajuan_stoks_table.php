<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_stoks', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel bahan baku
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->string('outlet');
            
            // Kolom untuk perbandingan
            $table->integer('stok_seharusnya');
            $table->integer('stok_aktual');
            $table->text('alasan');
            
            // Status persetujuan dari Operational Manager
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            
            // (Opsional) Jika abang ingin mencatat siapa barista yang mengajukan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_stoks');
    }
};