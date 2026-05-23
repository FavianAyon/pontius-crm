<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'development_id',
        'title',
        'slug',
        'status',
        'listing_type',
        'property_type',
        'price',
        'currency',
        'location',
        'bedrooms',
        'bathrooms',
        'area_m2',
        'description',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area_m2' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function development()
    {
        return $this->belongsTo(Development::class);
    }
}
