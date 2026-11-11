<?php

namespace Tests\Feature;

use App\Models\Antrean;
use App\Models\Artikel;
use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\Layanan;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterDanKaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_dokter_pages_and_actions_work_without_error(): void
    {
        $dokter = User::factory()->create([
            'role' => User::ROLE_DOKTER,
            'status' => User::STATUS_AKTIF,
        ]);

        $pemilik = User::factory()->create([
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_AKTIF,
        ]);

        $hewan = Hewan::create([
            'user_id' => $pemilik->id,
            'nama' => 'Milo',
            'jenis' => 'Kucing',
            'ras' => 'Persia',
            'jenis_kelamin' => 'jantan',
        ]);

        $jadwal = JadwalPelayanan::create([
            'dokter_id' => $dokter->id,
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'kuota' => 10,
            'status' => 'tersedia',
        ]);

        $pemesanan = Pemesanan::create([
            'user_id' => $pemilik->id,
            'hewan_id' => $hewan->id,
            'jadwal_pelayanan_id' => $jadwal->id,
            'status' => 'selesai',
        ]);

        Pemeriksaan::create([
            'pemesanan_id' => $pemesanan->id,
            'keluhan' => 'Lemas',
            'hasil_pemeriksaan' => 'Dehidrasi ringan',
            'tindakan' => 'Pemberian vitamin',
            'catatan' => 'Kontrol 3 hari lagi',
        ]);

        $this->actingAs($dokter)
            ->get(route('dokter.dashboard'))
            ->assertOk()
            ->assertViewIs('dokter.dashboard');

        $this->actingAs($dokter)
            ->get(route('dokter.riwayat.index', ['q' => 'Milo']))
            ->assertOk()
            ->assertViewIs('dokter.riwayat')
            ->assertSee('Milo');

        $this->actingAs($dokter)
            ->get(route('dokter.riwayat.show', $hewan))
            ->assertOk()
            ->assertViewIs('dokter.riwayat')
            ->assertSee('Dehidrasi ringan');

        $this->actingAs($dokter)
            ->get(route('dokter.profil'))
            ->assertOk()
            ->assertViewIs('dokter.profil');

        $this->actingAs($dokter)
            ->put(route('dokter.profil.update'), [
                'name' => 'Dokter Budi',
                'gelar_depan' => 'drh.',
                'gelar_belakang' => 'M.Vet.',
                'email' => $dokter->email,
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jl. Veteriner No. 1',
            ])
            ->assertRedirect(route('dokter.profil'));
    }

    public function test_kader_pages_and_actions_work_without_error(): void
    {
        $kader = User::factory()->create([
            'role' => User::ROLE_KADER,
            'status' => User::STATUS_AKTIF,
        ]);

        $dokter = User::factory()->create([
            'role' => User::ROLE_DOKTER,
            'status' => User::STATUS_AKTIF,
        ]);

        $pemilik = User::factory()->create([
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_AKTIF,
        ]);

        $hewan = Hewan::create([
            'user_id' => $pemilik->id,
            'nama' => 'Oyen',
            'jenis' => 'Kucing',
            'ras' => 'Domestik',
            'jenis_kelamin' => 'jantan',
        ]);

        $jadwal = JadwalPelayanan::create([
            'dokter_id' => $dokter->id,
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '15:00:00',
            'kuota' => 10,
            'status' => 'tersedia',
        ]);

        // Dashboard
        $this->actingAs($kader)
            ->get(route('kader.dashboard'))
            ->assertOk()
            ->assertViewIs('kader.dashboard');

        // Jadwal
        $this->actingAs($kader)
            ->get(route('kader.jadwal.index'))
            ->assertOk()
            ->assertViewIs('kader.jadwal');

        // Layanan (index, create, store, edit, update, toggle status)
        $this->actingAs($kader)
            ->get(route('kader.layanan.index'))
            ->assertOk()
            ->assertViewIs('kader.layanan');

        $this->actingAs($kader)
            ->get(route('kader.layanan.create'))
            ->assertOk()
            ->assertViewIs('kader.layanan');

        $this->actingAs($kader)
            ->post(route('kader.layanan.store'), [
                'nama' => 'Pemeriksaan Umum',
                'deskripsi' => 'Pemeriksaan fisik dasar',
                'biaya' => 50000,
                'durasi_menit' => 30,
                'dokter_ids' => [$dokter->id],
            ])
            ->assertRedirect(route('kader.layanan.index'));

        $layanan = Layanan::firstOrFail();

        $this->actingAs($kader)
            ->get(route('kader.layanan.edit', $layanan))
            ->assertOk()
            ->assertViewIs('kader.layanan');

        $this->actingAs($kader)
            ->put(route('kader.layanan.update', $layanan), [
                'nama' => 'Pemeriksaan Lengkap',
                'deskripsi' => 'Pemeriksaan menyeluruh',
                'biaya' => 75000,
                'durasi_menit' => 45,
                'dokter_ids' => [$dokter->id],
            ])
            ->assertRedirect(route('kader.layanan.index'));

        $this->actingAs($kader)
            ->patch(route('kader.layanan.status', $layanan))
            ->assertRedirect(route('kader.layanan.index'));

        // Pemesanan (index, create, store, update status)
        $this->actingAs($kader)
            ->get(route('kader.pemesanan.index', ['status_pembayaran' => 'belum_dibayar']))
            ->assertOk()
            ->assertViewIs('kader.pemesanan');

        $this->actingAs($kader)
            ->get(route('kader.pemesanan.create'))
            ->assertOk()
            ->assertViewIs('kader.pemesanan');

        $this->actingAs($kader)
            ->post(route('kader.pemesanan.store'), [
                'user_id' => $pemilik->id,
                'hewan_id' => $hewan->id,
                'jadwal_pelayanan_id' => $jadwal->id,
            ])
            ->assertRedirect(route('kader.pemesanan.index'));

        $pemesanan = Pemesanan::firstOrFail();

        $this->actingAs($kader)
            ->patch(route('kader.pemesanan.status', $pemesanan), [
                'status' => 'dikonfirmasi',
            ])
            ->assertRedirect();

        // Pembayaran (show & verifikasi)
        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'jumlah' => 75000,
            'metode' => 'qris',
            'status' => 'menunggu_verifikasi',
        ]);

        $this->actingAs($kader)
            ->get(route('kader.pembayaran.show', $pemesanan))
            ->assertOk()
            ->assertViewIs('kader.pembayaran');

        $this->actingAs($kader)
            ->patch(route('kader.pembayaran.verifikasi', $pemesanan))
            ->assertRedirect(route('kader.pemesanan.index'));

        $this->assertDatabaseHas('antrean', [
            'pemesanan_id' => $pemesanan->id,
            'dokter_id' => $dokter->id,
        ]);

        // Antrean
        $this->actingAs($kader)
            ->get(route('kader.antrean.index'))
            ->assertOk()
            ->assertViewIs('kader.antrean');

        // Artikel (index, create, store, edit, update, ajukan, destroy)
        $this->actingAs($kader)
            ->get(route('kader.artikel.index'))
            ->assertOk()
            ->assertViewIs('kader.artikel');

        $this->actingAs($kader)
            ->get(route('kader.artikel.create'))
            ->assertOk()
            ->assertViewIs('kader.artikel');

        $this->actingAs($kader)
            ->post(route('kader.artikel.store'), [
                'judul' => 'Tips Merawat Kucing',
                'ringkasan' => 'Panduan singkat',
                'isi' => 'Isi artikel lengkap',
                'kategori' => 'Perawatan',
            ])
            ->assertRedirect(route('kader.artikel.index'));

        $artikel = Artikel::firstOrFail();

        $this->actingAs($kader)
            ->get(route('kader.artikel.edit', $artikel))
            ->assertOk()
            ->assertViewIs('kader.artikel');

        $this->actingAs($kader)
            ->put(route('kader.artikel.update', $artikel), [
                'judul' => 'Tips Merawat Kucing Persia',
                'ringkasan' => 'Panduan singkat terbaru',
                'isi' => 'Isi artikel lengkap terbaru',
                'kategori' => 'Perawatan',
            ])
            ->assertRedirect(route('kader.artikel.index'));

        $this->actingAs($kader)
            ->patch(route('kader.artikel.ajukan', $artikel))
            ->assertRedirect(route('kader.artikel.index'));

        $this->actingAs($kader)
            ->delete(route('kader.artikel.destroy', $artikel))
            ->assertRedirect(route('kader.artikel.index'));

        // Profil
        $this->actingAs($kader)
            ->get(route('kader.profil'))
            ->assertOk()
            ->assertViewIs('kader.profil');

        $this->actingAs($kader)
            ->put(route('kader.profil.update'), [
                'name' => 'Kader Siti',
                'email' => $kader->email,
                'nomor_telepon' => '089876543210',
                'alamat' => 'Jl. Puskeswan No. 2',
            ])
            ->assertRedirect(route('kader.profil'));
    }

    public function test_pemilik_pages_and_actions_work_without_error(): void
    {
        $dokter = User::factory()->create([
            'role' => User::ROLE_DOKTER,
            'status' => User::STATUS_AKTIF,
        ]);

        $pemilik = User::factory()->create([
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_AKTIF,
        ]);

        $layanan = Layanan::create([
            'nama' => 'Vaksinasi Rabies',
            'deskripsi' => 'Vaksinasi tahunan hewan kesayangan',
            'biaya' => 60000,
            'durasi_menit' => 20,
            'aktif' => true,
        ]);
        $layanan->dokter()->sync([$dokter->id]);

        $jadwal = JadwalPelayanan::create([
            'dokter_id' => $dokter->id,
            'tanggal' => now()->addDay()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'kuota' => 10,
            'status' => 'tersedia',
        ]);

        // Dashboard
        $this->actingAs($pemilik)
            ->get(route('pemilik.dashboard'))
            ->assertOk()
            ->assertViewIs('pemilik.dashboard');

        // Data Hewan (index, store, edit, update, destroy)
        $this->actingAs($pemilik)
            ->get(route('pemilik.hewan.index'))
            ->assertOk()
            ->assertViewIs('pemilik.hewan');

        $this->actingAs($pemilik)
            ->post(route('pemilik.hewan.store'), [
                'nama' => 'Ciko',
                'jenis' => 'Kucing',
                'ras' => 'Anggora',
                'jenis_kelamin' => 'jantan',
                'tanggal_lahir' => now()->subYear()->toDateString(),
            ])
            ->assertRedirect(route('pemilik.hewan.index'));

        $hewan = Hewan::where('user_id', $pemilik->id)->firstOrFail();

        $this->actingAs($pemilik)
            ->get(route('pemilik.hewan.edit', $hewan))
            ->assertOk()
            ->assertViewIs('pemilik.hewan')
            ->assertSee('Ciko');

        $this->actingAs($pemilik)
            ->put(route('pemilik.hewan.update', $hewan), [
                'nama' => 'Ciko Putih',
                'jenis' => 'Kucing',
                'ras' => 'Persia',
                'jenis_kelamin' => 'jantan',
                'tanggal_lahir' => now()->subYear()->toDateString(),
            ])
            ->assertRedirect(route('pemilik.hewan.index'));

        // Jadwal & Layanan Pelayanan
        $this->actingAs($pemilik)
            ->get(route('pemilik.jadwal.index'))
            ->assertOk()
            ->assertViewIs('pemilik.jadwal')
            ->assertSee('Vaksinasi Rabies');

        // Pemesanan Antrean (index, store, bayar, batal)
        $this->actingAs($pemilik)
            ->get(route('pemilik.pemesanan.index', ['jadwal_id' => $jadwal->id]))
            ->assertOk()
            ->assertViewIs('pemilik.pemesanan');

        $this->actingAs($pemilik)
            ->post(route('pemilik.pemesanan.store'), [
                'hewan_id' => $hewan->id,
                'jadwal_pelayanan_id' => $jadwal->id,
                'layanan_ids' => [$layanan->id],
            ])
            ->assertRedirect(route('pemilik.pemesanan.index'));

        $pemesanan = Pemesanan::where('user_id', $pemilik->id)->firstOrFail();

        $this->assertDatabaseHas('pemesanan_layanan', [
            'pemesanan_id' => $pemesanan->id,
            'layanan_id' => $layanan->id,
            'biaya' => 60000,
        ]);

        $this->actingAs($pemilik)
            ->post(route('pemilik.pemesanan.bayar', $pemesanan), [
                'jumlah' => 60000,
                'metode' => 'qris',
            ])
            ->assertRedirect(route('pemilik.pemesanan.index'));

        $this->assertDatabaseHas('pembayaran', [
            'pemesanan_id' => $pemesanan->id,
            'jumlah' => 60000,
            'metode' => 'qris',
            'status' => 'menunggu_verifikasi',
        ]);

        $this->actingAs($pemilik)
            ->patch(route('pemilik.pemesanan.batal', $pemesanan))
            ->assertRedirect(route('pemilik.pemesanan.index'));

        $this->assertDatabaseHas('pemesanan', [
            'id' => $pemesanan->id,
            'status' => 'dibatalkan',
        ]);

        // Hewan temporary untuk uji hapus (yang tidak punya pemesanan)
        $hewanHapus = Hewan::create([
            'user_id' => $pemilik->id,
            'nama' => 'Bobi',
            'jenis' => 'Anjing',
            'jenis_kelamin' => 'jantan',
        ]);

        $this->actingAs($pemilik)
            ->delete(route('pemilik.hewan.destroy', $hewanHapus))
            ->assertRedirect(route('pemilik.hewan.index'));

        $this->assertDatabaseMissing('hewan', [
            'id' => $hewanHapus->id,
        ]);
    }
}
