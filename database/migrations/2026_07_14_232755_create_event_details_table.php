<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_details', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel events
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            // Relasi ke tabel bahan baku lu (sesuaikan kalau nama tabelnya beda ya bang, asumsi gua 'bahan_bakus' / 'bahan_baku')
            $table->unsignedBigInteger('bahan_baku_id'); 
            $table->integer('jumlah_dibutuhkan');
            $table->string('satuan')->nullable(); // Misal: gram, pcs, ml
            $table->timestamps();

            // Opsional: Bikin foreign key kalau tabel bahan baku lu beneran namanya itu
            // $table->foreign('bahan_baku_id')->references('id')->on('nama_tabel_bahan_baku_lu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_details');
    }
};