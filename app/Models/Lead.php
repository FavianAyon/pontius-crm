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
        'normalized_email',
        'normalized_phone',
        'normalized_whatsapp',
        'is_duplicate',
        'duplicate_of_lead_id',
        'duplicate_match_fields',
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

            $lead->refreshDuplicateStatus();
        });

        static::creating(function (Lead $lead) {
            if (auth()->check()) {
                $lead->registered_by_user_id ??= auth()->id();
                $lead->assigned_to_user_id ??= auth()->id();
            }
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

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
