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
        'documents_progress_percent',
        'pending_documents_count',
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
            $caseFile->createDocumentChecklist();
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
    public function createDocumentChecklist(): void
    {
        $templates = config("crm.case_file_document_templates.{$this->type}", []);

        foreach ($templates as $template) {
            $this->documents()->firstOrCreate(
                [
                    'document_type' => $template['document_type'],
                ],
                [
                    'name' => $template['name'],
                    'status' => 'pending',
                    'requested_at' => now(),
                ]
            );
        }
    }
    public function getDocumentsProgressPercentAttribute(): int
    {
        $total = $this->documents()->count();

        if ($total === 0) {
            return 0;
        }

        $approved = $this->documents()
            ->where('status', 'approved')
            ->count();

        return (int) round(($approved / $total) * 100);
    }

    public function getPendingDocumentsCountAttribute(): int
    {
        return $this->documents()
            ->whereIn('status', ['pending', 'requested', 'rejected'])
            ->count();
    }

    public function recalculateDocumentProgress(): void
    {
        $total = $this->documents()->count();

        $approved = $this->documents()
            ->where('status', 'approved')
            ->count();

        $pending = $this->documents()
            ->whereIn('status', ['pending', 'requested', 'rejected'])
            ->count();

        $this->updateQuietly([
            'documents_progress_percent' => $total > 0
                ? (int) round(($approved / $total) * 100)
                : 0,
            'pending_documents_count' => $pending,
        ]);
        $this->refreshStatusFromDocuments();
    }
    public function refreshStatusFromDocuments(): void
    {
        $total = $this->documents()->count();

        if ($total === 0) {
            return;
        }

        $approved = $this->documents()->where('status', 'approved')->count();
        $uploadedOrReview = $this->documents()
            ->whereIn('status', ['uploaded', 'in_review'])
            ->count();

        if ($approved === $total) {
            $this->updateQuietly(['status' => 'approved']);
            return;
        }

        if ($uploadedOrReview > 0) {
            $this->updateQuietly(['status' => 'in_review']);
            return;
        }

        $this->updateQuietly(['status' => 'open']);
    }
}
