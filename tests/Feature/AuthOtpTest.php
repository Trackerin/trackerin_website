<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserOtp;
use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Tests\TestCase;

class AuthOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Test sending OTP for registration.
     */
    public function test_can_send_otp_for_registration(): void
    {
        $response = $this->postJson('/api/v1/register/send-otp', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Kode OTP berhasil dikirim ke email Anda.');

        $this->assertDatabaseHas('user_otps', [
            'email' => 'test@example.com',
            'type' => 'register',
        ]);

        Mail::assertSent(OtpMail::class, function ($mail) {
            return $mail->hasTo('test@example.com') && $mail->type === 'register';
        });
    }

    /**
     * Test sending OTP to an already registered email fails.
     */
    public function test_cannot_send_otp_to_registered_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/v1/register/send-otp', [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test successful registration with valid OTP.
     */
    public function test_can_register_with_valid_otp(): void
    {
        $email = 'register@example.com';
        $otp = '123456';

        UserOtp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'register',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp' => $otp,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email'],
                'access_token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'John Doe',
        ]);

        // Check user is verified
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user->email_verified_at);

        // Check OTP was deleted
        $this->assertDatabaseMissing('user_otps', [
            'email' => $email,
        ]);
    }

    /**
     * Test registration with invalid OTP fails.
     */
    public function test_cannot_register_with_invalid_otp(): void
    {
        $email = 'register@example.com';
        
        UserOtp::create([
            'email' => $email,
            'otp' => '123456',
            'type' => 'register',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp' => '654321', // wrong OTP
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['otp']);
    }

    /**
     * Test registration with expired OTP fails.
     */
    public function test_cannot_register_with_expired_otp(): void
    {
        $email = 'register@example.com';
        $otp = '123456';

        UserOtp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'register',
            'expires_at' => Carbon::now()->subMinutes(1), // expired 1 minute ago
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp' => $otp,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['otp']);
    }

    /**
     * Test sending OTP for forgot password.
     */
    public function test_can_send_otp_for_forgot_password(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->postJson('/api/v1/forgot-password/send-otp', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Kode OTP untuk atur ulang kata sandi berhasil dikirim ke email Anda.');

        $this->assertDatabaseHas('user_otps', [
            'email' => 'user@example.com',
            'type' => 'forgot_password',
        ]);

        Mail::assertSent(OtpMail::class, function ($mail) {
            return $mail->hasTo('user@example.com') && $mail->type === 'forgot_password';
        });
    }

    /**
     * Test successful password reset with valid OTP.
     */
    public function test_can_reset_password_with_valid_otp(): void
    {
        $email = 'user@example.com';
        $otp = '999999';

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('oldpassword'),
        ]);

        // Issue a token to test it gets revoked
        $token = $user->createToken('test_token')->plainTextToken;

        UserOtp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'forgot_password',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/v1/forgot-password/reset', [
            'email' => $email,
            'otp' => $otp,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Kata sandi berhasil diatur ulang. Silakan masuk menggunakan kata sandi baru Anda.');

        // Refresh user and assert password changed
        $user->refresh();
        $this->assertTrue(\Hash::check('newpassword123', $user->password));

        // Check all tokens are revoked
        $this->assertCount(0, $user->tokens);

        // Check OTP was deleted
        $this->assertDatabaseMissing('user_otps', [
            'email' => $email,
        ]);
    }
}
