<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_contains_forgot_password_link(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee(route('password.request'));
    }

    public function test_guest_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Lupa Password?');
        $response->assertSee('Kirim Tautan Reset');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@kawwan.test',
            'status' => User::STATUS_AKTIF,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'user@kawwan.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_request_reset_link_with_nonexistent_email_does_not_enumerate(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'tidak_ada@kawwan.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_guest_can_view_reset_password_page_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@kawwan.test',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get("/reset-password/{$token}?email=" . urlencode('user@kawwan.test'));
        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('Simpan Password Baru');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@kawwan.test',
            'password' => Hash::make('password-lama'),
            'status' => User::STATUS_AKTIF,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'user@kawwan.test',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('password-baru-123', $user->password));
    }

    public function test_password_reset_preserves_user_status_and_role(): void
    {
        $pendingUser = User::factory()->create([
            'email' => 'pending@kawwan.test',
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_MENUNGGU,
        ]);

        $token = Password::broker()->createToken($pendingUser);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'pending@kawwan.test',
            'password' => 'password-baru-456',
            'password_confirmation' => 'password-baru-456',
        ]);

        $response->assertRedirect(route('login'));

        $pendingUser->refresh();
        // Status must NOT be promoted to aktif, role must not change
        $this->assertEquals(User::STATUS_MENUNGGU, $pendingUser->status);
        $this->assertEquals(User::ROLE_PEMILIK, $pendingUser->role);
        $this->assertTrue(Hash::check('password-baru-456', $pendingUser->password));
    }

    public function test_reset_password_fails_with_invalid_or_expired_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@kawwan.test',
            'password' => Hash::make('password-lama'),
        ]);

        $response = $this->from('/reset-password/token-palsu')->post('/reset-password', [
            'token' => 'token-palsu',
            'email' => 'user@kawwan.test',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ]);

        $response->assertSessionHasErrors('email');

        $user->refresh();
        $this->assertTrue(Hash::check('password-lama', $user->password));
    }

    public function test_reset_password_validates_minimum_length_and_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'user@kawwan.test',
        ]);

        $token = Password::broker()->createToken($user);

        // Terlalu pendek (< 8)
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'user@kawwan.test',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors('password');

        // Konfirmasi tidak cocok
        $response2 = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'user@kawwan.test',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);

        $response2->assertSessionHasErrors('password');
    }
}
