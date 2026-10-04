<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Reservation extends Model {
    use HasFactory;
    protected $fillable = ['user_id','offer_id','gang_id','code','status','expires_at','committed_at','attempts'];
    protected function casts(): array { return ['expires_at'=>'datetime','committed_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function gang(): BelongsTo { return $this->belongsTo(Gang::class); }
}
