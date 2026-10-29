<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'route_number' => $this->route_number,
            'status' => $this->status,
            'delivery_date' => $this->delivery_date?->toISOString(),
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'is_completed' => $this->is_completed,
            'total_distance' => $this->total_distance,
            'total_stops' => $this->total_stops,
            'notes' => $this->notes,
            
            // Relationships
            'driver' => new UserResource($this->whenLoaded('driver')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'shipments' => ShipmentResource::collection($this->whenLoaded('shipments')),
            'pause_logs' => RoutePauseLogResource::collection($this->whenLoaded('routePauseLogs')),
            
            // Computed properties
            'completion_percentage' => $this->when(isset($this->completion_percentage), $this->completion_percentage),
            'estimated_completion_time' => $this->when(isset($this->estimated_completion_time), $this->estimated_completion_time),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 