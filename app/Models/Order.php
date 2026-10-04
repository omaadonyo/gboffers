<?php
namespace App\Models;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Order extends Model {
    use HasFactory;
    protected $fillable = ['user_id','merchant_id','offer_id','gang_id','currency','subtotal','discount','total','payment_method','payment_provider','payment_reference','payment_status','fulfillment_status','status','quantity','notes'];
    public function customer(): BelongsTo { return $this->belongsTo(User::class,'user_id'); }
    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function gang(): BelongsTo { return $this->belongsTo(Gang::class); }
    public function payment(): HasOne { return $this->hasOne(Payment::class); }
    public function gbPass(): HasOne { return $this->hasOne(GbPass::class); }
    public function fulfillment(): HasOne { return $this->hasOne(Fulfillment::class); }
    public function transitionTo(OrderStatus $to): void {
        $from = OrderStatus::tryFrom($this->status) ?? OrderStatus::RESERVED;
        if (!$from->canTransitionTo($to)) throw new InvalidStatusTransitionException("Cannot transition {$from->value} to {$to->value}");
        $this->update(['status' => $to->value]);
    }
}
