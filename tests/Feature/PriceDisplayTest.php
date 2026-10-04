<?php

namespace Tests\Feature;

use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\OfferPriceTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function offer(array $overrides = []): Offer
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create([
            'user_id' => $owner->id, 'business_name' => 'Pearl', 'trading_name' => 'Pearl',
            'slug' => 'pearl-'.uniqid(), 'verification_status' => 'approved', 'is_active' => true,
        ]);

        return Offer::create(array_merge([
            'merchant_id' => $merchant->id, 'title' => 'Test Offer', 'slug' => 'test-offer-'.uniqid(),
            'normal_price' => 100000, 'status' => 'active',
        ], $overrides));
    }

    public function test_no_strikethrough_when_no_discount_unlocked(): void
    {
        $this->offer(['title' => 'Full Price Item', 'slug' => 'full-price-item']);

        $this->get('/explore')->assertOk()->assertSee('Full Price Item')->assertDontSee('line-through');
    }

    public function test_strikethrough_and_savings_show_once_tier_unlocks(): void
    {
        $offer = $this->offer(['title' => 'Discounted Item', 'slug' => 'discounted-item']);
        OfferPriceTier::create(['offer_id' => $offer->id, 'min_qty' => 1, 'price' => 80000, 'sort' => 0]);
        Gang::create(['offer_id' => $offer->id, 'code' => 'G-PD-1', 'target' => 5, 'confirmed_count' => 2, 'status' => 'forming']);

        $this->get('/explore')->assertOk()->assertSee('Discounted Item')->assertSee('line-through');
    }
}
