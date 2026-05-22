<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
class Lead extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'whatsapp',
        'source',
        'campaign',
        'medium',
        'interest_type',
        'budget_min',
        'budget_max',
        'preferred_location',
        'preferred_language',
        'status',
        'priority',
        'last_contacted_at',
        'next_follow_up_at',
        'notes',
        'metadata',
        'registered_by_user_id',
        'assigned_to_user_id',
        'intent',
        'interest_target_type',
        'development_id',
        'listing_id',
    ];
    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (auth()->check()) {
                $lead->registered_by_user_id ??= auth()->id();
                $lead->assigned_to_user_id ??= auth()->id();
            }
        });

        static::saving(function (Lead $lead) {
            $lead->full_name = trim(
                collect([
                    $lead->first_name,
                    $lead->last_name,
                ])->filter()->implode(' ')
            );
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('lead')
            ->logOnly([
                'full_name',
                'email',
                'phone',
                'whatsapp',
                'source',
                'intent',
                'interest_target_type',
                'status',
                'priority',
                'assigned_to_user_id',
                'next_follow_up_at',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')
            ->latest();
    }
    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

}
