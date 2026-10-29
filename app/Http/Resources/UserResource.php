<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            
            // Relationships
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
            'settings' => new UserSettingsResource($this->whenLoaded('settings')),
            
            // Driver metrics
            'completed_routes_count' => $this->when($this->completed_routes_count !== null, $this->completed_routes_count),
            'completed_shipments_count' => $this->when($this->completed_shipments_count !== null, $this->completed_shipments_count),
            'total_distance' => $this->when($this->completed_shipments_sum_distance !== null, $this->completed_shipments_sum_distance),
            'on_time_delivery_rate' => $this->when($this->completed_shipments_avg_on_time_delivery_rate !== null, 
                round($this->completed_shipments_avg_on_time_delivery_rate, 2)),
            
            // Merchandiser metrics
            'assigned_locations_count' => $this->when($this->assigned_locations_count !== null, $this->assigned_locations_count),
            'completed_visits_count' => $this->when($this->completed_visits_count !== null, $this->completed_visits_count),
            'avg_visit_duration' => $this->when($this->completed_visits_avg_visit_duration !== null, 
                round($this->completed_visits_avg_visit_duration, 2)),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'last_login_at' => $this->last_login_at?->toISOString(),
        ];
    }
} 