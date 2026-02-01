<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'target_audience' => $this->target_audience,
            'deliveries' => $this->whenLoaded('deliveries', fn() => NotificationDeliveryResource::collection($this->deliveries)),
            'created_at' => $this->created_at,
        ];
    }
}

