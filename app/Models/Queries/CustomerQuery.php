<?php

namespace App\Models\Queries;

use App\DTOs\CustomerFilterData;
use Illuminate\Database\Eloquent\Builder;

class CustomerQuery extends BaseQuery
{
    private ?CustomerFilterData $filters;

    public function __construct(Builder $query, ?CustomerFilterData $filters = null)
    {
        parent::__construct($query);
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        $this->applyNameFilter()
            ->applyEmailFilter()
            ->applyPhoneFilter()
            ->applyLocationFilter()
            ->applyActiveFilter()
            ->applyDateRangeFilter();

        return $this->query;
    }

    private function applyNameFilter(): self
    {
        if ($this->filters?->name) {
            $this->query->where('name', 'like', "%{$this->filters->name}%");
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

    private function applyPhoneFilter(): self
    {
        if ($this->filters?->phone) {
            $this->query->where('phone', 'like', "%{$this->filters->phone}%");
        }
        return $this;
    }

    private function applyLocationFilter(): self
    {
        if ($this->filters?->location) {
            $this->query->whereHas('locations', function ($query) {
                $query->where('address', 'like', "%{$this->filters->location}%")
                    ->orWhere('city', 'like', "%{$this->filters->location}%")
                    ->orWhere('state', 'like', "%{$this->filters->location}%");
            });
        }
        return $this;
    }

    private function applyActiveFilter(): self
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
} 