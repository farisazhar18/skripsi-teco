<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->date('tanggal_pelaksanaan');
            $table->string('outlet'); // Nampung session outlet aktif (hasanuddin / makmur)
            // Status tracking biar gampang di dashboard
            $table->enum('status', [
                'menunggu_logistik', // Baru diinput Op. Manager
                'sedang_dipesan',    // PO udah dibuat Logistik ke Supplier
                'bahan_ready',       // Bahan udah diterima Logistik & dipisah
                'selesai'            // Udah dieksekusi Barista
            ])->default('menunggu_logistik');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};