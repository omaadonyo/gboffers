<?php
namespace App\Enums;
enum DisputeStatus: string {
    case OPEN = 'open';
    case UNDER_REVIEW = 'under_review';
    case RESOLVED_CUSTOMER = 'resolved_customer';
    case RESOLVED_MERCHANT = 'resolved_merchant';
    case CLOSED = 'closed';
}
