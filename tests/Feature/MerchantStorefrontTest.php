<?php

namespace Tests\Feature;

use App\Livewire\GlobalSearch;
use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MerchantStorefrontTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(array $overrides = []): Merchant
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);

        return Merchant::create(array_merge([
            'user_id' => $owner->id,
            'business_name' => 'City Style',
            'trading_name' => 'CityStyle',
            'slug' => 'city-style',
            'verification_status' => 'approved',
            'is_active' => true,
        ], $overrides));
    }

    public function test_public_storefront_renders_for_active_merchant(): void
    {
        $m = $this->merchant();
        Offer::create(['merchant_id' => $m->id, 'title' => 'Ankara Bundle', 'slug' => 'ankara-bundle-x1', 'normal_price' => 90000, 'status' => 'active']);

        $this->get('/@city-style')
            ->assertOk()
            ->assertSee('CityStyle')
            ->assertSee('Ankara Bundle');
    }

    public function test_storefront_404s_for_inactive_or_unknown_merchant(): void
    {
        $this->merchant(['slug' => 'ghost-shop', 'is_active' => false]);

        $this->get('/@ghost-shop')->assertNotFound();
        $this->get('/@no-such-shop')->assertNotFound();
    }

    public function test_storefront_does_not_swallow_other_routes(): void
    {
        $this->get('/explore')->assertOk();
        $this->get('/wanted')->assertOk();
    }

    public function test_storefront_lists_only_active_offers(): void
    {
        $m = $this->merchant();
        Offer::create(['merchant_id' => $m->id, 'title' => 'Live Deal', 'slug' => 'live-deal-x1', 'normal_price' => 50000, 'status' => 'active']);
        Offer::create(['merchant_id' => $m->id, 'title' => 'Paused Deal', 'slug' => 'paused-deal-x1', 'normal_price' => 50000, 'status' => 'paused']);

        $this->get('/@city-style')->assertOk()->assertSee('Live Deal')->assertDontSee('Paused Deal');
    }

    public function test_global_search_returns_grouped_results(): void
    {
        $m = $this->merchant();
        $offer = Offer::create(['merchant_id' => $m->id, 'title' => 'Ankara Bundle', 'slug' => 'ankara-bundle-x2', 'normal_price' => 90000, 'status' => 'active']);
        Gang::create(['offer_id' => $offer->id, 'code' => 'ANK-123', 'target' => 10, 'status' => 'forming']);

        Livewire::test(GlobalSearch::class)
            ->set('q', 'Ankara')
            ->assertSee('Ankara Bundle')
            ->assertSee('CityStyle');
    }

    public function test_global_search_stays_closed_on_short_query(): void
    {
        \Livewire\Livewire::test(\App\Livewire\GlobalSearch::class)
            ->set('q', 'A')
            ->assertDontSee('See all results');
    }

    public function test_focus_shows_popular_suggestions(): void
    {
        $m = $this->merchant();
        Offer::create(['merchant_id' => $m->id, 'title' => 'Popular Bundle', 'slug' => 'popular-bundle-x3', 'normal_price' => 90000, 'confirmed_count' => 9, 'status' => 'active']);

        \Livewire\Livewire::test(\App\Livewire\GlobalSearch::class)
            ->call('reopen')
            ->assertSee('Popular right now')
            ->assertSee('Popular Bundle');
    }
}
