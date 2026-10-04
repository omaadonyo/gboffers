<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Gang extends Model {
    use HasFactory;
    protected $fillable = ['offer_id','code','target','confirmed_count','interested_count','status','unlocked_at','expires_at'];
    protected function casts(): array { return ['unlocked_at'=>'datetime','expires_at'=>'datetime']; }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function members(): HasMany { return $this->hasMany(GangMember::class); }
    public function confirmedMembers(): HasMany { return $this->hasMany(GangMember::class)->whereIn('status',['payment_confirmed','ready','redeemed','fulfilled']); }
    public function progressPct(): float { return $this->target > 0 ? min(100, round($this->confirmed_count / $this->target * 100, 1)) : 0; }
    public function needed(): int { return max(0, $this->target - $this->confirmed_count); }
}
