<?php

namespace App\Http\Controllers\Api;

use App\DTOs\RouteFilterData;
use App\Exceptions\RouteException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RouteRequest;
use App\Http\Resources\RouteResource;
use App\Models\Queries\RouteQuery;
use App\Services\Interfaces\RouteServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Routes",
 *     description="API Endpoints for route management"
 * )
 */
class RouteController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RouteServiceInterface $routeService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/routes",
     *     summary="Get all routes",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="route_number",
     *         in="query",
     *         description="Filter by route number"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status"
     *     ),
     *     @OA\Parameter(
     *         name="driver_id",
     *         in="query",
     *         description="Filter by driver ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of routes retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = RouteFilterData::fromRequest($request);
            $routes = $this->routeService->getAllRoutes($filters);

            return $this->collectionResponse(
                RouteResource::collection($routes),
                'Routes retrieved successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/routes/{id}",
     *     summary="Get route by ID",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $route = $this->routeService->getRouteById($id);
            return $this->resourceResponse(
                new RouteResource($route),
                'Route retrieved successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/routes",
     *     summary="Create a new route",
     *     tags={"Routes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Route data"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Route created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(RouteRequest $request): JsonResponse
    {
        try {
            $route = $this->routeService->createRoute($request->validated());

            return $this->resourceResponse(
                new RouteResource($route),
                'Route created successfully',
                201
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/routes/{id}",
     *     summary="Update an existing route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Route data"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(RouteRequest $request, int $id): JsonResponse
    {
        try {
            $route = $this->routeService->updateRoute($id, $request->validated());

            return $this->resourceResponse(
                new RouteResource($route),
                'Route updated successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/routes/{id}",
     *     summary="Delete a route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->routeService->deleteRoute($id);
            return $this->successResponse(
                null,
                'Route deleted successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/routes/{id}/shipments",
     *     summary="Get route shipments",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route shipments retrieved successfully"
     *     )
     * )
     */
    public function getRouteShipments(int $id): JsonResponse
    {
        try {
            $shipments = $this->routeService->getRouteShipments($id);
            return $this->collectionResponse(
                ShipmentResource::collection($shipments),
                'Route shipments retrieved successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/routes/{id}/inventory",
     *     summary="Get route inventory details",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route inventory details retrieved successfully"
     *     )
     * )
     */
    public function getRouteInventory(int $id): JsonResponse
    {
        try {
            $inventory = $this->routeService->getRouteInventory($id);
            return $this->successResponse(
                $inventory,
                'Route inventory details retrieved successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/routes/{id}/complete",
     *     summary="Mark route as completed",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route marked as completed successfully"
     *     )
     * )
     */
    public function completeRoute(int $id): JsonResponse
    {
        try {
            $route = $this->routeService->completeRoute($id);
            return $this->resourceResponse(
                new RouteResource($route),
                'Route marked as completed successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/routes/{id}/cancel",
     *     summary="Cancel route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route cancelled successfully"
     *     )
     * )
     */
    public function cancelRoute(int $id): JsonResponse
    {
        try {
            $route = $this->routeService->cancelRoute($id);
            return $this->resourceResponse(
                new RouteResource($route),
                'Route cancelled successfully'
            );
        } catch (RouteException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}