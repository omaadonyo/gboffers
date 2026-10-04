<?php

namespace App\Services\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Models\Order;

class MomoDirectPaymentProvider implements PaymentProviderInterface
{
    public function name(): string
    {
        return 'momo_direct';
    }

    public function instructions(Order $order): array
    {
        $order->loadMissing('merchant');
        $details = $order->merchant->payment_details ?? [];
        $network = is_array($details) ? ($details['network'] ?? 'MTN') : 'MTN';
        $account = is_array($details) ? ($details['account_number'] ?? $details['account_name'] ?? 'Ask merchant') : 'Ask merchant';
        $name = is_array($details) ? ($details['account_name'] ?? $order->merchant->trading_name) : $order->merchant->trading_name;

        return [
            'merchant' => $order->merchant->trading_name ?? $order->merchant->business_name,
            'amount' => (int) $order->total,
            'currency' => $order->currency ?? 'UGX',
            'pay_to' => ['Network' => $network, 'Merchant number' => $account, 'Account name' => $name],
            'reference' => (string) $order->payment_reference,
            'note' => 'Send exactly this amount via mobile money and use the reference code above as the payment reason. The merchant confirms receipt in their own account.',
            'steps' => ['Dial your MoMo menu', 'Choose Send Money to '.$network.' number '.$account, 'Enter amount '.number_format((int) $order->total).' UGX', 'Put reference '.(string) $order->payment_reference.' as the reason', 'Come back here and tap "I have paid"'],
        ];
    }

    public function verifyWebhook(array $payload, string $signature): string
    {
        throw new \RuntimeException('Direct MoMo payments are confirmed by the merchant, not webhooks.');
    }
}
