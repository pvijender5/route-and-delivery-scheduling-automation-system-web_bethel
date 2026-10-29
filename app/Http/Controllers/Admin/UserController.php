<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\UserFilterData;
use App\Exceptions\UserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\Queries\Admin\UserQuery;
use App\Services\Interfaces\Admin\UserServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Admin Users",
 *     description="API Endpoints for admin user management"
 * )
 */
class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="Get all users",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="query",
     *         description="Filter by role ID"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status"
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
     *     path="/api/admin/users/{id}",
     *     summary="Get user by ID",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully"
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
     *     path="/api/admin/users",
     *     summary="Create a new user",
     *     tags={"Admin Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
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
     *     path="/api/admin/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
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
     *     path="/api/admin/users/{id}",
     *     summary="Delete a user",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
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
     *     path="/api/admin/users/by-role/{roleId}",
     *     summary="Get users by role",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="roleId",
     *         in="path",
     *         required=true,
     *         description="Role ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully"
     *     )
     * )
     */
    public function getUsersByRole(Request $request, int $roleId): JsonResponse
    {
        try {
            $filters = UserFilterData::fromRequest($request);
            $filters = new UserFilterData(
                ...(array)$filters,
                role_id: $roleId,
                status: 'active',
                current_user_id: $request->user()->id
            );

            $users = $this->userService->getUsersByRole($filters);
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
     *     path="/api/admin/users/{id}/activity",
     *     summary="Get user activity",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User activity retrieved successfully"
     *     )
     * )
     */
    public function getUserActivity(int $id): JsonResponse
    {
        try {
            $activity = $this->userService->getUserActivity($id);
            return $this->successResponse(
                $activity,
                'User activity retrieved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users/{id}/approve",
     *     summary="Approve a user",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User approved successfully"
     *     )
     * )
     */
    public function approveUser(int $id): JsonResponse
    {
        try {
            $user = $this->userService->approveUser($id);
            return $this->resourceResponse(
                new UserResource($user),
                'User approved successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users/{id}/reject",
     *     summary="Reject a user",
     *     tags={"Admin Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User rejected successfully"
     *     )
     * )
     */
    public function rejectUser(int $id): JsonResponse
    {
        try {
            $user = $this->userService->rejectUser($id);
            return $this->resourceResponse(
                new UserResource($user),
                'User rejected successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users/bulk-action",
     *     summary="Perform bulk action on users",
     *     tags={"Admin Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Bulk action data"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk action performed successfully"
     *     )
     * )
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $result = $this->userService->performBulkAction(
                $request->input('action'),
                $request->input('user_ids')
            );
            
            return $this->successResponse(
                $result,
                'Bulk action performed successfully'
            );
        } catch (UserException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
