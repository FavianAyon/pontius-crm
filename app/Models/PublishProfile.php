<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishProfile extends Model
{
    protected $fillable = [
        'publishable_type',
        'publishable_id',
        'language',
        'seo_title',
        'seo_description',
        'og_title',
        'og_description',
        'public_description',
        'ai_summary',
        'keywords',
        'structured_data_json',
        'api_payload',
        'content_score',
        'generated_at',
        'metadata',
    ];

    protected $casts = [
        'keywords' => 'array',
        'structured_data_json' => 'array',
        'api_payload' => 'array',
        'metadata' => 'array',
        'generated_at' => 'datetime',
    ];

    public function publishable()
    {
        return $this->morphTo();
    }
}
