<?php

namespace App\Models\Queries;

use App\DTOs\ShipmentFilterData;
use Illuminate\Database\Eloquent\Builder;

class ShipmentQuery extends BaseQuery
{
    private ?ShipmentFilterData $filters;

    public function __construct(Builder $query, ?ShipmentFilterData $filters = null)
    {
        parent::__construct($query);
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        $this->applyStatusFilter()
            ->applyCustomerFilter()
            ->applyRouteFilter()
            ->applyDateRangeFilter()
            ->applyTrackingNumberFilter()
            ->applyDeliveryStatusFilter()
            ->applyPriorityFilter();

        return $this->query;
    }

    private function applyStatusFilter(): self
    {
        if ($this->filters?->status) {
            $this->query->where('status', $this->filters->status);
        }
        return $this;
    }

    private function applyCustomerFilter(): self
    {
        if ($this->filters?->customer_id) {
            $this->query->where('customer_id', $this->filters->customer_id);
        }

        if ($this->filters?->customer_name) {
            $this->query->whereHas('customer', function ($query) {
                $query->where('name', 'like', "%{$this->filters->customer_name}%");
            });
        }
        return $this;
    }

    private function applyRouteFilter(): self
    {
        if ($this->filters?->route_id) {
            $this->query->where('route_id', $this->filters->route_id);
        }

        if ($this->filters?->route_number) {
            $this->query->whereHas('route', function ($query) {
                $query->where('route_number', 'like', "%{$this->filters->route_number}%");
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

    private function applyTrackingNumberFilter(): self
    {
        if ($this->filters?->tracking_number) {
            $this->query->where('tracking_number', 'like', "%{$this->filters->tracking_number}%");
        }
        return $this;
    }

    private function applyDeliveryStatusFilter(): self
    {
        if ($this->filters?->delivery_status) {
            $this->query->where('delivery_status', $this->filters->delivery_status);
        }
        return $this;
    }

    private function applyPriorityFilter(): self
    {
        if (isset($this->filters?->is_priority)) {
            $this->query->where('is_priority', $this->filters->is_priority);
        }
        return $this;
    }

    /**
     * Apply common eager loading relationships for shipments
     */
    public function withCommonRelations(): self
    {
        $this->query->with([
            'customer',
            'route',
            'deliveryDetails',
            'deliveryProofs',
            'statusLogs'
        ]);
        return $this;
    }
} 