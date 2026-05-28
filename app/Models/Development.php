<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
class Development extends Model
{
    use SoftDeletes, LogsActivity;

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
        'is_public',
        'public_status',
        'description_es',
        'description_en',
        'seo_title_es',
        'seo_title_en',
        'seo_description_es',
        'seo_description_en',
        'developer_name',
        'delivery_date',
        'construction_status'
    ];

    protected $casts = [
        'metadata' => 'array',
        'delivery_date' => 'date',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function units()
    {
        return $this->hasMany(DevelopmentUnit::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Development $development) {
            $development->slug = Str::slug($development->name);
        });
        static::saved(function (Development $development) {
            if ($development->shouldRegeneratePublishProfiles()) {
                \App\Services\PublishProfileGenerator::generate($development, 'es');
                \App\Services\PublishProfileGenerator::generate($development, 'en');
            }
            self::clearPublicApiCache();
        });
    }

    public function recalculateInventory(): void
    {
        $this->updateQuietly([
            'total_units' => $this->units()->count(),
            'available_units' => $this->units()
                ->where('status', 'available')
                ->count(),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('development')
            ->logOnly([
                'name',
                'slug',
                'status',
                'sales_status',
                'location',
                'description',
                'total_units',
                'available_units',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
            'name',
            'slug',
            'status',
            'sales_status',
            'location',
            'description',
            'description_es',
            'description_en',
            'total_units',
            'available_units',
            'developer_name',
            'delivery_date',
            'construction_status',
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
