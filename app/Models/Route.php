<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\Roles;
use Carbon\Carbon;

class Route extends Model
{
    use SoftDeletes;

    protected $table = 'routes';

    protected $fillable = [
        'delivery_date',
        'total_stops',
        'origin_points',
        'last_dropoff_location',
        'total_quantity',
        'planned_departure_time',
        'fork_lift',
        'assigned_driver',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'planned_departure_time' => 'datetime',
        'fork_lift' => 'boolean'
    ];

    /**
     * Get the driver associated with the route
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver');
    }

    /**
     * Get the shipment deliveries for the route
     */
    public function shipmentDeliveries(): HasMany
    {
        return $this->hasMany(ShipmentDeliveryDetail::class, 'route_id');
    }

    /**
     * Get routes data filtered by status and other criteria
     *
     * @param array $statusArray
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRoutesData(array $statusArray, array $filters = [])
    {
        $query = self::with([
            'driver',
            'shipmentDeliveries.shipment.customer',
            'shipmentDeliveries.shipment.warehouse',
            'shipmentDeliveries.shipment.shipmentCustomerDeliveryLocations'
        ])
        ->whereIn('status', $statusArray);

        if (!empty($filters['delivery_date'])) {
            $date = Carbon::parse($filters['delivery_date']);
            $query->whereDate('delivery_date', $date);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->whereHas('shipmentDeliveries.shipment', function ($q) use ($filters) {
                $q->where('warehouse_id', $filters['warehouse_id']);
            });
        }

        if (!empty($filters['assigned_driver'])) {
            $query->where('assigned_driver', $filters['assigned_driver']);
        }

        return $query->get();
    }

    /**
     * Insert new routes from JSON data
     *
     * @param array $jsonData
     * @return array
     */
    public static function insertRoutes(array $jsonData): array
    {
        $routeIds = [];
        
        // Implementation of route insertion logic
        // This should handle the creation of routes and related records
        
        return $routeIds;
    }
} 