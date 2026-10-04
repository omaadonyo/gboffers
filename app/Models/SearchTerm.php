<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchTerm extends Model
{
    use HasFactory;

    protected $fillable = ['term', 'hits', 'last_searched_at'];

    protected function casts(): array
    {
        return ['last_searched_at' => 'datetime'];
    }
}
