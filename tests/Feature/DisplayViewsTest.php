<?php

namespace Tests\Feature;

use App\Livewire\OfferBrowser;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DisplayViewsTest extends TestCase
{
    use RefreshDatabase;

    private function world(): array
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create(['user_id' => $owner->id, 'business_name' => 'M', 'trading_name' => 'M', 'slug' => 'm', 'verification_status' => 'approved', 'is_active' => true]);
        $buyer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now(), 'name' => 'Ava Buyer']);
        $buyer->profile()->firstOrCreate([], ['display_name' => 'Ava Buyer', 'phone' => '+256772111111']);
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Stacked Deal', 'slug' => 'stacked-deal', 'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active']);
        $gang = Gang::create(['offer_id' => $offer->id, 'code' => 'G-ST-1', 'target' => 5, 'confirmed_count' => 1, 'status' => 'forming']);
        GangMember::create(['gang_id' => $gang->id, 'user_id' => $buyer->id, 'offer_id' => $offer->id, 'status' => 'reserved']);

        return compact('owner', 'merchant', 'buyer', 'offer', 'gang');
    }

    public function test_groups_urls_work_and_legacy_redirect(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/groups')->assertOk();
        $this->get('/gangs')->assertRedirect('/groups');
        auth()->logout();
        $this->get('/groups')->assertOk()->assertSee('Discover');
    }

    public function test_admin_groups_url_renamed(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($admin)->get('/admin/groups')->assertOk();
        $this->actingAs($admin)->get('/admin/gangs')->assertRedirect('/admin/groups');
    }

    public function test_homepage_cards_show_member_avatars_with_hover_identity(): void
    {
        $this->world();

        $home = $this->get('/')->assertOk();
        $home->assertSee('Ava Buyer');
        $home->assertSee('+2567', false);
        $home->assertSee('Group buyer');
    }

    public function test_explore_view_switcher(): void
    {
        $this->world();

        Livewire::test(OfferBrowser::class)
            ->assertSee('Stacked Deal')
            ->call('setView', 'list')
            ->assertSee('Stacked Deal')
            ->call('setView', 'showcase')
            ->assertSee('Stacked Deal')
            ->call('setView', 'nonsense')
            ->assertSet('view', 'grid');
    }

    public function test_explore_views_render_all_layouts(): void
    {
        $this->world();

        $this->get('/explore?view=list')->assertOk()->assertSee('Stacked Deal');
        $this->get('/explore?view=showcase')->assertOk()->assertSee('Stacked Deal');
    }
}
