<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folio',
        'type',
        'status',
        'lead_id',
        'listing_id',
        'development_unit_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (CaseFile $caseFile) {
            $caseFile->created_by_user_id ??= auth()->id();
            $caseFile->assigned_to_user_id ??= auth()->id();

            if (! $caseFile->folio) {
                $caseFile->folio = 'EXP-' . now()->format('Ymd') . '-' . str_pad((string) (self::query()->count() + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function developmentUnit()
    {
        return $this->belongsTo(DevelopmentUnit::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
    public function documents()
    {
        return $this->hasMany(CaseFileDocument::class)->latest();
    }
}
