<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevelopmentResource;
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
        return DevelopmentResource::collection(
            Development::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets'])
                ->latest()
                ->paginate(12)
        )->additional([
            'meta' => [
                'cached' => false,
            ],
        ]);;
    }

    public function development(string $slug)
    {
        return new DevelopmentResource(
            Development::query()
                ->where('slug', $slug)
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets',
                'units' => fn ($query) => $query
        ->where('is_public', true)
        ->where('public_status', 'published'),])
                ->firstOrFail()
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
}
