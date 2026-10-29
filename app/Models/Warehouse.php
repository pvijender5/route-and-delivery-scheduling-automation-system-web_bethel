<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'email',
        'primary_phone_no',
        'address',
        'latitude',
        'longitude',
        'state',
        'city',
        'zipcode',
        'timezone',
        'status',
        'created_by',
    ];
    
    public function warehouseTeamDetail()
    {
        return $this->hasMany(WarehouseTeamDetail::class);
    }
    
}
