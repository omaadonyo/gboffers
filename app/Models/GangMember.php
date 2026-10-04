<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GangMember extends Model {
    use HasFactory;
    protected $fillable = ['gang_id','user_id','offer_id','status','reservation_id','order_id','joined_at'];
    protected function casts(): array { return ['joined_at'=>'datetime']; }
    public function gang(): BelongsTo { return $this->belongsTo(Gang::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
