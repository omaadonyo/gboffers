<?php

namespace Tests\Feature;

use App\Livewire\Checkout;
use App\Livewire\MyOrders;
use App\Livewire\OfferImporter;
use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use App\Services\ImportService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutOrdersTest extends TestCase
{
    use RefreshDatabase;

    private function world(): array
    {
        $owner = User::factory()->create(['role' => 'merchant_owner', 'email_verified_at' => now()]);
        $merchant = Merchant::create(['user_id' => $owner->id, 'business_name' => 'M', 'trading_name' => 'M', 'slug' => 'm', 'verification_status' => 'approved', 'is_active' => true, 'payment_details' => ['network' => 'MTN', 'account_number' => '0772111111', 'account_name' => 'M']]);
        $buyer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $offer = Offer::create(['merchant_id' => $merchant->id, 'title' => 'Pay Deal', 'slug' => 'pay-deal', 'normal_price' => 100000, 'gang_target' => 5, 'status' => 'active', 'ends_at' => now()->addDays(2)]);
        $gang = Gang::create(['offer_id' => $offer->id, 'code' => 'G-PO-1', 'target' => 5, 'status' => 'forming']);

        return compact('owner', 'merchant', 'buyer', 'offer', 'gang');
    }

    private function order(array $w, string $method = 'momo_direct'): Order
    {
        return app(PaymentService::class)->createOrder($w['buyer'], $w['merchant'], $w['offer'], $w['gang'], 90000, 1, $method);
    }

    public function test_checkout_method_switch_and_terms(): void
    {
        $w = $this->world();
        $order = $this->order($w);

        $this->actingAs($w['buyer'])->get('/checkout/'.$order->id)
            ->assertOk()
            ->assertSee('Mobile money (MoMo)')
            ->assertSee('Dial your MoMo menu')
            ->assertSee($order->payment_reference);

        Livewire::actingAs($w['buyer'])->test(Checkout::class, ['order' => $order])
            ->call('switchMethod', 'flutterwave');

        $this->assertEquals('flutterwave', $order->fresh()->payment_method);
        $this->actingAs($w['buyer'])->get('/checkout/'.$order->id)
            ->assertOk()
            ->assertSee('Flutterwave')
            ->assertSee('never sees my card');
    }

    public function test_method_switch_blocked_after_report(): void
    {
        $w = $this->world();
        $order = $this->order($w);
        $order->update(['payment_status' => 'reported']);

        Livewire::actingAs($w['buyer'])->test(Checkout::class, ['order' => $order])
            ->call('switchMethod', 'iotec')
            ->assertStatus(422);
    }

    public function test_orders_page_lists_and_filters(): void
    {
        $w = $this->world();
        $o1 = $this->order($w);
        $o2 = $this->order($w);
        $o2->update(['status' => 'fulfilled']);

        $page = $this->actingAs($w['buyer'])->get('/orders')->assertOk();
        $page->assertSee('Order #'.$o1->id)->assertSee('Order #'.$o2->id);

        Livewire::actingAs($w['buyer'])->test(MyOrders::class)
            ->set('filter', 'done')
            ->assertSee('Order #'.$o2->id)
            ->assertDontSee('Order #'.$o1->id);
    }

    public function test_offer_countdown_renders(): void
    {
        $w = $this->world();

        $this->get(route('offers.show', $w['offer']))
            ->assertOk()
            ->assertSee('role="timer"', false)
            ->assertSee($w['offer']->ends_at->toISOString());
    }

    public function test_import_service_extracts_product(): void
    {
        Http::fake(['shop.test/*' => Http::response(
            '<html><head><title>Test Rice 25kg | Shop</title><meta property="og:description" content="Premium rice for families."><meta property="og:image" content="/img/rice.jpg"></head><body><p>Best rice in town, harvested this season from Mbale farms.</p><span>UGX 85,000</span></body></html>',
            200, ['Content-Type' => 'text/html']
        )]);

        $data = app(ImportService::class)->fetch('https://shop.test/rice-25kg');

        $this->assertEquals('Test Rice 25kg | Shop', $data['title']);
        $this->assertEquals('Premium rice for families.', $data['description']);
        $this->assertEquals('https://shop.test/img/rice.jpg', $data['image']);
        $this->assertEquals(85000, $data['price']);
    }

    public function test_import_service_rejects_bad_urls(): void
    {
        Http::fake(['shop.test/missing' => Http::response('', 404, ['Content-Type' => 'text/html'])]);

        $this->expectException(\RuntimeException::class);
        app(ImportService::class)->fetch('https://shop.test/missing');
    }

    public function test_merchant_can_import_offer_from_url(): void
    {
        Http::fake(['shop.test/*' => Http::response(
            '<html><head><title>Beans 10kg</title><meta property="og:image" content="https://shop.test/b.jpg"></head><body><span>UGX 42,500</span></body></html>',
            200, ['Content-Type' => 'text/html']
        )]);
        ['owner' => $owner, 'merchant' => $merchant] = $this->world();

        Livewire::actingAs($owner)->test(OfferImporter::class, ['merchantId' => $merchant->id])
            ->set('url', 'https://shop.test/beans')
            ->call('fetch')
            ->assertSet('title', 'Beans 10kg')
            ->assertSet('price', 42500)
            ->call('create');

        $offer = Offer::where('title', 'Beans 10kg')->firstOrFail();
        $this->assertEquals($merchant->id, $offer->merchant_id);
        $this->assertEquals('https://shop.test/b.jpg', $offer->image_path);
    }

    public function test_admin_import_page_requires_admin(): void
    {
        ['buyer' => $buyer] = $this->world();
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($admin)->get('/admin/import')->assertOk()->assertSee('Import from a link');
        $this->actingAs($buyer)->get('/admin/import')->assertForbidden();
    }
}
