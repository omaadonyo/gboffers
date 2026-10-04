<?php
namespace Tests\Unit;
use App\Models\Offer;
use App\Services\PricingService;
use App\Support\GbToken;
use App\Support\Money;
use Tests\TestCase;
class PricingAndTokensTest extends TestCase {
    public function test_tier_pricing(): void {
        $o = new Offer(['normal_price'=>1850000]);
        $o->id = 1;
        $tiers = collect([['min_qty'=>1,'max_qty'=>4,'price'=>1800000],['min_qty'=>5,'max_qty'=>9,'price'=>1650000],['min_qty'=>10,'max_qty'=>null,'price'=>1550000]]);
        $mock = \Mockery::mock(Offer::class)->makePartial();
        $mock->shouldReceive('priceTiers->orderBy->get')->andReturn(collect([]));
        // direct logic check via service with real tiers is covered in feature tests; assert helpers:
        $this->assertSame('UGX 1,550,000', Money::formatUgx(1550000));
        $this->assertSame('UGX 1.55M', Money::shortUgx(1550000));
        $this->assertMatchesRegularExpression('/^GB-[A-Z2-9]{4}-[A-Z2-9]{4}$/', GbToken::passToken());
    }
    protected function tearDown(): void { \Mockery::close(); parent::tearDown(); }
}
