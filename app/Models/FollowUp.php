<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class FollowUp extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'lead_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'scheduled_at',
        'completed_at',
        'type',
        'status',
        'notes',
        'result_notes',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (FollowUp $followUp) {
            $followUp->created_by_user_id ??= auth()->id();
            $followUp->assigned_to_user_id ??= auth()->id();
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function markAsCompleted(string $resultNotes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'result_notes' => $resultNotes,
        ]);

        if ($this->lead) {
            $this->lead->leadActivities()->create([
                'user_id' => auth()->id(),
                'type' => 'follow_up',
                'status' => 'completed',
                'title' => 'Seguimiento completado',
                'description' => "Seguimiento de tipo {$this->type} completado. Notas: {$resultNotes}",
                'completed_at' => now(),
                'metadata' => [
                    'follow_up_id' => $this->id,
                    'follow_up_type' => $this->type,
                ],
            ]);
        }
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('follow_up')
            ->logOnly([
                'lead_id',
                'assigned_to_user_id',
                'scheduled_at',
                'completed_at',
                'type',
                'status',
                'notes',
                'result_notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
