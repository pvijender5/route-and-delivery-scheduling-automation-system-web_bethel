<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CustomerData;
use App\DTOs\CustomerFilterData;
use App\Exceptions\CustomerException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\Interfaces\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Customers",
 *     description="API Endpoints for customer management"
 * )
 */
class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customerService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get all customers",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of customers retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No customers found"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $filter = new CustomerFilterData();
            $customers = $this->customerService->getAllCustomers($filter);

            return CustomerResource::collection($customers);
        } catch (CustomerException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Get customer by ID",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
    public function show(int $id): CustomerResource|JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomerById($id);
            return new CustomerResource($customer);
        } catch (CustomerException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Customer data"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(CustomerRequest $request): CustomerResource|JsonResponse
    {
        try {
            $customerData = CustomerData::fromRequest($request->validated());
            $customer = $this->customerService->createCustomer($customerData);

            return (new CustomerResource($customer))
                ->response()
                ->setStatusCode(201);
        } catch (CustomerException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Update an existing customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Customer data"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(CustomerRequest $request, int $id): CustomerResource|JsonResponse
    {
        try {
            $customerData = CustomerData::fromRequest($request->validated());
            $customer = $this->customerService->updateCustomer($id, $customerData);

            return new CustomerResource($customer);
        } catch (CustomerException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Delete a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->customerService->deleteCustomer($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully'
            ]);
        } catch (CustomerException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}

