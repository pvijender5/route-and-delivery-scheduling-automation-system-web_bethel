<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\DTOs\ProductData;
use App\Exceptions\ProductException;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\CacheServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service class for handling product-related business logic
 */
class ProductService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_PREFIX = 'product:';
    private const CACHE_TAG = 'products';

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CacheServiceInterface $cacheService
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CacheServiceInterface $cacheService
    ) {
        $this->productRepository = $productRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Get products query for datatables with eager loading and filters
     *
     * @param array<string, mixed> $params
     * @return Builder
     * @throws ProductException
     */
    public function getProductsQuery(array $params): Builder
    {
        try {
            $query = $this->productRepository->getBaseQuery();

            $this->applyFilters($query, $params);
            $this->applyEagerLoading($query);

            return $query;
        } catch (Throwable $e) {
            $this->logError('getProductsQuery', $e);
            throw new ProductException('Failed to get products query', 0, $e);
        }
    }

    /**
     * Create a new product with validation and transaction
     *
     * @param array<string, mixed> $data
     * @return Product
     * @throws ProductException
     */
    public function createProduct(array $data): Product
    {
        try {
            DB::beginTransaction();

            $productData = new ProductData($data);
            $this->validateProductData($productData);

            $product = $this->productRepository->create($productData->toArray());
            
            DB::commit();
            $this->clearProductCache();
            
            return $product;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('createProduct', $e);
            throw new ProductException('Failed to create product', 0, $e);
        }
    }

    /**
     * Get product details with caching and relationships
     *
     * @param int $id
     * @return Product
     * @throws ProductException
     */
    public function getProductDetails(int $id): Product
    {
        try {
            $cacheKey = $this->getCacheKey($id);
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => $this->productRepository->findWithRelations($id)
            );
        } catch (Throwable $e) {
            $this->logError('getProductDetails', $e);
            throw new ProductException('Failed to get product details', 0, $e);
        }
    }

    /**
     * Update product with validation and transaction
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product
     * @throws ProductException
     */
    public function updateProduct(int $id, array $data): Product
    {
        try {
            DB::beginTransaction();

            $productData = new ProductData($data);
            $this->validateProductData($productData);

            $product = $this->productRepository->update($id, $productData->toArray());
            
            DB::commit();
            $this->clearProductCache($id);
            
            return $product;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('updateProduct', $e);
            throw new ProductException('Failed to update product', 0, $e);
        }
    }

    /**
     * Delete product with transaction
     *
     * @param int $id
     * @return bool
     * @throws ProductException
     */
    public function deleteProduct(int $id): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->productRepository->delete($id);
            
            DB::commit();
            $this->clearProductCache($id);
            
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('deleteProduct', $e);
            throw new ProductException('Failed to delete product', 0, $e);
        }
    }

    /**
     * Get products by category with caching
     *
     * @param string $category
     * @return Collection
     * @throws ProductException
     */
    public function getProductsByCategory(string $category): Collection
    {
        try {
            $cacheKey = "category:{$category}:products";
            
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => $this->productRepository->findByCategory($category)
            );
        } catch (Throwable $e) {
            $this->logError('getProductsByCategory', $e);
            throw new ProductException('Failed to get products by category', 0, $e);
        }
    }

    /**
     * Apply filters to the query builder
     *
     * @param Builder $query
     * @param array<string, mixed> $params
     * @return void
     */
    private function applyFilters(Builder $query, array $params): void
    {
        if (!empty($params['category'])) {
            $query->whereHas('category', function ($q) use ($params) {
                $q->where('category', $params['category']);
            });
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('item_id', 'like', "%{$params['search']}%")
                  ->orWhere('item_name', 'like', "%{$params['search']}%");
            });
        }
    }

    /**
     * Apply eager loading to the query builder
     *
     * @param Builder $query
     * @return void
     */
    private function applyEagerLoading(Builder $query): void
    {
        $query->with(['category', 'category.parent']);
    }

    /**
     * Validate product data
     *
     * @param ProductData $data
     * @return void
     * @throws ProductException
     */
    private function validateProductData(ProductData $data): void
    {
        // Add custom validation logic here
        if (empty($data->item_id) || empty($data->item_name)) {
            throw new ProductException('Invalid product data');
        }
    }

    /**
     * Get cache key for product
     *
     * @param int $id
     * @return string
     */
    private function getCacheKey(int $id): string
    {
        return self::CACHE_KEY_PREFIX . $id;
    }

    /**
     * Clear product-related cache
     *
     * @param int|null $id
     * @return void
     */
    private function clearProductCache(?int $id = null): void
    {
        try {
            if ($id) {
                $this->cacheService->forget($this->getCacheKey($id));
            }
            $this->cacheService->tags([self::CACHE_TAG])->flush();
        } catch (Throwable $e) {
            $this->logError('clearProductCache', $e);
        }
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
        Log::error(
            "Error in ProductService::{$method}: {$exception->getMessage()}",
            [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString()
            ]
        );
    }
} 