<?php

namespace App\Repositories\Interfaces;

use App\Models\Route;
use Illuminate\Support\Collection;

interface RouteRepositoryInterface
{
    /**
     * Get route with all relations
     *
     * @param int $id
     * @return Route|null
     */
    public function getRouteWithRelations(int $id): ?Route;

    /**
     * Get shipment deliveries by status
     *
     * @param array $statusArray
     * @param int $routeId
     * @return Collection
     */
    public function getShipmentDeliveriesByStatus(array $statusArray, int $routeId): Collection;

    /**
     * Get delivered shipment count
     *
     * @param int $routeId
     * @return int
     */
    public function getDeliveredShipmentCount(int $routeId): int;

    /**
     * Get route activity
     *
     * @param int $routeId
     * @return mixed
     */
    public function getRouteActivity(int $routeId);

    /**
     * Get routes by status
     *
     * @param array $statusArray
     * @param array $filters
     * @return Collection
     */
    public function getRoutesByStatus(array $statusArray, array $filters): Collection;

    /**
     * Create routes
     *
     * @param array $data
     * @return array
     */
    public function createRoutes(array $data): array;

    /**
     * Update route task ID
     *
     * @param int $routeId
     * @param string $taskId
     * @return bool
     */
    public function updateRouteTaskId(int $routeId, string $taskId): bool;

    /**
     * Get canceled inventory
     *
     * @param array $shipmentIds
     * @return Collection
     */
    public function getCanceledInventory(array $shipmentIds): Collection;
} 