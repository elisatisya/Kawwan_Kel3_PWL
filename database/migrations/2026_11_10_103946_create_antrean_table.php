<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrean', function (Blueprint $table) {
             $table->id();
            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnDelete();
            $table->foreignId('dokter_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('tanggal');
            $table->string('nomor_antrean');
            $table->string('status')->default('menunggu');
            $table->timestamps();

            $table->unique(['dokter_id', 'tanggal', 'nomor_antrean']);
            $table->unique(['pemesanan_id', 'dokter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrean');
    }
};
