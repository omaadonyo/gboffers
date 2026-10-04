<?php
namespace App\Services;
use App\Contracts\PaymentProviderInterface;
use App\Enums\OrderStatus;
use App\Events\PaymentConfirmed;
use App\Events\PaymentReported;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Providers\FlutterwavePaymentProvider;
use App\Services\Providers\IotecPaymentProvider;
use App\Services\Providers\MerchantDirectPaymentProvider;
use App\Services\Providers\MomoDirectPaymentProvider;
use App\Support\GbToken;
use Illuminate\Support\Facades\DB;
class PaymentService {
    public function providerFor(string $name): PaymentProviderInterface {
        return match($name) {
            'momo_direct' => new MomoDirectPaymentProvider,
            'flutterwave' => new FlutterwavePaymentProvider,
            'iotec' => new IotecPaymentProvider,
            default => new MerchantDirectPaymentProvider,
        };
    }
    /** Create order + payment row after gang join (server-side amounts only). */
    public function createOrder(User $user, Merchant $merchant, Offer $offer, ?\App\Models\Gang $gang, int $unitPrice, int $qty = 1, string $method = 'merchant_direct'): Order {
        return DB::transaction(function () use ($user,$merchant,$offer,$gang,$unitPrice,$qty,$method) {
            $subtotal = $unitPrice * $qty;
            $order = Order::create([
                'user_id'=>$user->id,'merchant_id'=>$merchant->id,'offer_id'=>$offer->id,'gang_id'=>$gang?->id,
                'currency'=>'UGX','subtotal'=>$subtotal,'discount'=>max(0,(int)$offer->normal_price*$qty-$subtotal),'total'=>$subtotal,
                'payment_method'=>$method,'payment_provider'=>$method,'payment_status'=>'pending','fulfillment_status'=>'pending','status'=>OrderStatus::PAYMENT_PENDING->value,'quantity'=>$qty,
            ]);
            $token = GbToken::passToken();
            while (Order::where('payment_reference',$token)->exists()) $token = GbToken::passToken();
            $order->update(['payment_reference'=>$token]);
            Payment::create(['order_id'=>$order->id,'merchant_id'=>$merchant->id,'amount'=>$subtotal,'currency'=>'UGX','method'=>$method,'provider'=>$method,'merchant_reference'=>$token,'status'=>'pending']);
            return $order->fresh();
        });
    }
    public function reportPaid(Order $order, ?string $evidencePath = null): void {
        // Customer acknowledgement only — NEVER marks paid. (Rule: screenshots are not proof.)
        DB::transaction(function () use ($order, $evidencePath) {
            $order->payment()->update(['status'=>'reported','reported_at'=>now(),'evidence_path'=>$evidencePath]);
            event(new PaymentReported($order->fresh()));
        });
    }
    /** Merchant verifies money in THEIR OWN account, then confirms. Server-side only. */
    public function confirmPayment(Order $order, User $staff, Merchant $merchant, bool $acknowledged): void {
        abort_unless($acknowledged, 422, 'Merchant must explicitly confirm receipt.');
        abort_unless((int)$order->merchant_id === (int)$merchant->id, 403);
        DB::transaction(function () use ($order, $staff) {
            $o = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (!in_array($o->payment_status, ['pending','reported'])) abort(422, 'Payment is not awaiting confirmation.');
            $o->payment()->update(['status'=>'confirmed','confirmed_at'=>now(),'confirmed_by'=>$staff->id]);
            $o->update(['payment_status'=>'confirmed','status'=>OrderStatus::PAYMENT_CONFIRMED->value]);
            // member -> confirmed (counts toward target)
            \App\Models\GangMember::where('order_id',$o->id)->update(['status'=>'payment_confirmed']);
            if ($o->gang_id) {
                $gang = \App\Models\Gang::whereKey($o->gang_id)->lockForUpdate()->first();
                if ($gang) { $gang->increment('confirmed_count'); $o->offer()->increment('confirmed_count'); (new GangService(app(ReservationService::class)))->checkUnlock($gang->fresh()); }
            }
            // issue GBPass
            app(GBPassService::class)->issueFor($o->fresh());
            $o->update(['status'=>OrderStatus::READY_FOR_REDEMPTION->value]);
            event(new PaymentConfirmed($o->fresh(), $staff));
        });
    }
}
