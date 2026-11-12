<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::firstOrCreate(
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

        $this->call([
            KaderSeeder::class,
            LayananSeeder::class,
            DokterSeeder::class,
        ]);
    }
}
