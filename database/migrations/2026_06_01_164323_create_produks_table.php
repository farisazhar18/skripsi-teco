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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kategori');
            $table->integer('harga_reguler')->nullable();
            $table->integer('harga_large')->nullable();
            $table->boolean('tersedia_hot')->default(false);
            $table->boolean('tersedia_ice')->default(false);
            $table->string('tipe_produk')->default('racikan'); // racikan / vendor
            $table->integer('stok_produk')->nullable(); // buat brownies/lasagna
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
