<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    ];

    protected $casts = [
        'metadata' => 'array',
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
}
