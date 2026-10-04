<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_users_can_use_the_site(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'customer']);

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($user)->get('/wallet')->assertOk();
        $this->actingAs($user)->get('/profile')->assertOk();
    }

    public function test_unverified_merchant_owner_reaches_dashboard(): void
    {
        $owner = User::factory()->unverified()->create(['role' => 'merchant_owner']);
        Merchant::create(['user_id' => $owner->id, 'business_name' => 'Shop', 'trading_name' => 'Shop', 'slug' => 'shop', 'verification_status' => 'approved', 'is_active' => true]);

        $this->actingAs($owner)->get('/merchant')->assertOk();
    }

    public function test_pending_banner_shows_until_verified(): void
    {
        $unverified = User::factory()->unverified()->create(['role' => 'customer']);
        $verified = User::factory()->create(['role' => 'customer']);

        $this->actingAs($unverified)->get('/')->assertOk()->assertSee('not verified yet');
        $this->actingAs($verified)->get('/')->assertOk()->assertDontSee('not verified yet');
    }

    public function test_error_pages_are_animated_full_height_with_links(): void
    {
        $this->get('/no-such-page-anywhere')
            ->assertNotFound()
            ->assertSee('animate-float', false)
            ->assertSee('min-h-screen', false)
            ->assertSee('Explore')
            ->assertSee('Wanted');
    }

    public function test_customer_dashboard_side_hub(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $this->actingAs($customer)->get('/dashboard')
            ->assertOk()
            ->assertSee('My groups')
            ->assertSee('GBPass wallet')
            ->assertSee('My stats')
            ->assertSee('w-3/4', false);

        $this->actingAs($customer)->get('/orders')->assertOk()->assertSee('My orders');
        $this->actingAs($customer)->get('/wallet')->assertOk();
        $this->actingAs($customer)->get('/analytics')->assertOk()->assertSee('My stats');
        $this->actingAs($customer)->get('/profile')->assertOk();
    }
}
