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
        Schema::create('pembelians', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal');
        $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');

        $table->integer('jumlah');
        $table->string('satuan_beli')->nullable();
        $table->integer('jumlah_konversi')->default(0);

        $table->text('keterangan')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
