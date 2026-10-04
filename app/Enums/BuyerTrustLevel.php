<?php
namespace App\Enums;
enum BuyerTrustLevel: string {
    case NEW = 'new';
    case VERIFIED = 'verified';
    case TRUSTED = 'trusted';
    case LIMITED = 'limited';
    public function reservationLimit(): int {
        return match($this) {
            self::NEW => 2, self::VERIFIED => 3, self::TRUSTED => 5, self::LIMITED => 0,
        };
    }
}
