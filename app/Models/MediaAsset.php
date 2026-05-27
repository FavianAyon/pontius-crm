<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'collection',
        'file_path',
        'disk',
        'title',
        'alt_text',
        'caption',
        'mime_type',
        'file_size',
        'sort_order',
        'is_featured',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'metadata' => 'array',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::disk($this->disk ?? 'public')->url($this->file_path);
    }
}
