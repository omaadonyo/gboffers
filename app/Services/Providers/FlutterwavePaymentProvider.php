<?php

namespace App\Services\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Models\Order;

class FlutterwavePaymentProvider implements PaymentProviderInterface
{
    public function name(): string
    {
        return 'flutterwave';
    }

    public function instructions(Order $order): array
    {
        $order->loadMissing('merchant');
        $ref = (string) $order->payment_reference;
        $base = rtrim((string) config('gboffers.payments.flutterwave.checkout_url', ''), '/');

        return [
            'merchant' => $order->merchant->trading_name ?? $order->merchant->business_name,
            'amount' => (int) $order->total,
            'currency' => $order->currency ?? 'UGX',
            'pay_to' => ['Provider' => 'Flutterwave', 'Reference' => $ref],
            'reference' => $ref,
            'checkout_url' => $base !== '' ? $base.'?reference='.$ref.'&amount='.(int) $order->total : null,
            'note' => 'Pay by card, bank transfer or mobile money through Flutterwave, then come back and tap "I have paid". The merchant still confirms receipt.',
        ];
    }

    public function verifyWebhook(array $payload, string $signature): string
    {
        $secret = (string) config('gboffers.payments.flutterwave.webhook_secret', '');
        if ($secret === '' || ! hash_equals($secret, $signature)) {
            throw new \RuntimeException('Invalid Flutterwave webhook signature.');
        }

        return (string) ($payload['data']['tx_ref'] ?? $payload['tx_ref'] ?? throw new \RuntimeException('Missing tx_ref.'));
    }
}
