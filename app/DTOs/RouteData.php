<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class RouteData
{
    public string $routeId;
    public string $name;
    public string $description;
    public string $status;
    public ?string $driverId;
    public ?string $vehicleId;
    public array $tasks;
    public array $waypoints;
    public array $metadata;

    /**
     * Create a new RouteData instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->routeId = $data['route_id'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->driverId = $data['driver_id'] ?? null;
        $this->vehicleId = $data['vehicle_id'] ?? null;
        $this->tasks = $data['tasks'] ?? [];
        $this->waypoints = $data['waypoints'] ?? [];
        $this->metadata = $data['metadata'] ?? [];
    }

    /**
     * Convert the DTO to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'route_id' => $this->routeId,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'driver_id' => $this->driverId,
            'vehicle_id' => $this->vehicleId,
            'tasks' => $this->tasks,
            'waypoints' => $this->waypoints,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Create a new instance from a collection
     *
     * @param Collection $collection
     * @return self
     */
    public static function fromCollection(Collection $collection): self
    {
        return new self($collection->toArray());
    }

    /**
     * Validate the DTO data
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(): bool
    {
        if (empty($this->routeId)) {
            throw new \InvalidArgumentException('Route ID is required');
        }

        if (empty($this->name)) {
            throw new \InvalidArgumentException('Route name is required');
        }

        if (!in_array($this->status, ['pending', 'active', 'completed', 'cancelled'])) {
            throw new \InvalidArgumentException('Invalid route status');
        }

        return true;
    }
} 