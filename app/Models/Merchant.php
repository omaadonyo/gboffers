<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Merchant extends Model {
    use HasFactory;
    protected $fillable = ['user_id','category_id','business_name','trading_name','slug','description','phone','email','location','payment_details','verification_status','verified_at','rating_avg','completed_count','fulfillment_rate','return_policy','delivery_options','is_active'];
    protected function casts(): array { return ['verified_at'=>'datetime','payment_details'=>'array','delivery_options'=>'array','is_active'=>'boolean']; }
    public function owner(): BelongsTo { return $this->belongsTo(User::class,'user_id'); }
    public function offers(): HasMany { return $this->hasMany(Offer::class); }
    public function staff(): HasMany { return $this->hasMany(MerchantUser::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function isVerified(): bool { return $this->verification_status === 'approved'; }
    public function getRouteKeyName(): string { return 'slug'; }
}
