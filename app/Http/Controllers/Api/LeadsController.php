<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    /**
     * Get all leads (with filters and pagination)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Lead::query();

        // Filter by assigned user if not admin
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to_user_id', $user->id)
                  ->orWhere('registered_by_user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to_user_id', $request->assigned_to);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $leads = $query->with(['assignedTo:id,name,email', 'registeredBy:id,name,email'])
                      ->orderByDesc('created_at')
                      ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'pagination' => [
                'total' => $leads->total(),
                'per_page' => $leads->perPage(),
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
            ]
        ]);
    }

    /**
     * Get a single lead with all relationships
     */
    public function show(Lead $lead)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->hasRole('admin') &&
            $lead->assigned_to_user_id !== $user->id &&
            $lead->registered_by_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver este lead'
            ], 403);
        }

        $lead->load([
            'assignedTo:id,name,email',
            'registeredBy:id,name,email',
            'development:id,name,slug',
            'listing:id,name,slug',
            'developmentUnit:id,name',
            'leadActivities:id,lead_id,type,description,created_at',
        ]);

        return response()->json([
            'success' => true,
            'data' => $lead
        ]);
    }

    /**
     * Create a new lead
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'source' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'intent' => 'nullable|string',
            'interest_target_type' => 'nullable|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'preferred_location' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $validated['registered_by_user_id'] = auth()->id();

        if (!isset($validated['assigned_to_user_id'])) {
            $validated['assigned_to_user_id'] = auth()->id();
        }

        $lead = Lead::create($validated);
        $lead->load(['assignedTo:id,name,email', 'registeredBy:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Lead creado exitosamente',
            'data' => $lead
        ], 201);
    }

    /**
     * Update a lead
     */
    public function update(Request $request, Lead $lead)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->hasRole('admin') && $lead->assigned_to_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar este lead'
            ], 403);
        }

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'source' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'intent' => 'nullable|string',
            'interest_target_type' => 'nullable|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'preferred_location' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'next_follow_up_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $lead->update($validated);
        $lead->load(['assignedTo:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Lead actualizado exitosamente',
            'data' => $lead
        ]);
    }

    /**
     * Delete a lead
     */
    public function destroy(Lead $lead)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->hasRole('admin') && $lead->registered_by_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este lead'
            ], 403);
        }

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead eliminado exitosamente'
        ]);
    }

    /**
     * Get leads assigned to current user
     */
    public function myLeads(Request $request)
    {
        $user = auth()->user();
        $status = $request->input('status', null);

        $query = Lead::where('assigned_to_user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $leads = $query->with(['assignedTo:id,name,email'])
                      ->orderByDesc('next_follow_up_at')
                      ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'pagination' => [
                'total' => $leads->total(),
                'per_page' => $leads->perPage(),
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
            ]
        ]);
    }

    /**
     * Get leads statistics
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();
        $baseQuery = Lead::query();

        if (!$user->hasRole('admin')) {
            $baseQuery->where('assigned_to_user_id', $user->id);
        }

        $stats = [
            'total' => $baseQuery->count(),
            'by_status' => $baseQuery->groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status'),
            'by_priority' => $baseQuery->groupBy('priority')->selectRaw('priority, count(*) as count')->pluck('count', 'priority'),
            'pending_follow_up' => $baseQuery->whereNotNull('next_follow_up_at')
                                            ->where('next_follow_up_at', '<=', now())
                                            ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Create a new activity for a lead
     */
    public function storeActivity(Request $request, Lead $lead)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->hasRole('admin') &&
            $lead->assigned_to_user_id !== $user->id &&
            $lead->registered_by_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para agregar actividades a este lead'
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'direction' => 'nullable|string|in:inbound,outbound',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date_format:Y-m-d H:i:s',
            'completed_at' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
        ]);

        $activity = $lead->leadActivities()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Actividad creada exitosamente',
            'data' => $activity
        ], 201);
    }

    /**
     * Get all activities for a lead
     */
    public function getActivities(Request $request, Lead $lead)
    {
        $user = auth()->user();

        // Check authorization
        if (!$user->hasRole('admin') &&
            $lead->assigned_to_user_id !== $user->id &&
            $lead->registered_by_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver las actividades de este lead'
            ], 403);
        }

        $activities = $lead->leadActivities()
                          ->with('user:id,name,email')
                          ->latest()
                          ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $activities->items(),
            'pagination' => [
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
            ]
        ]);
    }

    /**
     * Reassign a lead to another agent
     */
    public function reassign(Request $request, Lead $lead)
    {
        $user = auth()->user();

        // Check authorization - only admins can reassign
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Solo administradores pueden reasignar leads'
            ], 403);
        }

        $validated = $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id'
        ]);

        $oldAssigneeId = $lead->assigned_to_user_id;
        $oldAssigneeName = $lead->assignedTo?->name ?? 'desconocido';
        $newAssigneeName = User::find($validated['assigned_to_user_id'])->name;
        $lead->update($validated);

        // Log the reassignment activity
        $lead->leadActivities()->create([
            'type' => 'reassignment',
            'title' => 'Lead reasignado',
            'description' => "Lead reasignado de {$oldAssigneeName} a {$newAssigneeName}",
            'user_id' => auth()->id(),
            'status' => 'completed'
        ]);

        $lead->load('assignedTo:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Lead reasignado exitosamente',
            'data' => $lead
        ]);
    }

    /**
     * Get available agents for assignment
     */
    public function getAvailableAgents()
    {
        $agents = User::role('agent')
                     ->where('is_active', true)
                     ->select('id', 'name', 'email')
                     ->withCount('assignedLeads')
                     ->get();

        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }

    /**
     * Get follow-ups with filters and pagination
     */
    public function getFollowUps(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\FollowUp::query();

        // Filter by assigned user if not admin
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to_user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('lead', function ($q) use ($search) {
                $q->where('full_name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $followUps = $query->with(['lead:id,full_name,email', 'assignedTo:id,name'])
                          ->orderByDesc('scheduled_at')
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $followUps->items(),
            'pagination' => [
                'total' => $followUps->total(),
                'per_page' => $followUps->perPage(),
                'current_page' => $followUps->currentPage(),
                'last_page' => $followUps->lastPage(),
            ]
        ]);
    }

    /**
     * Complete a follow-up
     */
    public function completeFollowUp($followUpId, Request $request)
    {
        $followUp = \App\Models\FollowUp::findOrFail($followUpId);
        $user = auth()->user();

        if (!$user->hasRole('admin') && $followUp->assigned_to_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para completar este seguimiento'
            ], 403);
        }

        $resultNotes = $request->input('result_notes', null);
        $followUp->markAsCompleted($resultNotes);

        return response()->json([
            'success' => true,
            'message' => 'Seguimiento completado exitosamente',
            'data' => $followUp
        ]);
    }

    /**
     * Get tasks with filters and pagination
     */
    public function getTasks(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Task::query();

        // Filter by assigned user if not admin
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to_user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $tasks = $query->with(['lead:id,full_name,email', 'assignedTo:id,name'])
                      ->orderByDesc('due_at')
                      ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
            'pagination' => [
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
            ]
        ]);
    }

    /**
     * Complete a task
     */
    public function completeTask($taskId)
    {
        $task = \App\Models\Task::findOrFail($taskId);
        $user = auth()->user();

        if (!$user->hasRole('admin') && $task->assigned_to_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para completar esta tarea'
            ], 403);
        }

        $task->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Tarea completada exitosamente',
            'data' => $task
        ]);
    }
}
