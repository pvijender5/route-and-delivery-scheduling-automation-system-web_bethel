<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use App\Http\Requests\ShipmentRequest;
use App\Events\ShipmentStatusChanged;
use App\Models\Shipment;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\ActivityLog;
use App\Models\ShipmentDeliveryDetailProof;
use App\Constants\ShipmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use DataTables;
use Throwable;

class ShipmentController extends Controller
{
    private ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Display shipment listing page
     */
    public function index(): View
    {
        try {
            $breadcrumbs = [
                ['name' => 'Manage Customer & Shipment', 'url' => route('shipments.index')],
                ['name' => 'All Shipments'],
            ];

            $warehouses = Cache::remember('warehouses', 3600, function () {
                return Warehouse::all();
            });

            $customers = Cache::remember('customers', 3600, function () {
                return Customer::all();
            });

            return view('shipments.index', compact('breadcrumbs', 'warehouses', 'customers'));
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@index: ' . $e->getMessage());
            return view('shipments.index')->with('error', 'Unable to load shipments. Please try again.');
        }
    }

    /**
     * Get shipments for datatables
     */
    public function datatables(Request $request): JsonResponse
    {
        try {
            $query = $this->shipmentService->getShipmentsQuery($request->all());

            return DataTables::of($query)
                ->addColumn('customer_name', fn($shipment) => $shipment->customer->name ?? '')
                ->addColumn('warehouse_name', fn($shipment) => $shipment->warehouse->name ?? '')
                ->addColumn('delivery_status', fn($shipment) => view('shipments.status', ['status' => $shipment->status]))
                ->addColumn('actions', fn($shipment) => view('shipments.actions', ['id' => $shipment->id]))
                ->rawColumns(['delivery_status', 'actions'])
                ->make(true);
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@datatables: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load shipment data'], 500);
        }
    }

    /**
     * Store a new shipment
     */
    public function store(ShipmentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $shipment = $this->shipmentService->createShipment($request->validated());

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'message' => 'Shipment created successfully',
                'data' => $shipment
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@store: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to create shipment'], 500);
        }
    }

    /**
     * Update shipment status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => ['required', 'string', 'in:' . implode(',', ShipmentStatus::VALID_STATUSES)]
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $shipment = $this->shipmentService->updateStatus($id, $request->status);

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json(['message' => 'Status updated successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@updateStatus: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to update status'], 500);
        }
    }

    /**
     * Upload delivery proof
     */
    public function uploadDeliveryProof(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
                'proof_type' => 'required|in:signature,photo,document'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $proof = $this->shipmentService->uploadDeliveryProof(
                $id,
                $request->file('proof_file'),
                $request->proof_type
            );

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'message' => 'Delivery proof uploaded successfully',
                'data' => $proof
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@uploadDeliveryProof: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to upload delivery proof'], 500);
        }
    }

    /**
     * Get shipment details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $shipment = $this->shipmentService->getShipmentDetails($id);
            return response()->json(['data' => $shipment]);
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@show: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch shipment details'], 500);
        }
    }

    /**
     * Update shipment details
     */
    public function update(ShipmentRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $shipment = $this->shipmentService->updateShipment($id, $request->validated());

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'message' => 'Shipment updated successfully',
                'data' => $shipment
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@update: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to update shipment'], 500);
        }
    }

    /**
     * Delete shipment
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $this->shipmentService->deleteShipment($id);

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json(['message' => 'Shipment deleted successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete shipment'], 500);
        }
    }

    /**
     * Restore soft deleted shipment
     */
    public function restore(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $shipment = Shipment::withTrashed()->findOrFail($id);
            $shipment->restore();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'shipment_id' => $id,
                'action' => 'restored',
                'description' => 'Shipment restored'
            ]);

            DB::commit();
            Cache::tags(['shipments'])->flush();

            return response()->json(['message' => 'Shipment restored successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error in ShipmentController@restore: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to restore shipment'], 500);
        }
    }

    /**
     * Get shipment inventory
     */
    public function getShipmentInventory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'shipment_id' => 'required|exists:shipments,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $shipment = Shipment::with('shipmentProps')->findOrFail($request->shipment_id);
            return response()->json(['data' => $shipment->shipmentProps]);
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@getShipmentInventory: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch inventory data'], 500);
        }
    }

    /**
     * Get delivery proofs
     */
    public function fetchDeliveryProof(int $shipmentId): JsonResponse
    {
        try {
            $proofs = ShipmentDeliveryDetailProof::where('shipment_id', $shipmentId)->get();
            return response()->json(['data' => $proofs]);
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@fetchDeliveryProof: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch delivery proofs'], 500);
        }
    }

    /**
     * Get shipment notes
     */
    public function getNotes(int $id): JsonResponse
    {
        try {
            $shipment = Shipment::findOrFail($id);
            return response()->json(['data' => $shipment->props_notes ?? []]);
        } catch (Throwable $e) {
            Log::error('Error in ShipmentController@getNotes: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch notes'], 500);
        }
    }
}