<?php

namespace App\Models\Queries\Admin;

use App\DTOs\Admin\UserFilterData;
use App\Models\Queries\BaseQuery;
use App\Models\Roles;
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
        $this->applyRoleFilter()
            ->applyStatusFilter()
            ->applySearchFilter()
            ->applyCarrierOwnerFilter()
            ->applyDateRangeFilter()
            ->applyPermissionFilter()
            ->applyCompanyFilter()
            ->applySortOrder();

        return $this->query;
    }

    private function applyRoleFilter(): self
    {
        if ($this->filters?->role_id) {
            if ($this->filters->role_id == Roles::CARRIEROWNER_ID) {
                $this->query->whereIn('role_id', [Roles::CARRIEROWNER_ID, Roles::SUBDRIVER_ID]);
            } else {
                $this->query->where('role_id', $this->filters->role_id);
            }
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

    private function applySearchFilter(): self
    {
        if ($this->filters?->search) {
            $this->query->where(function ($query) {
                $query->where('name', 'like', "%{$this->filters->search}%")
                    ->orWhere('email', 'like', "%{$this->filters->search}%")
                    ->orWhere('phone', 'like', "%{$this->filters->search}%");
            });
        }
        return $this;
    }

    private function applyCarrierOwnerFilter(): self
    {
        if ($this->filters?->carrier_owner_id) {
            $this->query->where('carrier_owner_id', $this->filters->carrier_owner_id);
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

    private function applyPermissionFilter(): self
    {
        if ($this->filters?->permission) {
            $this->query->whereHas('permissions', function ($query) {
                $query->where('name', $this->filters->permission);
            });
        }
        return $this;
    }

    private function applyCompanyFilter(): self
    {
        if ($this->filters?->company_id) {
            $this->query->where('company_id', $this->filters->company_id);
        }
        return $this;
    }

    private function applySortOrder(): self
    {
        if ($this->filters?->current_user_id) {
            $this->query->orderByRaw("id = ? DESC", [$this->filters->current_user_id]);
        } else {
            $this->query->orderBy($this->filters?->sort_by ?? 'created_at', $this->filters?->sort_direction ?? 'desc');
        }
        return $this;
    }

    /**
     * Include common relationships for admin user listing
     */
    public function withAdminRelations(): self
    {
        $this->query->with([
            'role',
            'permissions',
            'company',
            'carrierOwner',
            'documents',
            'settings'
        ]);
        return $this;
    }

    /**
     * Include carrier owner specific relationships
     */
    public function withCarrierOwnerRelations(): self
    {
        $this->query->with([
            'subDrivers',
            'vehicles',
            'insuranceDocuments',
            'contracts'
        ]);
        return $this;
    }

    /**
     * Include user activity metrics
     */
    public function withActivityMetrics(): self
    {
        $this->query->withCount([
            'logins',
            'activities',
            'documents'
        ])->withMax('logins', 'created_at as last_login');
        return $this;
    }
} 