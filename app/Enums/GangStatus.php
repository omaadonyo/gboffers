<?php
namespace App\Enums;
enum GangStatus: string {
    case FORMING = 'forming';
    case UNLOCKED = 'unlocked';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
