<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevelopmentResource;
use App\Http\Resources\DevelopmentUnitResource;
use App\Http\Resources\ListingResource;
use App\Models\Development;
use App\Models\DevelopmentUnit;
use App\Models\Listing;

class PublicInventoryController extends Controller
{
    public function listings()
    {
        $query = Listing::query()
            ->where('is_public', true)
            ->where('public_status', 'published')
            ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets']);

        request()->whenFilled('search', function ($search) use ($query) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description_es', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%");
            });
        });

        request()->whenFilled('property_type', fn ($value) =>
        $query->where('property_type', $value)
        );

        request()->whenFilled('listing_type', fn ($value) =>
        $query->where('listing_type', $value)
        );

        request()->whenFilled('min_price', fn ($value) =>
        $query->where('price', '>=', $value)
        );

        request()->whenFilled('max_price', fn ($value) =>
        $query->where('price', '<=', $value)
        );

        request()->whenFilled('bedrooms', fn ($value) =>
        $query->where('bedrooms', '>=', $value)
        );

        request()->whenFilled('bathrooms', fn ($value) =>
        $query->where('bathrooms', '>=', $value)
        );

        request()->whenFilled('location', fn ($value) =>
        $query->where('location', 'like', "%{$value}%")
        );

        return ListingResource::collection(
            $query->latest()->paginate(request('per_page', 12))
        );
    }

    public function listing(string $slug)
    {
        return new ListingResource(
            Listing::query()
                ->where('slug', $slug)
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets'])
                ->firstOrFail()
        );
    }

    public function developments()
    {
        $query = Development::query()
            ->where('is_public', true)
            ->where('public_status', 'published')
            ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets']);

        request()->whenFilled('search', function ($search) use ($query) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description_es', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%");
            });
        });

        request()->whenFilled('location', fn ($value) =>
        $query->where('location', 'like', "%{$value}%")
        );

        request()->whenFilled('sales_status', fn ($value) =>
        $query->where('sales_status', $value)
        );

        request()->whenFilled('construction_status', fn ($value) =>
        $query->where('construction_status', $value)
        );

        return DevelopmentResource::collection(
            $query->latest()->paginate(request('per_page', 12))
        );
    }

    public function development(string $slug)
    {
        return new DevelopmentResource(
            Development::query()
                ->where('slug', $slug)
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with([
                    'publishProfileEs',
                    'publishProfileEn',
                    'mediaAssets',
                    'units' => fn ($query) => $query
                        ->where('is_public', true)
                        ->where('public_status', 'published')
                        ->orderBy('unit_number'),
                ])
                ->firstOrFail()
        );
    }

    public function developmentUnits()
    {
        $query = DevelopmentUnit::query()
            ->where('is_public', true)
            ->where('public_status', 'published')
            ->with(['development', 'publishProfileEs', 'publishProfileEn', 'mediaAssets']);

        request()->whenFilled('development_id', fn ($value) =>
        $query->where('development_id', $value)
        );

        request()->whenFilled('status', fn ($value) =>
        $query->where('status', $value)
        );

        request()->whenFilled('min_price', fn ($value) =>
        $query->where('price', '>=', $value)
        );

        request()->whenFilled('max_price', fn ($value) =>
        $query->where('price', '<=', $value)
        );

        request()->whenFilled('bedrooms', fn ($value) =>
        $query->where('bedrooms', '>=', $value)
        );

        request()->whenFilled('bathrooms', fn ($value) =>
        $query->where('bathrooms', '>=', $value)
        );

        request()->whenFilled('view_type', fn ($value) =>
        $query->where('view_type', $value)
        );

        return DevelopmentUnitResource::collection(
            $query->orderBy('unit_number')->paginate(request('per_page', 12))
        );
    }

    public function developmentUnit(string $slug)
    {
        return new DevelopmentResource(
            DevelopmentUnit::query()
                ->where('slug', $slug)
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['development','publishProfileEs', 'publishProfileEn', 'mediaAssets'])
                ->firstOrFail()
        );
    }
    public function manifest()
    {
        return response()->json([
            'listings' => Listing::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->select('id', 'slug', 'updated_at')
                ->get(),

            'developments' => Development::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->select('id', 'slug', 'updated_at')
                ->get(),

            'development_units' => DevelopmentUnit::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->select('id', 'slug', 'development_id', 'updated_at')
                ->get(),
        ]);
    }
    public function sitemap()
    {
        $baseUrl = rtrim(config('app.frontend_url', config('app.url')), '/');

        return response()->json([
            'listings' => Listing::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->get()
                ->map(fn ($listing) => [
                    'url' => "{$baseUrl}/listings/{$listing->slug}",
                    'lastmod' => $listing->updated_at?->toDateString(),
                ]),

            'developments' => Development::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->get()
                ->map(fn ($development) => [
                    'url' => "{$baseUrl}/developments/{$development->slug}",
                    'lastmod' => $development->updated_at?->toDateString(),
                ]),

            'development_units' => DevelopmentUnit::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->get()
                ->map(fn ($unit) => [
                    'url' => "{$baseUrl}/development-units/{$unit->slug}",
                    'lastmod' => $unit->updated_at?->toDateString(),
                ]),
        ]);
    }
    public function aiContext()
    {
        $lang = request('lang', 'es');

        return response()->json([
            'generated_at' => now()->toIso8601String(),

            'listings' => Listing::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn'])
                ->get()
                ->map(function ($listing) use ($lang) {
                    $profile = $lang === 'en'
                        ? $listing->publishProfileEn
                        : $listing->publishProfileEs;

                    return [
                        'type' => 'listing',
                        'slug' => $listing->slug,
                        'title' => $listing->title,
                        'summary' => $profile?->ai_summary,
                        'keywords' => $profile?->keywords,
                        'payload' => $profile?->api_payload,
                    ];
                }),

            'developments' => Development::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn'])
                ->get()
                ->map(function ($development) use ($lang) {
                    $profile = $lang === 'en'
                        ? $development->publishProfileEn
                        : $development->publishProfileEs;

                    return [
                        'type' => 'development',
                        'slug' => $development->slug,
                        'title' => $development->name,
                        'summary' => $profile?->ai_summary,
                        'keywords' => $profile?->keywords,
                        'payload' => $profile?->api_payload,
                    ];
                }),

            'development_units' => DevelopmentUnit::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn', 'development'])
                ->get()
                ->map(function ($unit) use ($lang) {
                    $profile = $lang === 'en'
                        ? $unit->publishProfileEn
                        : $unit->publishProfileEs;

                    return [
                        'type' => 'development_unit',
                        'slug' => $unit->slug,
                        'title' => $unit->development?->name . ' - ' . $unit->unit_number,
                        'summary' => $profile?->ai_summary,
                        'keywords' => $profile?->keywords,
                        'payload' => $profile?->api_payload,
                    ];
                }),
        ]);
    }
}
