<?php

namespace App\Repositories;

use App\DTOs\CustomerFilterData;
use App\Models\Customer;
use App\Models\Queries\CustomerQuery;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(
        private readonly Customer $model
    ) {}

    public function findById(int $id): ?Customer
    {
        return $this->model->with(['locations', 'contacts'])->find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function update(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function getAll(CustomerFilterData $filters): Collection
    {
        $query = new CustomerQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->with(['locations', 'contacts'])
            ->get();
    }

    public function getPaginated(CustomerFilterData $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = new CustomerQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->with(['locations', 'contacts'])
            ->paginate($perPage);
    }

    public function getActive(): Collection
    {
        $filters = new CustomerFilterData(is_active: true);
        $query = new CustomerQuery($this->model->newQuery(), $filters);
        return $query->apply()
            ->with(['locations', 'contacts'])
            ->get();
    }
} 