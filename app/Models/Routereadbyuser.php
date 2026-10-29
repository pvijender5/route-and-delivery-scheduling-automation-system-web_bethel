<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routereadbyuser extends Model
{
    use HasFactory;

    protected $table = 'route_read_by_user';
    use HasFactory;

    /**
     * Create Record user
     * @param route_id,user_id,user_interface
     */
    public static function createRecord($request,$userid,$user_interface){
        $route = new Routereadbyuser;
        $route->user_id = $userid;
        $route->route_id = $request->route_id;
        $route->user_interface = $user_interface;
        $route->save();
        return $route;
    }
    /**
     * Check Shipment Read or not
     * @param  user_id,route_id,user_interface
    */
    public static function checkUserRoute($route_id,$userid,$user_interface){
        return Routereadbyuser::where(['route_id'=>$route_id,'user_id'=>$userid,'user_interface'=>$user_interface])->first();
    }
}
