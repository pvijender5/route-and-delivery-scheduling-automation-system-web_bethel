<?php

namespace App\Models\Queries;

use App\DTOs\RouteFilterData;
use Illuminate\Database\Eloquent\Builder;

class RouteQuery extends BaseQuery
{
    private ?RouteFilterData $filters;

    public function __construct(Builder $query, ?RouteFilterData $filters = null)
    {
        parent::__construct($query);
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        $this->applyRouteNumberFilter()
            ->applyStatusFilter()
            ->applyDriverFilter()
            ->applyDateRangeFilter()
            ->applyVehicleFilter()
            ->applyCustomerFilter()
            ->applyLocationFilter()
            ->applyCompletionStatusFilter();

        return $this->query;
    }

    private function applyRouteNumberFilter(): self
    {
        if ($this->filters?->route_number) {
            $this->query->where('route_number', 'like', "%{$this->filters->route_number}%");
        }
        return $this;
    }

    private function applyStatusFilter(): self
    {
        if ($this->filters?->status) {
            $this->query->where('status', $this->filters->status);
        }
        return $this;
    }

    private function applyDriverFilter(): self
    {
        if ($this->filters?->driver_id) {
            $this->query->where('driver_id', $this->filters->driver_id);
        }

        if ($this->filters?->driver_name) {
            $this->query->whereHas('driver', function ($query) {
                $query->where('name', 'like', "%{$this->filters->driver_name}%");
            });
        }
        return $this;
    }

    private function applyDateRangeFilter(): self
    {
        if ($this->filters?->start_date) {
            $this->query->where('delivery_date', '>=', $this->filters->start_date);
        }

        if ($this->filters?->end_date) {
            $this->query->where('delivery_date', '<=', $this->filters->end_date);
        }
        return $this;
    }

    private function applyVehicleFilter(): self
    {
        if ($this->filters?->vehicle_id) {
            $this->query->where('vehicle_id', $this->filters->vehicle_id);
        }

        if ($this->filters?->vehicle_number) {
            $this->query->whereHas('vehicle', function ($query) {
                $query->where('vehicle_number', 'like', "%{$this->filters->vehicle_number}%");
            });
        }
        return $this;
    }

    private function applyCustomerFilter(): self
    {
        if ($this->filters?->customer_id) {
            $this->query->whereHas('shipments', function ($query) {
                $query->where('customer_id', $this->filters->customer_id);
            });
        }
        return $this;
    }

    private function applyLocationFilter(): self
    {
        if ($this->filters?->location) {
            $this->query->whereHas('shipments.deliveryLocation', function ($query) {
                $query->where('address', 'like', "%{$this->filters->location}%")
                    ->orWhere('city', 'like', "%{$this->filters->location}%")
                    ->orWhere('state', 'like', "%{$this->filters->location}%");
            });
        }
        return $this;
    }

    private function applyCompletionStatusFilter(): self
    {
        if (isset($this->filters?->is_completed)) {
            $this->query->where('is_completed', $this->filters->is_completed);
        }
        return $this;
    }

    /**
     * Apply common eager loading relationships for routes
     */
    public function withCommonRelations(): self
    {
        $this->query->with([
            'driver',
            'vehicle',
            'shipments.customer',
            'shipments.deliveryLocation',
            'shipments.deliveryDetails',
            'routePauseLogs'
        ]);
        return $this;
    }

    /**
     * Include shipment inventory details
     */
    public function withInventoryDetails(): self
    {
        $this->query->with(['shipments.inventoryDetails']);
        return $this;
    }

    /**
     * Include only active shipments
     */
    public function withActiveShipments(): self
    {
        $this->query->whereHas('shipments', function ($query) {
            $query->where('status', 'active');
        });
        return $this;
    }
} 