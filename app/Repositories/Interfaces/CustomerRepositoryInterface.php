<?php

namespace App\Repositories\Interfaces;

use App\Models\Customer;
use App\Models\CustomerLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    /**
     * Get base query for customers
     *
     * @return Builder
     */
    public function getBaseQuery(): Builder;

    /**
     * Find customer with relations
     *
     * @param int $id
     * @return Customer
     */
    public function findWithRelations(int $id): Customer;

    /**
     * Create new customer
     *
     * @param array $data
     * @return Customer
     */
    public function create(array $data): Customer;

    /**
     * Update customer
     *
     * @param int $id
     * @param array $data
     * @return Customer
     */
    public function update(int $id, array $data): Customer;

    /**
     * Update customer status
     *
     * @param int $id
     * @param string $status
     * @return Customer
     */
    public function updateStatus(int $id, string $status): Customer;

    /**
     * Delete customer
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get customer locations
     *
     * @param int $customerId
     * @return Collection
     */
    public function getLocations(int $customerId): Collection;

    /**
     * Create customer location
     *
     * @param int $customerId
     * @param array $data
     * @return CustomerLocation
     */
    public function createLocation(int $customerId, array $data): CustomerLocation;

    /**
     * Update customer location
     *
     * @param int $customerId
     * @param int $locationId
     * @param array $data
     * @return CustomerLocation
     */
    public function updateLocation(int $customerId, int $locationId, array $data): CustomerLocation;

    /**
     * Delete customer location
     *
     * @param int $customerId
     * @param int $locationId
     * @return bool
     */
    public function deleteLocation(int $customerId, int $locationId): bool;
} 