<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'assigned_by' => $this->assigned_by,
            'assigned_by_user' => $this->whenLoaded('assignedBy', fn() => new UserResource($this->assignedBy)),
            'status' => $this->status,
            'assigned_at' => $this->assigned_at,
        ];
    }
}

