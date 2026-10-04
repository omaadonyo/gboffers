<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_owner_can_view_merchant_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        Merchant::create([
            'user_id' => $user->id,
            'business_name' => 'Pearl Electronics',
            'slug' => 'pearl-electronics',
            'verification_status' => 'approved',
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/merchant')->assertOk();
    }

    public function test_admin_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_customer_is_forbidden_from_merchant_area(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/merchant')->assertForbidden();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/merchant')->assertRedirect(route('login'));
        $this->get('/admin')->assertRedirect(route('login'));
    }
}
