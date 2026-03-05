<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceTypeManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(5, min(100, (int) $request->get('per_page', 10)));

        $query = ServiceType::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->string('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $serviceTypes = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('admin.service-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType = ServiceType::create($validated);

        $this->notifyResidents(
            'New Service Available',
            'A new service "' . $serviceType->name . '" is now available.'
        );

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type created successfully');
    }

    public function edit($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType->update($validated);

        $this->notifyResidents(
            'Service Updated',
            'The service "' . $serviceType->name . '" has been updated.'
        );

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type updated successfully');
    }

    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceName = $serviceType->name;
        $serviceType->delete();

        $this->notifyResidents(
            'Service Removed',
            'The service "' . $serviceName . '" is no longer available.'
        );

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type deleted successfully');
    }

    private function notifyResidents(string $title, string $message): void
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => 'alert',
            'target_audience' => 'residents',
        ]);

        $users = User::where('user_type', 'resident')->get();

        if ($users->isEmpty()) {
            return;
        }

        $now = now();

        $deliveries = $users->map(static function (User $user) use ($notification, $now): array {
            return [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'read' => false,
                'delivered_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        NotificationDelivery::insert($deliveries);
    }
}

