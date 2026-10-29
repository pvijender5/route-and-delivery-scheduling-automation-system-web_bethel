<?php

namespace App\Services;

use App\Repositories\Interfaces\RouteRepositoryInterface;
use App\DTOs\RouteData;
use App\DTOs\RouteFilterData;
use App\Constants\Roles;
use App\Exceptions\RouteException;
use App\Services\Interfaces\CacheServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class RouteService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_PREFIX = 'route:';
    private const CACHE_TAG = 'routes';

    private RouteRepositoryInterface $routeRepository;
    private CacheServiceInterface $cacheService;

    public function __construct(
        RouteRepositoryInterface $routeRepository,
        CacheServiceInterface $cacheService
    ) {
        $this->routeRepository = $routeRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Get route details with delivery data
     *
     * @param int $routeId
     * @return array
     * @throws RouteException
     */
    public function getRouteDetails(int $routeId): array
    {
        try {
            $cacheKey = $this->getCacheKey($routeId);
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($routeId) {
                    $route = $this->routeRepository->getRouteWithRelations($routeId);
                    if (!$route) {
                        throw new RouteException("Route not found");
                    }

                    $statusArray = Roles::ACTIVESHIPMENTS_ARRAY;
                    $shipmentDetails = $this->routeRepository->getShipmentDeliveriesByStatus($statusArray, $routeId);
                    
                    $metrics = $this->calculateRouteMetrics($shipmentDetails);
                    $deliveredShipmentCount = $this->routeRepository->getDeliveredShipmentCount($routeId);
                    $routeActivity = $this->routeRepository->getRouteActivity($routeId);
                    
                    return [
                        'route' => $route,
                        'onTimeDeliveryCount' => $metrics['onTimeDeliveryCount'],
                        'deliveredShipmentCount' => $deliveredShipmentCount,
                        'deliveryDetails' => $shipmentDetails,
                        'routeActivity' => $routeActivity ?? 'undefined',
                        'props_weight_total' => $metrics['props_weight_total']
                    ];
                }
            );
        } catch (Throwable $e) {
            $this->logError('getRouteDetails', $e);
            throw new RouteException('Failed to get route details', 0, $e);
        }
    }

    /**
     * Get routes query for map/track filtering
     *
     * @param RouteFilterData $filters
     * @param array $statusArray
     * @return Collection
     * @throws RouteException
     */
    public function getFilteredRoutes(RouteFilterData $filters, array $statusArray): Collection
    {
        try {
            $cacheKey = "routes:filtered:" . md5(serialize($filters) . serialize($statusArray));
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($filters, $statusArray) {
                    $routes = $this->routeRepository->getRoutesByStatus($statusArray, $filters->toArray());
                    return $this->calculateRouteDeliveryAverages($routes);
                }
            );
        } catch (Throwable $e) {
            $this->logError('getFilteredRoutes', $e);
            throw new RouteException('Failed to get filtered routes', 0, $e);
        }
    }

    /**
     * Create new routes from JSON data
     *
     * @param RouteData $routeData
     * @return array
     * @throws RouteException
     */
    public function createRoutes(RouteData $routeData): array
    {
        try {
            DB::beginTransaction();

            $routeIds = $this->routeRepository->createRoutes($routeData->toArray());
            
            if (!empty($routeIds) && isset($routeData->tasks)) {
                foreach ($routeIds as $routeId) {
                    foreach ($routeData->tasks as $task) {
                        $this->routeRepository->updateRouteTaskId($routeId, $task['id']);
                    }
                }
            }
            
            DB::commit();
            $this->clearRouteCache();
            
            return $routeIds;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('createRoutes', $e);
            throw new RouteException('Failed to create routes', 0, $e);
        }
    }

    /**
     * Get route details with canceled inventory
     *
     * @param int $routeId
     * @return array
     * @throws RouteException
     */
    public function getRouteWithCanceledInventory(int $routeId): array
    {
        try {
            $cacheKey = "route:{$routeId}:canceled_inventory";
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($routeId) {
                    $route = $this->routeRepository->getRouteWithRelations($routeId);
                    if (!$route) {
                        throw new RouteException("Route not found");
                    }

                    $shipmentIds = $route->shipmentDeliveries->pluck('shipment_id')->toArray();
                    $canceledInventory = $this->routeRepository->getCanceledInventory($shipmentIds);

                    return [
                        'route' => $route,
                        'canceled_inventory' => $canceledInventory
                    ];
                }
            );
        } catch (Throwable $e) {
            $this->logError('getRouteWithCanceledInventory', $e);
            throw new RouteException('Failed to get route with canceled inventory', 0, $e);
        }
    }

    /**
     * Calculate route metrics from shipment details
     *
     * @param Collection $shipmentDetails
     * @return array
     */
    private function calculateRouteMetrics(Collection $shipmentDetails): array
    {
        $onTimeDeliveryCount = 0;
        $props_weight_total = 0;
        
        foreach ($shipmentDetails as $detail) {
            if (isset($detail->shipment->props_weight)) {
                $props_weight_total += $detail->shipment->props_weight;
            }
            
            if ($detail->on_time_delivery == 1) {
                $onTimeDeliveryCount++;
            }
        }
        
        return [
            'onTimeDeliveryCount' => $onTimeDeliveryCount,
            'props_weight_total' => $props_weight_total
        ];
    }

    /**
     * Calculate route delivery averages
     *
     * @param Collection $routes
     * @return Collection
     */
    private function calculateRouteDeliveryAverages(Collection $routes): Collection
    {
        return $routes->map(function ($route) {
            $totalDeliveries = $route->shipmentDeliveries->count();
            $route->on_time_delivery_avg = $totalDeliveries > 0
                ? ($route->shipmentDeliveries->where('on_time_delivery', 1)->count() / $totalDeliveries) * 100
                : 0;
            return $route;
        });
    }

    /**
     * Get cache key for route
     *
     * @param int $id
     * @return string
     */
    private function getCacheKey(int $id): string
    {
        return self::CACHE_KEY_PREFIX . $id;
    }

    /**
     * Clear route cache
     *
     * @param int|null $id
     * @return void
     */
    private function clearRouteCache(?int $id = null): void
    {
        if ($id) {
            $this->cacheService->forget($this->getCacheKey($id));
        }
        $this->cacheService->tags([self::CACHE_TAG])->flush();
    }

    /**
     * Log error with context
     *
     * @param string $method
     * @param Throwable $exception
     * @return void
     */
    private function logError(string $method, Throwable $exception): void
    {
        Log::error("Error in RouteService@{$method}: " . $exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 