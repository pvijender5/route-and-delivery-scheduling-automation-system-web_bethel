<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentDeliveryDetailProof;
use App\Models\ShipmentCustomerDeliveryLocation;
use App\Events\ShipmentStatusChanged;
use App\Exceptions\ShipmentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ShipmentService
{
    /**
     * Get shipments query for datatables with filters
     */
    public function getShipmentsQuery(array $filters): Builder
    {
        try {
            $query = Shipment::with(['customer', 'warehouse', 'shipmentDeliveries'])
                ->orderBy('id', 'desc');

            if (isset($filters['warehouse_id'])) {
                $query->where('warehouse_id', $filters['warehouse_id']);
            }

            if (isset($filters['delivery_status'])) {
                if ($filters['delivery_status'] === 'deleted') {
                    $query->onlyTrashed();
                } else {
                    $query->whereHas('shipmentDeliveries', function ($q) use ($filters) {
                        $q->where('delivery_status', $filters['delivery_status']);
                    });
                }
            }

            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('order_id', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('warehouse', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            return $query;
        } catch (Throwable $e) {
            Log::error('Error in getShipmentsQuery: ' . $e->getMessage());
            throw new ShipmentException('Failed to get shipments query', 0, $e);
        }
    }

    /**
     * Create a new shipment with delivery location
     */
    public function createShipment(array $data): Shipment
    {
        try {
            $shipment = Shipment::create([
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_id' => $data['order_id'],
                'route_Date' => $data['route_date'],
                'status' => $data['status'] ?? 'pending',
                'fork_lift' => $data['fork_lift'] ?? false,
                'props_delivery_window' => $data['delivery_window'],
                'props_notes' => $data['notes'] ?? []
            ]);

            if (isset($data['delivery_location'])) {
                $this->createDeliveryLocation($shipment->id, $data['delivery_location']);
            }

            $this->clearShipmentCache();
            return $shipment->fresh(['customer', 'warehouse', 'shipmentCustomerDeliveryLocation']);
        } catch (Throwable $e) {
            Log::error('Error in createShipment: ' . $e->getMessage());
            throw new ShipmentException('Failed to create shipment', 0, $e);
        }
    }

    /**
     * Create delivery location for a shipment
     */
    private function createDeliveryLocation(int $shipmentId, array $locationData): void
    {
        ShipmentCustomerDeliveryLocation::create([
            'shipment_id' => $shipmentId,
            'delivery_location_name' => $locationData['name'],
            'delivery_location_address_line_1' => $locationData['address_line_1'],
            'delivery_location_city' => $locationData['city'],
            'delivery_location_state' => $locationData['state'],
            'delivery_location_zipcode' => $locationData['zipcode'],
            'delivery_contact_phone' => $locationData['contact_phone']
        ]);
    }

    /**
     * Update shipment status and trigger event
     */
    public function updateStatus(int $id, string $status): Shipment
    {
        try {
            $shipment = Shipment::findOrFail($id);
            $shipment->status = $status;
            $shipment->save();

            if ($shipment->shipmentDeliveries) {
                $shipment->shipmentDeliveries->delivery_status = $status;
                $shipment->shipmentDeliveries->save();
            }

            event(new ShipmentStatusChanged($shipment));
            $this->clearShipmentCache();
            
            return $shipment;
        } catch (Throwable $e) {
            Log::error('Error in updateStatus: ' . $e->getMessage());
            throw new ShipmentException('Failed to update shipment status', 0, $e);
        }
    }

    /**
     * Upload delivery proof file
     */
    public function uploadDeliveryProof(int $shipmentId, UploadedFile $file, string $proofType): ShipmentDeliveryDetailProof
    {
        try {
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('delivery_proofs', $fileName, 'public');

            $proof = ShipmentDeliveryDetailProof::create([
                'shipment_id' => $shipmentId,
                'proof_type' => $proofType,
                'file_path' => $path
            ]);

            $this->clearShipmentCache();
            return $proof;
        } catch (Throwable $e) {
            Log::error('Error in uploadDeliveryProof: ' . $e->getMessage());
            throw new ShipmentException('Failed to upload delivery proof', 0, $e);
        }
    }

    /**
     * Get shipment details with relationships
     */
    public function getShipmentDetails(int $id): Shipment
    {
        try {
            return Cache::remember(
                "shipment_details_{$id}",
                now()->addMinutes(30),
                fn() => Shipment::with([
                    'customer',
                    'warehouse',
                    'shipmentDeliveries',
                    'shipmentCustomerDeliveryLocation',
                    'shipmentProps',
                    'deliveryProofs'
                ])->findOrFail($id)
            );
        } catch (Throwable $e) {
            Log::error('Error in getShipmentDetails: ' . $e->getMessage());
            throw new ShipmentException('Failed to get shipment details', 0, $e);
        }
    }

    /**
     * Update shipment and its delivery location
     */
    public function updateShipment(int $id, array $data): Shipment
    {
        try {
            $shipment = Shipment::findOrFail($id);
            
            $shipment->update([
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_id' => $data['order_id'],
                'route_Date' => $data['route_date'],
                'status' => $data['status'],
                'fork_lift' => $data['fork_lift'] ?? false,
                'props_delivery_window' => $data['delivery_window'],
                'props_notes' => $data['notes'] ?? []
            ]);

            if (isset($data['delivery_location'])) {
                $this->updateDeliveryLocation($shipment, $data['delivery_location']);
            }

            $this->clearShipmentCache();
            return $shipment->fresh(['customer', 'warehouse', 'shipmentCustomerDeliveryLocation']);
        } catch (Throwable $e) {
            Log::error('Error in updateShipment: ' . $e->getMessage());
            throw new ShipmentException('Failed to update shipment', 0, $e);
        }
    }

    /**
     * Update delivery location for a shipment
     */
    private function updateDeliveryLocation(Shipment $shipment, array $locationData): void
    {
        $shipment->shipmentCustomerDeliveryLocation()->update([
            'delivery_location_name' => $locationData['name'],
            'delivery_location_address_line_1' => $locationData['address_line_1'],
            'delivery_location_city' => $locationData['city'],
            'delivery_location_state' => $locationData['state'],
            'delivery_location_zipcode' => $locationData['zipcode'],
            'delivery_contact_phone' => $locationData['contact_phone']
        ]);
    }

    /**
     * Delete shipment and related records
     */
    public function deleteShipment(int $id): bool
    {
        try {
            $shipment = Shipment::findOrFail($id);
            
            // Delete related records
            $shipment->shipmentCustomerDeliveryLocation()->delete();
            $shipment->shipmentDeliveries()->delete();
            $shipment->shipmentProps()->delete();
            
            // Delete delivery proofs and their files
            $shipment->deliveryProofs->each(function ($proof) {
                Storage::disk('public')->delete($proof->file_path);
                $proof->delete();
            });
            
            $result = $shipment->delete();
            $this->clearShipmentCache();
            
            return $result;
        } catch (Throwable $e) {
            Log::error('Error in deleteShipment: ' . $e->getMessage());
            throw new ShipmentException('Failed to delete shipment', 0, $e);
        }
    }

    /**
     * Clear shipment related cache
     */
    private function clearShipmentCache(): void
    {
        Cache::tags(['shipments'])->flush();
    }
} 