<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Driverdetail;
use App\Models\PersonalAccessTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class CarrierOwnerService
{
    /**
     * Get carrier owners query for datatables
     *
     * @return Builder
     */
    public function getCarrierOwnersQuery(): Builder
    {
        return User::whereHas('role', function ($query) {
            $query->where('name', 'carrier-owner');
        })->with(['driverDetail', 'shipments']);
    }

    /**
     * Create a new carrier owner
     *
     * @param array $data
     * @return User
     */
    public function createCarrierOwner(array $data): User
    {
        try {
            $user = User::create([
                'name' => $data['carrier_owner_name'],
                'email' => $data['email'],
                'primary_contact_number' => $data['primary_contact_number'],
                'role_id' => Role::where('name', 'carrier-owner')->first()->id,
                'status' => $data['status'],
                'password' => Hash::make(Str::random(10))
            ]);

            Driverdetail::create([
                'user_id' => $user->id,
                'carrier_company_name' => $data['carrier_company_name'],
                'carrier_owner_name' => $data['carrier_owner_name'],
                'carrier_owner_id' => $data['carrier_owner_id'],
                'truck_id' => $data['truck_id'],
                'status' => $data['status']
            ]);

            Cache::tags(['carrier_owners'])->flush();
            return $user;
        } catch (Throwable $e) {
            Log::error('Error creating carrier owner: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update carrier owner status
     *
     * @param int $id
     * @param string $status
     * @return User
     */
    public function updateStatus(int $id, string $status): User
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $status;
            $user->save();

            if ($status === 'inactive') {
                PersonalAccessTokens::where('tokenable_id', $id)->delete();
            }

            Cache::tags(['carrier_owners'])->flush();
            return $user;
        } catch (Throwable $e) {
            Log::error('Error updating carrier owner status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get carrier owner details
     *
     * @param int $id
     * @return User
     */
    public function getCarrierOwnerDetails(int $id): User
    {
        try {
            return Cache::remember("carrier_owner_{$id}", 1800, function () use ($id) {
                return User::with(['driverDetail', 'shipments', 'role'])
                    ->findOrFail($id);
            });
        } catch (Throwable $e) {
            Log::error('Error fetching carrier owner details: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update carrier owner details
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateCarrierOwner(int $id, array $data): User
    {
        try {
            $user = User::findOrFail($id);
            
            $user->update([
                'name' => $data['carrier_owner_name'],
                'email' => $data['email'],
                'primary_contact_number' => $data['primary_contact_number'],
                'status' => $data['status']
            ]);

            $user->driverDetail->update([
                'carrier_company_name' => $data['carrier_company_name'],
                'carrier_owner_name' => $data['carrier_owner_name'],
                'carrier_owner_id' => $data['carrier_owner_id'],
                'truck_id' => $data['truck_id'],
                'status' => $data['status'],
                'make_default' => $data['make_default'] ?? false,
                'resequencing_shipments_allowed' => $data['resequencing_shipments_allowed'] ?? false
            ]);

            if ($data['status'] === 'inactive') {
                PersonalAccessTokens::where('tokenable_id', $id)->delete();
            }

            Cache::tags(['carrier_owners'])->flush();
            return $user->fresh(['driverDetail']);
        } catch (Throwable $e) {
            Log::error('Error updating carrier owner: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete carrier owner
     *
     * @param int $id
     * @return bool
     */
    public function deleteCarrierOwner(int $id): bool
    {
        try {
            $user = User::findOrFail($id);
            
            // Delete related records
            $user->driverDetail()->delete();
            PersonalAccessTokens::where('tokenable_id', $id)->delete();
            
            $result = $user->delete();
            
            Cache::tags(['carrier_owners'])->flush();
            return $result;
        } catch (Throwable $e) {
            Log::error('Error deleting carrier owner: ' . $e->getMessage());
            throw $e;
        }
    }
} 