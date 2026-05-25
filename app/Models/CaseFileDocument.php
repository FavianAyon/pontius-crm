<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Storage;

class CaseFileDocument extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'case_file_id',
        'name',
        'document_type',
        'status',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'uploaded_by_user_id',
        'requested_at',
        'uploaded_at',
        'validated_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'validated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
    protected static function booted(): void
    {
        static::saving(function (CaseFileDocument $document) {
            if ($document->isDirty('file_path') && $document->file_path) {
                $document->uploaded_by_user_id ??= auth()->id();
                $document->uploaded_at ??= now();

                if (in_array($document->status, ['pending', 'requested'], true)) {
                    $document->status = 'uploaded';
                }
            }
        });
        static::saved(function (CaseFileDocument $document) {
            $document->caseFile?->recalculateDocumentProgress();
        });

        static::deleted(function (CaseFileDocument $document) {
            $document->caseFile?->recalculateDocumentProgress();
        });

        static::restored(function (CaseFileDocument $document) {
            $document->caseFile?->recalculateDocumentProgress();
        });
    }
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('case_file_document')
            ->logOnly([
                'case_file_id',
                'name',
                'document_type',
                'status',
                'file_path',
                'uploaded_by_user_id',
                'requested_at',
                'uploaded_at',
                'validated_at',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }
}
