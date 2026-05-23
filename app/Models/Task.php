<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Task extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'lead_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->created_by_user_id ??= auth()->id();
            $task->assigned_to_user_id ??= auth()->id();
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

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if ($this->lead) {
            $this->lead->leadActivities()->create([
                'user_id' => auth()->id(),
                'type' => 'task',
                'status' => 'completed',
                'title' => $this->title,
                'description' => $this->description,
                'completed_at' => now(),
                'metadata' => [
                    'task_id' => $this->id,
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
            ->useLogName('task')
            ->logOnly([
                'lead_id',
                'assigned_to_user_id',
                'created_by_user_id',
                'title',
                'description',
                'type',
                'status',
                'priority',
                'due_at',
                'completed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
