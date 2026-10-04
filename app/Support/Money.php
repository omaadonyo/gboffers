<?php
namespace App\Support;
class Money {
    public static function formatUgx(int $amount): string {
        return 'UGX ' . number_format($amount);
    }
    public static function shortUgx(int $amount): string {
        if ($amount >= 1000000) { $m = $amount / 1000000; return 'UGX ' . rtrim(rtrim(number_format($m, 2), '0'), '.') . 'M'; }
        if ($amount >= 1000) { $k = $amount / 1000; return 'UGX ' . rtrim(rtrim(number_format($k, 1), '0'), '.') . 'K'; }
        return 'UGX ' . number_format($amount);
    }
}
