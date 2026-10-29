<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class FuelPrice extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'fuel_price';

    protected $fillable = [
        'id', 'price', 'period', 'product_name', 'product', 'area_name'
    ];

 
  public static function updateFuelPriceData()
    {
        // get diesel price from  https://api.eia.gov/ API
        $dieselPrices=getDieselPrice();  
        if(isset($dieselPrices) && isset($dieselPrices['response']) && isset($dieselPrices['response']['data']))
        {
            $responseData=$dieselPrices['response']['data'];
            if (!empty($responseData[0]['value'])) {
                
                $priceData=$responseData[0];
                $price = $priceData['value'];
                $price=round($price,2);

                $fuelPrice = self::first();
                if ($fuelPrice) {
                    self::where('id', $fuelPrice->id)
                    ->update([
                        'price' => $price,
                        'period' => $priceData['period'],
                        'product_name' => $priceData['product-name'],
                        'product' => $priceData['product'],
                        'area_name' => $priceData['area-name'],
                    ]);
        
                }else
                {
                    self::create([
                        'price' => $price,
                        'period' => $priceData['period'],
                        'product_name' => $priceData['product-name'],
                        'product' => $priceData['product'],
                        'area_name' => $priceData['area-name'],
                    ]);

                }
                
                return $priceData;
                
                
            }
        }

    }

    public static function getFuelPrice()
    {

        $fuelPrice = self::first();
        return $fuelPrice;

    }    
    
}
