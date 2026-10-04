<?php
namespace Tests\Feature;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use App\Services\GangService;
use App\Services\PaymentService;
use App\Services\RedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class MarketplaceFlowTest extends TestCase {
    use RefreshDatabase;
    protected function seedCore(): array {
        $cat = Category::create(['name'=>'Electronics','slug'=>'electronics']);
        $owner = User::create(['name'=>'Owner','email'=>'o@x.ug','password'=>Hash::make('p'),'role'=>'merchant_owner']);
        $m = Merchant::create(['user_id'=>$owner->id,'category_id'=>$cat->id,'business_name'=>'Pearl','slug'=>'pearl','verification_status'=>'approved','is_active'=>true]);
        $offer = Offer::create(['merchant_id'=>$m->id,'category_id'=>$cat->id,'title'=>'TV','slug'=>'tv','normal_price'=>1800000,'gang_target'=>2,'status'=>'active','ends_at'=>now()->addDay()]);
        $offer->priceTiers()->create(['min_qty'=>1,'price'=>1800000,'sort'=>0]);
        $offer->priceTiers()->create(['min_qty'=>2,'price'=>1550000,'sort'=>1]);
        return [$m, $offer];
    }
    public function test_join_reserve_report_confirm_redeem(): void {
        [$m, $offer] = $this->seedCore();
        $u = User::create(['name'=>'Sarah K','email'=>'s@x.ug','password'=>Hash::make('p'),'role'=>'customer']);
        $u->profile()->create(['trust_level'=>'verified']);
        $member = app(GangService::class)->joinGang($u, $offer);
        $this->assertSame('reserved', $member->status);
        $this->assertNotEquals(0, $member->reservation_id);
        // interested never counts:
        $this->assertSame(0, $offer->fresh()->confirmed_count);
        $gang = $member->gang()->first();
        $order = app(PaymentService::class)->createOrder($u, $m, $offer, $gang, 1800000);
        $member->update(['order_id'=>$order->id,'status'=>'payment_pending']);
        app(PaymentService::class)->reportPaid($order);
        $this->assertSame('reported', $order->payment()->first()->status);
        $this->assertSame('pending', $order->fresh()->payment_status); // report != confirmed
        app(PaymentService::class)->confirmPayment($order, $m->owner, $m, true);
        $order->refresh();
        $this->assertSame('confirmed', $order->payment_status);
        $pass = $order->gbPass()->first();
        $this->assertNotNull($pass);
        $this->assertMatchesRegularExpression('/^GB-/', $pass->token);
        // customer cannot redeem own pass:
        $this->expectException(\App\Exceptions\InvalidRedemptionException::class);
        app(RedemptionService::class)->redeem($pass, $m, $u, '0000');
    }
    public function test_double_redeem_impossible(): void {
        [$m, $offer] = $this->seedCore();
        $u = User::create(['name'=>'A','email'=>'a@x.ug','password'=>Hash::make('p'),'role'=>'customer']);
        $u->profile()->create(['trust_level'=>'verified']);
        $member = app(GangService::class)->joinGang($u, $offer);
        $gang = $member->gang()->first();
        $order = app(PaymentService::class)->createOrder($u, $m, $offer, $gang, 1800000);
        $member->update(['order_id'=>$order->id,'status'=>'payment_pending']);
        app(PaymentService::class)->confirmPayment($order, $m->owner, $m, true);
        $pass = $order->fresh()->gbPass()->first();
        $pin = '1234';
        $pass->update(['pin_hash'=>Hash::make($pin)]);
        $ok = app(RedemptionService::class)->redeem($pass, $m, $m->owner, $pin);
        $this->assertSame('redeemed', $ok->status);
        try { app(RedemptionService::class)->redeem($pass, $m, $m->owner, $pin); $this->fail('second redeem must fail'); }
        catch (\App\Exceptions\InvalidRedemptionException $e) { $this->assertTrue(true); }
    }
    public function test_reservation_expiry(): void {
        [$m, $offer] = $this->seedCore();
        $u = User::create(['name'=>'B','email'=>'b@x.ug','password'=>Hash::make('p'),'role'=>'customer']);
        $u->profile()->create(['trust_level'=>'new']);
        $member = app(GangService::class)->joinGang($u, $offer);
        $res = $member->reservation()->first();
        $res->update(['expires_at'=>now()->subMinute()]);
        $n = app(\App\Services\ReservationService::class)->expireDue();
        $this->assertGreaterThanOrEqual(1, $n);
        $this->assertSame('expired', $res->fresh()->status);
    }
}
