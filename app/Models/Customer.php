<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model
{
    use HasFactory;
        protected $fillable = ['id', 'customer_id', 'customer_class', 'name', 'contact_name', 'email', 'phone', 'euid', 'routeEuid', 'location_name', 'addressLine1', 'addressLine2', 'city', 'state', 'country', 'postal_code'];
        protected $table = 'customers';
    /**
     * Returns all customers.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllCustomers()
    {
        return self::all();
    }

    /**
     * Returns customers for Datatables.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getCustomersForDatatables()
    {
        return static::query();
    }

    /**
     * Returns shipment data for a specific customer and shipment status.
     *
     * @param int $customerId The ID of the customer.
     * @param string $shipmentStatus The status of the shipment.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getShipmentsData($customerId, $shipmentStatus)
    {
        return Shipment::where('customer_id', $customerId)
            ->whereJsonContains('labels', $shipmentStatus)
            ->get();
    }
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'customer_id', 'customer_id');
    }
    public function merchandiserDetail()
    {
        return $this->hasOne(MerchandiserDetail::class, 'assigned_customer');
    }
    /**
     * public serach function
     */
    public static function globalsearch($search){
        return self::select('id','name')->where('name','LIKE','%'.$search.'%')->get();
    }
    

}
