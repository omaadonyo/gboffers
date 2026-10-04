<?php

namespace App\Support;

class FeaturedPricing
{
    public const PER_DAY = 1000;

    public const BUNDLE_DAYS = 10;

    public const BUNDLE_PRICE = 6500;

    public static function priceForDays(int $days): int
    {
        $days = max(1, min(30, $days));

        return $days === self::BUNDLE_DAYS ? self::BUNDLE_PRICE : $days * self::PER_DAY;
    }

    public static function options(): array
    {
        return [1, 3, 7, 10];
    }
}
