<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use App\Models\Request;
use App\Models\RequestStatusHistory;
use App\Models\Assignment;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class RequestManagementController extends Controller
{
    public function index(HttpRequest $request)
    {
        $query = Request::with(['user', 'serviceType', 'assignments.user']);

        $perPage = max(5, min(100, (int) $request->get('per_page', 10)));

        // Apply filters only when a non-empty value is provided
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->integer('service_type_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $requests = $query->latest()->paginate($perPage)->withQueryString();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['staff', 'admin', 'clerk', 'inspector']);
        })->get();
        $users = User::all();

        return view('admin.requests.index', compact('requests', 'serviceTypes', 'staff', 'users'));
    }

    public function create()
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $users = User::all();
        return view('admin.requests.create', compact('serviceTypes', 'users'));
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_type_id' => 'required|exists:service_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:created,assigned,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $requestModel = Request::create([
            'user_id' => $validated['user_id'],
            'service_type_id' => $validated['service_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'barangay' => $validated['barangay'] ?? null,
            'city' => $validated['city'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
        ]);

        // Create status history
        RequestStatusHistory::create([
            'request_id' => $requestModel->id,
            'status' => $validated['status'],
            'notes' => 'Request created by admin',
            'changed_by' => auth()->id(),
        ]);

        // Assign to staff if provided
        if (!empty($validated['assignee_id'])) {
            Assignment::create([
                'request_id' => $requestModel->id,
                'user_id' => $validated['assignee_id'],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.requests.show', $requestModel->id)
            ->with('success', 'Request created successfully.');
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

        $serviceTypes = ServiceType::where('is_active', true)->get();
        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['staff', 'admin', 'clerk', 'inspector']);
        })->get();
        $users = User::all();

        return view('admin.requests.show', compact('requestModel', 'serviceTypes', 'staff', 'users'));
    }

    public function edit($id)
    {
        $requestModel = Request::with(['user', 'serviceType', 'assignments.user'])->findOrFail($id);
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $users = User::all();
        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['staff', 'admin', 'clerk', 'inspector']);
        })->get();

        return view('admin.requests.edit', compact('requestModel', 'serviceTypes', 'users', 'staff'));
    }

    public function update(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_type_id' => 'required|exists:service_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:created,assigned,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
            'status_notes' => 'nullable|string',
        ]);

        $oldStatus = $requestModel->status;

        $requestModel->update([
            'user_id' => $validated['user_id'],
            'service_type_id' => $validated['service_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'barangay' => $validated['barangay'] ?? null,
            'city' => $validated['city'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
        ]);

        // Create status history if status changed
        if ($oldStatus !== $validated['status']) {
            RequestStatusHistory::create([
                'request_id' => $requestModel->id,
                'status' => $validated['status'],
                'notes' => $validated['status_notes'] ?? 'Status updated by admin',
                'changed_by' => auth()->id(),
            ]);

            $this->notifyRequestOwner(
                $requestModel,
                'Request status updated',
                'Your request "' . $requestModel->title . '" status changed from ' . $oldStatus . ' to ' . $validated['status'] . '.'
            );
        }

        // Handle assignment
        if (!empty($validated['assignee_id'])) {
            Assignment::updateOrCreate(
                [
                    'request_id' => $requestModel->id,
                    'user_id' => $validated['assignee_id'],
                    'status' => 'active',
                ],
                [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]
            );
        }

        return redirect()->route('admin.requests.show', $requestModel->id)
            ->with('success', 'Request updated successfully.');
    }

    public function destroy($id)
    {
        $requestModel = Request::findOrFail($id);
        $requestModel->delete();

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request deleted successfully.');
    }

    public function updateStatus(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);

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
            'notes' => $validated['notes'] ?? 'Status updated by admin',
            'changed_by' => auth()->id(),
        ]);

        if ($oldStatus !== $validated['status']) {
            $this->notifyRequestOwner(
                $requestModel,
                'Request status updated',
                'Your request "' . $requestModel->title . '" status changed from ' . $oldStatus . ' to ' . $validated['status'] . '.'
            );
        }

        return redirect()->back()->with('success', 'Request status updated successfully.');
    }

    public function assign(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Assignment::updateOrCreate(
            [
                'request_id' => $requestModel->id,
                'user_id' => $validated['user_id'],
                'status' => 'active',
            ],
            [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]
        );

        // Update status if needed
        if ($requestModel->status === 'created') {
            $requestModel->update(['status' => 'assigned']);
            RequestStatusHistory::create([
                'request_id' => $requestModel->id,
                'status' => 'assigned',
                'notes' => 'Request assigned to staff',
                'changed_by' => auth()->id(),
            ]);

            $this->notifyRequestOwner(
                $requestModel,
                'Request assigned',
                'Your request "' . $requestModel->title . '" has been assigned to a staff member.'
            );
        }

        return redirect()->back()->with('success', 'Request assigned successfully.');
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

