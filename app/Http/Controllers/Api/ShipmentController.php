<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentPropsDeliveryInventory;
use App\Models\User;
use App\Models\Shipmentreadbyuser;
use App\Models\WarehouseTeamDetail;
use App\Facades\CurlFacade;
use App\Facades\AcumeticaFacade;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Routereadbyuser;
use App\Models\AccumeticaData;
use Illuminate\Support\Facades\Validator;
use App\Models\ShipmentCustomerDeliveryLocations;
use App\Models\ShipmentDeliveryDetail;
use App\Models\Routes;
use Illuminate\Database\QueryException;
use App\Constants\Roles;
use App\Models\Driverdetail;
use App\Models\LoadboardRoute;
use App\Models\CustomerLocation;
use App\Models\MerchandiserAssignedLocation;
use App\Models\ActivityLog;
use App\Events\ShipmentEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Get all shipments
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Shipment::with([
                'customer',
                'warehouse',
                'shipmentCustomerDeliveryLocations',
                'shipmentDeliveryDetails',
                'propsDeliveryInventory'
            ]);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_range')) {
                $dates = explode(',', $request->date_range);
                $query->whereBetween('route_date', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }

            $shipments = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'status' => 'success',
                'message' => 'Shipments retrieved successfully',
                'data' => $shipments
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching shipments: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve shipments'
            ], 500);
        }
    }

    /**
     * Get shipment by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $shipment = Shipment::with([
                'customer',
                'warehouse',
                'shipmentCustomerDeliveryLocations',
                'shipmentDeliveryDetails',
                'propsDeliveryInventory'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Shipment retrieved successfully',
                'data' => $shipment
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching shipment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Shipment not found'
            ], 404);
        }
    }

    /**
     * function to update
     * single column props_notes
     * api 
     */
    public function updatePropsNotes(Request $request, $id)
    {
        try {
            $requestData = $request->all();
          
            $propsNotes = $requestData['props_notes'] ?? null;

            if ($propsNotes === null) {
                return response()->json(['message' => 'The props notes field is required.'], 400);
            }

            $request->validate([
                'props_notes' => 'required|string',
            ]);
            $shipment = Shipment::find($id);
            // dd($shipment);
            if (!$shipment) {
                $response = [
                    'message' => 'Shipment not found',
                ];
                return response($response, 404);
            }
            $shipment->props_notes = $request->input('props_notes');
            $shipment->save(); 
            /** Add activity Log */
            $user = $request->user();
            $user = User::getUserbyId($user->id);
            $event_type = "shipment-propnotes";
            $event_description ="The " .$user->name ." ".$user->role->name. " has added a props_notes in shipment <a href='".url('/shipment-view')."/".$shipment->id."'>".$shipment->name."</a>.";
            insertActiveLog($user->id,"Shipment propnotes", $event_type, $shipment->id, $event_description,$route_id=null);
            /** End Activity Log */
            $response = [
                'message' => 'propsNotes Updated',
                'shipment' => $shipment,
            ];
            
            return response($response, 200);
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 409);
        }
    }

      /**
     * Function to 
     * get all details
     * from shipments
     * table
     */
    public function pickupShipments(Request $request)
    {
        try {
            $user = $request->user();
            $status='PICKUP';
            $warehouseIds = WarehouseTeamDetail::where('team_member', $user->id)->pluck('warehouse_id')->toArray();

            $shipment = Shipment::select('id', 'name', 'labels', 'route_Date', 'order_id', 'order_type', 'warehouse_id', 'fork_lift', 'customer_id', 'vehicle_types', 'props_delivery_window', 'status', 'deleted_at', 'deleted_by')
                ->whereJsonContains('labels', $status)
                ->whereIn('warehouse_id', $warehouseIds)
                ->where('deleted_by', null)
                ->with([
                    'warehouse',
                    'customer' => function ($query) {
                        $query->select('id', 'phone', 'customer_id', 'name');
                    },
                    'shipmentDeliveries.routes'
            ])->get();

                if (!$shipment) {
                    return response()->json(['status' => 'error', 'message' => 'Shipment not found'], 404);
                }
              //  $count = 0;
                foreach($shipment as $key=>$val){
                    $checkshipment = Shipmentreadbyuser::where(['shipment_id'=>$val->id,'user_id'=>$user->id])->first();
                    if(isset($checkshipment) && $checkshipment){
                       
                        $val->read_status = 1;
                    }else{
                      
                        $val->read_status = 0;
                    }
                }
                return response()->json([ 'status' => 'success', 'message' => "Pickup shipment found successfully.",'data' => $shipment,], 200);
            } catch (\Exception $e) {
                return response([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 201);
            }
    }
         

    /**
     * Function to 
     * get shipment details
     * from shipments
     * table
     * @params $shipment table id
     */ 
    public function pickupShipmentDetails($id)
    {
        try {
                $status='PICKUP';
                $shipment = Shipment::select('id', 'name', 'labels', 'route_Date', 'order_id', 'order_type', 'warehouse_id', 'props_notes', 'fork_lift', 'customer_id', 'vehicle_types', 'props_delivery_window', 'status', 'deleted_at', 'deleted_by')
                ->whereJsonContains('labels', $status)
                ->where('id', $id)
                ->with([
                    'warehouse',
                    'customer' => function ($query) {
                        $query->select('id', 'phone', 'customer_id', 'name');
                    },
                    'shipmentDeliveries.routes'
                ])->first();

                if (!$shipment) {
                    return response()->json(['status' => 'error', 'message' => 'Shipment not found'], 404);
                }
                return response()->json(['data' => $shipment, 'status' => 'success', 'message' => "Pickup shipment detail found successfully."], 200);
            } catch (\Exception $e) {
                return response([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 201);
            }
    }
// function to get shipmentInventorydetails by shipment id.
public function getShipmentInventory($shipmentId){
    try {
        $shipment = Shipment::find($shipmentId);

        if (!$shipment) {
            return response()->json(['status' => 'error', 'message' => 'Shipment not found.'], 404);
        }
        $shipmentInventory = ShipmentPropsDeliveryInventory::where('shipment_id', $shipmentId)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipment inventory details',
            'data' => $shipmentInventory
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Shipment Read by user
 * @param $user_id,shipment_id,werehouseid
 */
 public function readShipmentByUser(Request $request){
    try{
        $user = $request->user();
        $request->validate([
            'shipment_id' => 'required',
            'werehouse_id' => 'required'
        ]);
        $checkReadShipment = Shipmentreadbyuser::checkReadShipment($request,$user->id);
        if($checkReadShipment){
            return response()->json(['status' => 'error', 'message' => 'Shipment already read'], 404);
 
        }
        $readShipment = Shipmentreadbyuser::createRecord($request,$user->id);
        if($readShipment){
            return response()->json([
                'status' => 'success',
                'message' => 'Shipment read successfully',
                'data' => $readShipment
            ], 200);
        }
    }catch(\Exception $e){
        return response()->json(['status' => 'error', 'message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
    }
 }
// public function deleteShipment(Request $request,$shipmentId)
// {
//     // dd($request->headers->all());

//     $response = AcumeticaFacade::deleteShipment($shipmentId);
//     \Log::info("Raw API Response", ['body' => $response->body(), 'status' => $response->status()]);
//     if ($response->status() == 401) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Unauthorized. Check API token.'
//         ], 401);
//     }
//     if ($response->status() == 404) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Shipment not found.'
//         ], 404);
//     }

//     if ($response->successful()) {
//         return response()->json([
//             'status' => true,
//             'message' => 'Shipment deleted successfully.',
//             'data' => $response->json()
//         ], 200);
//     }
//     $errorResponse = json_decode($response->body(), true);
//     // if (!$errorResponse) {
//     //     \Log::error("Failed to delete shipment - Empty or invalid JSON response", [
//     //         'raw_body' => $response->body(),
//     //         'status' => $response->status()
//     //     ]);
//     // } else {
//     //     \Log::error("Failed to delete shipment", ['error' => $errorResponse]);
//     // }
//     $errorMessage = $errorResponse['exceptionMessage'] ?? $errorResponse['message'] ?? 'Unknown error occurred.';

//     return response()->json([
//         'status' => false,
//         'message' => 'Failed to delete shipment.',
//         'details' => $errorMessage
//     ], $response->status());
// }


// public function getToken()
// {
//     $token = CurlFacade::getToken();

//     if ($token) {
//         return response()->json(['token' => $token], 200);
//     } else {
//         return response()->json(['message' => 'Failed to get token.'], 400); 
//     }
// }

// public function getRoutes(Request $request)
// {
    
//     // \Log::info('getRoutes request parameters:', compact('select', 'filter', 'expand', 'custom'));
//     $response = AcumeticaFacade::createOrUpdateRoute();
//      \Log::info("Raw API Response", ['body' => $response->body(), 'status' => $response->status()]);

//     if ($response->status() == 401) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Unauthorized. Check API token.'
//         ], 401);
//     }

//     if ($response->status() == 404) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Routes not found.'
//         ], 404);
//     }
//     if ($response->successful()) {
//         return response()->json([
//             'status' => true,
//             'message' => 'Routes retrieved successfully.',
//             'data' => $response->json()
//         ], 200);
//     }
//     $errorResponse = json_decode($response->body(), true);
//     $errorMessage = $errorResponse['exceptionMessage'] ?? $errorResponse['message'] ?? 'Unknown error occurred.';

//     return response()->json([
//         'status' => false,
//         'message' => 'Failed to retrieve routes.',
//         'details' => $errorMessage
//     ], $response->status());
// }
//  public function removeToken(): JsonResponse
//     {
//         Session::forget('api_access_token');
//         return response()->json(['message' => 'API access token removed successfully.']);
//     }


// public function deleteRoute($id)
// {
//     $response = AcumeticaFacade::deleteRoute($id);
//     if ($response->status() == 401) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Unauthorized. Check API token.'
//         ], 401);
//     }
//     if ($response->status() == 404) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Route not found.'
//         ], 404);
//     }
//     if ($response->successful()) {
//         return response()->json([
//             'status' => true,
//             'message' => 'Route deleted successfully.',
//             'data' => $response->json()
//         ], 200);
//     }

//     $errorResponse = $response->json();
//     $errorMessage = $errorResponse['exceptionMessage'] ?? 
//                     $errorResponse['message'] ?? 
//                     'An unknown error occurred.';

//     return response()->json([
//         'status' => false,
//         'message' => 'Failed to delete route.',
//         'details' => $errorMessage
//     ], $response->status());
// }
//     public function confirmShipment(Request $request, $shipmentId)
//     {
//         $response = AcumeticaFacade::confirmShipment($shipmentId);
//         if ($response->successful()) {
//             return response()->json([
//                 'status' => true,
//                 'message' => 'Shipment confirmed successfully.',
//                 'data' => $response->json(),
//             ], 200);
//         }
//         $errorMessage = 'Failed to confirm shipment.';
//         if ($response->status() === 400) {
//             $errorMessage = 'Bad Request: There was an issue with the data sent.';
//         } elseif ($response->status() === 404) {
//             $errorMessage = 'Not Found: The shipment ID was not found.';
//         } elseif ($response->status() === 500) {
//             $errorMessage = 'Internal Server Error: Something went wrong on the server.';
//         }elseif ($response->status() === 401) {
//             $errorMessage = 'Unauthorized. Check API token.';
//      }
//         return response()->json([
//             'status' => false,
//             'message' => $errorMessage,
//             'details' => $response->body(),  
//             ], $response->status());
//     }
// public function getFile($shipmentId, $filename)
// {
   
//         // Call the AccumaticaFacade to get the file content
//         $response = AcumeticaFacade::getFile($shipmentId, $filename);

//         // Check if the response was successful
//         if ($response->status() >= 200 && $response->status() < 300) {
//             // Return the file content as a response
//             return response($response->body(), Response::HTTP_OK)
//                 ->header('Content-Type', 'application/octet-stream')
//                 ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
//         }

//         // Handle errors based on the status code
//         $errorMessage = 'Failed to retrieve file.';

//         // Customize the error message based on status code
//         if ($response->status() === 400) {
//             $errorMessage = 'Bad Request: There was an issue with the data sent.';
//         } elseif ($response->status() === 404) {
//             $errorMessage = 'Not Found: The shipment ID or filename was not found.';
//         } elseif ($response->status() === 500) {
//             $errorMessage = 'Internal Server Error: Something went wrong on the server.';
//         } elseif ($response->status() === 401) {
//             $errorMessage = 'Unauthorized: Please check your API token or authentication.';
//         }

//         // Return error response with status code
//         return response()->json([
//             'status' => false,
//             'message' => $errorMessage,
//             'details' => $response->body(),
//         ], $response->status());
// }
// public function uploadShipmentFile(Request $request, $shipmentId): JsonResponse
// {
//     // dd( $request->all());
//        $request->validate([
//         'file' => 'required|file'
//     ]);

//     $file = $request->file('file');

//     $filePath = $file->getPathname();

//     Log::info('Received file for shipment upload', [
//         'shipmentId'   => $shipmentId,
//         'originalName' => $file->getClientOriginalName(),
//         'tempPath'     => $filePath
//     ]);
        // $response = AcumeticaFacade::uploadShipmentFile($shipmentId, $filePath);
//         // dd( $response);

//         if ($response && $response->successful()) {
//             return response()->json([
//                 'status'  => true,
//                 'message' => 'File uploaded successfully',
//                 'data'    => $response->json()
//             ], $response->status());
//         } else {
//             $errorMessage = $this->getErrorMessage($response);

//             Log::error('File upload failed', [
//                 'shipmentId' => $shipmentId,
//                 'status'     => $response ? $response->status() : 'No response',
//                 'error'      => $errorMessage,
//                 'response'   => $response ? $response->body() : 'No response'
//             ]);

//             return response()->json([
//                 'status'  => false,
//                 'message' => $errorMessage,
//                 'details' => $response ? $response->body() : 'No response received'
//             ], $response ? $response->status() : 500);
//         }
// }

// /**
//  * Get a user-friendly error message based on the response status.
//  *
//  * @param  Response|null  $response
//  * @return string
//  */
// private function getErrorMessage(Response $response): string
// {
//     if (!$response) {
//         return 'No response received from the server.';
//     }

//     return match ($response->status()) {
//         400 => 'Bad Request: There was an issue with the data sent.',
//         401 => 'Unauthorized: Please check your API token or authentication.',
//         403 => 'Forbidden: You do not have permission to perform this action.',
//         404 => 'Not Found: The shipment ID or filename was not found.',
//         408 => 'Request Timeout: The server took too long to respond.',
//         409 => 'Conflict: There was a conflict with the current state of the resource.',
//         422 => 'Unprocessable Entity: The data provided was invalid.',
//         429 => 'Too Many Requests: You have sent too many requests in a given amount of time.',
//         500 => 'Internal Server Error: Something went wrong on the server.',
//         502 => 'Bad Gateway: Received an invalid response from the upstream server.',
//         503 => 'Service Unavailable: The server is currently unavailable.',
//         504 => 'Gateway Timeout: The upstream server failed to send a request in time.',
//         default => 'An unexpected error occurred.',
//     };
// }


// public function correctShipment(Request $request, $shipmentId)
//     {
//             // Get correction data from request
//             $correctionData = $request->all();

//             // Call AccumaticaFacade to send the correction request
//             $response = AcumeticaFacade::correctShipment($shipmentId, $correctionData);

//             // Handle different response statuses
//             if ($response->successful()) {
//                 return response()->json([
//                     'status' => true,
//                     'message' => 'Shipment corrected successfully.',
//                     'data' => $response->json()
//                 ], Response::HTTP_OK);
//             }

//             // Error handling based on status code
//             $errorMessage = 'Failed to correct shipment.';
//             if ($response->status() === 400) {
//                 $errorMessage = 'Bad Request: There was an issue with the data sent.';
//             } elseif ($response->status() === 404) {
//                 $errorMessage = 'Not Found: The shipment ID was not found.';
//             } elseif ($response->status() === 500) {
//                 $errorMessage = 'Internal Server Error: Something went wrong on the server.';
//             } elseif ($response->status() === 401) {
//                 $errorMessage = 'Unauthorized: Check API token.';
//             }

//             return response()->json([
//                 'status' => false,
//                 'message' => $errorMessage,
//                 'details' => $response->body(),
//             ], $response->status());
     
//     }
// public function markShipmentLoaded(Request $request, $shipmentId)
//     {
//             $response = AcumeticaFacade::markShipmentLoaded($shipmentId);

//             if ($response->successful()) {
//                 return response()->json([
//                     'status' => true,
//                     'message' => 'Shipment marked as loaded successfully.',
//                     'data' => $response->json()
//                 ], Response::HTTP_OK);
//             }
//             $errorMessage = 'Failed to mark shipment as loaded.';
//             if ($response->status() === 400) {
//                 $errorMessage = 'Bad Request: Invalid data sent.';
//             } elseif ($response->status() === 404) {
//                 $errorMessage = 'Not Found: The shipment ID was not found.';
//             } elseif ($response->status() === 500) {
//                 $errorMessage = 'Internal Server Error: Something went wrong on the server.';
//             } elseif ($response->status() === 401) {
//                 $errorMessage = 'Unauthorized: Check API token.';
//             }

//             return response()->json([
//                 'status' => false,
//                 'message' => $errorMessage,
//                 'details' => $response->body(),
//             ], $response->status());
//     }
//       public function markShipmentNotLoaded(Request $request, $shipmentId)
//     {
//             $response = AcumeticaFacade::markShipmentNotLoaded($shipmentId);

//             if ($response->successful()) {
//                 return response()->json([
//                     'status' => true,
//                     'message' => 'Shipment marked as NOT loaded successfully.',
//                     'data' => $response->json()
//                 ], Response::HTTP_OK);
//             }

//             // Error handling based on HTTP response status
//             $errorMessage = 'Failed to mark shipment as NOT loaded.';
//             if ($response->status() === 400) {
//                 $errorMessage = 'Bad Request: Invalid data sent.';
//             } elseif ($response->status() === 401) {
//                 $errorMessage = 'Unauthorized: Invalid API credentials.';
//             } elseif ($response->status() === 404) {
//                 $errorMessage = 'Shipment not found.';
//             }

//             return response()->json([
//                 'status' => false,
//                 'message' => $errorMessage,
//                 'error' => $response->json()
//             ], $response->status());
//     }
//     public function updateRoute()
// {
//     $response = AcumeticaFacade::createOrUpdateRoute();
//     if ($response->status() == 401) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Unauthorized. Check API token.'
//         ], 401);
//     }
//     if ($response->status() == 404) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Routes not found.'
//         ], 404);
//     }
//     if ($response->successful()) {
//         return response()->json([
//             'status' => true,
//             'message' => 'Routes retrieved successfully.',
//             'data' => $response->json()
//         ], 200);
//     }
//     $errorResponse = json_decode($response->body(), true);
//     $errorMessage = $errorResponse['exceptionMessage'] ?? $errorResponse['message'] ?? 'Unknown error occurred.';

//     return response()->json([
//         'status' => false,
//         'message' => 'Failed to retrieve routes.',
//         'details' => $errorMessage
//     ], $response->status());
// }

//     public function createOrUpdateShipment()
// {
//     $response = AcumeticaFacade::createOrUpdateShipment($select = null, $filter = null, $expand = null, $custom = null);
//     if ($response->status() == 401) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Unauthorized. Check API token.'
//         ], 401);
//     }
//     if ($response->status() == 404) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Shipments not found.'
//         ], 404);
//     }
//     if ($response->successful()) {
//         return response()->json([
//             'status' => true,
//             'message' => 'Shipments retrieved successfully.',
//             'data' => $response->json()
//         ], 200);
//     }
//     $errorResponse = json_decode($response->body(), true);
//     $errorMessage = $errorResponse['exceptionMessage'] ?? $errorResponse['message'] ?? 'Unknown error occurred.';

//     return response()->json([
//         'status' => false,
//         'message' => 'Failed to retrieve Shipments.',
//         'details' => $errorMessage
//     ], $response->status());
// }

    /**
     * Create a new shipment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'order_id' => 'required|unique:shipments',
                'route_date' => 'required|date',
                'status' => 'required|in:pending,assigned,in_transit,delivered,cancelled',
                'delivery_location' => 'required|array',
                'delivery_location.address' => 'required|string',
                'delivery_location.contact_name' => 'required|string',
                'delivery_location.contact_phone' => 'required|string',
                'inventory' => 'required|array',
                'inventory.*.product_id' => 'required|exists:products,id',
                'inventory.*.quantity' => 'required|numeric|min:1'
            ]);

            $shipment = Shipment::create($validated);

            // Create delivery location
            $shipment->shipmentCustomerDeliveryLocations()->create($validated['delivery_location']);

            // Create inventory items
            foreach ($validated['inventory'] as $item) {
                $shipment->propsDeliveryInventory()->create($item);
            }

            DB::commit();

            // Clear cache
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'status' => 'success',
                'message' => 'Shipment created successfully',
                'data' => $shipment
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating shipment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create shipment'
            ], 500);
        }
    }

    /**
     * Update an existing shipment
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'exists:customers,id',
                'warehouse_id' => 'exists:warehouses,id',
                'order_id' => 'unique:shipments,order_id,' . $id,
                'route_date' => 'date',
                'status' => 'in:pending,assigned,in_transit,delivered,cancelled',
                'delivery_location' => 'array',
                'delivery_location.address' => 'string',
                'delivery_location.contact_name' => 'string',
                'delivery_location.contact_phone' => 'string',
                'inventory' => 'array',
                'inventory.*.product_id' => 'exists:products,id',
                'inventory.*.quantity' => 'numeric|min:1'
            ]);

            $shipment->update($validated);

            if (isset($validated['delivery_location'])) {
                $shipment->shipmentCustomerDeliveryLocations()->update($validated['delivery_location']);
            }

            if (isset($validated['inventory'])) {
                $shipment->propsDeliveryInventory()->delete();
                foreach ($validated['inventory'] as $item) {
                    $shipment->propsDeliveryInventory()->create($item);
                }
            }

            DB::commit();

            // Clear cache
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'status' => 'success',
                'message' => 'Shipment updated successfully',
                'data' => $shipment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating shipment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update shipment'
            ], 500);
        }
    }

    /**
     * Delete a shipment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);
            
            // Delete related records
            $shipment->shipmentCustomerDeliveryLocations()->delete();
            $shipment->propsDeliveryInventory()->delete();
            $shipment->shipmentDeliveryDetails()->delete();
            $shipment->delete();

            DB::commit();

            // Clear cache
            Cache::tags(['shipments'])->flush();

            return response()->json([
                'status' => 'success',
                'message' => 'Shipment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting shipment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete shipment'
            ], 500);
        }
    }

    /**
     * Upload delivery proof files
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function uploadDeliveryProof(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'proof_files.*' => 'required|file|mimes:jpeg,png,pdf|max:10240'
            ]);

            $shipment = Shipment::findOrFail($id);
            $uploadedFiles = [];

            foreach ($request->file('proof_files') as $file) {
                $path = $file->store('delivery_proofs/' . $id, 'public');
                
                $uploadedFiles[] = $shipment->deliveryProofs()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'uploaded_by' => auth()->id()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery proof files uploaded successfully',
                'data' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading delivery proof: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload delivery proof files'
            ], 500);
        }
    }

    /**
     * Get delivery proof files
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getDeliveryProofs(int $id): JsonResponse
    {
        try {
            $shipment = Shipment::with('deliveryProofs')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery proofs retrieved successfully',
                'data' => $shipment->deliveryProofs
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting delivery proofs: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve delivery proofs'
            ], 500);
        }
    }
}


