<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika database lama masih memakai kolom 'slug', rename ke 'link' terlebih dahulu
        if (Schema::hasColumn('artikel', 'slug') && ! Schema::hasColumn('artikel', 'link')) {
            Schema::table('artikel', function (Blueprint $table) {
                $table->renameColumn('slug', 'link');
            });
        }

        Schema::table('artikel', function (Blueprint $table) {
            $table->string('judul', 500)->change();
            $table->string('link', 500)->change();

            if (! Schema::hasColumn('artikel', 'sumber')) {
                $table->string('sumber', 150)->nullable()->after('link');
            }

            $table->text('isi')->nullable()->change();
            $table->text('gambar')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('artikel', function (Blueprint $table) {
            if (Schema::hasColumn('artikel', 'sumber')) {
                $table->dropColumn('sumber');
            }

            $table->string('judul', 255)->change();
            $table->string('link', 255)->change();
            $table->text('isi')->nullable(false)->change();
            $table->string('gambar', 255)->nullable()->change();
        });
    }
};
