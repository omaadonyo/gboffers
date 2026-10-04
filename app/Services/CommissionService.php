<?php
namespace App\Services;
use App\Models\Commission;
use App\Models\Order;
class CommissionService {
    public function calculateFor(Order $order): array {
        $order->loadMissing('offer.category','merchant');
        $slug = strtolower((string) ($order->offer->category->slug ?? ''));
        $cfg = config('gboffers.commission', []);
        if (str_contains($slug,'food') && isset($cfg['food'])) { $rule='food:fixed:'.($cfg['food']['amount'] ?? 3000); $amount=(int)($cfg['food']['amount'] ?? 3000); }
        elseif ((str_contains($slug,'electronic')||str_contains($slug,'tech')) && isset($cfg['electronics'])) { $rule='electronics:fixed:'.($cfg['electronics']['amount'] ?? 5000); $amount=(int)($cfg['electronics']['amount'] ?? 5000); }
        else { $rate=(float)($cfg['default']['rate'] ?? 2.0); $rule='default:pct:'.$rate; $amount=(int) round(((int)$order->total) * $rate / 100); }
        return ['amount'=>$amount,'rule'=>$rule];
    }
    public function record(Order $order): Commission {
        $calc = $this->calculateFor($order);
        return Commission::updateOrCreate(['order_id'=>$order->id], [
            'merchant_id'=>$order->merchant_id,'amount'=>$calc['amount'],'currency'=>$order->currency ?? 'UGX','rule'=>$calc['rule'],'status'=>'pending',
        ]);
    }
}
