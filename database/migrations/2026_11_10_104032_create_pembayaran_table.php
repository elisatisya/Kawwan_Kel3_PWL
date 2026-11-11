<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')
                ->unique()
                ->constrained('pemesanan')
                ->cascadeOnDelete();
            $table->foreignId('dicatat_oleh')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->bigInteger('jumlah');
            $table->string('metode')->default('transfer');
            $table->string('status')->default('menunggu_verifikasi');
            $table->text('catatan')->nullable();
            $table->timestamp('dibayar_at')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
