<?php
namespace App\Services;
use App\Models\Offer;
class PricingService {
    public function priceFor(Offer $offer, int $confirmedCount): int {
        $tiers = $offer->priceTiers()->orderBy('min_qty')->get();
        if ($tiers->isEmpty()) return (int) $offer->normal_price;
        $price = (int) $offer->normal_price;
        foreach ($tiers as $tier) {
            $maxOk = $tier->max_qty === null || $confirmedCount <= (int) $tier->max_qty;
            if ($confirmedCount >= (int) $tier->min_qty && $maxOk) $price = (int) $tier->price;
        }
        // also allow lower tier already unlocked by count exceeding min
        foreach ($tiers as $tier) { if ($confirmedCount >= (int) $tier->min_qty) $price = min($price, (int) $tier->price); }
        return $price;
    }
    public function nextTier(Offer $offer, int $confirmedCount): ?array {
        $tiers = $offer->priceTiers()->orderBy('min_qty')->get();
        foreach ($tiers as $tier) { if ((int) $tier->min_qty > $confirmedCount) return ['buyers' => (int) $tier->min_qty, 'price' => (int) $tier->price]; }
        return null;
    }
    public function savings(int $normal, int $gang): int { return max(0, $normal - $gang); }
    public function discountPct(int $normal, int $gang): float { return $normal > 0 ? round(($normal - $gang) / $normal * 100, 1) : 0; }
    public function bestAudiencePrice(Offer $offer): ?int {
        $best = $offer->audiences()->min('price');
        return $best === null ? null : (int) $best;
    }
    public function groupOptions(Offer $offer): array {
        $options = [['label' => 'Open group', 'min_buyers' => (int) ($offer->gang_target ?? 5), 'price' => (int) $offer->normal_price]];
        foreach ($offer->audiences()->orderBy('sort')->get() as $a) {
            $options[] = ['label' => $a->label, 'min_buyers' => (int) $a->min_buyers, 'price' => (int) $a->price];
        }
        return $options;
    }
}
