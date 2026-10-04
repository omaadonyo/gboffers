<?php
namespace App\Services\Providers;
use App\Contracts\PaymentProviderInterface;
use App\Models\Order;
class MerchantDirectPaymentProvider implements PaymentProviderInterface {
    public function name(): string { return 'merchant_direct'; }
    public function instructions(Order $order): array {
        $order->loadMissing('merchant');
        $details = $order->merchant->payment_details ?? [];
        return [
            'merchant' => $order->merchant->trading_name ?? $order->merchant->business_name,
            'amount' => (int) $order->total,
            'currency' => $order->currency ?? 'UGX',
            'pay_to' => is_array($details) ? $details : ['info' => $details],
            'reference' => (string) $order->payment_reference,
            'note' => 'Your payment goes directly to the merchant. GBOffers does not receive or hold this payment.',
        ];
    }
    public function verifyWebhook(array $payload, string $signature): string {
        throw new \RuntimeException('Direct merchant payments have no webhooks.');
    }
}
