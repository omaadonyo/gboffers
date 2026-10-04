<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OfferPriceTier extends Model {
    use HasFactory;
    protected $table = 'offer_price_tiers';
    protected $fillable = ['offer_id', 'min_qty', 'max_qty', 'price', 'sort'];
    protected function casts(): array {
        $c = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
        foreach (['starts_at','ends_at','payment_deadline','redemption_deadline','unlocked_at','expires_at','verified_at','joined_at','committed_at','reported_at','confirmed_at','payment_confirmed_at','redeemed_at','out_at','delivered_at','fulfilled_at','confirmed_by_customer_at','resolved_at','phone_verified_at'] as $d) { $c[$d] = 'datetime'; }
        foreach (['permissions','payment_details','delivery_options','payload','meta'] as $j) { $c[$j] = 'array'; }
        foreach (['is_active','featured','delivery_available'] as $b) { $c[$b] = 'boolean'; }
        return $c;
    }
}
