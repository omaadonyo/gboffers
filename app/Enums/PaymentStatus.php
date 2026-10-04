<?php
namespace App\Enums;
enum PaymentStatus: string {
    case PENDING = 'pending';
    case REPORTED = 'reported';
    case CONFIRMED = 'confirmed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    public function label(): string { return ucfirst($this->value); }
}
