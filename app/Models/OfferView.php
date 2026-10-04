<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferView extends Model
{
    use HasFactory;

    protected $fillable = ['offer_id', 'user_id', 'ip'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
