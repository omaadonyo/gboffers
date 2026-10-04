<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferAudience extends Model
{
    use HasFactory;

    protected $fillable = ['offer_id', 'label', 'min_buyers', 'price', 'sort'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
