<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Success response with data
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error response
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }

    /**
     * Response with resource
     *
     * @param JsonResource $resource
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Success', int $code = 200): JsonResponse
    {
        return $resource
            ->additional(['message' => $message])
            ->response()
            ->setStatusCode($code);
    }

    /**
     * Response with resource collection
     *
     * @param ResourceCollection $collection
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function collectionResponse(ResourceCollection $collection, string $message = 'Success', int $code = 200): JsonResponse
    {
        return $collection
            ->additional(['message' => $message])
            ->response()
            ->setStatusCode($code);
    }
} 