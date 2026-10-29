<?php
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Notifications;
use App\Models\FuelPrice;
use App\Models\FuelSurchargeSchedule;
use App\Models\ActivityLog;
use App\Models\FirebaseToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Google\Client as GoogleClient;

// Create a new PHP file in the app directory, e.g., CommonFunctions.php


/**
 * Concat delivery llocation address 
 */

if (!function_exists('concatenateAddress')) {
    function concatenateAddress($address)
    {

        if ($address) {
            $addressFields = [
                $address->delivery_location_address_line_1 ?? '',
                $address->delivery_location_address_line_2 ?? '',
                $address->delivery_location_city ?? '',
                $address->delivery_location_state ?? '',
                $address->delivery_location_zipcode ?? '',
                $address->delivery_location_country ?? ''
            ];
            // Remove empty fields
            $addressFields = array_filter($addressFields);

            // Concatenate the fields with commas
            $concatenatedAddress = implode(', ', $addressFields);

            return $concatenatedAddress;
        } else {
            return "N/A";
        }

    }
}
if (!function_exists('getFormattedDeliveryWindow')) {
    function getFormattedDeliveryWindow($deliveryWindowData)
    {
        // Check if the delivery window data is valid
        if (is_array($deliveryWindowData) && isset($deliveryWindowData[0]['start']) && isset($deliveryWindowData[0]['end'])) {
            $startTime = gettimewithtimezone($deliveryWindowData[0]['start']);
            $endTime = gettimewithtimezone($deliveryWindowData[0]['end']);
            // Format the start and end times as required (e.g., 'h:i A')
            // $formattedStartTime = $startTime->format('h:i A');
            // $formattedEndTime = $endTime->format('h:i A');
            // Combine the formatted times with a hyphen
            return $startTime . ' - ' . $endTime;
        }

        // Return a default value or handle invalid data as needed
        return 'N/A'; // Replace with an appropriate default value or message
    }
}


    if ($remainingMinutes > 0) {
        $result .= $remainingMinutes . ' m';
    }

    return $result;
}

//Send common SMS function 
function sendMobileSms($phone, $message)
{
    $callingCode = config('app.env') == 'production' ? '+1' : '+91';
    $to = $callingCode.$phone;
    try {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");
        $client = new Client($account_sid, $auth_token);

        $client->messages->create($to, [
            'from' => $twilio_number,
            'body' => $message]);
    } catch (Exception $e) {
        return $e->getMessage();
    }
}


//Calculate PRT values from inventory
function calculatePRTByInventory($inventoryItem)
{
    $P_quantity = 0;
    $R_quantity = 0;
    $T_quantity = 0;

    if (isset($inventoryItem['unit_type']) && isset($inventoryItem['quantity'])) {
        $unitType = $inventoryItem['unit_type'];
        $quantity = $inventoryItem['quantity'];

        if (strpos($unitType, "PAL") !== false) {
            $pal = trim(str_replace('PAL', '', $unitType));
            $pal = ($quantity / $pal);
            $P_quantity += $pal;
        } else if (strpos($unitType, "RL") !== false) {
            $rl = trim(str_replace('RL', '', $unitType));
            $rl = ($quantity / $rl);
            $R_quantity += $rl;
        } else if (strpos($unitType, "EACH") !== false) {
            $T_quantity += $quantity;
        }
    }

    return compact('P_quantity', 'R_quantity', 'T_quantity');
}


// Get waypoins distance from google api
function getDistances($origin, $destination, $waypoints)
{
    $truckHeight = env('TRUCK_HEIGHT');
    $truckWeight = env('TRUCK_WEIGHT');
    $apiKey = env('HERE_APP_KEY');
    $url = "https://router.hereapi.com/v8/routes?origin={$origin}&destination={$destination}&transportMode=truck&return=summary&truck[height]={$truckHeight}&truck[weight]={$truckWeight}&apikey={$apiKey}";
    // Only add waypoints if they exist in lat,lng format
    if (!empty($waypoints)) {
        $waypointsString = implode('&via=', array_map('urlencode', explode('|', $waypoints)));
        $url .= "&via={$waypointsString}";
    }

    // Make a request to HERE Routing API
    $response = Http::get($url);
    return $response->json();
}

/** Calculate created time */
function humanTiming ($time)
{

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => ' year',
        2592000 => ' month',
        604800 => ' week',
        86400 => ' day',
        3600 => 'h',
        60 => 'm',
        1 => 's'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.''.$text.(($numberOfUnits>1)?'':'');
    }

}




 function getDieselPrice()
 
    {
        $apiUrl = 'https://api.eia.gov/v2/petroleum/pri/gnd/data/';
        $apiKey = 'AXvXoo6c0cqutRgmYZY79SM7hhhZ#ZFSFD3gfjhgj';

        try {
            $response = Http::get($apiUrl, [
                'frequency' => 'weekly',
                'data[0]' => 'value',
                'facets[duoarea][]' => 'R1Z',
                'facets[product][]' => 'EPD2D',
                'sort[0][column]' => 'period',
                'sort[0][direction]' => 'desc',
                'offset' => 0,
                'length' => 10,
                'api_key' => $apiKey,
            ]);

            $data = $response->json();

            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
/** Create Active Log 
 * params Creator_id, actioin
*/
if (!function_exists('insertActiveLog')) {

    function insertActiveLog($userId,$action, $eventType, $shipment_id, $eventDescription, $routeId){
        $createlog = ActivityLog::logActivity($userId,$action,$eventType, $shipment_id, $eventDescription, $routeId);
    }

}


 // Create way points query format for google apis
 function convertArrayToQueryString($waypoints)
 {
     $formattedLocations = [];

     foreach ($waypoints as $location) {
         $formattedLocation = implode(',', array_map('urlencode', explode(',', $location)));
         $formattedLocations[] = $formattedLocation;
     }
     $queryString = implode('|', $formattedLocations);
     $queryString = str_replace(',,', ',', $queryString);
     return $queryString;
 }
