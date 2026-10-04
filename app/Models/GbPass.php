<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GbPass extends Model {
    use HasFactory;
    protected $fillable = ['order_id','user_id','merchant_id','offer_id','gang_id','token','qr_payload','pin_hash','pin_encrypted','amount','quantity','status','payment_confirmed_at','redeemed_at','redeemed_by','expires_at'];
    protected $hidden = ['pin_hash'];
    protected function casts(): array { return ['payment_confirmed_at'=>'datetime','redeemed_at'=>'datetime','expires_at'=>'datetime']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class,'user_id'); }
    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
}
