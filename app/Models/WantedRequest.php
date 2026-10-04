<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WantedRequest extends Model
{
    use HasFactory;

    protected $table = 'wanted_requests';

    protected $fillable = ['user_id', 'guest_name', 'guest_contact', 'title', 'description', 'budget', 'quantity', 'location', 'condition', 'expires_at', 'status', 'responses_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authorName(): string
    {
        return $this->user?->displayName() ?? ($this->guest_name ?: 'Guest');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SupplierResponse::class);
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
