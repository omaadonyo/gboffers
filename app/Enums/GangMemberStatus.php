<?php

namespace App\Enums;

enum GangMemberStatus: string
{
    case INTERESTED = 'interested';
    case RESERVED = 'reserved';
    case PAYMENT_PENDING = 'payment_pending';
    case PAYMENT_CONFIRMED = 'payment_confirmed';
    case READY = 'ready';
    case REDEEMED = 'redeemed';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case DISPUTED = 'disputed';
    case REFUNDED = 'refunded';

    /** Only these count toward the gang target (Rule #3). */
    public static function confirmedStates(): array
    {
        return [
            self::PAYMENT_CONFIRMED,
            self::READY,
            self::REDEEMED,
            self::FULFILLED,
        ];
    }

    public function countsTowardTarget(): bool
    {
        return in_array($this, self::confirmedStates(), true);
    }

    public function label(): string
    {
        return match ($this) {
            self::INTERESTED => 'Interested',
            self::RESERVED => 'Reserved',
            self::PAYMENT_PENDING => 'Payment pending',
            self::PAYMENT_CONFIRMED => 'Confirmed',
            self::READY => 'Ready',
            self::REDEEMED => 'Redeemed',
            self::FULFILLED => 'Fulfilled',
            self::CANCELLED => 'Cancelled',
            self::EXPIRED => 'Expired',
            self::DISPUTED => 'Disputed',
            self::REFUNDED => 'Refunded',
        };
    }
}
