<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'status' => $this->status,
            'notes' => $this->notes,
            'changed_by' => $this->changed_by,
            'changed_by_user' => $this->whenLoaded('changedBy', fn() => new UserResource($this->changedBy)),
            'created_at' => $this->created_at,
        ];
    }
}

