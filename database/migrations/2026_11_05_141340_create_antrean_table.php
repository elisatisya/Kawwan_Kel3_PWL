
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

            // Satu dokter hanya mendapat satu antrean
            // untuk satu booking yang sama.
            $table->unique(
                ['pemesanan_id', 'dokter_id'],
                'antrean_pemesanan_dokter_unique'
            );

            // Nomor antrean tidak boleh duplikat
            // pada dokter dan tanggal yang sama.
            $table->unique(
                ['dokter_id', 'tanggal', 'nomor_antrean'],
                'antrean_dokter_tanggal_nomor_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrean');
    }
};
