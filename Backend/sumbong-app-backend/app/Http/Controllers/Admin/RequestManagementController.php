<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        }

        return redirect()->back()->with('success', 'Request assigned successfully.');
    }
}

