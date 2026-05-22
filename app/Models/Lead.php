<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

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
        static::saving(function (Lead $lead) {
            $lead->full_name = trim(
                collect([
                    $lead->first_name,
                    $lead->last_name,
                ])->filter()->implode(' ')
            );
        });
    }
}
