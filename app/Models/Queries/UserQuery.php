<?php

namespace App\Models\Queries;

use App\DTOs\UserFilterData;
use Illuminate\Database\Eloquent\Builder;

class UserQuery extends BaseQuery
{
    private ?UserFilterData $filters;

    public function __construct(Builder $query, ?UserFilterData $filters = null)
    {
        parent::__construct($query);
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        $this->applyNameFilter()
            ->applyEmailFilter()
            ->applyRoleFilter()
            ->applyStatusFilter()
            ->applyDateRangeFilter()
            ->applyDriverFilter()
            ->applyMerchandiserFilter();

        return $this->query;
    }

    private function applyNameFilter(): self
    {
        if ($this->filters?->name) {
            $this->query->where(function ($query) {
                $query->where('first_name', 'like', "%{$this->filters->name}%")
                    ->orWhere('last_name', 'like', "%{$this->filters->name}%");
            });
        }
        return $this;
    }

    private function applyEmailFilter(): self
    {
        if ($this->filters?->email) {
            $this->query->where('email', 'like', "%{$this->filters->email}%");
        }
        return $this;
    }

    private function applyRoleFilter(): self
    {
        if ($this->filters?->role) {
            $this->query->whereHas('roles', function ($query) {
                $query->where('name', $this->filters->role);
            });
        }
        return $this;
    }

    private function applyStatusFilter(): self
    {
        if (isset($this->filters?->is_active)) {
            $this->query->where('is_active', $this->filters->is_active);
        }
        return $this;
    }

    private function applyDateRangeFilter(): self
    {
        if ($this->filters?->start_date) {
            $this->query->where('created_at', '>=', $this->filters->start_date);
        }

        if ($this->filters?->end_date) {
            $this->query->where('created_at', '<=', $this->filters->end_date);
        }
        return $this;
    }

    private function applyDriverFilter(): self
    {
        if (isset($this->filters?->is_driver)) {
            $this->query->whereHas('roles', function ($query) {
                $query->where('name', 'driver');
            });
        }
        return $this;
    }

    private function applyMerchandiserFilter(): self
    {
        if (isset($this->filters?->is_merchandiser)) {
            $this->query->whereHas('roles', function ($query) {
                $query->where('name', 'merchandiser');
            });
        }
        return $this;
    }

    /**
     * Include driver performance metrics
     */
    public function withDriverMetrics(): self
    {
        $this->query->withCount(['completedRoutes', 'completedShipments'])
            ->withSum('completedShipments', 'distance')
            ->withAvg('completedShipments', 'on_time_delivery_rate');
        return $this;
    }

    /**
     * Include merchandiser performance metrics
     */
    public function withMerchandiserMetrics(): self
    {
        $this->query->withCount(['assignedLocations', 'completedVisits'])
            ->withAvg('completedVisits', 'visit_duration');
        return $this;
    }

    /**
     * Apply common eager loading relationships for users
     */
    public function withCommonRelations(): self
    {
        $this->query->with([
            'roles',
            'permissions',
            'profile',
            'settings'
        ]);
        return $this;
    }
} 