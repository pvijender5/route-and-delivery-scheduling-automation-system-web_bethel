<?php
namespace App\Models;


use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\Roles;
use App\Constants\Commonconstant;
use Carbon\Carbon;
use App\Models\RateSetting;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ShipmentConsolidation;
use App\Models\SettingSmsTemplate;
use Log;
use Illuminate\Support\Str;

class Routes extends Model
{

    use SoftDeletes;
    use HasFactory;
    protected $table = 'routes';
    protected $fillable = [
                            'delivery_date',
                            'total_stops',
                            'origin_points',
                            'last_dropoff_location',
                            'fork_lift',
                            'total_quantity',
                            'planned_departure_time',
                            'loaded_duration',
                            'total_duration',
                            'total_distance',
                            'loaded_distance',
                            'total_load',
                            'recommended_price',
                            'total_price',
                            'loading_by',
                            'assigned_driver',
                            'assigned_truck',
                            'loading_complete',
                            'status',
                            'is_paused',
                            'P_quantity',
                            'object_id',
                            'acumatica_id',
                            'R_quantity',
                            'T_quantity',
                            'delivery_instruction',
                            'lock_status'
                          ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'assigned_truck');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver');
    }
    public function shipmentDeliveries()
    {
        // set relationsip for latest record
        return $this->hasMany(ShipmentDeliveryDetail::class, 'route_id','id');

    }
    public function routesPauseLogs()
    {
        return $this->hasMany(RoutesPauseLogs::class, 'route_id');

    }
    
    /**function to add 
     * filter by assigned_driver 
     * and delivery_status column and get 
     * data from route and 
     * shipment_delivery_details table

     */

    public function scopeFilterRoutes($query, $request)
    {
        // add condion for loader
         $query->where('loading_by', 'loader');
         
         $query->whereIn('status', [Roles::LOADING, Roles::LOADED, Roles::DRIVER_ASSIGNED]);
        
        // Apply filters based on driverId if provided in the request
        if ($request->has('assigned_driver')) {
            $query->where('assigned_driver', $request->input('assigned_driver'));
        }

        // Apply filter for 'status' column in the 'routes' table
        if (!empty($request->get('status'))) {
            $query->where('status', $request->input('status'));
        }

        // Apply filter for 'delivery_date' column in the 'routes' table
        if (!empty($request->get('delivery_date'))) {
            $query->where('delivery_date', $request->input('delivery_date'));
        }

        // $query->with('shipmentDeliveries');

        // Apply a filter for 'delivery_status' from 'shipment_delivery_details'
        if (!empty($request->get('delivery_status'))) {
            $query->whereHas('shipmentDeliveries', function ($subQuery) use ($request) {
                $subQuery->where('delivery_status', $request->input('delivery_status'));
            });
        }


        $query->where(function ($subQuery) use ($request) {
            if ($request->get('route_type') === 'active') {
                $subQuery->whereIn('status', ['in-transit-delay', 'in-transit-arrived', 'in-transit-on-time', 'in-transit-cancelled']);
            } elseif ($request->get('route_type') === 'assigned') {
                $subQuery->whereIn('status', ['loading', 'loaded' , 'driver-assigned']);
            }
        });


        if (!empty($request->get('search'))) {
            // You can add additional search logic here if needed.
            // $query->where(...);
        }
        
        $query->with('shipmentDeliveries', 'driver.driverDetail', 'truck');

        return $query;
    }
    /**
     * insert Routes Record for shipments
     */
    public static function insertRoutes($request)
    {
    try{
        $elements = ($request['routeData']['elements']);
        $shipments = $request['shipmentData']['id'];
        $address = $request['routeData']['address'];
        $originAddress=$address[0];
        $originAddress = str_replace('+', ' ', $originAddress);

         $routeIds = []; 
        //$route_no=1;
        foreach ($elements as $index => $element) {
            if ($element['totalLoad'] > 0) {
                // remove warehouses from array
                $routearray = array_slice($element['route'], 1, -1);
                $loadarray = array_slice($element['load'], 1, -1);
                $timearray = array_slice($element['time'], 1, -1);
                $distancearray = array_slice($element['distance'], 1, -1);
                // save route data  
                $route = new Routes();
                $route->total_stops = count($routearray);
                $route->total_duration = $element['totalTime'];
                $route->total_load = $element['totalLoad'];
                $route->total_distance = $element['totalDistance'];
                $route->object_id = Str::uuid();
                
                $route->loaded_duration=end($timearray);
                $route->loaded_distance=array_sum($distancearray);
                $route->save();
                $routeIds[] = $route->id;

                $RouteLength=$route->total_stops;

                // save shipment delivery detail data  
               foreach ($routearray as $indexroute => $elementroute) {
                    $indexShiment=($elementroute-1);
                    $deliveryDetail = ShipmentDeliveryDetail::where('shipment_id', $shipments[$indexShiment])->orderBy('id', 'desc')->first();
                    if($deliveryDetail)
                    {
                        $deliveryDetail->route_no = $RouteLength;
                        //$deliveryDetail->route_no = $indexroute+1;
                        $deliveryDetail->route_id = $route->id;
                        $deliveryDetail->delivery_status = Roles::PLANNED;
                        $deliveryDetail->load=$loadarray[$indexroute];
                        $deliveryDetail->duration=$timearray[$indexroute];
                        //$deliveryDetail->task_object_id = Str::uuid();
                        $deliveryDetail->eta=$timearray[$indexroute];
                        $deliveryDetail->distance=$distancearray[$indexroute];
                        $result = $deliveryDetail->save();  

                    }
                    $RouteLength--;
                }
                

                // save PRT Data for routes by adding all PRT values
                
                //Get sum of PRT values
                $PRTSums = ShipmentDeliveryDetail::selectRaw('SUM(P_quantity) as sumPQuantity, SUM(R_quantity) as sumRQuantity, SUM(T_quantity) as sumTQuantity')->where('route_id', $route->id)->first();
                $Shipment= Shipment::whereIn('id', $shipments)->get();
                $warehouse_id = $Shipment->first()->warehouse_id;
                //Get sum of shipments quantity
                $QuantitySums = ShipmentPropsDeliveryInventory::selectRaw('SUM(quantity) as quantity')->whereIn('shipment_id', $shipments)->first();
                

                if($Shipment)
                { 
                   // Get forklift 
                    $forklift = 0;
                    foreach ($Shipment as $value) {
                        if ($value->fork_lift==1) {
                            $forklift = 1;
                            break;
                        }
                    }

                   // get warehouse name
                   $warehouse= Warehouse::where('id', $warehouse_id)->first();
                   $warehouseName="N/A";
                   if($warehouse)
                   {
                    $warehouseName=$warehouse->name.", ".$originAddress;
                   }
                   /////////////////////
                  
                   // Get last Dropoff Delivery Location
                   $lastDropShipmentId = ShipmentDeliveryDetail::where('route_id', $route->id)->with('shipmentCustomerDeliveryLocations')->orderBy('route_no', 'desc')->first();
                    if($lastDropShipmentId && isset($lastDropShipmentId->shipmentCustomerDeliveryLocations))
                   {
                    $routeArrayLastElement=end($routearray);
                    $addressOfLastdropoff=$address[$routeArrayLastElement];
                    $addressOfLastdropoff= str_replace('+', ' ', $addressOfLastdropoff);
                    $lastDropoffLocation=$lastDropShipmentId->shipmentCustomerDeliveryLocations->delivery_location_name;
                    $lastDropoffLocation=$lastDropoffLocation.", ".$addressOfLastdropoff;
                   }else
                   {
                    $lastDropoffLocation= "N/A";
                   }
                   //////////////////////////////////////

                   //////// Generate random values for red, green, and blue components////////
                    $red = mt_rand(0, 127);
                    $green = mt_rand(0, 127);
                    $blue = mt_rand(0, 127);
                    // Format the values as a hexadecimal color code
                    $colorCode = sprintf("#%02X%02X%02X", $red, $green, $blue);
                    ////////////////////////////////////////////////////////////////////////////
                                    
                   // Update routes values 
                    Routes::where('id', $route->id)
                    ->update([
                        'origin_points' => $warehouseName,
                        'last_dropoff_location'=>$lastDropoffLocation,
                        'P_quantity' => $PRTSums->sumPQuantity,
                        'R_quantity' => $PRTSums->sumRQuantity,
                        'T_quantity' => $PRTSums->sumTQuantity,
                        'delivery_date' =>$Shipment[0]->route_Date,
                        'planned_departure_time' =>$Shipment[0]->route_Date." 09:00:00", // add 9 am time, will come from env file
                        'fork_lift' => $forklift,
                        'total_quantity'=>$QuantitySums->quantity,
                        'color'=>$colorCode
                    ]);

                    // update durantion and eta according to google api response
                    app('App\Http\Controllers\Admin\RoutesController')->updateRoutesDistanceAndDurations($route->id);
                    ///////////////////////////////////////////////////////////


                     // Update remmended price of route
                     RateSetting::updateRecommendedPriceRoute($route->id);
                     /////////////////////////////////

                    //Update route Id in shipment_consolidations table
                    foreach ($Shipment as $values) {
                        if($values->consolidation_id) {
                            ShipmentConsolidation::where('consolidation_id', $values->consolidation_id)->update(['route_id' => $route->id]);
                        }
                    }
                    $user = Auth::user();
                    $event_message = "The " . $user->role->name . " " . $user->name . " created a new route <a href='".url('/routes').'/'.'map?selected='.$route->id."'>R".$route->id."</a>";
                    $message = "The " . $user->role->name . " " . $user->name . " created a new route R".$route->id.".";
                    self::sendnotification($message,$route->id,$warehouse_id);
                    insertActiveLog($user->id,'Create Route','create-route', $shipment_id=null, $event_message, $route->id);

                } 
                
            }
        }
        // dd($routeIds);
        return $routeIds;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again later.']);
            //return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        

    }
    /** Get Routes data according to status */
    public static function routesData($status,$request=null){
        $statusArrayShiment = Roles::ACTIVESHIPMENTS_ARRAY;
        if(isset($request) && $request!=null && $request->ajax()){
            $whereHouseId = $request->wherehouse_id;
            $routeStatus = $request->status;
            $searchInput = $request->searchInput;
            if ($request->delivery_date) {
                $date = Carbon::parse($request->delivery_date);
            } else {
                $date = $request->delivery_date;
            }
            $routes = Routes::whereHas('shipmentDeliveries', function ($query) use ($statusArrayShiment) {
                $query->whereIn('delivery_status', $statusArrayShiment);
            })
            ->has('shipmentDeliveries.shipment') // Fetch routes with at least one shipmentDeliveries record   
            ->when($date, function ($query) use ($date) {
                return $query->where('delivery_date', $date);
            })
            ->when($whereHouseId, function ($query) use ($whereHouseId) {
                $whereHouseId=explode(",",$whereHouseId);
                return $query->whereHas('shipmentDeliveries.shipment', function ($query) use ($whereHouseId) {
                   // whereIn('status', [Roles::DELIVERED, Roles::COMPLETED])
                    return $query->whereIn('warehouse_id', $whereHouseId);
                });
            })
            ->when($routeStatus, function ($query) use ($routeStatus) {
                return $query->where('status', $routeStatus);
            })
            ->when($searchInput, function ($query) use ($searchInput) {
                if (strtoupper(substr($searchInput, 0, 1)) == 'R') {
                    $search = substr($searchInput, 1);
                    return $query->Where('id', 'like', '%' . $search . '%');
                } else {
                    return $query->whereHas('shipmentDeliveries.shipment', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%')->orWhere('warehouse_id', 'like', '%' . $searchInput . '%')
                            ->orWhere('customer_id', 'like', '%' . $searchInput . '%');
                    })->orWhere('id', 'like', '%' . $searchInput . '%')->orWhere('status', 'like', '%' . $searchInput . '%');
                }
            })
            ->get();
            $routes = $routes->whereIn('status', $status);
        }else{
            $routes = Routes::whereIn('status',$status)
            ->whereHas('shipmentDeliveries', function ($query) use ($statusArrayShiment) {
                $query->whereIn('delivery_status', $statusArrayShiment);
            })        
            ->has('shipmentDeliveries.shipment') // Fetch routes with at least one shipmentDeliveries record
            ->orderBy('id', 'desc')
            ->get();
           // dd($routes);
        }
       

       
        $status=Roles::ACTIVESHIPMENTS_ARRAY;
        $route_data = [];
        foreach ($routes as $key => $val) {
           $val = self::getShimpmentDeliveryObject($status, $val->id,$val);
        }
        return $routes;
    }
    
    public static function routesReportData($request=null){
        $routesData = [];
        $routes = [];
        if(isset($request) && $request!=null && $request->ajax()) {            
            if($request->startDate!="" && $request->endDate!="") {
                $inputCarbon = Carbon::createFromFormat('m/d/Y', $request->startDate);
                $startDate = $inputCarbon->format('Y-m-d 00:00:00');
    
                $inputCarbon1 = Carbon::createFromFormat('m/d/Y', $request->endDate);
                $endDate = $inputCarbon1->format('Y-m-d 00:00:00');
                $routes = Routes::whereBetween('delivery_date', [$startDate, $endDate])->whereIn('status', [Roles::DELIVERED, Roles::COMPLETED])->with([
                    'shipmentDeliveries.shipment.warehouse',
                    'driver.driverDetail',
                    'routesPauseLogs'
                ])
                ->orderBy('id', 'asc')
                ->get();

            } else {
                $routes = Routes::whereIn('status', [Roles::DELIVERED, Roles::COMPLETED])->with([
                    'shipmentDeliveries.shipment.warehouse',
                    'driver.driverDetail',
                    'routesPauseLogs'
                ])
                ->orderBy('id', 'asc')
                ->get();

            }    
        } else {
            $routes = Routes::whereIn('status', [Roles::DELIVERED, Roles::COMPLETED])->with([
                'shipmentDeliveries.shipment.warehouse',
                'driver.driverDetail.user.role',
                'routesPauseLogs'
            ])
            ->orderBy('id', 'desc')
            ->get();
        }
        if($routes) {
            $totalRoutes = $routes->count();
            $onTimeDeliveries = 0;
            $totalDeliveryPrice = 0;
            $totalShipments = 0;
            $totalDelayedDeliveries = 0;
            $subDriverRoutes = 0;
            foreach ($routes as $route) {
                if(isset($route->driver) && isset($route->driver->driverDetail) && isset($route->driver->driverDetail->user))
                {
                    if (isset($route->driver->driverDetail->user->role_id) && $route->driver->driverDetail->user->role_id == Roles::CARRIEROWNER_ID || $route->driver->driverDetail->user->role_id == Roles::SUBDRIVER_ID){
                        $totalDeliveryPrice += $route->total_price;
                        $subDriverRoutes++;
                    }
                }

                foreach ($route->shipmentDeliveries as $shipmentDelivery) {
                    $totalShipments += 1;
                    if ($shipmentDelivery->on_time_delivery == 1) {
                        $onTimeDeliveries += 1;
                    } else if($shipmentDelivery->on_time_delivery == 0) {
                        $totalDelayedDeliveries += 1;
                    }
                }
            }
            // Calculate averages
            $perentOnTimeDeliveries = 0; 
            $perentDelayedDeliveries = 0;    
            $averageDeliveryPrice = 0;
            if($totalShipments != 0){
                $perentOnTimeDeliveries = ($onTimeDeliveries/$totalShipments)*100;
                $perentDelayedDeliveries = ($totalDelayedDeliveries / $totalShipments)*100;
            }
            if($totalRoutes != 0 && $subDriverRoutes>0){
                $averageDeliveryPrice = $totalDeliveryPrice / $subDriverRoutes;
            }
            return $result = [
                        'routes' => $routes,
                        'totalRoutes' => $totalRoutes,
                        'perentOnTimeDeliveries' => $perentOnTimeDeliveries,
                        'averageDeliveryPrice' => $averageDeliveryPrice,
                        'totalShipments' => $totalShipments,
                        'perentDelayedDeliveries' => $perentDelayedDeliveries, 
                    ];
        } 
    }    

    /** Single route data according to route_id */

    public static function routeById($route_id, $shipmentStatus=NULL){
        $item_address = [];
        $ids = [];
        $load = 0;
        $fork_lift = [];
        $routes = Routes::where('id',$route_id)
            ->with('shipmentDeliveries.shipment')
            ->has('shipmentDeliveries.shipment') // Fetch routes with at least one shipmentDeliveries record
            ->first();
        
        if($routes)
        {
            if($shipmentStatus!=NULL)
            { 
                $status= $shipmentStatus;
            }else
            {
                $status=Roles::ACTIVESHIPMENTS_ARRAY;
            }

            $shipment = self::getShimpmentDeliveryObject($status, $route_id,$routes);
            return $shipment;
        }
           
           
    }

    /** route item details */

    public static function getShimpmentDeliveryObject($status,$id,$val){
        $item_address = [];
        $name = [];
        $ids = [];
        $shipment_ids = [];
        $load = 0;
        $fork_lift = [];
        $name = [];
        $props_weight = [];
        $duration = [];
        $prt = [];
        $fork_lift=[];
        $shipment_detail = ShipmentDeliveryDetail::getShipmentDeliveryByStatus($status, $id);
        if($shipment_detail){
            foreach ($shipment_detail as $k => $v) {
                if($v->delivery_status!=Roles::CANCELED)
                {
                    $item_address[] = (isset($v->shipment->shipmentCustomerDeliveryLocations)) ?  $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_address_line_1 . ',' . $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_address_line_2 . ',' . $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_city . ',' . $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_state . ',' .
                    $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_zipcode . ',' . $v->shipment->shipmentCustomerDeliveryLocations->delivery_location_country : '';
                    // $item_address['start'] = (isset($v->shipment->warehouse)) ? $v->shipment->warehouse->address . ',' . $v->shipment->warehouse->city . ',' . $v->shipment->warehouse->state : '';
                    $load = $load + $v->load;
                    $ids[] = $v->id;
                    $shipment_ids[] = $v->shipment_id;
                    $fork_lift[] = (isset($v->shipment->fork_lift)) ? $v->shipment->fork_lift :0;
                    $name[]=$v->shipment->name;
                    $props_weight[]=$v->shipment->props_weight;
                    $duration[]=$v->duration;
                    $prt[]="P:".$v->P_quantity." R:".$v->R_quantity." T:".$v->T_quantity;

                }
            }
            $wherehose_id = (isset($shipment_detail[0]->shipment->warehouse)) ? $shipment_detail[0]->shipment->warehouse->id : 0;
            $val->wherehouse_id = (isset($wherehose_id)) ? $wherehose_id : '' ;
            $val->start = (isset($shipment_detail[0]->shipment->warehouse)) ? $shipment_detail[0]->shipment->warehouse->address . ',' . $shipment_detail[0]->shipment->warehouse->city . ',' . $shipment_detail[0]->shipment->warehouse->state. ',' . $shipment_detail[0]->shipment->warehouse->zipcode : '';
            $val->item_address = $item_address;
            $val->ids=$ids;
            $val->shipment_ids=$shipment_ids;
            $val->name = $name;
            $val->props_weight = $props_weight;
            $val->props_weight_total = array_sum($props_weight);
            $val->duration = $duration;
            $val->prt = $prt;
            //$val->fork_lift = $fork_lift;
            if(in_array(1,$fork_lift)){
                self::updateforkLift(1,$val->id);
            }else{
                self::updateforkLift(0,$val->id);
            }
           // $val->fork_lift = $fork_lift;
            $val->load = ($load != 0) ? round($load * 0.00062137, 2) : 0;
        }
            return $val;
    }
    /** 
     * Update forklift based on route id
     */
    public static function updateforkLift($lift_id,$id){
        return Routes::where('id',$id)->update(['fork_lift'=>$lift_id]);
    }
    /**
     * Global serach For route
     */
    public static function globalsearch($search){
        $routes = self::select('id','status')->Where('id', 'like', '%' . $search . '%')->whereHas('shipmentDeliveries')->get();
        if($routes){
            foreach($routes as $key=>$val){
                $val->name = 'R'.$val->id; 
                if(in_array($val->status,Roles::PLANROUTESTATUS)){
                    $val->route_url = 'map';
                }else{
                    $val->route_url = 'trackRoute';
                }
            }
        }
        return $routes;
    }

    /**
     * Send notification to loader
     */
    public static function sendnotification($message, $route_id, $warehouse_id){
        $allLoaderIds = [];
        $jsonData = [];
        $user = Auth::user();
        $allloaduser = WarehouseTeamDetail::where('warehouse_id' ,$warehouse_id)->get('team_member');
        if ($allloaduser->isNotEmpty()) {
            foreach ($allloaduser as $key => $value) 
            {
                $userdata = User::getUserbyId($value->team_member);
                $allLoaderIds[] = $value->team_member;
                if ($userdata->device_token) {
                    pushNotification($userdata->device_token, $userdata->deviceType, $message, 'Route-create');
                }               
            }
        }
        if (!empty($allLoaderIds)) {
            $jsonData = json_encode($allLoaderIds);
        } else {
            $jsonData = null;
        }
        $notification = createNotification($message, $user->id, 'create-route', 'route', $shipment_id = null, $route_id, $jsonData);
 
    }
    
    /** Update route status as well as eta of shipment */

    public static function updateShipmentETA($etaData){
        $route = Routes::where('id',$etaData['route_id'])->first();
        if($route)
        {
            if(isset($etaData['route_status']) && $etaData['route_status']!="" &&  $route->status!=Roles::DELIVERED && $route->status!=Roles::COMPLETED)
            {
                $route->status=$etaData['route_status'];
                $route->save();
            }
            
            // Update shipment ETA value
            if(isset($etaData['eta']) && $etaData['eta']!="")
            {
                foreach ($etaData['eta'] as $index => $element) {
                    $shipmentDeliveryDetailData = ShipmentDeliveryDetail::where(['id' => $element['id'], 'route_id' => $etaData['route_id']]);
                    $shipmentDeliveryDetailData->update(['eta' => $element['eta']]);
                    $messageSend = $shipmentDeliveryDetailData->first();
                    $dataReport = $messageSend->shipment->shipmentCustomerDeliveryLocations;
                    $smsTemp = SettingSmsTemplate::where('name', Commonconstant::SMSMODULENAME['IN_TRANSIT_ON_TIME'])->where('status', '=', 'active')->value('description');
                    //For send sms to customer before 25 minuts of delivery
                    if($messageSend->customer_message_sent == false && $element['eta'] <= 25 && $dataReport->delivery_contact_phone && $smsTemp) {
                        self::sendSms($element, $etaData, $messageSend, $dataReport, $smsTemp);
                    }
                }
            }
        }
    }

    //For send sms to customer before 25 minuts of delivery
    public static function sendSms($element, $etaData, $messageSend, $dataReport, $smsTemp) {
        $order_id = $messageSend->shipment->order_id;
        $delivery_contact_phone = $dataReport->delivery_contact_phone;
        $delivery_location_name = isset($dataReport->delivery_contact_name) ? $dataReport->delivery_contact_name : 'customer';
        $eta = $element['eta'];
        $customerServicePhoneNo = getenv("CUSTOMER_SERVICE_PHONE_NO");   
        $customerServiceEmail = getenv("CUSTOMER_SERVICE_EMAIL");
        $appName =getenv("APP_NAME");
        $tags = array("[CustomerName]", "[orderId]", "[customerServicePhoneNo]", "[beforeDeliveryMinutes]","[customerServiceEmail]", "[appName]");
        $values = array($delivery_location_name, $order_id, $customerServicePhoneNo, $eta ,$customerServiceEmail, $appName);
        $smsTemplate = strip_tags($smsTemp);
        $message = str_replace($tags, $values, $smsTemplate);
        if($delivery_contact_phone){
            $cleanedPhoneNumber = preg_replace('/[^0-9]/', '', $delivery_contact_phone);
            Log::info($cleanedPhoneNumber, $values);
            sendMobileSms($cleanedPhoneNumber, $message);
            ShipmentDeliveryDetail::where(['id' => $element['id'], 'route_id' => $etaData['route_id']])->update(['customer_message_sent' => true]);
        }
    } 
    
    
    public static function updateAcumaticaRouteId($id,$acumatica_id){
        return Routes::where('id',$id)->update(['acumatica_id'=>$acumatica_id]);

    }
}