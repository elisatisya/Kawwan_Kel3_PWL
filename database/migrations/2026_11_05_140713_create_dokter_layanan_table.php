
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokter_layanan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dokter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['dokter_id', 'layanan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter_layanan');
    }
};
