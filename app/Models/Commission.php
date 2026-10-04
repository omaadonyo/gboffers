<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';

    protected $fillable = ['merchant_id', 'order_id', 'amount', 'currency', 'rule', 'status', 'invoiced_at', 'settled_at', 'settlement_reference'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    protected function casts(): array
    {
        $c = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
        foreach (['starts_at', 'ends_at', 'payment_deadline', 'redemption_deadline', 'unlocked_at', 'expires_at', 'verified_at', 'joined_at', 'committed_at', 'reported_at', 'confirmed_at', 'payment_confirmed_at', 'redeemed_at', 'out_at', 'delivered_at', 'fulfilled_at', 'confirmed_by_customer_at', 'resolved_at', 'phone_verified_at'] as $d) {
            $c[$d] = 'datetime';
        }
        foreach (['permissions', 'payment_details', 'delivery_options', 'payload', 'meta'] as $j) {
            $c[$j] = 'array';
        }
        foreach (['is_active', 'featured', 'delivery_available'] as $b) {
            $c[$b] = 'boolean';
        }

        return $c;
    }
}
