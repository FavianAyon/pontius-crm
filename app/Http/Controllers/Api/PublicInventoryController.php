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
        return ListingResource::collection(
            Listing::query()
                ->where('is_public', true)
                ->where('public_status', 'published')
                ->with(['publishProfileEs', 'publishProfileEn', 'mediaAssets'])
                ->latest()
                ->paginate(12)
        )->additional([
            'meta' => [
                'cached' => false,
            ],
        ]);
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
