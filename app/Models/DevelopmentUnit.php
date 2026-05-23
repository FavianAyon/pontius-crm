<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DevelopmentUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'development_id',
        'unit_number',
        'slug',
        'flexmls_id',
        'status',
        'price',
        'currency',
        'bedrooms',
        'bathrooms',
        'area_m2',
        'floor',
        'view_type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area_m2' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (DevelopmentUnit $unit) {
            $developmentSlug = $unit->development?->slug ?? 'development';

            $unit->slug = \Illuminate\Support\Str::slug(
                $developmentSlug . '-' . $unit->unit_number
            );
        });

        static::saved(function (DevelopmentUnit $unit) {
            $unit->development?->recalculateInventory();
        });

        static::deleted(function (DevelopmentUnit $unit) {
            $unit->development?->recalculateInventory();
        });

        static::restored(function (DevelopmentUnit $unit) {
            $unit->development?->recalculateInventory();
        });
    }

    public function development()
    {
        return $this->belongsTo(Development::class);
    }
}
