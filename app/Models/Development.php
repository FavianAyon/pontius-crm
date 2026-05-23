<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Development extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'sales_status',
        'location',
        'description',
        'total_units',
        'available_units',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
