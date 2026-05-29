<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Development;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevelopmentsController extends Controller
{
    /**
     * Get all developments (with filters and pagination)
     */
    public function index(Request $request)
    {
        $query = Development::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sales_status')) {
            $query->where('sales_status', $request->sales_status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Public only (for non-admin)
        if ($request->filled('public_only')) {
            $query->where('is_public', true);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $developments = $query->with(['units', 'listings', 'mediaAssets', 'featuredImage'])
                              ->orderByDesc('created_at')
                              ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $developments->items(),
            'pagination' => [
                'total' => $developments->total(),
                'per_page' => $developments->perPage(),
                'current_page' => $developments->currentPage(),
                'last_page' => $developments->lastPage(),
            ]
        ]);
    }

    /**
     * Get a single development with all relationships
     */
    public function show(Development $development)
    {
        $development->load([
            'units' => function ($query) {
                $query->where('status', 'available')->limit(10);
            },
            'listings' => function ($query) {
                $query->where('status', 'active')->limit(10);
            },
            'mediaAssets',
            'featuredImage',
            'publishProfiles'
        ]);

        return response()->json([
            'success' => true,
            'data' => $development
        ]);
    }

    /**
     * Create a new development
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:planning,construction,completed,inactive',
            'sales_status' => 'required|in:not_started,pre_launch,launching,on_sale,sold_out',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_es' => 'nullable|string',
            'description_en' => 'nullable|string',
            'developer_name' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|date',
            'construction_status' => 'nullable|string',
            'total_units' => 'nullable|integer|min:0',
            'available_units' => 'nullable|integer|min:0',
            'is_public' => 'boolean',
            'public_status' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $development = Development::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desarrollo creado exitosamente',
            'data' => $development
        ], 201);
    }

    /**
     * Update a development
     */
    public function update(Request $request, Development $development)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|in:planning,construction,completed,inactive',
            'sales_status' => 'nullable|in:not_started,pre_launch,launching,on_sale,sold_out',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_es' => 'nullable|string',
            'description_en' => 'nullable|string',
            'developer_name' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|date',
            'construction_status' => 'nullable|string',
            'total_units' => 'nullable|integer|min:0',
            'available_units' => 'nullable|integer|min:0',
            'is_public' => 'boolean',
            'public_status' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $development->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desarrollo actualizado exitosamente',
            'data' => $development
        ]);
    }

    /**
     * Delete a development
     */
    public function destroy(Development $development)
    {
        $development->delete();

        return response()->json([
            'success' => true,
            'message' => 'Desarrollo eliminado exitosamente'
        ]);
    }

    /**
     * Get statistics for developments
     */
    public function statistics(Request $request)
    {
        $query = Development::query();

        // Public only (for non-admin)
        if ($request->filled('public_only')) {
            $query->where('is_public', true);
        }

        $stats = [
            'total' => $query->count(),
            'by_status' => $query->groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status'),
            'by_sales_status' => $query->groupBy('sales_status')->selectRaw('sales_status, count(*) as count')->pluck('count', 'sales_status'),
            'total_units' => $query->sum('total_units'),
            'available_units' => $query->sum('available_units'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get developments with limited info for listings
     */
    public function publicIndex(Request $request)
    {
        $query = Development::where('is_public', true)->where('public_status', 'active');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('location', 'like', $search);
            });
        }

        $developments = $query->with(['featuredImage', 'listings' => function ($q) {
            $q->where('is_public', true)->limit(5);
        }])
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $developments->items(),
            'pagination' => [
                'total' => $developments->total(),
                'per_page' => $developments->perPage(),
                'current_page' => $developments->currentPage(),
                'last_page' => $developments->lastPage(),
            ]
        ]);
    }
}
