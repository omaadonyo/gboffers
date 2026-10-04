<?php
namespace App\Enums;
enum GbPassStatus: string {
    case ISSUED = 'issued';
    case READY = 'ready';
    case REDEEMED = 'redeemed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
