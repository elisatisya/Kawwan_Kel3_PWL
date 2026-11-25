<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $layanans = [
            [
                'nama' => 'Pemeriksaan Kesehatan Umum',
                'deskripsi' => 'Pemeriksaan kondisi kesehatan hewan secara umum.',
                'durasi_menit' => 30,
                'biaya' => 0,
            ],
            [
                'nama' => 'Vaksinasi',
                'deskripsi' => 'Pelayanan pemberian vaksin untuk hewan.',
                'durasi_menit' => 30,
                'biaya' => 75000,
            ],
            [
                'nama' => 'Sterilisasi',
                'deskripsi' => 'Pelayanan tindakan sterilisasi hewan.',
                'durasi_menit' => 60,
                'biaya' => 250000,
            ],
            [
                'nama' => 'Pengobatan',
                'deskripsi' => 'Pelayanan pemeriksaan dan penanganan keluhan kesehatan hewan.',
                'durasi_menit' => 45,
                'biaya' => 150000,
            ],
            [
                'nama' => 'Pemberian Vitamin',
                'deskripsi' => 'Pelayanan pemberian vitamin sesuai kebutuhan hewan.',
                'durasi_menit' => 15,
                'biaya' => 50000,
            ],
            [
                'nama' => 'Konsultasi Kesehatan Hewan',
                'deskripsi' => 'Konsultasi mengenai kesehatan dan perawatan hewan.',
                'durasi_menit' => 20,
                'biaya' => 0,
            ],
        ];

        foreach ($layanans as $data) {
            Layanan::firstOrCreate(
                ['nama' => $data['nama']],
                [
                    'deskripsi' => $data['deskripsi'],
                    'biaya' => $data['biaya'],
                    'durasi_menit' => $data['durasi_menit'],
                    'aktif' => false,
                ]
            );
        }
    }
}

