<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Development;
use Illuminate\Http\Request;

class ListingsController extends Controller
{
    /**
     * Get all listings (with filters and pagination)
     */
    public function index(Request $request)
    {
        $query = Listing::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('development_id')) {
            $query->where('development_id', $request->development_id);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $listings = $query->with(['development:id,name,slug', 'mediaAssets', 'featuredImage'])
                         ->orderByDesc('created_at')
                         ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'total' => $listings->total(),
                'per_page' => $listings->perPage(),
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
            ]
        ]);
    }

    /**
     * Get a single listing with all relationships
     */
    public function show(Listing $listing)
    {
        $listing->load([
            'development:id,name,slug,location,developer_name',
            'mediaAssets',
            'featuredImage',
            'publishProfiles',
            'caseFiles'
        ]);

        return response()->json([
            'success' => true,
            'data' => $listing
        ]);
    }

    /**
     * Create a new listing
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'development_id' => 'required|exists:developments,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,sold',
            'listing_type' => 'required|in:sale,rent',
            'property_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'location' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_m2' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'description_es' => 'nullable|string',
            'description_en' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_public' => 'boolean',
            'public_status' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $listing = Listing::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Listado creado exitosamente',
            'data' => $listing
        ], 201);
    }

    /**
     * Update a listing
     */
    public function update(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'development_id' => 'nullable|exists:developments,id',
            'title' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,sold',
            'listing_type' => 'nullable|in:sale,rent',
            'property_type' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'location' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_m2' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'description_es' => 'nullable|string',
            'description_en' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_public' => 'boolean',
            'public_status' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $listing->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Listado actualizado exitosamente',
            'data' => $listing
        ]);
    }

    /**
     * Delete a listing
     */
    public function destroy(Listing $listing)
    {
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listado eliminado exitosamente'
        ]);
    }

    /**
     * Get listings by development
     */
    public function byDevelopment(Development $development, Request $request)
    {
        $query = $development->listings();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->with(['mediaAssets', 'featuredImage'])
                         ->paginate($request->input('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'total' => $listings->total(),
                'per_page' => $listings->perPage(),
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
            ]
        ]);
    }

    /**
     * Get statistics for listings
     */
    public function statistics(Request $request)
    {
        $query = Listing::query();

        if ($request->filled('development_id')) {
            $query->where('development_id', $request->development_id);
        }

        $stats = [
            'total' => $query->count(),
            'by_status' => $query->groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status'),
            'by_type' => $query->groupBy('property_type')->selectRaw('property_type, count(*) as count')->pluck('count', 'property_type'),
            'average_price' => $query->avg('price'),
            'min_price' => $query->min('price'),
            'max_price' => $query->max('price'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get public listings
     */
    public function publicIndex(Request $request)
    {
        $query = Listing::where('is_public', true)->where('status', 'active');

        if ($request->filled('development_id')) {
            $query->where('development_id', $request->development_id);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('location', 'like', $search);
            });
        }

        $listings = $query->with(['development:id,name,slug', 'featuredImage'])
                         ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $listings->items(),
            'pagination' => [
                'total' => $listings->total(),
                'per_page' => $listings->perPage(),
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
            ]
        ]);
    }
}
