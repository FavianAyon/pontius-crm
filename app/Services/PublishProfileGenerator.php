<?php

namespace App\Services;

use App\Models\Development;
use App\Models\DevelopmentUnit;
use App\Models\Listing;
use App\Models\PublishProfile;

class PublishProfileGenerator
{
    public static function generate(object $model, string $language = 'es'): PublishProfile
    {
        $data = self::buildData($model, $language);

        return $model->publishProfiles()->updateOrCreate(
            ['language' => $language],
            [
                'seo_title' => $data['seo_title'],
                'seo_description' => $data['seo_description'],
                'og_title' => $data['og_title'],
                'og_description' => $data['og_description'],
                'public_description' => $data['public_description'],
                'ai_summary' => $data['ai_summary'],
                'keywords' => $data['keywords'],
                'structured_data_json' => $data['structured_data_json'],
                'api_payload' => $data['api_payload'],
                'content_score' => $data['content_score'],
                'generated_at' => now(),
            ]
        );
    }

    protected static function buildData(object $model, string $language): array
    {
        if ($model instanceof Listing) {
            return self::forListing($model, $language);
        }

        if ($model instanceof Development) {
            return self::forDevelopment($model, $language);
        }

        if ($model instanceof DevelopmentUnit) {
            return self::forDevelopmentUnit($model, $language);
        }

        throw new \InvalidArgumentException('Unsupported publishable model.');
    }

    protected static function forListing(Listing $listing, string $language): array
    {
        $title = $listing->title;
        $location = $listing->location;
        $price = $listing->price ? number_format((float) $listing->price) . ' ' . $listing->currency : null;

        $description = $language === 'en'
            ? ($listing->description_en ?: $listing->description)
            : ($listing->description_es ?: $listing->description);

        $seoTitle = trim("{$title} {$location}");
        $seoDescription = trim("{$title} in {$location}. {$price}. {$listing->bedrooms} bedrooms, {$listing->bathrooms} bathrooms, {$listing->area_m2} m².");

        $keywords = array_filter([
            $title,
            $location,
            $listing->property_type,
            $listing->listing_type,
            $listing->currency,
        ]);

        $payload = [
            'id' => $listing->id,
            'type' => 'listing',
            'slug' => $listing->slug,
            'title' => $title,
            'location' => $location,
            'price' => $listing->price,
            'currency' => $listing->currency,
            'bedrooms' => $listing->bedrooms,
            'bathrooms' => $listing->bathrooms,
            'area_m2' => $listing->area_m2,
            'description' => $description,
            'images' => $listing->mediaAssets()
                ->where('is_public', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($media) => [
                    'url' => $media->url,
                    'title' => $media->title,
                    'alt' => $media->alt_text,
                    'caption' => $media->caption,
                    'is_featured' => $media->is_featured,
                ])
                ->values()
                ->toArray(),
        ];

        return [
            'seo_title' => str($seoTitle)->limit(65, '')->toString(),
            'seo_description' => str($seoDescription)->limit(155, '')->toString(),
            'og_title' => $title,
            'og_description' => str($description ?: $seoDescription)->limit(180, '')->toString(),
            'public_description' => $description,
            'ai_summary' => self::buildAiSummary($payload),
            'keywords' => array_values($keywords),
            'structured_data_json' => self::schemaForListing($payload),
            'api_payload' => $payload,
            'content_score' => self::score($payload),
        ];
    }

    protected static function forDevelopment(Development $development, string $language): array
    {
        $description = $language === 'en'
            ? $development->description_en
            : $development->description_es;

        $payload = [
            'id' => $development->id,
            'type' => 'development',
            'slug' => $development->slug,
            'name' => $development->name,
            'location' => $development->location,
            'sales_status' => $development->sales_status,
            'total_units' => $development->total_units,
            'available_units' => $development->available_units,
            'developer_name' => $development->developer_name,
            'delivery_date' => $development->delivery_date?->format('Y-m-d'),
            'construction_status' => $development->construction_status,
            'description' => $description ?: $development->description,
            'images' => $development->mediaAssets()
                ->where('is_public', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($media) => [
                    'url' => $media->url,
                    'title' => $media->title,
                    'alt' => $media->alt_text,
                    'caption' => $media->caption,
                    'is_featured' => $media->is_featured,
                ])
                ->values()
                ->toArray(),
        ];

        return [
            'seo_title' => str($development->name . ' ' . $development->location)->limit(65, '')->toString(),
            'seo_description' => str(($payload['description'] ?: $development->name) . ' ' . $development->location)->limit(155, '')->toString(),
            'og_title' => $development->name,
            'og_description' => str($payload['description'] ?: $development->location)->limit(180, '')->toString(),
            'public_description' => $payload['description'],
            'ai_summary' => self::buildAiSummary($payload),
            'keywords' => array_values(array_filter([
                $development->name,
                $development->location,
                $development->sales_status,
                $development->developer_name,
            ])),
            'structured_data_json' => self::schemaForDevelopment($payload),
            'api_payload' => $payload,
            'content_score' => self::score($payload),
        ];
    }

    protected static function forDevelopmentUnit(DevelopmentUnit $unit, string $language): array
    {
        $description = $language === 'en'
            ? $unit->description_en
            : $unit->description_es;

        $title = trim(($unit->development?->name ?? '') . ' ' . $unit->unit_number);

        $payload = [
            'id' => $unit->id,
            'type' => 'development_unit',
            'slug' => $unit->slug,
            'title' => $title,
            'development' => $unit->development?->name,
            'unit_number' => $unit->unit_number,
            'status' => $unit->status,
            'price' => $unit->price,
            'currency' => $unit->currency,
            'bedrooms' => $unit->bedrooms,
            'bathrooms' => $unit->bathrooms,
            'area_m2' => $unit->area_m2,
            'floor' => $unit->floor,
            'view_type' => $unit->view_type,
            'unit_type' => $unit->unit_type,
            'orientation' => $unit->orientation,
            'description' => $description,
            'images' => $unit->mediaAssets()
                ->where('is_public', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($media) => [
                    'url' => $media->url,
                    'title' => $media->title,
                    'alt' => $media->alt_text,
                    'caption' => $media->caption,
                    'is_featured' => $media->is_featured,
                ])
                ->values()
                ->toArray(),
        ];

        return [
            'seo_title' => str($title)->limit(65, '')->toString(),
            'seo_description' => str("{$title}. {$unit->bedrooms} bedrooms, {$unit->bathrooms} bathrooms, {$unit->area_m2} m².")->limit(155, '')->toString(),
            'og_title' => $title,
            'og_description' => str($description ?: $title)->limit(180, '')->toString(),
            'public_description' => $description,
            'ai_summary' => self::buildAiSummary($payload),
            'keywords' => array_values(array_filter([
                $title,
                $unit->development?->name,
                $unit->status,
                $unit->unit_type,
                $unit->view_type,
            ])),
            'structured_data_json' => self::schemaForListing($payload),
            'api_payload' => $payload,
            'content_score' => self::score($payload),
        ];
    }

    protected static function buildAiSummary(array $payload): string
    {
        return collect($payload)
            ->reject(fn ($value) => is_array($value) || blank($value))
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->implode("\n");
    }

    protected static function schemaForListing(array $payload): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $payload['title'] ?? null,
            'description' => $payload['description'] ?? null,
            'url' => $payload['slug'] ?? null,
            'image' => collect($payload['images'] ?? [])->pluck('url')->values()->toArray(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $payload['price'] ?? null,
                'priceCurrency' => $payload['currency'] ?? null,
            ],
        ];
    }

    protected static function schemaForDevelopment(array $payload): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Residence',
            'name' => $payload['name'] ?? null,
            'description' => $payload['description'] ?? null,
            'image' => collect($payload['images'] ?? [])->pluck('url')->values()->toArray(),
        ];
    }

    protected static function score(array $payload): int
    {
        $fields = ['title', 'name', 'description', 'location', 'images'];
        $completed = collect($fields)
            ->filter(fn ($field) => filled($payload[$field] ?? null))
            ->count();

        return (int) round(($completed / count($fields)) * 100);
    }
}
