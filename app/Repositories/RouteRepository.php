<?php

namespace App\Repositories;

use App\Models\Route;
use App\Models\ShipmentDeliveryDetail;
use App\Models\ShipmentPropsDeliveryInventory;
use App\Models\CustomerLocationsFhfNotes;
use App\Models\Routeactivity;
use App\Constants\Roles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RouteRepository
{
    /**
     * Get route with relationships
     *
     * @param int $routeId
     * @return Route|null
     */
    public function getRouteWithRelations(int $routeId): ?Route
    {
        return Route::with([
            'driver',
            'shipmentDeliveries.shipment.fhfNotes',
            'shipmentDeliveries.shipment.warehouse',
            'shipmentDeliveries.shipment.shipmentCustomerDeliveryLocations'
        ])->find($routeId);
    }

    /**
     * Get shipment deliveries by status
     *
     * @param array $statusArray
     * @param int $routeId
     * @return Collection
     */
    public function getShipmentDeliveriesByStatus(array $statusArray, int $routeId): Collection
    {
        return ShipmentDeliveryDetail::where('route_id', $routeId)
            ->whereIn('delivery_status', $statusArray)
            ->with([
                'shipment.warehouse',
                'shipment.shipmentCustomerDeliveryLocations',
                'shipment.fhfNotes'
            ])
            ->get();
    }

    /**
     * Get delivered shipment count
     *
     * @param int $routeId
     * @return int
     */
    public function getDeliveredShipmentCount(int $routeId): int
    {
        return ShipmentDeliveryDetail::where('route_id', $routeId)
            ->where('delivery_status', Roles::DELIVERED)
            ->count();
    }

    /**
     * Get route activity
     *
     * @param int $routeId
     * @return Routeactivity|null
     */
    public function getRouteActivity(int $routeId): ?Routeactivity
    {
        return Routeactivity::where('route_id', $routeId)->first();
    }

    /**
     * Get routes by status
     *
     * @param array $statusArray
     * @param array $filters
     * @return Collection
     */
    public function getRoutesByStatus(array $statusArray, array $filters = []): Collection
    {
        return Route::getRoutesData($statusArray, $filters);
    }

    /**
     * Create new routes
     *
     * @param array $jsonData
     * @return array
     */
    public function createRoutes(array $jsonData): array
    {
        DB::beginTransaction();
        try {
            $routeIds = Route::insertRoutes($jsonData);
            DB::commit();
            return $routeIds;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get canceled inventory
     *
     * @param array $shipmentIds
     * @return Collection
     */
    public function getCanceledInventory(array $shipmentIds): Collection
    {
        return ShipmentPropsDeliveryInventory::select(
            'item_name',
            'item_id',
            'unit_type',
            'unit_weight',
            DB::raw('SUM(quantity) as quantity')
        )
            ->whereIn('shipment_id', $shipmentIds)
            ->groupBy('item_name', 'item_id', 'unit_type', 'unit_weight')
            ->get();
    }

    /**
     * Update route task ID
     *
     * @param int $routeId
     * @param string $taskId
     * @return bool
     */
    public function updateRouteTaskId(int $routeId, string $taskId): bool
    {
        $deliveryDetail = ShipmentDeliveryDetail::where('route_id', $routeId)->first();
        if ($deliveryDetail) {
            $deliveryDetail->task_acumetica_id = $taskId;
            return $deliveryDetail->save();
        }
        return false;
    }
} 