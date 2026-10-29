<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RouteService;
use Illuminate\Http\Request;
use App\Models\Routes;
use App\Models\Truck;
use App\Models\Warehouse;
use App\Constants\Roles;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class RoutesController extends Controller
{
    protected RouteService $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    /**
     * Show Routing on map path in plan route
     */
    public function map(Request $request): View
    {
        try {
            $trucks = Truck::all();
            $wherehouse = Warehouse::all();
            $routes = $this->routeService->getFilteredRoutes([], [Roles::PLANROUTESTATUS]);
            
            return view('routes.map', compact('routes', 'wherehouse', 'trucks', 'track'));
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@map: ' . $e->getMessage());
            return view('routes.map')->with('error', 'Unable to load map data');
        }
    }

    /**
     * Show Routing on map path in track route
     */
    public function trackRoute(Request $request): View
    {
        try {
            $trucks = Truck::all();
            $wherehouse = Warehouse::all();
            $routes = $this->routeService->getFilteredRoutes([], Roles::TRACKROUTESTATUS);
            $track = 1;
            
            return view('routes.track', compact('routes', 'wherehouse', 'trucks', 'track'));
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@trackRoute: ' . $e->getMessage());
            return view('routes.track')->with('error', 'Unable to load track data');
        }
    }

    /**
     * Filter routes by status, date, warehouse etc.
     */
    public function mapFilter(Request $request): JsonResponse
    {
        try {
            $routes = $this->routeService->getFilteredRoutes($request->all(), [Roles::PLANROUTESTATUS]);
            return response()->json(['routes' => $routes]);
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@mapFilter: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to filter routes'], 500);
        }
    }

    /**
     * Filter track routes by status, date, warehouse etc.
     */
    public function trackRouteFilter(Request $request): JsonResponse
    {
        try {
            $routes = $this->routeService->getFilteredRoutes($request->all(), Roles::TRACKROUTESTATUS_ARRAY);
            return response()->json(['routes' => $routes]);
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@trackRouteFilter: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to filter track routes'], 500);
        }
    }

    /**
     * Create routes from python app response
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $jsonData = $request->json()->all();
            $routeIds = $this->routeService->createRoutes($jsonData);
            
            return response()->json([
                'message' => 'Routes created successfully',
                'route_ids' => $routeIds
            ]);
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@create: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to create routes'], 500);
        }
    }

    /**
     * Get Route data by route id
     */
    public function getRouteById(Request $request, int $id): JsonResponse
    {
        try {
            $routeData = $this->routeService->getRouteDetails($id);
            return response()->json(['routedata' => $routeData]);
        } catch (Throwable $e) {
            Log::error('Error in RoutesController@getRouteById: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch route details'], 500);
        }
    }

    // ... Add other methods following the same pattern
}    