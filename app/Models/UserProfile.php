<?php
namespace App\Models;
use App\Enums\BuyerTrustLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserProfile extends Model {
    use HasFactory;
    protected $fillable = ['user_id','phone','phone_verified_at','avatar_path','display_name','city','visibility','trust_level','completed_count','expired_count','cancelled_count','dispute_count'];
    protected function casts(): array { return ['phone_verified_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function trust(): BuyerTrustLevel { return BuyerTrustLevel::tryFrom($this->trust_level) ?? BuyerTrustLevel::NEW; }
}
