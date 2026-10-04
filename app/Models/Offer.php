<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Offer extends Model {
    use HasFactory;
    protected $fillable = ['merchant_id','category_id','title','slug','description','normal_price','image_path','quantity_total','quantity_reserved','quantity_sold','min_buyers','max_buyers','gang_target','starts_at','ends_at','payment_deadline','redemption_deadline','fulfillment_method','pickup_location','delivery_available','terms','cancellation_policy','status','featured','views','interested_count','confirmed_count'];
    protected function casts(): array { return ['starts_at'=>'datetime','ends_at'=>'datetime','payment_deadline'=>'datetime','redemption_deadline'=>'datetime','delivery_available'=>'boolean','featured'=>'boolean']; }
    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function priceTiers(): HasMany { return $this->hasMany(OfferPriceTier::class)->orderBy('min_qty'); }
    public function audiences(): HasMany { return $this->hasMany(OfferAudience::class)->orderBy('sort'); }
    public function images(): HasMany { return $this->hasMany(OfferImage::class)->orderBy('sort'); }
    public function gangs(): HasMany { return $this->hasMany(Gang::class); }
    public function viewLogs(): HasMany { return $this->hasMany(OfferView::class); }
    public function shares(): HasMany { return $this->hasMany(Share::class); }
    public function featuredListings(): HasMany { return $this->hasMany(FeaturedListing::class)->latest(); }
    public function activeFeature(): ?FeaturedListing {
        return $this->featuredListings->first(fn ($f) => $f->status === 'active' && (! $f->ends_at || $f->ends_at->isFuture()));
    }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function getRouteKeyName(): string { return 'slug'; }
    public function availableLeft(): int { return max(0, $this->quantity_total - $this->quantity_reserved - $this->quantity_sold); }
}
