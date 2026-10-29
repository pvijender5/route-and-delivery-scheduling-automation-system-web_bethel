<?php

namespace App\Services;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;

class MyService
{
    public function processData($data)
    {
        return strtoupper($data);
    }
  public function getShipments()
      {
          try {
              $shipments = Shipment::all();

              if ($shipments->isEmpty()) {
                  return response()->json(['message' => 'Shipments not found'], 404);
              }

              return response()->json(['ShipmentsData' => $shipments], 200);
          } catch (\Exception $e) {
              return response()->json(['message' => $e->getMessage()], 500);
          }
      }

    public function deleteShipment($deduplicationId): JsonResponse
    {
        try {
           $shipment = Shipment::where('deduplication_id', $deduplicationId)->first();
          if (is_null($shipment) || empty($shipment)) {
                return response()->json(['message' => 'Shipment not found'], 404);
            }
            $shipment->delete();
            return response()->json(['message' => 'Shipment deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    
}
