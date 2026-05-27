<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class DevelopmentUnit extends Model
{
    use SoftDeletes, LogsActivity;

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
        static::saved(function (DevelopmentUnit $unit) {
            if ($unit->shouldRegeneratePublishProfiles()) {
                \App\Services\PublishProfileGenerator::generate($unit, 'es');
                \App\Services\PublishProfileGenerator::generate($unit, 'en');
            }
            self::clearPublicApiCache();
        });
    }

    public function development()
    {
        return $this->belongsTo(Development::class);
    }

    public function changeStatus(string $status): void
    {
        $this->update([
            'status' => $status,
        ]);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('development_unit')
            ->logOnly([
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
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
    public function caseFiles()

    {

        return $this->hasMany(CaseFile::class);

    }
    public function mediaAssets()
    {
        return $this->morphMany(MediaAsset::class, 'mediable')->orderBy('sort_order');
    }

    public function featuredImage()
    {
        return $this->morphOne(MediaAsset::class, 'mediable')
            ->where('collection', 'gallery')
            ->where('is_featured', true);
    }
    public function publishProfiles()
    {
        return $this->morphMany(PublishProfile::class, 'publishable');
    }

    public function publishProfileEs()
    {
        return $this->morphOne(PublishProfile::class, 'publishable')
            ->where('language', 'es');
    }

    public function publishProfileEn()
    {
        return $this->morphOne(PublishProfile::class, 'publishable')
            ->where('language', 'en');
    }
    public function shouldRegeneratePublishProfiles(): bool
    {
        return $this->wasChanged([
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
            'unit_type',
            'orientation',
            'description',
            'description_es',
            'description_en',
            'is_public',
            'public_status',
        ]);
    }
    protected static function clearPublicApiCache(): void
    {
        Cache::forget('public_inventory_manifest');
        Cache::forget('public_inventory_sitemap');
        Cache::forget('public_inventory_ai_context_es');
        Cache::forget('public_inventory_ai_context_en');
    }
}
