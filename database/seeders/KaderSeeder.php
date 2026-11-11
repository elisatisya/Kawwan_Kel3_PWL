<?php

namespace Database\Seeders;

use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaderSeeder extends Seeder
{
    public function run(): void
    {
        // Akun kader
        User::firstOrCreate(
            ['email' => 'kader@kawwan.com'],
            [
                'name' => 'Kader Puskesmas',
                'nik' => '3201010000000010',
                'nomor_telepon' => '081234567891',
                'alamat' => 'Jl. Kesehatan Hewan No. 1',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KADER,
                'status' => User::STATUS_AKTIF,
                'verified_at' => now(),
            ]
        );

        // Jadwal 3 hari ke depan
        foreach ([0, 1, 2] as $i) {
            JadwalPelayanan::firstOrCreate(
                ['tanggal' => now()->addDays($i)->toDateString(), 'waktu_mulai' => '09:00:00'],
                ['waktu_selesai' => '12:00:00', 'kuota' => 10, 'status' => 'tersedia']
            );
        }

        // Pemilik demo + hewannya, buat ngetes booking
        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik@kawwan.com'],
            [
                'name' => 'Pemilik Demo',
                'nik' => '3201010000000020',
                'nomor_telepon' => '081234567892',
                'alamat' => 'Jl. Contoh No. 2',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_AKTIF,
                'verified_at' => now(),
            ]
        );

        foreach ([
            ['Mochi', 'Kucing', 'Persia', 'betina', '2023-03-10'],
            ['Bruno', 'Anjing', 'Golden Retriever', 'jantan', '2022-08-21'],
        ] as [$nama, $jenis, $ras, $jk, $lahir]) {
            Hewan::firstOrCreate(
                ['user_id' => $pemilik->id, 'nama' => $nama],
                ['jenis' => $jenis, 'ras' => $ras, 'jenis_kelamin' => $jk, 'tanggal_lahir' => $lahir]
            );
        }
    }
}
