<?php

namespace Tests\Feature;

use App\Livewire\Merchant\Offers;
use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GroupDiscountsTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): array
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create([
            'user_id' => $owner->id, 'business_name' => 'City Style', 'trading_name' => 'CityStyle',
            'slug' => 'city-style', 'verification_status' => 'approved', 'is_active' => true,
        ]);

        return [$owner, $merchant];
    }

    public function test_merchant_can_post_product_with_group_discounts(): void
    {
        [$owner] = $this->owner();

        Livewire::actingAs($owner)->test(Offers::class)
            ->set('title', 'Ankara Bundle X')
            ->set('normal_price', 120000)
            ->set('gang_target', 6)
            ->set('tiers', '6:105000')
            ->set('audiences', "Students:5:100000\nStaff SACCO:10:95000\nbad line")
            ->call('save')
            ->assertSee('Offer created.');

        $offer = Offer::where('title', 'Ankara Bundle X')->first();
        $this->assertNotNull($offer);
        $this->assertCount(2, $offer->audiences);
        $this->assertEquals('Students', $offer->audiences->first()->label);
        $this->assertEquals(100000, $offer->audiences->first()->price);
    }

    public function test_offer_page_shows_discounts_per_group(): void
    {
        [$owner, $merchant] = $this->owner();
        $offer = Offer::create([
            'merchant_id' => $merchant->id, 'title' => 'Grouped Deal', 'slug' => 'grouped-deal',
            'normal_price' => 120000, 'gang_target' => 6, 'status' => 'active',
        ]);
        $offer->audiences()->create(['label' => 'Students', 'min_buyers' => 5, 'price' => 100000, 'sort' => 0]);

        $this->get(route('offers.show', $offer->slug))
            ->assertOk()
            ->assertSee('Discounts per group')
            ->assertSee('Students')
            ->assertSee('UGX 100,000');
    }

    public function test_offer_page_hides_group_panel_without_audiences(): void
    {
        [$owner, $merchant] = $this->owner();
        $offer = Offer::create([
            'merchant_id' => $merchant->id, 'title' => 'Plain Deal', 'slug' => 'plain-deal',
            'normal_price' => 120000, 'gang_target' => 6, 'status' => 'active',
        ]);

        $this->get(route('offers.show', $offer->slug))->assertOk()->assertDontSee('Discounts per group');
    }

    public function test_group_options_lists_open_gang_plus_audiences(): void
    {
        [$owner, $merchant] = $this->owner();
        $offer = Offer::create([
            'merchant_id' => $merchant->id, 'title' => 'Priced Deal', 'slug' => 'priced-deal',
            'normal_price' => 120000, 'gang_target' => 6, 'status' => 'active',
        ]);
        $offer->audiences()->create(['label' => 'Students', 'min_buyers' => 5, 'price' => 100000, 'sort' => 0]);

        $options = app(PricingService::class)->groupOptions($offer);

        $this->assertCount(2, $options);
        $this->assertEquals('Open group', $options[0]['label']);
        $this->assertEquals('Students', $options[1]['label']);
        $this->assertEquals(100000, $options[1]['price']);
    }

    public function test_explore_and_homepage_render_skeletons_and_new_sections(): void
    {
        [$owner, $merchant] = $this->owner();
        $offer = Offer::create([
            'merchant_id' => $merchant->id, 'title' => 'Trending Deal', 'slug' => 'trending-deal',
            'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active', 'confirmed_count' => 3,
        ]);
        Gang::create(['offer_id' => $offer->id, 'code' => 'G-SK-1', 'target' => 5, 'confirmed_count' => 3, 'status' => 'forming']);

        $this->get('/explore')->assertOk()->assertSee('animate-pulse');
        $this->get('/')->assertOk()->assertSee('Trending now')->assertSee('Deal of the day');
    }
}
