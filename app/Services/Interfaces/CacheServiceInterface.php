<?php

namespace App\Services\Interfaces;

interface CacheServiceInterface
{
    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function put(string $key, $value, int $ttl): bool;

    /**
     * Get an item from the cache.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, callable $callback);

    /**
     * Get a cache store instance by name.
     *
     * @param string|null $name
     * @return mixed
     */
    public function store(string $name = null);

    /**
     * Get a cache tags instance.
     *
     * @param array|string $names
     * @return mixed
     */
    public function tags($names);
} 