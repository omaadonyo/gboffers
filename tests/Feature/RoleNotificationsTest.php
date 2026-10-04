<?php

namespace Tests\Feature;

use App\Events\GangCompleted;
use App\Events\GBPassIssued;
use App\Events\GBPassRedeemed;
use App\Events\OrderDisputed;
use App\Events\OrderFulfilled;
use App\Events\ReservationExpired;
use App\Livewire\Admin\Disputes as AdminDisputes;
use App\Livewire\Admin\Merchants as AdminMerchants;
use App\Livewire\NotificationBell;
use App\Models\Dispute;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\GbPass;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\GangUpdateNotification;
use App\Notifications\PlatformNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function world(): array
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $merchant = Merchant::create([
            'user_id' => $owner->id, 'business_name' => 'City Style', 'trading_name' => 'CityStyle',
            'slug' => 'city-style', 'verification_status' => 'pending', 'is_active' => true,
        ]);
        $offer = Offer::create([
            'merchant_id' => $merchant->id, 'title' => 'Ankara Set', 'slug' => 'ankara-set-n1',
            'normal_price' => 120000, 'status' => 'active',
        ]);

        return compact('owner', 'customer', 'merchant', 'offer');
    }

    public function test_pass_issued_notifies_customer(): void
    {
        ['customer' => $customer, 'merchant' => $merchant, 'offer' => $offer] = $this->world();
        $order = Order::create(['user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'subtotal' => 100000, 'total' => 100000]);
        $pass = GbPass::create(['order_id' => $order->id, 'user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'token' => 'GB-TEST-0001', 'pin_hash' => 'x', 'amount' => 100000]);

        event(new GBPassIssued($pass));

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $customer->id, 'notifiable_type' => User::class,
        ]);
        $this->assertStringContainsString('GBPass ready', $customer->notifications()->first()->data['title']);
    }

    public function test_pass_redeemed_notifies_merchant_owner_and_customer(): void
    {
        ['owner' => $owner, 'customer' => $customer, 'merchant' => $merchant, 'offer' => $offer] = $this->world();
        $order = Order::create(['user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'subtotal' => 100000, 'total' => 100000]);
        $pass = GbPass::create(['order_id' => $order->id, 'user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'token' => 'GB-TEST-0002', 'pin_hash' => 'x', 'amount' => 100000]);

        event(new GBPassRedeemed($pass, $owner));

        $this->assertStringContainsString('redeemed', strtolower($owner->notifications()->first()->data['title']));
        $this->assertStringContainsString('redeemed', strtolower($customer->notifications()->first()->data['title']));
    }

    public function test_order_fulfilled_notifies_customer(): void
    {
        ['customer' => $customer, 'merchant' => $merchant, 'offer' => $offer] = $this->world();
        $order = Order::create(['user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'subtotal' => 100000, 'total' => 100000]);

        event(new OrderFulfilled($order));

        $this->assertStringContainsString('fulfilled', strtolower($customer->notifications()->first()->data['title']));
    }

    public function test_reservation_expired_notifies_reserver(): void
    {
        ['customer' => $customer, 'offer' => $offer] = $this->world();
        $res = Reservation::create(['user_id' => $customer->id, 'offer_id' => $offer->id, 'code' => 'RES-1', 'expires_at' => now()->subMinute()]);

        event(new ReservationExpired($res));

        $this->assertStringContainsString('expired', strtolower($customer->notifications()->first()->data['title']));
    }

    public function test_merchant_approval_notifies_owner(): void
    {
        ['owner' => $owner, 'merchant' => $merchant] = $this->world();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        Livewire::actingAs($admin)->test(AdminMerchants::class)->call('approve', $merchant->id);

        $this->assertStringContainsString('approved', strtolower($owner->notifications()->first()->data['title']));
        $this->assertEquals('approved', $merchant->fresh()->verification_status);
    }

    public function test_dispute_opened_notifies_merchant_owner_and_admins(): void
    {
        ['owner' => $owner, 'customer' => $customer, 'merchant' => $merchant, 'offer' => $offer] = $this->world();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $order = Order::create(['user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'subtotal' => 100000, 'total' => 100000]);
        $dispute = Dispute::create(['order_id' => $order->id, 'reporter_id' => $customer->id, 'respondent_merchant_id' => $merchant->id, 'reason' => 'Not delivered']);

        event(new OrderDisputed($dispute));

        $this->assertStringContainsString('Dispute', $owner->notifications()->first()->data['title']);
        $this->assertStringContainsString('Dispute', $admin->notifications()->first()->data['title']);
        $this->assertEquals(0, $customer->notifications()->count());
    }

    public function test_dispute_resolved_notifies_reporter_and_merchant(): void
    {
        ['owner' => $owner, 'customer' => $customer, 'merchant' => $merchant, 'offer' => $offer] = $this->world();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $order = Order::create(['user_id' => $customer->id, 'merchant_id' => $merchant->id, 'offer_id' => $offer->id, 'subtotal' => 100000, 'total' => 100000]);
        $dispute = Dispute::create(['order_id' => $order->id, 'reporter_id' => $customer->id, 'respondent_merchant_id' => $merchant->id, 'reason' => 'Not delivered']);

        Livewire::actingAs($admin)->test(AdminDisputes::class)
            ->call('openResolve', $dispute->id)
            ->set('resolution', 'Refund issued to the customer.')
            ->set('outcome', 'resolved_customer')
            ->call('resolve');

        $this->assertStringContainsString('resolved', strtolower($customer->notifications()->first()->data['title']));
        $this->assertStringContainsString('resolved', strtolower($owner->notifications()->first()->data['title']));
    }

    public function test_gang_completed_notifies_members(): void
    {
        ['customer' => $customer, 'offer' => $offer] = $this->world();
        $gang = Gang::create(['offer_id' => $offer->id, 'code' => 'G-NOTIF-1', 'target' => 5, 'status' => 'forming']);
        GangMember::create(['gang_id' => $gang->id, 'user_id' => $customer->id, 'offer_id' => $offer->id]);

        event(new GangCompleted($gang));

        $this->assertTrue($customer->notifications()->where('type', GangUpdateNotification::class)->exists());
    }

    public function test_bell_shows_unread_and_marks_read(): void
    {
        ['customer' => $customer] = $this->world();
        $customer->notify(new PlatformNotification('Hello', 'World', '/groups', 'bell'));
        $customer->notify(new PlatformNotification('Hello 2', 'World 2', '/orders', 'shopping-cart'));

        Livewire::actingAs($customer)->test(NotificationBell::class)
            ->assertSee('Hello')
            ->assertSee('Hello 2')
            ->call('markAllRead');

        $this->assertEquals(0, $customer->unreadNotifications()->count());
    }

    public function test_guests_see_no_bell(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Notifications');
    }
}
