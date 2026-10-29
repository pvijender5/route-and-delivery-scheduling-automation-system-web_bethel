<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerLocation;
use App\DTOs\CustomerData;
use App\DTOs\CustomerFilterData;
use App\DTOs\CustomerLocationData;
use App\Exceptions\CustomerException;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Services\Interfaces\CacheServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_PREFIX = 'customer:';
    private const CACHE_TAG = 'customers';

    private CustomerRepositoryInterface $customerRepository;
    private CacheServiceInterface $cacheService;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CacheServiceInterface $cacheService
    ) {
        $this->customerRepository = $customerRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Get customers query for datatables
     *
     * @param CustomerFilterData $filters
     * @return Builder
     * @throws CustomerException
     */
    public function getCustomersQuery(CustomerFilterData $filters): Builder
    {
        try {
            $query = $this->customerRepository->getBaseQuery();
            $this->applyFilters($query, $filters);
            return $query;
        } catch (Throwable $e) {
            $this->logError('getCustomersQuery', $e);
            throw new CustomerException('Failed to get customers query', 0, $e);
        }
    }

    /**
     * Create a new customer
     *
     * @param CustomerData $data
     * @return Customer
     * @throws CustomerException
     */
    public function createCustomer(CustomerData $data): Customer
    {
        try {
            DB::beginTransaction();

            $customer = $this->customerRepository->create($data->toArray());

            if ($data->location) {
                $this->customerRepository->createLocation($customer->id, $data->location->toArray());
            }

            DB::commit();
            $this->clearCustomerCache();

            return $customer->fresh(['locations']);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('createCustomer', $e);
            throw new CustomerException('Failed to create customer', 0, $e);
        }
    }

    /**
     * Update customer status
     *
     * @param int $id
     * @param string $status
     * @return Customer
     * @throws CustomerException
     */
    public function updateStatus(int $id, string $status): Customer
    {
        try {
            $customer = $this->customerRepository->updateStatus($id, $status);
            $this->clearCustomerCache($id);
            return $customer;
        } catch (Throwable $e) {
            $this->logError('updateStatus', $e);
            throw new CustomerException('Failed to update customer status', 0, $e);
        }
    }

    /**
     * Get customer details
     *
     * @param int $id
     * @return Customer
     * @throws CustomerException
     */
    public function getCustomerDetails(int $id): Customer
    {
        try {
            $cacheKey = $this->getCacheKey($id);
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => $this->customerRepository->findWithRelations($id)
            );
        } catch (Throwable $e) {
            $this->logError('getCustomerDetails', $e);
            throw new CustomerException('Failed to get customer details', 0, $e);
        }
    }

    /**
     * Update customer details
     *
     * @param int $id
     * @param CustomerData $data
     * @return Customer
     * @throws CustomerException
     */
    public function updateCustomer(int $id, CustomerData $data): Customer
    {
        try {
            DB::beginTransaction();

            $customer = $this->customerRepository->update($id, $data->toArray());
            
            DB::commit();
            $this->clearCustomerCache($id);

            return $customer->fresh(['locations']);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('updateCustomer', $e);
            throw new CustomerException('Failed to update customer', 0, $e);
        }
    }

    /**
     * Delete customer
     *
     * @param int $id
     * @return bool
     * @throws CustomerException
     */
    public function deleteCustomer(int $id): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->customerRepository->delete($id);
            
            DB::commit();
            $this->clearCustomerCache($id);

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('deleteCustomer', $e);
            throw new CustomerException('Failed to delete customer', 0, $e);
        }
    }

    /**
     * Get customer locations
     *
     * @param int $id
     * @return Collection
     * @throws CustomerException
     */
    public function getCustomerLocations(int $id): Collection
    {
        try {
            $cacheKey = "customer:{$id}:locations";
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => $this->customerRepository->getLocations($id)
            );
        } catch (Throwable $e) {
            $this->logError('getCustomerLocations', $e);
            throw new CustomerException('Failed to get customer locations', 0, $e);
        }
    }

    /**
     * Add customer location
     *
     * @param int $customerId
     * @param CustomerLocationData $data
     * @return CustomerLocation
     * @throws CustomerException
     */
    public function addCustomerLocation(int $customerId, CustomerLocationData $data): CustomerLocation
    {
        try {
            DB::beginTransaction();

            $location = $this->customerRepository->createLocation($customerId, $data->toArray());
            
            DB::commit();
            $this->clearCustomerCache($customerId);

            return $location;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('addCustomerLocation', $e);
            throw new CustomerException('Failed to add customer location', 0, $e);
        }
    }

    /**
     * Update customer location
     *
     * @param int $customerId
     * @param int $locationId
     * @param CustomerLocationData $data
     * @return CustomerLocation
     * @throws CustomerException
     */
    public function updateCustomerLocation(int $customerId, int $locationId, CustomerLocationData $data): CustomerLocation
    {
        try {
            DB::beginTransaction();

            $location = $this->customerRepository->updateLocation($customerId, $locationId, $data->toArray());
            
            DB::commit();
            $this->clearCustomerCache($customerId);

            return $location;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('updateCustomerLocation', $e);
            throw new CustomerException('Failed to update customer location', 0, $e);
        }
    }

    /**
     * Delete customer location
     *
     * @param int $customerId
     * @param int $locationId
     * @return bool
     * @throws CustomerException
     */
    public function deleteCustomerLocation(int $customerId, int $locationId): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->customerRepository->deleteLocation($customerId, $locationId);
            
            DB::commit();
            $this->clearCustomerCache($customerId);

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('deleteCustomerLocation', $e);
            throw new CustomerException('Failed to delete customer location', 0, $e);
        }
    }

    /**
     * Apply filters to the query
     *
     * @param Builder $query
     * @param CustomerFilterData $filters
     * @return void
     */
    private function applyFilters(Builder $query, CustomerFilterData $filters): void
    {
        if ($filters->search) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters->search}%")
                    ->orWhere('customer_id', 'LIKE', "%{$filters->search}%")
                    ->orWhere('email', 'LIKE', "%{$filters->search}%")
                    ->orWhere('phone', 'LIKE', "%{$filters->search}%");
            });
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        $query->orderBy($filters->sortBy ?? 'id', $filters->sortOrder ?? 'desc');
    }

    /**
     * Get cache key for customer
     *
     * @param int $id
     * @return string
     */
    private function getCacheKey(int $id): string
    {
        return self::CACHE_KEY_PREFIX . $id;
    }

    /**
     * Clear customer cache
     *
     * @param int|null $id
     * @return void
     */
    private function clearCustomerCache(?int $id = null): void
    {
        if ($id) {
            $this->cacheService->forget($this->getCacheKey($id));
            $this->cacheService->forget("customer:{$id}:locations");
        }
        $this->cacheService->tags([self::CACHE_TAG])->flush();
    }

    /**
     * Log error with context
     *
     * @param string $method
     * @param Throwable $exception
     * @return void
     */
    private function logError(string $method, Throwable $exception): void
    {
        Log::error("Error in CustomerService@{$method}: " . $exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString()
        ]);
    }
}