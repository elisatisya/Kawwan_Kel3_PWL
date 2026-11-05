
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->text('deskripsi')->nullable();

            // Tarif 0 berarti layanan gratis
            $table->unsignedBigInteger('biaya')->default(0);

            // Estimasi durasi pelayanan dalam menit
            $table->unsignedInteger('durasi_menit')->nullable();

            // Status aktif atau nonaktif
            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
