<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Constants\Commonconstant;
use App\Models\SettingSmsTemplate;
use App\Models\Routes;
use DateTime;

class SendScheduledSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a scheduled SMS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $currentDate = now();
            $startDate = $currentDate->startOfDay();
            $routes = Routes::whereNotNull('assigned_driver')->where('delivery_date', $startDate)
                    ->with([
                        'shipmentDeliveries',
                        'shipmentDeliveries.shipment' => function ($query) {
                            $query->select('id', 'order_id', 'props_delivery_window');
                        },
                        'shipmentDeliveries.shipment.shipmentCustomerDeliveryLocations',
                        'driver.driverDetail'
                    ])->get();
                    
            $smsTemp = SettingSmsTemplate::where('name', Commonconstant::SMSMODULENAME['DRIVER_ASSIGNED'])->where('status', '=', 'active')->value('description');
            if(isset($routes) && isset($smsTemp)) {
                foreach($routes as $route) {
                    $date = new DateTime($route->delivery_date);
                    // Format the date as m-d-Y
                    $delivery_date = $date->format('m-d-Y');

                    $driver_name = $route->driver->name;
                    foreach($route->shipmentDeliveries as $result){
                        $data = $result->shipment->shipmentCustomerDeliveryLocations;
                        $timeArray = $result->shipment->props_delivery_window;
                        $delivery_window = '';
                        if (isset($timeArray[0]['start']) && isset($timeArray[0]['end'])) {
                            $startTime = gettimewithtimezone($timeArray[0]['start']);
                            $endTime = gettimewithtimezone($timeArray[0]['end']);
                            $delivery_window = $startTime . ' - ' . $endTime;
                        }                        
                        $order_id = $result->shipment->order_id;
                        $delivery_contact_phone = $data->delivery_contact_phone;
                        $delivery_location_name = $data->delivery_location_customer;
                        $appName =getenv("APP_NAME");
                        $customerServicePhoneNo = getenv("CUSTOMER_SERVICE_PHONE_NO");   
                        $customerServiceEmail = getenv("CUSTOMER_SERVICE_EMAIL");                    
                        $tags = array("[CustomerName]", "[deliveryDate]", "[deliveryWindow]", "[driverName]", "[orderId]", "[customerServicePhoneNo]", "[customerServiceEmail]", "[appName]");
                        $values = array($delivery_location_name, $delivery_date, $delivery_window, $driver_name, $order_id, $customerServicePhoneNo, $customerServiceEmail, $appName);
                        $smsTemplate = strip_tags($smsTemp);
                        $message = str_replace($tags, $values, $smsTemplate);
                        if($delivery_contact_phone){
                            $cleanedPhoneNumber = preg_replace('/[^0-9]/', '', $delivery_contact_phone);
                            Log::info($cleanedPhoneNumber, $values);
                            sendMobileSms($cleanedPhoneNumber, $message);
                        }
                    }
                }
            } else {
                $this->info('No Data found.');
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }    
    }
}
