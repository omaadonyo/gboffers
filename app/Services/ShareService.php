<?php
namespace App\Services;
use App\Models\Gang;
use App\Support\Money;
class ShareService {
    public function gangMessage(Gang $gang): string {
        $gang->loadMissing('offer');
        $o = $gang->offer;
        $price = app(PricingService::class)->priceFor($o, $gang->confirmed_count);
        $need = max(0, $gang->target - $gang->confirmed_count);
        $link = url('/offers/'.$o->slug.'?group='.$gang->code);
        return "I\u2019m joining a GBOffers group for {$o->title}.\nNormal: ".Money::formatUgx((int)$o->normal_price)."\nGroup price: ".Money::formatUgx($price)."\nWe need {$need} more buyer(s).\nJoin me: {$link}";
    }
    public function whatsappUrl(Gang $gang): string {
        return 'https://wa.me/?text='.rawurlencode($this->gangMessage($gang));
    }
    public function track(?\App\Models\User $user, ?Offer $offer, ?Gang $gang, string $channel = 'whatsapp', ?\App\Models\Merchant $merchant = null): void {
        $key = 'shared:'.($user?->id ?? request()->ip()).':'.($offer?->id ?? 0).':'.($gang?->id ?? 0).':'.$channel;
        if (! \Illuminate\Support\Facades\Cache::add($key, true, 300)) return;
        \App\Models\Share::create([
            'user_id' => $user?->id,
            'offer_id' => $offer?->id,
            'gang_id' => $gang?->id,
            'merchant_id' => $offer?->merchant_id ?? $merchant?->id,
            'channel' => $channel,
        ]);
    }
}
