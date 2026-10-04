<?php
namespace App\Contracts;
interface PaymentProviderInterface {
    public function name(): string;
    /** Human-readable payment instructions for direct-merchant model. */
    public function instructions(\App\Models\Order $order): array;
    /** Verify an async provider webhook payload. Returns provider txn id or throws. */
    public function verifyWebhook(array $payload, string $signature): string;
}
