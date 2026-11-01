<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $pemilik;

    private User $pendingUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_AKTIF,
        ]);

        $this->pemilik = User::factory()->create([
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_AKTIF,
        ]);

        $this->pendingUser = User::factory()->create([
            'name' => 'Calon Pengguna',
            'nik' => '3201019999990001',
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_MENUNGGU,
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->pemilik)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Prioritas Verifikasi Pengguna');
        $response->assertSee('Calon Pengguna');
    }

    public function test_admin_can_view_verification_hub(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/verifikasi');
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pengguna');
        $response->assertSee('Calon Pengguna');
        $response->assertSee('3201019999990001');
    }

    public function test_admin_can_approve_user_verification(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/verifikasi/{$this->pendingUser->id}/approve", [
            'catatan' => 'Identitas KTP valid',
        ]);

        $response->assertSessionHas('success');
        $this->pendingUser->refresh();
        $this->assertEquals(User::STATUS_AKTIF, $this->pendingUser->status);
        $this->assertNotNull($this->pendingUser->verified_at);
        $this->assertEquals($this->admin->id, $this->pendingUser->verified_by);
        $this->assertEquals('Identitas KTP valid', $this->pendingUser->catatan_verifikasi);
    }

    public function test_admin_can_reject_user_verification(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/verifikasi/{$this->pendingUser->id}/reject", [
            'catatan' => 'Format NIK tidak valid',
        ]);

        $response->assertSessionHas('warning');
        $this->pendingUser->refresh();
        $this->assertEquals(User::STATUS_DITOLAK, $this->pendingUser->status);
        $this->assertEquals('Format NIK tidak valid', $this->pendingUser->catatan_verifikasi);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/verifikasi/{$this->pemilik->id}/toggle");
        $response->assertSessionHas('info');

        $this->pemilik->refresh();
        $this->assertEquals(User::STATUS_NONAKTIF, $this->pemilik->status);

        // Toggle back to active
        $response2 = $this->actingAs($this->admin)->post("/admin/verifikasi/{$this->pemilik->id}/toggle");
        $response2->assertSessionHas('success');

        $this->pemilik->refresh();
        $this->assertEquals(User::STATUS_AKTIF, $this->pemilik->status);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/verifikasi/{$this->admin->id}/toggle");
        $response->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertEquals(User::STATUS_AKTIF, $this->admin->status);
    }

    public function test_admin_can_create_staff_account(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/pengguna', [
            'name' => 'drh. Sarah Wijaya',
            'email' => 'sarah.dokter@kawwan.com',
            'nomor_telepon' => '081234567899',
            'role' => User::ROLE_DOKTER,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'sarah.dokter@kawwan.com',
            'role' => User::ROLE_DOKTER,
            'status' => User::STATUS_AKTIF,
        ]);
    }

    public function test_login_with_nik_redirects_admin_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => $this->admin->nik,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_login_with_email_redirects_admin_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_login_with_invalid_credentials_does_not_crash(): void
    {
        $response = $this->post('/login', [
            'login' => 'nonexistent@kawwan.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }
}
