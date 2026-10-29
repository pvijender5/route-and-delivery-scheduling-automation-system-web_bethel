<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\AccumeticaDataController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\ShipmentDeliveryDetailsProofController;
use App\Http\Controllers\Api\ShipmentDeliveryController;
use App\Http\Controllers\Api\MerchandiserAssignedLocationController;
use App\Http\Controllers\Api\MerchandiserDetailController;
use App\Http\Controllers\Api\LoadboardRouteController;
use App\Http\Controllers\Api\RoutePauseLogController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\CustomerLocationsFhfNotesController;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

//Broadcast::routes(['middleware'=>['auth-sanctum']]);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
   
    Route::post('login', 'login'); 
    Route::post('forgotPassword', 'forgotPassword');
    Route::post('otpVerification','otpVerification');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/routes', [RouteController::class, 'getRouteDetails']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::get('customers',[CustomerController::class, 'index']);
    Route::get('shipments',[ShipmentController::class, 'index']);
    Route::post('/shipments/{id}',[ShipmentController::class, 'updatePropsNotes']);
    // Route::get('/routes', [RouteController::class,'index']);
    Route::get('/getRoutes', [RouteController::class, 'index']);
    Route::get('/usersWorkDiary', [UsersController::class, 'userWorkDiary']);
    Route::get('/carrierOwnerWorkDiary', [UsersController::class, 'carrierOwnerWorkDiary']);
    Route::post('/workDiarySendEmail', [UsersController::class, 'workDiarySendEmail']);
    Route::get('/userDetails', [UsersController::class, 'getUserDetails']);
    Route::get('/merchandiserCities', [UsersController::class, 'getMerchandiserCities']);

    Route::get('/getRoutes/{id}', [RouteController::class, 'routeDetails']);
    Route::put('/completeRouteLoading/{id}', [RouteController::class, 'updateRoute']);
    Route::put('/completeShipmentLoading/{id}', [ShipmentDeliveryController::class, 'completeShipmentLoading']);
    Route::get('/getDeliveryDetails/{id}', [RouteController::class, 'deliveryDetails']);
    // search route 
    //Route::get('/routes', [RouteController::class, 'getRouteDetails']);
    Route::get('/getRoutesByDriverId/{id}', [RouteController::class, 'getRoutesByDriverId']);
    Route::get('/merchandiserAssignedShipments/{id}', [MerchandiserAssignedLocationController::class, 'merchandiserAssignedShipments']);


    Route::get('loadboardRoutes', [LoadboardRouteController::class, 'loadboardRoutes']);
    Route::get('acceptLoadboardRoutes', [LoadboardRouteController::class, 'fetchAcceptedRoutes']);
    Route::put('accepetLoadboardRoutes', [LoadboardRouteController::class, 'accepetLoadboardRoutes']);
    Route::put('assignedLoadboardRoutes/{id}', [LoadboardRouteController::class, 'assignedLoadboardRoutes']);
    Route::put('assignedDriverToRoutes', [LoadboardRouteController::class, 'assignedDriverToRoutes']);
    Route::get('truckDetails', [LoadboardRouteController::class, 'truckDetails']);

    Route::get('carrierOwnerAndSubdrivers/{carrierOwnerId}', [LoadboardRouteController::class, 'carrierOwnerAndSubdrivers']);
    Route::post('saveRoutePauseLog',[RoutePauseLogController::class, 'saveRoutePauseLog']);
    Route::get('getRoutesShipmentNotesByDriver', [RouteController::class, 'getRoutesShipmentNotesByDriver']);

    // Pickup Orders
    Route::get('/pickupShipments', [ShipmentController::class, 'pickupShipments']);
    Route::get('/pickupShipmentDetails/{id}', [ShipmentController::class, 'pickupShipmentDetails']);
    Route::get('/getShipmentInventory/{shipmentId}', [ShipmentController::class, 'getShipmentInventory']);
    Route::get('/getRouteShipmentInventory/{routeId}', [RouteController::class, 'getRouteShipmentInventory']);

    Route::put('resequenceShipments', [RouteController::class, 'resequenceShipments']);

    Route::post('getShipmentsEta', [RouteController::class, 'getShipmentsEta']);

    Route::post('/customerLocationsfhfNotes', [CustomerLocationsFhfNotesController::class, 'store']);

    Route::post('completeShipmentDelivery', [ShipmentDeliveryDetailsProofController::class, 'store']);
    //Cancel Shipment from route
    Route::put('/cancelShipmentFromRoute/{id}',[ShipmentDeliveryController::class,'cancelShipmentFromRoute']);
    // Route::put('/cancelShipmentFromRoute/{id}', function ($id) {
    // dd($id);
// });

    Route::get('/getSuperAdminnDetails',[UsersController::class,'getSuperAdminnDetails']);

    Route::post('startRoute', [RouteController::class, 'startRoute']);

    Route::get('/loaderWarehouseAssignRoutes', [RouteController::class, 'loaderWarehouseAssignRoutes']);

    Route::post('completeInventoryLoading', [RouteController::class, 'completeInventoryLoading']);
    Route::post('cancelInventoryLoading', [RouteController::class, 'cancelInventoryLoading']);
    Route::get('/usernotifications',[AuthController::class,'userNotifications']);
    Route::get('/myloads',[ShipmentDeliveryController::class,'myLoads']);
    Route::post('/completeRoute', [RouteController::class, 'completeRoute']);
    Route::post('/readshipmentbyuser',[ShipmentController::class,'readShipmentByUser']);
    Route::post('/readnotification',[AuthController::class,'readNotification']);
    Route::post('/readroutebyuser', [ShipmentDeliveryController::class, 'readRouteByUser']);
    Route::get('/userbubblecount',[AuthController::class,'userBubbleCount']);
    Route::post('/markallloaded', [RouteController::class, 'markAllLoaded']);


    
});


Route::post('/sendlocation',[ShipmentDeliveryController::class,'sendLocation']);
Route::post('/pushShipments',[AccumeticaDataController::class, 'pushShipments']);
Route::post('/sendlocation2',[AccumeticaDataController::class, 'sendLocation2']);
Route::post('/pushCustomers',[AccumeticaDataController::class, 'pushCustomers']);
Route::post('/pushEmployees',[AccumeticaDataController::class, 'pushEmployees']);
Route::post('/pushTrucks',[AccumeticaDataController::class, 'pushTrucks']);
Route::post('/pushWarehouses',action: [AccumeticaDataController::class, 'pushWarehouses']);
Route::post('/pushCustomerLocations',[AccumeticaDataController::class, 'pushCustomerLocations']);
Route::post('/pushProductCategories',[AccumeticaDataController::class, 'pushProductCategories']);
Route::post('/pushProducts',[AccumeticaDataController::class, 'pushProducts']);
Route::post('/pushDeliveries',[AccumeticaDataController::class, 'pushDeliveries']);
Route::get('/updateInventoryPRT',[AccumeticaDataController::class,'updateInventoryPRT']);
Route::get('/testPushNotification',[ShipmentDeliveryController::class,'testPushNotification']);
Route::get('/users/work-diary/{id}/download', [UsersController::class, 'workDairyPdf']);
Route::get('/users/carrier-owner/work-diary/{id}/download',[UsersController::class,'carrierOwnerWorkDairyPdf']);
Route::delete('/shipments',[AccumeticaDataController::class,'deleteShipment']);

// Acumetica Integration Routes
Route::prefix('acumetica')->name('acumetica.')->group(function () {
    Route::post('shipments/push', [AccumeticaDataController::class, 'pushShipments'])->name('shipments.push');
    Route::post('customers/push', [AccumeticaDataController::class, 'pushCustomers'])->name('customers.push');
    Route::post('employees/push', [AccumeticaDataController::class, 'pushEmployees'])->name('employees.push');
    Route::post('trucks/push', [AccumeticaDataController::class, 'pushTrucks'])->name('trucks.push');
    Route::post('warehouses/push', [AccumeticaDataController::class, 'pushWarehouses'])->name('warehouses.push');
    Route::post('customer-locations/push', [AccumeticaDataController::class, 'pushCustomerLocations'])->name('customer-locations.push');
    Route::post('product-categories/push', [AccumeticaDataController::class, 'pushProductCategories'])->name('product-categories.push');
    Route::post('products/push', [AccumeticaDataController::class, 'pushProducts'])->name('products.push');
    Route::post('deliveries/push', [AccumeticaDataController::class, 'pushDeliveries'])->name('deliveries.push');
    Route::get('inventory/update-prt', [AccumeticaDataController::class, 'updateInventoryPRT'])->name('inventory.update-prt');
    Route::delete('shipments', [AccumeticaDataController::class, 'deleteShipment'])->name('shipments.delete');
});

// Shipment Routes
Route::prefix('shipments')->name('shipments.')->group(function () {
    Route::post('location', [ShipmentDeliveryController::class, 'sendLocation'])->name('location.send');
    Route::post('location2', [AccumeticaDataController::class, 'sendLocation2'])->name('location2.send');
});

// User Routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('work-diary/{id}/download', [UsersController::class, 'workDiaryPdf'])->name('work-diary.download');
    Route::get('carrier-owner/work-diary/{id}/download', [UsersController::class, 'carrierOwnerWorkDiaryPdf'])->name('carrier-owner.work-diary.download');
});

// Notification Routes
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('test', [ShipmentDeliveryController::class, 'testPushNotification'])->name('test');
});


