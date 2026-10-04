<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case RESERVED = 'reserved';
    case PAYMENT_PENDING = 'payment_pending';
    case PAYMENT_CONFIRMED = 'payment_confirmed';
    case READY_FOR_REDEMPTION = 'ready_for_redemption';
    case REDEEMED = 'redeemed';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case DISPUTED = 'disputed';
    case REFUNDED = 'refunded';

    /** Valid state-machine transitions. No arbitrary jumps allowed. */
    public static function transitions(): array
    {
        return [
            self::DRAFT->value => [self::RESERVED, self::CANCELLED],
            self::RESERVED->value => [self::PAYMENT_PENDING, self::EXPIRED, self::CANCELLED],
            self::PAYMENT_PENDING->value => [self::PAYMENT_CONFIRMED, self::EXPIRED, self::CANCELLED],
            self::PAYMENT_CONFIRMED->value => [self::READY_FOR_REDEMPTION, self::DISPUTED],
            self::READY_FOR_REDEMPTION->value => [self::REDEEMED, self::DISPUTED, self::EXPIRED],
            self::REDEEMED->value => [self::FULFILLED, self::DISPUTED],
            self::FULFILLED->value => [self::DISPUTED],
            self::DISPUTED->value => [self::REFUNDED, self::FULFILLED, self::CANCELLED],
            self::CANCELLED->value => [],
            self::EXPIRED->value => [],
            self::REFUNDED->value => [],
        ];
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, self::transitions()[$this->value] ?? [], true);
    }
}
