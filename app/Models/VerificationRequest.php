<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    use HasFactory;

    public const TYPE_PROFILE = 'profile';

    public const TYPE_SHOP = 'shop';

    public const PRICE_PROFILE = 3500;

    public const PRICE_SHOP = 50000;

    protected $fillable = ['user_id', 'merchant_id', 'type', 'amount', 'status', 'notes', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function priceFor(string $type): int
    {
        return $type === self::TYPE_SHOP ? self::PRICE_SHOP : self::PRICE_PROFILE;
    }

    public static function labelFor(string $type): string
    {
        return $type === self::TYPE_SHOP ? 'Shop stock verification' : 'Profile verification';
    }
}
