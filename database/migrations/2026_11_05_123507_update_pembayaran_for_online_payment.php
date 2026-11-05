<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable();
        });

        DB::statement("
            ALTER TABLE pembayaran
            ALTER COLUMN metode SET DEFAULT 'transfer'
        ");

        DB::statement("
            ALTER TABLE pembayaran
            ALTER COLUMN status SET DEFAULT 'menunggu_verifikasi'
        ");
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });

        DB::statement("
            ALTER TABLE pembayaran
            ALTER COLUMN metode SET DEFAULT 'tunai'
        ");

        DB::statement("
            ALTER TABLE pembayaran
            ALTER COLUMN status SET DEFAULT 'lunas'
        ");
    }
};