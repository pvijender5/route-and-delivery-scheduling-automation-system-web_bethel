<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasFactory;

    protected $table = 'trucks';
    protected $fillable = [
        'truck_model_name',
        'trailer_type',
        'vin',
        'reg_no',
        'payload_capacity',
        'unladen_weight',
        'gross_weight',
        'status'
        // Add more fillable fields as needed
    ];
    public function driverDetail()
    {
        return $this->hasOne(Driverdetail::class, 'truck_id');
    }
    public static function getAllTruckList()
    {
        // You might want to consider using a query builder method here
        return static::all();
    }

    public static function getDatatableQuery()
    {
        // You can customize this query further if needed
        return self::query();
    }
    public static function getDistinctTrailerTypes()
    {
        return self::distinct('trailer_type')->pluck('trailer_type');
    }
}
