<?php
namespace App\Enums;
enum UserRole: string {
    case CUSTOMER = 'customer';
    case MERCHANT_OWNER = 'merchant_owner';
    case MERCHANT_STAFF = 'merchant_staff';
    case ADMIN = 'admin';
}
