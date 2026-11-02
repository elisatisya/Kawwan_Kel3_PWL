<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\Pemeriksaan;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@kawwan.com'],
            [
                'name' => 'Administrator Kawwan',
                'nik' => '3201010000000001',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jl. Kesehatan Hewan No. 1, Jakarta Pusat',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_AKTIF,
                'email_verified_at' => now(),
                'verified_at' => now(),
            ]
        );
    }
}
