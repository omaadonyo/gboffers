<?php

namespace App\Support;

class VerificationPricing
{
    public const PROFILE_FEE = 3500;

    public const PROFILE_SLA = '5–10 minutes';

    public const SHOP_FEE = 50000;

    public const SHOP_SLA = '24 hours';

    public static function feeFor(string $type): int
    {
        return $type === 'shop' ? self::SHOP_FEE : self::PROFILE_FEE;
    }

    public static function slaFor(string $type): string
    {
        return $type === 'shop' ? self::SHOP_SLA : self::PROFILE_SLA;
    }
}
