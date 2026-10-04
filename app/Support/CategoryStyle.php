<?php

namespace App\Support;

class CategoryStyle
{
    public const ICONS = [
        'food' => 'utensils',
        'electronics' => 'tv',
        'fashion' => 'shirt',
        'home' => 'sofa',
        'beauty' => 'sparkles',
        'services' => 'wrench',
        'experiences' => 'ticket',
        'travel' => 'map',
    ];

    public static function icon(?string $slug): string
    {
        return self::ICONS[$slug ?? ''] ?? 'layout-grid';
    }

    public static function iconFor(mixed $category): string
    {
        $stored = is_object($category) ? ($category->icon ?? null) : null;
        if (is_string($stored) && $stored !== '') {
            return $stored;
        }

        return self::icon(is_object($category) ? ($category->slug ?? null) : null);
    }
}
