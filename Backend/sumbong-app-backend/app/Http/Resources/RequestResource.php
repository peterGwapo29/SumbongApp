<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'service_type_id' => $this->service_type_id,
            'service_type' => $this->whenLoaded('serviceType', fn() => new ServiceTypeResource($this->serviceType)),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'location' => [
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'barangay' => $this->barangay,
                'city' => $this->city,
            ],
            'priority' => $this->priority,
            'attachments' => $this->whenLoaded('attachments', fn() => AttachmentResource::collection($this->attachments)),
            'assignments' => $this->whenLoaded('assignments', fn() => AssignmentResource::collection($this->assignments)),
            'status_history' => $this->whenLoaded('statusHistory', fn() => RequestStatusHistoryResource::collection($this->statusHistory)),
            'feedback' => $this->whenLoaded('feedback', fn() => FeedbackResource::collection($this->feedback)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

