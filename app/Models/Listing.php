<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Listing extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('listing')
            ->logOnly([
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
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function caseFiles()
    {
        return $this->hasMany(CaseFile::class);
    }
}
