<?php

namespace App\Services\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Models\Order;

class IotecPaymentProvider implements PaymentProviderInterface
{
    public function name(): string
    {
        return 'iotec';
    }

    public function instructions(Order $order): array
    {
        $order->loadMissing('merchant');
        $ref = (string) $order->payment_reference;
        $base = rtrim((string) config('gboffers.payments.iotec.checkout_url', ''), '/');

        return [
            'merchant' => $order->merchant->trading_name ?? $order->merchant->business_name,
            'amount' => (int) $order->total,
            'currency' => $order->currency ?? 'UGX',
            'pay_to' => ['Provider' => 'iOTEC', 'Reference' => $ref],
            'reference' => $ref,
            'checkout_url' => $base !== '' ? $base.'?reference='.$ref.'&amount='.(int) $order->total : null,
            'note' => 'Complete payment through iOTEC, then come back and tap "I have paid". The merchant still confirms receipt.',
        ];
    }

    public function verifyWebhook(array $payload, string $signature): string
    {
        $secret = (string) config('gboffers.payments.iotec.webhook_secret', '');
        if ($secret === '' || ! hash_equals($secret, $signature)) {
            throw new \RuntimeException('Invalid iOTEC webhook signature.');
        }

        return (string) ($payload['reference'] ?? throw new \RuntimeException('Missing reference.'));
    }
}
