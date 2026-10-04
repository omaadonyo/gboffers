<?php
namespace App\Support;
class GbToken {
    public static function passToken(): string {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $pick = fn(int $n): string => implode('', array_map(fn() => $alphabet[random_int(0, strlen($alphabet) - 1)], range(1, $n)));
        return 'GB-' . $pick(4) . '-' . $pick(4);
    }
    public static function gangCode(): string {
        return 'GANG-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }
    public static function inviteCode(): string { return strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)); }
    public static function redemptionPin(): string { return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT); }
    public static function deliveryOtp(): string { return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT); }
    public static function paymentReference(string $token): string { return $token; }
}
