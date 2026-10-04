<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    use HasFactory;

    protected $table = 'disputes';

    protected $fillable = ['order_id', 'reporter_id', 'respondent_merchant_id', 'reason', 'description', 'status', 'resolution', 'resolved_at', 'resolved_by'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'respondent_merchant_id');
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
