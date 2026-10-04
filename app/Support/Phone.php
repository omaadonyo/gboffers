<?php
namespace App\Support;
class Phone {
    public static function normalizeUg(string $input): ?string {
        $d = preg_replace('/[^0-9+]/', '', trim($input));
        if ($d === '' || $d === null) return null;
        if (str_starts_with($d, '+256')) { $rest = substr($d, 4); }
        elseif (str_starts_with($d, '256')) { $rest = substr($d, 3); }
        elseif (str_starts_with($d, '0')) { $rest = substr($d, 1); }
        else { $rest = $d; }
        $rest = preg_replace('/[^0-9]/', '', $rest);
        if (strlen($rest) !== 9) return null;
        return '+256' . $rest;
    }
    public static function validUg(string $input): bool { return self::normalizeUg($input) !== null; }
    public static function mask(string $phone): string {
        return strlen($phone) > 4 ? substr($phone, 0, 4) . ' *** ' . substr($phone, -3) : $phone;
    }
}
