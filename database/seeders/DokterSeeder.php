<?php

namespace Database\Seeders;

use App\Models\JadwalPelayanan;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        $dokter = User::firstOrCreate(
            ['email' => 'dokter@kawwan.com'],
            [
                'name' => 'Dian Fitriani',
                'gelar_depan' => 'drh.',
                'nik' => '3201010000000030',
                'nomor_telepon' => '081234567893',
                'alamat' => 'Jl. Kesehatan Hewan No. 3',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOKTER,
                'status' => User::STATUS_AKTIF,
                'verified_at' => now(),
            ]
        );

        foreach (Layanan::all() as $layanan) {
            $layanan->dokter()->syncWithoutDetaching([$dokter->id]);
        }

        JadwalPelayanan::whereNull('dokter_id')->update(['dokter_id' => $dokter->id]);
    }
}