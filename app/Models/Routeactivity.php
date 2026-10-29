<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routeactivity extends Model
{
    use HasFactory;
    protected $table = 'routeactivity';
    protected $fillable = ['id', 'route_id', 'lat', 'lng', 'status'];



    /**Check Route */

    public static function checkLocation($route_id)
    {
        return self::where('route_id', $route_id)->first();
    }
    /** Create Record */
    public static function createRecord($request)
    {
        return self::create([
            'route_id' => $request->route_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => 1
        ]);
    }

    /** Update Record */
    public static function updateRecord($request){
       return self::where('route_id', $request->route_id)->update(['lat'=>$request->lat,'lng'=>$request->lng]);
    }
}