<?php
namespace App\Enums;
enum FulfillmentStatus: string {
    case PENDING = 'pending';
    case AWAITING_PICKUP = 'awaiting_pickup';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED_PENDING_CONFIRMATION = 'delivered_pending_confirmation';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
}
