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

        // 2. Dokter Hewan
        $dokter = User::firstOrCreate(
            ['email' => 'dokter@kawwan.com'],
            [
                'name' => 'drh. Rian Kurniawan',
                'nik' => '3201012345670001',
                'nomor_telepon' => '081234567891',
                'alamat' => 'Jl. Dr. Saharjo No. 12, Jakarta Selatan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOKTER,
                'status' => User::STATUS_AKTIF,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'verified_by' => $admin->id,
            ]
        );

        // 3. Kader Puskesmas
        $kader = User::firstOrCreate(
            ['email' => 'kader@kawwan.com'],
            [
                'name' => 'Siti Rahmawati, S.K.M.',
                'nik' => '3201012345670002',
                'nomor_telepon' => '081234567892',
                'alamat' => 'Komplek Puskeswan No. 5, Jakarta Timur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KADER,
                'status' => User::STATUS_AKTIF,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'verified_by' => $admin->id,
            ]
        );

        // 4. Pemilik Hewan - Aktif & Terverifikasi
        $pemilik1 = User::firstOrCreate(
            ['nik' => '3201019876540001'],
            [
                'name' => 'Anisa Maharani',
                'email' => 'anisa@example.com',
                'nomor_telepon' => '081398765432',
                'alamat' => 'Jl. Melati Indah No. 15, RT 02/RW 04, Tebet, Jakarta Selatan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_AKTIF,
                'verified_at' => now()->subDays(3),
                'verified_by' => $admin->id,
                'catatan_verifikasi' => 'Data KTP dan domisili valid dan sesuai.',
            ]
        );

        // 5. Pemilik Hewan - Menunggu Verifikasi (Prioritas 1)
        $pemilik2 = User::firstOrCreate(
            ['nik' => '3201019876540002'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'nomor_telepon' => '081287654321',
                'alamat' => 'Jl. Kenanga Raya No. 42, Kebayoran Baru, Jakarta Selatan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_MENUNGGU,
                'catatan_verifikasi' => null,
            ]
        );

        // 6. Pemilik Hewan - Menunggu Verifikasi (Prioritas 2)
        $pemilik3 = User::firstOrCreate(
            ['nik' => '3201019876540003'],
            [
                'name' => 'Dian Pratama',
                'email' => 'dian.pratama@example.com',
                'nomor_telepon' => '081555667788',
                'alamat' => 'Jl. Anggrek Garuda No. 8, Cilandak, Jakarta Selatan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_MENUNGGU,
                'catatan_verifikasi' => null,
            ]
        );

        // 7. Pemilik Hewan - Ditolak
        $pemilik4 = User::firstOrCreate(
            ['nik' => '3201019876540004'],
            [
                'name' => 'Farhan Nugraha',
                'email' => 'farhan@example.com',
                'nomor_telepon' => '081711223344',
                'alamat' => 'Jl. Mawar Bodas No. 101, Pancoran',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_DITOLAK,
                'verified_at' => now()->subDay(),
                'verified_by' => $admin->id,
                'catatan_verifikasi' => 'Format NIK tidak sesuai dengan data kependudukan wilayah setempat.',
            ]
        );

        // 8. Pemilik Hewan - Nonaktif
        $pemilik5 = User::firstOrCreate(
            ['nik' => '3201019876540005'],
            [
                'name' => 'Eko Wahyudi',
                'email' => 'eko.wahyudi@example.com',
                'nomor_telepon' => '081999887766',
                'alamat' => 'Jl. Cempaka Putih Tengah No. 22',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'status' => User::STATUS_NONAKTIF,
                'catatan_verifikasi' => 'Akun dinonaktifkan sementara atas permintaan pengguna.',
            ]
        );

        // Data Hewan
        $hewan1 = Hewan::firstOrCreate(
            ['nama' => 'Milo', 'user_id' => $pemilik1->id],
            [
                'jenis' => 'Kucing',
                'ras' => 'Persia Peaknose',
                'jenis_kelamin' => 'Jantan',
                'tanggal_lahir' => now()->subYears(2)->toDateString(),
            ]
        );

        $hewan2 = Hewan::firstOrCreate(
            ['nama' => 'Bruno', 'user_id' => $pemilik1->id],
            [
                'jenis' => 'Anjing',
                'ras' => 'Golden Retriever',
                'jenis_kelamin' => 'Jantan',
                'tanggal_lahir' => now()->subYears(3)->toDateString(),
            ]
        );

        $hewan3 = Hewan::firstOrCreate(
            ['nama' => 'Luna', 'user_id' => $pemilik2->id],
            [
                'jenis' => 'Kucing',
                'ras' => 'British Shorthair',
                'jenis_kelamin' => 'Betina',
                'tanggal_lahir' => now()->subYear()->toDateString(),
            ]
        );

        // Jadwal Pelayanan
        $jadwalHariIni = JadwalPelayanan::firstOrCreate(
            ['tanggal' => now()->toDateString(), 'waktu_mulai' => '08:30:00'],
            [
                'waktu_selesai' => '12:00:00',
                'kuota' => 15,
                'status' => 'tersedia',
            ]
        );

        $jadwalBesok = JadwalPelayanan::firstOrCreate(
            ['tanggal' => now()->addDay()->toDateString(), 'waktu_mulai' => '09:00:00'],
            [
                'waktu_selesai' => '13:00:00',
                'kuota' => 12,
                'status' => 'tersedia',
            ]
        );

        // Pemesanan
        $pemesanan1 = Pemesanan::firstOrCreate(
            ['user_id' => $pemilik1->id, 'hewan_id' => $hewan1->id, 'jadwal_pelayanan_id' => $jadwalHariIni->id],
            ['status' => 'sedang_dilayani']
        );

        $pemesanan2 = Pemesanan::firstOrCreate(
            ['user_id' => $pemilik1->id, 'hewan_id' => $hewan2->id, 'jadwal_pelayanan_id' => $jadwalHariIni->id],
            ['status' => 'dikonfirmasi']
        );

        $pemesanan3 = Pemesanan::firstOrCreate(
            ['user_id' => $pemilik2->id, 'hewan_id' => $hewan3->id, 'jadwal_pelayanan_id' => $jadwalHariIni->id],
            ['status' => 'menunggu']
        );

        // Pemeriksaan
        Pemeriksaan::firstOrCreate(
            ['pemesanan_id' => $pemesanan1->id],
            [
                'keluhan' => 'Kucing tidak mau makan dan lemas sejak kemarin',
                'hasil_pemeriksaan' => 'Suhu tubuh 39.2C, radang tenggorokan ringan',
                'tindakan' => 'Pemberian vitamin dan antibiotik oral',
                'catatan' => 'Kontrol kembali dalam 3 hari bila belum membaik.',
            ]
        );

        // Artikel
        Artikel::firstOrCreate(
            ['slug' => 'panduan-vaksinasi-kucing-pemula'],
            [
                'user_id' => $kader->id,
                'judul' => 'Panduan Vaksinasi Kucing untuk Pemula',
                'ringkasan' => 'Vaksinasi berkala penting untuk mencegah virus berbahaya seperti FPV dan rabies.',
                'isi' => 'Vaksinasi pada kucing adalah salah satu langkah paling penting dalam memelihara hewan kesayangan Anda...',
                'kategori' => 'Kesehatan',
                'status' => 'publikasi',
                'published_at' => now()->subDays(5),
            ]
        );
    }
}
