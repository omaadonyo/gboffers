<?php
namespace App\Enums;
enum CommissionStatus: string {
    case PENDING = 'pending';
    case INVOICED = 'invoiced';
    case SETTLED = 'settled';
    case DISPUTED = 'disputed';
    case WAIVED = 'waived';
}
