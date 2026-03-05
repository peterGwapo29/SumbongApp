<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use App\Models\Request;
use App\Models\RequestStatusHistory;
use App\Models\Assignment;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index(HttpRequest $request)
    {
        $user = $request->user();
        $query = Request::with(['serviceType', 'attachments', 'statusHistory']);

        // If not admin, only show user's own requests
        if (!$user->isAdmin() && !$user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type
        if ($request->has('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->latest()->paginate(15);

        return RequestResource::collection($requests);
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'created';
        $validated['priority'] = $validated['priority'] ?? 'medium';

        $requestModel = Request::create($validated);

        // Create status history
        RequestStatusHistory::create([
            'request_id' => $requestModel->id,
            'status' => 'created',
            'changed_by' => $request->user()->id,
        ]);

        return new RequestResource($requestModel->load(['serviceType', 'user']));
    }

    public function show($id)
    {
        $requestModel = Request::with([
            'user',
            'serviceType',
            'attachments',
            'assignments.user',
            'statusHistory.changedBy',
            'feedback.user'
        ])->findOrFail($id);

        return new RequestResource($requestModel);
    }

    public function update(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);
        $user = $request->user();

        // Only allow users to update their own requests, or admins/staff
        if ($requestModel->user_id !== $user->id && !$user->isAdmin() && !$user->isStaff()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'address' => 'sometimes|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'priority' => 'sometimes|in:low,medium,high,urgent',
        ]);

        $requestModel->update($validated);

        return new RequestResource($requestModel->fresh(['serviceType', 'user']));
    }

    public function destroy($id)
    {
        $requestModel = Request::findOrFail($id);
        $requestModel->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }

    public function updateStatus(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isStaff()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:created,assigned,in_progress,resolved,closed',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $requestModel->status;
        $requestModel->update(['status' => $validated['status']]);

        // Create status history
        RequestStatusHistory::create([
            'request_id' => $requestModel->id,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'changed_by' => $user->id,
        ]);

        if ($oldStatus !== $validated['status']) {
            $this->notifyRequestOwner(
                $requestModel,
                'Request status updated',
                'Your request "' . $requestModel->title . '" status changed from ' . $oldStatus . ' to ' . $validated['status'] . '.'
            );
        }

        return new RequestResource($requestModel->fresh(['serviceType', 'user', 'statusHistory']));
    }

    public function assign(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isStaff()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Create or update assignment
        Assignment::updateOrCreate(
            [
                'request_id' => $requestModel->id,
                'user_id' => $validated['user_id'],
                'status' => 'active',
            ],
            [
                'assigned_by' => $user->id,
                'assigned_at' => now(),
            ]
        );

        // Update request status if needed
        if ($requestModel->status === 'created') {
            $requestModel->update(['status' => 'assigned']);
            RequestStatusHistory::create([
                'request_id' => $requestModel->id,
                'status' => 'assigned',
                'changed_by' => $user->id,
            ]);

            $this->notifyRequestOwner(
                $requestModel,
                'Request assigned',
                'Your request "' . $requestModel->title . '" has been assigned to a staff member.'
            );
        }

        return new RequestResource($requestModel->fresh(['serviceType', 'user', 'assignments.user']));
    }

    public function adminIndex(HttpRequest $request)
    {
        $query = Request::with(['user', 'serviceType', 'attachments', 'assignments.user']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $requests = $query->latest()->paginate(20);

        return RequestResource::collection($requests);
    }

    public function adminShow($id)
    {
        $requestModel = Request::with([
            'user',
            'serviceType',
            'attachments',
            'assignments.user',
            'statusHistory.changedBy',
            'feedback.user'
        ])->findOrFail($id);

        return new RequestResource($requestModel);
    }

    public function stats(HttpRequest $request)
    {
        $stats = [
            'total' => Request::count(),
            'by_status' => Request::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_priority' => Request::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'by_service_type' => Request::select('service_types.name', DB::raw('count(*) as count'))
                ->join('service_types', 'requests.service_type_id', '=', 'service_types.id')
                ->groupBy('service_types.name')
                ->pluck('count', 'name'),
            'recent' => Request::with(['user', 'serviceType'])
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn($r) => new RequestResource($r)),
        ];

        return response()->json($stats);
    }

    private function notifyRequestOwner(Request $requestModel, string $title, string $message): void
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => 'request_update',
            'target_audience' => 'residents',
        ]);

        NotificationDelivery::create([
            'notification_id' => $notification->id,
            'user_id' => $requestModel->user_id,
            'read' => false,
            'delivered_at' => now(),
        ]);
    }
}

