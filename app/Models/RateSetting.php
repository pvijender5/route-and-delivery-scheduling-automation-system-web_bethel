<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Routes;
use App\Constants\Commonconstant;
use Auth;


class RateSetting extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'rate_setting';

    protected $fillable = [
        'id', 'rate_per_mile', 'base_rate', 'base_add', 'fsc', 'fsc_add', 'rate_inc', 'min_per_mile', 'fork_bill', 'fork_pay', 'stop_pay', 'base_var', 'exp_var', 'add_base', 'extra', 'miles', 'stops', 'fork_lift', 'diesel_price', 'created_at', 'updated_at'
    ];

 
  public static function getRateSetting()
    {
        // Get all users
        return self::first();
    }

    // Fetch rate values from first row
    public static function updateRecommendedPriceRoute($route_id)
    {
        
        $routes = Routes::where('id', $route_id)->first();
        if($routes)
        {
            $miles=round(($routes->loaded_distance*Commonconstant::MILES),2);
            $stops=$routes->total_stops;
            $forkLift=$routes->fork_lift;
            $rateSetting = RateSetting::getRateSetting();
            $recommendedPrice=calculateRecommendedPrice($rateSetting,$miles,$stops,$forkLift);
            if($recommendedPrice && $recommendedPrice!=null && isset($recommendedPrice['totalRateCarrier']) && $recommendedPrice['totalRateCarrier']!=NULL && !is_nan($recommendedPrice['totalRateCarrier']))
            {
                Routes::where('id', $route_id)
                ->update([
                    'recommended_price' => $recommendedPrice['totalRateCarrier'],
                    'total_price' => $recommendedPrice['totalRateCarrier']
                ]);   
            }
            
        }
        
    }  
    
}
