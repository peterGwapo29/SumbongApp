<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = NotificationDelivery::with('notification')
            ->where('user_id', $user->id);

        if ($request->has('read')) {
            $query->where('read', $request->boolean('read'));
        }

        $deliveries = $query->latest('delivered_at')->paginate(20);

        $notifications = $deliveries->map(function (NotificationDelivery $delivery) {
            $notification = $delivery->notification;
            $notification->setRelation('deliveries', collect([$delivery]));

            return $notification;
        });

        return NotificationResource::collection($notifications);
    }

    public function show($id)
    {
        $user = request()->user();
        $delivery = NotificationDelivery::with('notification')
            ->where('notification_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return new NotificationResource($delivery->notification);
    }

    public function markAsRead($id)
    {
        $user = request()->user();
        $delivery = NotificationDelivery::where('notification_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$delivery->read) {
            $delivery->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        NotificationDelivery::where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:alert,request_update,assignment,system',
            'target_audience' => 'required|in:all,residents,staff',
        ]);

        $notification = Notification::create($validated);

        // Create deliveries based on target audience
        $this->createDeliveries($notification);

        return new NotificationResource($notification);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'type' => 'sometimes|in:alert,request_update,assignment,system',
            'target_audience' => 'sometimes|in:all,residents,staff',
        ]);

        $notification->update($validated);

        return new NotificationResource($notification);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }

    private function createDeliveries(Notification $notification)
    {
        $query = User::query();

        if ($notification->target_audience === 'residents') {
            $query->where('user_type', 'resident');
        } elseif ($notification->target_audience === 'staff') {
            $query->whereHas('role', function ($q) {
                $q->whereIn('name', ['staff', 'admin', 'clerk', 'inspector']);
            });
        }

        $users = $query->get();

        $deliveries = $users->map(function ($user) use ($notification) {
            return [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'read' => false,
                'delivered_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        NotificationDelivery::insert($deliveries);
    }
}

