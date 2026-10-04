<?php

namespace Tests\Feature;

use App\Livewire\Merchant\Setup;
use App\Models\Merchant;
use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MerchantSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_without_shop_is_sent_to_setup_not_404(): void
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);

        $response = $this->actingAs($owner)->get('/merchant');

        $this->assertNotEquals(404, $response->status());
        $response->assertRedirect(route('merchant.setup'));
    }

    public function test_setup_creates_pending_shop_and_notifies_admins(): void
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        Livewire::actingAs($owner)->test(Setup::class)
            ->set('business_name', 'Test Kitchen Ltd')
            ->set('trading_name', 'Test Kitchen')
            ->set('phone', '+256700111222')
            ->set('location', 'Kampala')
            ->set('network', 'MTN')
            ->set('account_number', '0770111222')
            ->set('account_name', 'Test Kitchen')
            ->call('save')
            ->assertRedirect(route('merchant.dashboard'));

        $merchant = Merchant::where('user_id', $owner->id)->firstOrFail();
        $this->assertEquals('pending', $merchant->verification_status);
        $this->assertFalse((bool) $merchant->is_active);
        $this->assertTrue($admin->notifications()->where('type', PlatformNotification::class)->exists());
    }

    public function test_setup_redirects_away_when_shop_exists(): void
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        Merchant::create(['user_id' => $owner->id, 'business_name' => 'Has Shop', 'trading_name' => 'Has Shop', 'slug' => 'has-shop', 'verification_status' => 'approved', 'is_active' => true]);

        Livewire::actingAs($owner)->test(Setup::class)->assertRedirect(route('merchant.dashboard'));
    }

    public function test_custom_error_pages_render(): void
    {
        $this->get('/no-such-page-anywhere')->assertNotFound()->assertSee('wandered off');

        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $this->actingAs($customer)->get('/admin')->assertForbidden()->assertSee('cannot go there');
    }
}
