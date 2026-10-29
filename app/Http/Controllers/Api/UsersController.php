<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserFilterData;
use App\Exceptions\UserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\{UserResource, UserPerformanceResource};
use App\Services\Interfaces\UserServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for user management"
 * )
 */
class UsersController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by name"
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = UserFilterData::fromRequest($request);
            $users = $this->userService->getAllUsers($filters);

            return $this->collectionResponse(
                UserResource::collection($users),
                'Users retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            return $this->resourceResponse(
                new UserResource($user),
                'User retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->resourceResponse(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return $this->resourceResponse(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return $this->successResponse(
                null,
                'User deleted successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/drivers",
     *     summary="Get all drivers",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of drivers retrieved successfully"
     *     )
     * )
     */
    public function getDrivers(Request $request): JsonResponse
    {
        try {
            $filters = UserFilterData::fromRequest($request);
            $filters = new UserFilterData(...(array)$filters, is_driver: true);
            
            $drivers = $this->userService->getDrivers($filters);
            return $this->collectionResponse(
                UserResource::collection($drivers),
                'Drivers retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/merchandisers",
     *     summary="Get all merchandisers",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of merchandisers retrieved successfully"
     *     )
     * )
     */
    public function getMerchandisers(Request $request): JsonResponse
    {
        try {
            $filters = UserFilterData::fromRequest($request);
            $filters = new UserFilterData(...(array)$filters, is_merchandiser: true);
            
            $merchandisers = $this->userService->getMerchandisers($filters);
            return $this->collectionResponse(
                UserResource::collection($merchandisers),
                'Merchandisers retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/performance",
     *     summary="Get user performance metrics",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User performance metrics retrieved successfully"
     *     )
     * )
     */
    public function getUserPerformance(int $id): JsonResponse
    {
        try {
            $performance = $this->userService->getUserPerformance($id);
            return $this->resourceResponse(
                new UserPerformanceResource($performance),
                'User performance metrics retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/activate",
     *     summary="Activate a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User activated successfully"
     *     )
     * )
     */
    public function activateUser(int $id): JsonResponse
    {
        try {
            $user = $this->userService->activateUser($id);
            return $this->resourceResponse(
                new UserResource($user),
                'User activated successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/deactivate",
     *     summary="Deactivate a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deactivated successfully"
     *     )
     * )
     */
    public function deactivateUser(int $id): JsonResponse
    {
        try {
            $user = $this->userService->deactivateUser($id);
            return $this->resourceResponse(
                new UserResource($user),
                'User deactivated successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}



