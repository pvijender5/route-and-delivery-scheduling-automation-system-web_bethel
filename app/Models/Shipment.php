<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ShipmentDeliveryDetail;
use App\Models\ShipmentCustomerDeliveryLocations;
use App\Models\CustomerLocation;
use App\Models\MerchandiserAssignedLocation;
use App\Models\User;

class Shipment extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'shipments';

    protected $fillable = [
        'id',
        'name',
        'route_Date',
        'order_id',
        'order_type',
        'warehouse_id',
        'customer_id',
        'vehicle_types',
        'labels',
        'deduplication_id',
        'consolidation_id',
        'props_priority',
        'props_size',
        'props_weight',
        'props_delivery_service_time',
        'props_notes',
        'props_delivery_window',
        'fork_lift',
        'shipment_status',
        'status',
        'updated_at'
    ];

    protected $casts = [
        'vehicle_types' => 'json',
        'labels' => 'json',
        'props_notes' => 'json',
        'props_delivery_window' => 'json',
        'fork_lift' => 'boolean',
        'route_Date' => 'datetime',
        'props_priority' => 'integer',
        'props_weight' => 'float',
        'props_size' => 'json'
    ];

    /**
     * Get the warehouse associated with the shipment.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the customer associated with the shipment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the delivery location for the shipment.
     */
    public function shipmentCustomerDeliveryLocations(): HasOne
    {
        return $this->hasOne(ShipmentCustomerDeliveryLocations::class, 'shipment_id');
    }

    /**
     * Get the shipment properties.
     */
    public function shipmentProps(): HasMany
    {
        return $this->hasMany(ShipmentPropsDeliveryInventory::class, 'shipment_id');
    }

    /**
     * Get the driver details for the shipment.
     */
    public function driverDetail(): BelongsTo
    {
        return $this->belongsTo(Driverdetail::class, 'customer_id', 'user_id');
    }

    /**
     * Get the activity logs for the shipment.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'shipment_id');
    }

    /**
     * Get the latest delivery details for the shipment.
     */
    public function shipmentDeliveries(): HasOne
    {
        return $this->hasOne(ShipmentDeliveryDetail::class, 'shipment_id')->latestOfMany();
    }

    /**
     * Get the FHF notes for the shipment.
     */
    public function fhfNotes(): HasMany
    {
        return $this->hasMany(CustomerLocationsFhfNotes::class, 'shipment_id', 'id');
    }

    /**
     * Get the user who deleted the shipment.
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get all shipments with their delivery details.
     */
    public static function getAllWithDeliveries(): Collection
    {
        return self::with(['shipmentDeliveries'])->get();
    }

    /**
     * Get all shipments with essential relationships.
     */
    public static function getAllWithRelations(): Collection
    {
        return self::with([
            'warehouse',
            'customer',
            'shipmentProps',
            'shipmentCustomerDeliveryLocations'
        ])->get();
    }

    /**
     * Update the notes of a shipment.
     */
    public function updateNotes(string $newText): void
    {
        $notes = $this->props_notes ?? [];
        $notes[0]['text'] = $newText;
        $this->props_notes = $notes;
        $this->save();
    }

    /**
     * Get the query builder instance for datatable with eager loaded relationships.
     */
    public static function getDatatableQuery(?string $shipmentStatus = null): Builder
    {
        $query = self::query()->with([
            'shipmentCustomerDeliveryLocations',
            'warehouse',
            'shipmentDeliveries',
            'shipmentProps'
        ]);

        if ($shipmentStatus !== null) {
            $query->whereJsonDoesntContain('labels', $shipmentStatus);
        }

        return $query;
    }

    /**
     * Get the query builder instance for pickup orders.
     */
    public static function getPickupOrdersQuery(): Builder
    {
        return self::with([
            'customer',
            'shipmentCustomerDeliveryLocations',
            'shipmentDeliveries',
            'warehouse'
        ])->whereJsonContains('labels', 'PICKUP');
    }

    /**
     * Fetch shipments by status and location.
     */
    public static function getByStatusAndLocation(string $shipmentStatus, int $customerId): Collection
    {
        return self::with([
            'customer',
            'shipmentCustomerDeliveryLocations',
            'shipmentDeliveries',
            'warehouse'
        ])
        ->whereJsonContains('labels', $shipmentStatus)
        ->where('customer_id', $customerId)
        ->get();
    }

    /**
     * Get shipment by ID with relationships.
     */
    public static function getByIdWithRelations(int $shipmentId): ?self
    {
        return self::with([
            'warehouse',
            'customer',
            'shipmentProps',
            'shipmentDeliveries',
            'shipmentCustomerDeliveryLocations'
        ])
        ->where('id', $shipmentId)
        ->withTrashed()
        ->first();
    }

    /**
     * Get delivery details by shipment ID.
     */
    public static function getDeliveryDetails(int $shipmentId): ?ShipmentDeliveryDetail
    {
        return ShipmentDeliveryDetail::with([
            'deliveryProofs',
            'routes.truck',
            'routes.driver',
            'shipment'
        ])
        ->where('shipment_id', $shipmentId)
        ->first();
    }

    /**
     * Get activity log by shipment ID.
     */
    public function getActivityLog(): ?self
    {
        return $this->withTrashed(['activityLogs.user'])
            ->where('id', $this->id)
            ->first();
    }

    /**
     * Get inventory details by shipment ID.
     */
    public function getInventoryDetails(): ?self
    {
        return $this->with(['shipmentProps'])
            ->where('id', $this->id)
            ->first();
    }

    /**
     * Search shipments by name.
     */
    public static function searchByName(string $search): Collection
    {
        return self::select('id', 'name')
            ->where('name', 'LIKE', "%{$search}%")
            ->get();
    }

    /**
     * Get merchandiser notification data.
     */
    public static function getMerchandiserNotificationData(int $shipmentId): array
    {
        return ShipmentCustomerDeliveryLocations::where('shipment_id', $shipmentId)
            ->pluck('delivery_location_name_original')
            ->toArray();
    }
}

