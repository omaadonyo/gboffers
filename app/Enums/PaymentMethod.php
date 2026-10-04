<?php
namespace App\Enums;
enum PaymentMethod: string {
    case MERCHANT_DIRECT = 'merchant_direct';
    case PROTECTED = 'protected';
    case MOMO_DIRECT = 'momo_direct';
    case FLUTTERWAVE = 'flutterwave';
    case IOTEC = 'iotec';
    public function label(): string {
        return match($this) {
            self::MERCHANT_DIRECT => 'Pay merchant directly',
            self::PROTECTED => 'Protected checkout',
            self::MOMO_DIRECT => 'Mobile money (MoMo)',
            self::FLUTTERWAVE => 'Flutterwave',
            self::IOTEC => 'iOTEC Pay',
        };
    }
    public function hint(): string {
        return match($this) {
            self::MERCHANT_DIRECT => 'Bank transfer to the merchant',
            self::MOMO_DIRECT => 'MTN / Airtel to the merchant till',
            self::FLUTTERWAVE => 'Card, bank or MoMo via Flutterwave',
            self::IOTEC => 'Pay via iOTEC',
            self::PROTECTED => 'GBOffers-held escrow',
        };
    }
}
