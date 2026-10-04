<?php

namespace Tests\Feature;

use App\Livewire\MyGangs;
use App\Livewire\WantedIndex;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use App\Models\WantedRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AccessAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function world(): array
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $m1 = Merchant::create(['user_id' => $owner->id, 'business_name' => 'Shop One', 'trading_name' => 'Shop One', 'slug' => 'shop-one', 'verification_status' => 'approved', 'is_active' => true]);
        $m2 = Merchant::create(['user_id' => $owner->id, 'business_name' => 'Shop Two', 'trading_name' => 'Shop Two', 'slug' => 'shop-two', 'verification_status' => 'approved', 'is_active' => true]);

        return compact('admin', 'owner', 'customer', 'm1', 'm2');
    }

    public function test_admin_can_access_merchant_pages_and_switch_shops(): void
    {
        ['admin' => $admin] = $this->world();

        $this->actingAs($admin)->get('/merchant')->assertOk()->assertSee('Shop One');
        $this->actingAs($admin)->get('/merchant?merchant=shop-two')->assertOk()->assertSee('Shop Two');
        $this->actingAs($admin)->get('/merchant/offers')->assertOk();
        $this->actingAs($admin)->get('/merchant/orders')->assertOk();
        $this->actingAs($admin)->get('/merchant/payments')->assertOk();
        $this->actingAs($admin)->get('/merchant/scan')->assertOk();
        $this->actingAs($admin)->get('/merchant/commissions')->assertOk();
        $this->actingAs($admin)->get('/merchant/analytics')->assertOk();
    }

    public function test_admin_can_impersonate_and_stop(): void
    {
        ['admin' => $admin, 'customer' => $customer] = $this->world();

        $this->actingAs($admin)->post(route('admin.impersonate', $customer))
            ->assertRedirect(route('dashboard'));
        $this->assertEquals($customer->id, auth()->id());
        $this->assertEquals($admin->id, session('impersonator'));

        $this->get('/')->assertOk()->assertSee('Admin preview');
        $this->post(route('impersonate.stop'))->assertRedirect(route('admin.users'));
        $this->assertEquals($admin->id, auth()->id());
        $this->assertNull(session('impersonator'));
    }

    public function test_admin_cannot_impersonate_admins_self_or_suspended(): void
    {
        ['admin' => $admin, 'customer' => $customer] = $this->world();
        $admin2 = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $customer->update(['is_suspended' => true]);

        $this->actingAs($admin)->post(route('admin.impersonate', $admin2))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.impersonate', $admin))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.impersonate', $customer))->assertForbidden();
    }

    public function test_analytics_authorization_matrix(): void
    {
        ['admin' => $admin, 'owner' => $owner, 'customer' => $customer] = $this->world();

        $this->actingAs($admin)->get('/admin/analytics')->assertOk();
        $this->actingAs($owner)->get('/merchant/analytics')->assertOk();
        $this->actingAs($customer)->get('/analytics')->assertOk();

        $this->actingAs($customer)->get('/merchant/analytics')->assertForbidden();
        $this->actingAs($customer)->get('/admin/analytics')->assertForbidden();
        $this->actingAs($owner)->get('/admin/analytics')->assertForbidden();
        auth()->logout();
        $this->get('/analytics')->assertRedirect(route('login'));
    }

    public function test_wanted_post_form_lives_in_modal_with_search(): void
    {
        $this->get('/wanted')->assertOk()->assertSee('Post a request')->assertDontSee('What do you want?');

        Livewire::test(WantedIndex::class)
            ->call('openPost')
            ->assertSee('What do you want?')
            ->call('closePost')
            ->assertDontSee('What do you want?');
    }

    public function test_guests_can_post_wanted_requests_with_throttle(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            Livewire::test(WantedIndex::class)
                ->set('title', "Wanted item $i")
                ->set('budget', 50000)
                ->set('guest_name', 'Guest Buyer')
                ->call('save')
                ->assertHasNoErrors();
        }
        $this->assertEquals(3, WantedRequest::whereNull('user_id')->count());
        $this->assertEquals('Guest Buyer', WantedRequest::first()->authorName());

        Livewire::test(WantedIndex::class)
            ->set('title', 'Fourth item')
            ->set('budget', 50000)
            ->set('guest_name', 'Guest Buyer')
            ->call('save')
            ->assertHasErrors(['title']);
        $this->assertEquals(3, WantedRequest::whereNull('user_id')->count());
    }

    public function test_guests_must_give_a_name(): void
    {
        Livewire::test(WantedIndex::class)
            ->set('title', 'Nameless want')
            ->set('budget', 50000)
            ->call('save')
            ->assertHasErrors(['guest_name']);
    }

    public function test_anyone_can_respond_to_wanted_requests(): void
    {
        RateLimiter::clear('wanted-resp:127.0.0.1');
        $wanted = WantedRequest::create(['user_id' => null, 'guest_name' => 'Buyer', 'title' => 'Need TV', 'budget' => 1000000, 'status' => 'open']);

        Livewire::test(WantedIndex::class)
            ->call('respond', $wanted->id)
            ->set('r_message', 'Sealed unit, pickup Kampala')
            ->set('r_price', 950000)
            ->set('r_name', 'Supplier Sam')
            ->call('sendResponse')
            ->assertHasNoErrors();

        $this->assertEquals(1, $wanted->fresh()->responses_count);
        $this->assertEquals('Supplier Sam', $wanted->responses()->first()->authorName());
        $this->get('/wanted')->assertOk()->assertSee('Supplier Sam')->assertSee('Respond with your price');
    }

    public function test_guest_responses_are_throttled(): void
    {
        RateLimiter::clear('wanted-resp:127.0.0.1');
        $wanted = WantedRequest::create(['user_id' => null, 'guest_name' => 'Buyer', 'title' => 'Need Fridge', 'budget' => 800000, 'status' => 'open']);

        for ($i = 1; $i <= 3; $i++) {
            Livewire::test(WantedIndex::class)
                ->call('respond', $wanted->id)
                ->set('r_message', "Offer $i")
                ->set('r_price', 700000)
                ->set('r_name', 'Sam')
                ->call('sendResponse')
                ->assertHasNoErrors();
        }
        Livewire::test(WantedIndex::class)
            ->call('respond', $wanted->id)
            ->set('r_message', 'Offer 4')
            ->set('r_price', 700000)
            ->set('r_name', 'Sam')
            ->call('sendResponse')
            ->assertHasErrors(['r_message']);
    }

    public function test_groups_directory_tabs(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $this->get('/groups')->assertOk()->assertSee('Discover')->assertDontSee('My groups</button>');

        Livewire::actingAs($customer)->test(MyGangs::class)
            ->assertSee('Discover')
            ->set('tab', 'mine')
            ->assertSee('Nothing here yet');
    }

    public function test_my_groups_renders_member_cards_with_actions(): void
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create(['user_id' => $owner->id, 'business_name' => 'M', 'trading_name' => 'M', 'slug' => 'mm', 'verification_status' => 'approved', 'is_active' => true]);
        $buyer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Member Deal', 'slug' => 'member-deal', 'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active']);
        $gang = Gang::create(['offer_id' => $offer->id, 'code' => 'G-MD-1', 'target' => 5, 'confirmed_count' => 1, 'status' => 'forming']);
        GangMember::create(['gang_id' => $gang->id, 'user_id' => $buyer->id, 'offer_id' => $offer->id, 'status' => 'reserved']);

        Livewire::actingAs($buyer)->test(MyGangs::class)
            ->set('tab', 'mine')
            ->assertSee('Member Deal')
            ->assertSee('reserved')
            ->assertSee('Track');
    }

    public function test_group_terminology_and_new_homepage_sections(): void
    {
        ['merchant' => $m] = (function () {
            $o = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
            $mm = Merchant::create(['user_id' => $o->id, 'business_name' => 'M', 'trading_name' => 'M', 'slug' => 'm', 'verification_status' => 'approved', 'is_active' => true]);

            return ['merchant' => $mm];
        })();
        $offer = Offer::create(['merchant_id' => $m->id, 'title' => 'Group Deal', 'slug' => 'group-deal', 'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active']);
        Gang::create(['offer_id' => $offer->id, 'code' => 'G-T-1', 'target' => 5, 'confirmed_count' => 2, 'status' => 'forming']);

        $home = $this->get('/')->assertOk()->assertSee('Groups forming now');
        $this->assertStringNotContainsString('Gangs forming now', $home->getContent());
        $this->assertStringContainsString('Trending now', $home->getContent());
        $this->assertStringContainsString('Previous products', $home->getContent());

        $this->get(route('offers.show', $offer))->assertOk()->assertSee('Group price');
    }

    public function test_sidebar_is_collapsible(): void
    {
        ['admin' => $admin] = $this->world();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('data-flux-sidebar-collapse', false)
            ->assertSee('data-flux-sidebar-group-dropdown', false)
            ->assertSee('transition: width', false);
    }
}
