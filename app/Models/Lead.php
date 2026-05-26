<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
class Lead extends Model
{
    use SoftDeletes, LogsActivity, Notifiable;

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
        'normalized_email',
        'normalized_phone',
        'normalized_whatsapp',
        'is_duplicate',
        'duplicate_of_lead_id',
        'duplicate_match_fields',
        'development_unit_id',
        'completeness_percent',
    ];
    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'metadata' => 'array',
        'duplicate_match_fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Lead $lead) {
            $lead->full_name = trim(
                collect([$lead->first_name, $lead->last_name])
                    ->filter()
                    ->implode(' ')
            );
            $lead->completeness_percent = $lead->calculateCompletenessPercent();

            $lead->refreshDuplicateStatus();
        });

        static::creating(function (Lead $lead) {
            if (auth()->check()) {
                $lead->registered_by_user_id ??= auth()->id();
                $lead->assigned_to_user_id ??= auth()->id();
            }
            $lead->assigned_to_user_id ??= self::getNextAvailableAgentId() ?? auth()->id();

        });

        static::created(function (Lead $lead) {
            $lead->createInitialContactTask();

            if ($lead->assignedTo) {
                \Filament\Notifications\Notification::make()
                    ->title(__('leads.lead_assigned_title'))
                    ->body(__('leads.lead_assigned_body', [
                        'name' => $lead->full_name ?: $lead->phone ?: $lead->email,
                    ]))
                    ->success()
                    ->sendToDatabase($lead->assignedTo);
            }
        });
        static::saved(function (Lead $lead) {
            $lead->createIncompleteLeadTaskIfNeeded();
            $lead->closeIncompleteLeadTaskIfCompleted();
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

    public function duplicateOf()
    {
        return $this->belongsTo(Lead::class, 'duplicate_of_lead_id');
    }

    public static function normalizeEmail(?string $email): ?string
    {
        return $email ? strtolower(trim($email)) : null;
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) === 10) {
            return '52' . $digits;
        }

        return $digits;
    }

    public function findPossibleDuplicate(): ?Lead
    {
        return self::query()
            ->where('id', '!=', $this->id ?? 0)
            ->where(function ($query) {
                if ($this->normalized_email) {
                    $query->orWhere('normalized_email', $this->normalized_email);
                }

                if ($this->normalized_phone) {
                    $query->orWhere('normalized_phone', $this->normalized_phone);
                }

                if ($this->normalized_whatsapp) {
                    $query->orWhere('normalized_whatsapp', $this->normalized_whatsapp);
                }
            })
            ->first();
    }
    public function getDuplicateMatchFields(Lead $duplicate): array
    {
        $matches = [];

        if ($this->normalized_email && $this->normalized_email === $duplicate->normalized_email) {
            $matches[] = 'email';
        }

        if ($this->normalized_phone && $this->normalized_phone === $duplicate->normalized_phone) {
            $matches[] = 'phone';
        }

        if ($this->normalized_whatsapp && $this->normalized_whatsapp === $duplicate->normalized_whatsapp) {
            $matches[] = 'whatsapp';
        }

        return $matches;
    }

    public function refreshDuplicateStatus(): void
    {
        $this->normalized_email = self::normalizeEmail($this->email);
        $this->normalized_phone = self::normalizePhone($this->phone);
        $this->normalized_whatsapp = self::normalizePhone($this->whatsapp);

        $duplicate = $this->findPossibleDuplicate();

        if ($duplicate) {
            $this->is_duplicate = true;
            $this->duplicate_of_lead_id = $duplicate->id;
            $this->duplicate_match_fields = $this->getDuplicateMatchFields($duplicate);
            return;
        }

        $this->is_duplicate = false;
        $this->duplicate_of_lead_id = null;
        $this->duplicate_match_fields = null;
    }

    public function mergeInto(Lead $mainLead): void
    {
        $this->leadActivities()->update([
            'lead_id' => $mainLead->id,
        ]);

        activity()
            ->performedOn($mainLead)
            ->causedBy(auth()->user())
            ->withProperties([
                'merged_lead_id' => $this->id,
                'merged_lead_name' => $this->full_name,
            ])
            ->log('lead_merged');

        $this->delete();
    }

    public function development()
    {
        return $this->belongsTo(Development::class);
    }
    public function developmentUnit()
    {
        return $this->belongsTo(DevelopmentUnit::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function getPrimaryContactAttribute(): ?string
    {
        return $this->whatsapp ?: $this->phone ?: $this->email;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $this->whatsapp);

        return "https://wa.me/{$phone}";
    }

    public function getCallUrlAttribute(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        return "tel:{$this->phone}";
    }

    public function getEmailUrlAttribute(): ?string
    {
        if (! $this->email) {
            return null;
        }

        return "mailto:{$this->email}";
    }
    public function tasks()
    {
        return $this->hasMany(Task::class)->latest();
    }
    public function createInitialContactTask(): void
    {
        if (! $this->email && ! $this->phone && ! $this->whatsapp) {
            return;
        }

        if ($this->tasks()->where('type', 'initial_contact')->exists()) {
            return;
        }

        $this->tasks()->create([
            'assigned_to_user_id' => $this->assigned_to_user_id,
            'created_by_user_id' => $this->registered_by_user_id,
            'title' => __('tasks.initial_contact_title'),
            'description' => __('tasks.initial_contact_description'),
            'type' => 'initial_contact',
            'status' => 'open',
            'priority' => $this->priority ?? 'normal',
            'due_at' => now()->addMinutes(config('crm.initial_contact_due_minutes', 60)),
        ]);
    }
    public function caseFiles()
    {
        return $this->hasMany(CaseFile::class)->latest();
    }
    public function calculateCompletenessPercent(): int
    {
        $fields = [
            'first_name',
            'phone',
            'whatsapp',
            'email',
            'source',
            'intent',
            'interest_target_type',
            'priority',
            'status',
        ];

        $completed = collect($fields)
            ->filter(fn ($field) => filled($this->{$field}))
            ->count();

        return (int) round(($completed / count($fields)) * 100);
    }
    public function getMissingFieldsAttribute(): array
    {
        $fields = [
            'first_name' => __('leads.first_name'),
            'phone' => __('leads.phone'),
            'whatsapp' => __('leads.whatsapp'),
            'email' => __('leads.email'),
            'source' => __('leads.source'),
            'intent' => __('leads.intent'),
            'interest_target_type' => __('leads.interest_target_type'),
            'priority' => __('leads.priority'),
            'status' => __('leads.status'),
        ];

        return collect($fields)
            ->filter(fn ($label, $field) => blank($this->{$field}))
            ->values()
            ->toArray();
    }
    public function createIncompleteLeadTaskIfNeeded(): void
    {
        if ($this->completeness_percent >= 80) {
            return;
        }

        if ($this->tasks()->where('type', 'complete_lead_info')->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            return;
        }

        $this->tasks()->create([
            'assigned_to_user_id' => $this->assigned_to_user_id ?? auth()->id(),
            'created_by_user_id' => auth()->id(),
            'title' => __('tasks.complete_lead_info_title'),
            'description' => __('tasks.complete_lead_info_description'),
            'type' => 'complete_lead_info',
            'status' => 'open',
            'priority' => 'normal',
            'due_at' => now()->addDay(),
        ]);
    }
    public function closeIncompleteLeadTaskIfCompleted(): void
    {
        if ($this->completeness_percent < 80) {
            return;
        }

        $this->tasks()
            ->where('type', 'complete_lead_info')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
    }

    protected static function getNextAvailableAgentId(): ?int
    {
        return User::role('agent')
            ->where('is_active', true)
            ->withCount(['assignedLeads'])
            ->orderBy('assigned_leads_count')
            ->value('id');
    }
    public function createReassignmentReviewTask(): void
    {
        if (! $this->assigned_to_user_id) {
            return;
        }

        $exists = $this->tasks()
            ->where('type', 'reassignment_review')
            ->where('assigned_to_user_id', $this->assigned_to_user_id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        if ($exists) {
            return;
        }

        $this->tasks()->create([
            'assigned_to_user_id' => $this->assigned_to_user_id,
            'created_by_user_id' => auth()->id(),
            'title' => __('tasks.review_reassigned_lead_title'),
            'description' => __('tasks.review_reassigned_lead_description'),
            'type' => 'reassignment_review',
            'status' => 'open',
            'priority' => 'normal',
            'due_at' => now()->addHours(2),
        ]);
    }
    public function assignments()
    {
        return $this->hasMany(LeadAssignment::class)->latest();
    }

}
