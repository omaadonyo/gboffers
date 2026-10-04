<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignupVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_sends_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Verify Me',
            'email' => 'verifyme@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $user = User::where('email', 'verifyme@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_resend_sends_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_signup_as_merchant_reaches_shop_setup(): void
    {
        $response = $this->post('/register', [
            'name' => 'Shop Owner',
            'email' => 'shopowner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'merchant_owner',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $user = User::where('email', 'shopowner@example.com')->firstOrFail();
        $this->assertEquals('merchant_owner', $user->role);

        $this->actingAs($user)->get('/merchant')->assertRedirect(route('merchant.setup'));
        $this->actingAs($user)->get('/merchant/setup')->assertOk()->assertSee('Open your shop');
    }

    public function test_signup_role_cannot_be_forged_to_admin(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/register');
        $response->assertInvalid('role');
        $this->assertNull(User::where('email', 'sneaky@example.com')->first());
    }
}
