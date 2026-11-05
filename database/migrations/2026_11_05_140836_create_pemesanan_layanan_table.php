
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemesanan_layanan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnDelete();

            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->restrictOnDelete();

            $table->foreignId('dokter_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Simpan harga saat booking agar riwayat biaya tidak berubah
            $table->unsignedBigInteger('biaya');

            $table->timestamps();

            $table->unique(['pemesanan_id', 'layanan_id', 'dokter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanan_layanan');
    }
};
