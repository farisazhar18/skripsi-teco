<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket'); // Cth: Paket E
            $table->string('deskripsi'); // Cth: Chicken Lasagna + 1 Minuman
            // Relasi ke produk makanannya (biar sistem tau Paket E itu produk yang mana)
            $table->unsignedBigInteger('makanan_produk_id'); 
            $table->integer('harga'); // Cth: 42000
            $table->timestamps();

            // Opsional: Buka comment di bawah kalau nama tabel produk lu beneran 'produks'
            // $table->foreign('makanan_produk_id')->references('id')->on('produks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_events');
    }
};