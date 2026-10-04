<?php

namespace Tests\Feature;

use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOversightTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_admin_can_view_all_oversight_pages(): void
    {
        $admin = $this->admin();
        foreach (['/admin', '/admin/offers', '/admin/groups', '/admin/orders', '/admin/users', '/admin/merchants', '/admin/disputes'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_merchant_cannot_view_admin_pages(): void
    {
        $merchant = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        foreach (['/admin', '/admin/offers', '/admin/groups', '/admin/orders', '/admin/users', '/admin/merchants', '/admin/disputes'] as $path) {
            $this->actingAs($merchant)->get($path)->assertForbidden();
        }
    }

    public function test_customer_cannot_view_admin_pages(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        foreach (['/admin', '/admin/offers', '/admin/users'] as $path) {
            $this->actingAs($customer)->get($path)->assertForbidden();
        }
    }

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/admin', '/admin/offers', '/admin/groups', '/admin/orders', '/admin/users', '/admin/merchants', '/admin/disputes'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_admin_can_suspend_and_verify_users(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('toggleSuspend', $user->id);

        $this->assertTrue($user->fresh()->is_suspended);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('toggleSuspend', $admin->id);

        $this->assertFalse($admin->fresh()->is_suspended);
    }
}
