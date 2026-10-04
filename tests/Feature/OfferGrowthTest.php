<?php

namespace Tests\Feature;

use App\Jobs\ExpireFeaturedListingsJob;
use App\Livewire\Admin\Featured;
use App\Livewire\Merchant\Offers;
use App\Livewire\OfferBrowser;
use App\Livewire\OfferShow;
use App\Models\FeaturedListing;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\OfferView;
use App\Models\SearchTerm;
use App\Models\User;
use App\Notifications\PlatformNotification;
use App\Services\PaymentService;
use App\Services\Providers\FlutterwavePaymentProvider;
use App\Services\Providers\IotecPaymentProvider;
use App\Services\Providers\MomoDirectPaymentProvider;
use App\Support\FeaturedPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OfferGrowthTest extends TestCase
{
    use RefreshDatabase;

    private function world(): array
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create(['user_id' => $owner->id, 'business_name' => 'M', 'trading_name' => 'M', 'slug' => 'm', 'verification_status' => 'approved', 'is_active' => true]);
        $buyer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now(), 'name' => 'Scores Buyer']);
        $buyer->profile()->firstOrCreate([], ['display_name' => 'Scores Buyer']);
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Growth Deal', 'slug' => 'growth-deal', 'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active']);
        $gang = Gang::create(['offer_id' => $offer->id, 'code' => 'G-GR-1', 'target' => 5, 'confirmed_count' => 2, 'status' => 'forming']);

        return compact('owner', 'merchant', 'buyer', 'offer', 'gang');
    }

    public function test_offer_page_lists_all_confirmed_buyers(): void
    {
        ['buyer' => $buyer, 'offer' => $offer, 'gang' => $gang] = $this->world();
        $buyer2 = User::factory()->create(['role' => 'customer', 'email_verified_at' => now(), 'name' => 'Second Buyer']);
        GangMember::create(['gang_id' => $gang->id, 'user_id' => $buyer->id, 'offer_id' => $offer->id, 'status' => 'payment_confirmed']);
        GangMember::create(['gang_id' => $gang->id, 'user_id' => $buyer2->id, 'offer_id' => $offer->id, 'status' => 'ready']);

        $this->get(route('offers.show', $offer))
            ->assertOk()
            ->assertSee('Confirmed buyers')
            ->assertSee('Scores Buyer')
            ->assertSee('Second B.');
    }

    public function test_interested_first_then_confirm_flow(): void
    {
        ['buyer' => $buyer, 'offer' => $offer] = $this->world();

        Livewire::actingAs($buyer)->test(OfferShow::class, ['offer' => $offer])
            ->call('interested')
            ->assertSee('interested ✓');
        $this->assertDatabaseHas('gang_members', ['user_id' => $buyer->id, 'status' => 'interested']);

        $this->actingAs($buyer)->get(route('offers.show', $offer))->assertOk()->assertSee('Confirm & pay directly');
    }

    public function test_offer_views_tracked_once_per_hour(): void
    {
        ['offer' => $offer] = $this->world();

        $this->get(route('offers.show', $offer))->assertOk();
        $this->get(route('offers.show', $offer))->assertOk();

        $this->assertEquals(1, OfferView::where('offer_id', $offer->id)->count());
        $this->assertEquals(2, $offer->fresh()->views);
    }

    public function test_search_terms_tracked_throttled(): void
    {
        Livewire::test(OfferBrowser::class)->set('q', 'Growth');

        $row = SearchTerm::where('term', 'growth')->firstOrFail();
        $this->assertEquals(1, $row->hits);

        Livewire::test(OfferBrowser::class)->set('q', 'Growth');
        $this->assertEquals(1, $row->fresh()->hits);
    }

    public function test_featured_pricing_rules(): void
    {
        $this->assertEquals(1000, FeaturedPricing::priceForDays(1));
        $this->assertEquals(7000, FeaturedPricing::priceForDays(7));
        $this->assertEquals(6500, FeaturedPricing::priceForDays(10));
    }

    public function test_merchant_feature_request_and_admin_approval(): void
    {
        ['owner' => $owner, 'merchant' => $merchant] = $this->world();
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Feature Me', 'slug' => 'feature-me', 'normal_price' => 50000, 'gang_target' => 5, 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        Livewire::actingAs($owner)->test(Offers::class)
            ->call('feature', $offer->id)
            ->set('featureDays', 10)
            ->call('submitFeature');

        $listing = FeaturedListing::firstOrFail();
        $this->assertEquals('pending', $listing->status);
        $this->assertEquals(6500, $listing->amount);

        Livewire::actingAs($admin)->test(Featured::class)->call('approve', $listing->id);

        $this->assertEquals('active', $listing->fresh()->status);
        $this->assertTrue($offer->fresh()->featured);
        $this->assertTrue($owner->notifications()->where('type', PlatformNotification::class)->exists());
    }

    public function test_featured_expiry_job_unfeatures(): void
    {
        ['merchant' => $merchant] = $this->world();
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Old Feature', 'slug' => 'old-feature', 'normal_price' => 50000, 'gang_target' => 5, 'status' => 'active', 'featured' => true]);
        FeaturedListing::create(['offer_id' => $offer->id, 'days' => 1, 'amount' => 1000, 'status' => 'active', 'starts_at' => now()->subDays(2), 'ends_at' => now()->subHour()]);

        (new ExpireFeaturedListingsJob)->handle();

        $this->assertEquals('expired', FeaturedListing::first()->status);
        $this->assertFalse($offer->fresh()->featured);
    }

    public function test_payment_providers_and_checkout(): void
    {
        ['buyer' => $buyer, 'merchant' => $merchant, 'offer' => $offer, 'gang' => $gang] = $this->world();

        $svc = app(PaymentService::class);
        $this->assertInstanceOf(MomoDirectPaymentProvider::class, $svc->providerFor('momo_direct'));
        $this->assertInstanceOf(FlutterwavePaymentProvider::class, $svc->providerFor('flutterwave'));
        $this->assertInstanceOf(IotecPaymentProvider::class, $svc->providerFor('iotec'));

        $order = $svc->createOrder($buyer, $merchant, $offer, $gang, 90000, 1, 'momo_direct');
        $this->assertEquals('momo_direct', $order->payment_method);
        $this->assertNotEmpty($order->payment_reference);

        $this->actingAs($buyer)->get('/checkout/'.$order->id)
            ->assertOk()
            ->assertSee('Mobile money (MoMo)')
            ->assertSee($order->payment_reference);
    }

    public function test_admin_insights_page_renders(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($admin)->get('/admin/insights')->assertOk()->assertSee('search trends');
        $this->actingAs($admin)->get('/admin/featured')->assertOk()->assertSee('Featured listings');
    }
}
