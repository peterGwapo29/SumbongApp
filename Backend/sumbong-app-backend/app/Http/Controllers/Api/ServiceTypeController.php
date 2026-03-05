<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceTypeResource;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();

        return ServiceTypeResource::collection($serviceTypes);
    }

    public function show($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        return new ServiceTypeResource($serviceType);
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

        return new ServiceTypeResource($serviceType);
    }

    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'department' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $serviceType->update($validated);

        $this->notifyResidents(
            'Service Updated',
            'The service "' . $serviceType->name . '" has been updated.'
        );

        return new ServiceTypeResource($serviceType);
    }

    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->delete();

        $this->notifyResidents(
            'Service Removed',
            'The service "' . $serviceType->name . '" is no longer available.'
        );

        return response()->json(['message' => 'Service type deleted successfully']);
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

