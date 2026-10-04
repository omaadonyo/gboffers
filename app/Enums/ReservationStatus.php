<?php
namespace App\Enums;
enum ReservationStatus: string {
    case ACTIVE = 'active';
    case COMMITTED = 'committed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
