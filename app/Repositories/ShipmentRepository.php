<?php

namespace App\Repositories;

use App\DTOs\ShipmentFilterData;
use App\Models\Queries\ShipmentQuery;
use App\Models\Shipment;
use App\Repositories\Interfaces\ShipmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function __construct(
        private readonly Shipment $model
    ) {}

    public function findById(int $id): ?Shipment
    {
        return $this->model->with([
            'customer',
            'route',
            'deliveryDetails',
            'deliveryProofs',
            'statusLogs'
        ])->find($id);
    }

    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        return $this->model->where('tracking_number', $trackingNumber)->first();
    }

    public function create(array $data): Shipment
    {
        return $this->model->create($data);
    }

    public function update(Shipment $shipment, array $data): bool
    {
        return $shipment->update($data);
    }

    public function delete(Shipment $shipment): bool
    {
        return $shipment->delete();
    }

    public function getAll(ShipmentFilterData $filters): Collection
    {
        $query = new ShipmentQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->withCommonRelations()
            ->get();
    }

    public function getPaginated(ShipmentFilterData $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = new ShipmentQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->withCommonRelations()
            ->paginate($perPage);
    }

    public function getByCustomerId(int $customerId, ShipmentFilterData $filters): Collection
    {
        $filters = new ShipmentFilterData(
            ...(array)$filters,
            customer_id: $customerId
        );
        
        $query = new ShipmentQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->withCommonRelations()
            ->get();
    }

    public function getByRouteId(int $routeId, ShipmentFilterData $filters): Collection
    {
        $filters = new ShipmentFilterData(
            ...(array)$filters,
            route_id: $routeId
        );
        
        $query = new ShipmentQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->withCommonRelations()
            ->get();
    }

    public function getByStatus(string $status, ShipmentFilterData $filters): Collection
    {
        $filters = new ShipmentFilterData(
            ...(array)$filters,
            status: $status
        );
        
        $query = new ShipmentQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->withCommonRelations()
            ->get();
    }
} 