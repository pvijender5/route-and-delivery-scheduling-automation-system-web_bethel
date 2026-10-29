<?php

namespace App\Http\Resources\Admin;

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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'role_id' => $this->role_id,
            'company_id' => $this->company_id,
            'carrier_owner_id' => $this->carrier_owner_id,
            
            // Relationships
            'role' => new RoleResource($this->whenLoaded('role')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'carrier_owner' => new UserResource($this->whenLoaded('carrierOwner')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'settings' => new UserSettingsResource($this->whenLoaded('settings')),
            
            // Carrier owner specific relationships
            'sub_drivers' => UserResource::collection($this->whenLoaded('subDrivers')),
            'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles')),
            'insurance_documents' => DocumentResource::collection($this->whenLoaded('insuranceDocuments')),
            'contracts' => ContractResource::collection($this->whenLoaded('contracts')),
            
            // Activity metrics
            'logins_count' => $this->when($this->logins_count !== null, $this->logins_count),
            'activities_count' => $this->when($this->activities_count !== null, $this->activities_count),
            'documents_count' => $this->when($this->documents_count !== null, $this->documents_count),
            'last_login' => $this->when($this->last_login, $this->last_login),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
        ];
    }
} 