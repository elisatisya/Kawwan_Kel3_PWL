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
            ],
            [
                'nama' => 'Vaksinasi',
                'deskripsi' => 'Pelayanan pemberian vaksin untuk hewan.',
                'durasi_menit' => 30,
            ],
            [
                'nama' => 'Sterilisasi',
                'deskripsi' => 'Pelayanan tindakan sterilisasi hewan.',
                'durasi_menit' => 60,
            ],
            [
                'nama' => 'Pengobatan',
                'deskripsi' => 'Pelayanan pemeriksaan dan penanganan keluhan kesehatan hewan.',
                'durasi_menit' => 45,
            ],
            [
                'nama' => 'Pemberian Vitamin',
                'deskripsi' => 'Pelayanan pemberian vitamin sesuai kebutuhan hewan.',
                'durasi_menit' => 15,
            ],
            [
                'nama' => 'Konsultasi Kesehatan Hewan',
                'deskripsi' => 'Konsultasi mengenai kesehatan dan perawatan hewan.',
                'durasi_menit' => 20,
            ],
        ];

        foreach ($layanans as $data) {
            Layanan::firstOrCreate(
                ['nama' => $data['nama']],
                [
                    'deskripsi' => $data['deskripsi'],
                    'biaya' => 0,
                    'durasi_menit' => $data['durasi_menit'],
                    'aktif' => false,
                ]
            );
        }
    }
}

