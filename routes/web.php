<?php

use App\Http\Controllers\Admin\CarrierownerController;
use App\Http\Controllers\Admin\GlobelsearchController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RoutesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\PickupOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\TruckController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingSmsTemplateController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\LoaderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CustomerLocationController;
use App\Http\Controllers\Admin\SystemReportController;
use App\Http\Controllers\Admin\RateSettingController;
use App\Http\Controllers\Admin\DeliveryNotificationController;
use App\Http\Controllers\Admin\FuelPriceController;
use App\Models\Customer;
use App\Models\Truck;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Driverdetail;

Broadcast::routes(['middleware' => ['web']]);
/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/customers', function () {
    return view('customers.index');
})->middleware(['auth', 'verified'])->name('customers.index');
Route::get('/privacy-policy', [CustomerController::class, 'privacyPloicy'])->name('customers.PrivacyPolicy');


Route::middleware(['auth', 'checkRole:admin,super-admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/datatables', [UserController::class, 'datatables'])->name('users.datatables');
    
    Route::post('/users/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('/users/unlock-status', [UserController::class, 'unlockStatus'])->name('users.unlockStatus');
    Route::post('/users/unlock-driver-status', [UserController::class, 'unlockDriverAccess'])->name('users.unlockDriverAccess');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    
    // Category Routes
    Route::get('categories', [ProductCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories/datatables', [ProductCategoryController::class, 'datatables'])->name('categories.datatables');
    
    Route::post('categories/save', [ProductCategoryController::class, 'saveCategory'])->name('categories.saveCategory');
    Route::post('update-category/{id}', [ProductCategoryController::class, 'updateCategory'])->name('categories.updateCategory');
    
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/datatables', [ProductController::class, 'datatables'])->name('products.datatables');
    
    //Warehouses routes
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/datatables', [WarehouseController::class, 'datatables'])->name('warehouse.datatables');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::get('/warehouse/edit/{id}', [WarehouseController::class, 'edit'])->name('warehouse.edit');
    Route::post('/warehouse/update/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::post('/validate-unique-name', [WarehouseController::class, 'validateUniqueName'])->name('validate.unique.name');
    Route::get('/location', [CustomerLocationController::class, 'index'])->name('customerLocation.index');
    Route::get('/location/datatables', [CustomerLocationController::class, 'datatables'])->name('location.datatables');
    
    //Sysetm reports
    Route::get('/reports', [SystemReportController::class,'index'])->name('reports');
    Route::get('/report/search', [SystemReportController::class, 'searchData'])->name('report/search');
    Route::get('/reports/datatables', [SystemReportController::class, 'datatables'])->name('reports.datatables');
    
});

Route::middleware(['auth', 'checkRole:router,dispatcher,admin,super-admin'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('web');
    Route::get('/customer-view/{id}', [CustomerController::class, 'show'])->name('customers.view');
    Route::get('/proof-of-delivery', [CarrierownerController::class, 'showProof'])->name('carrierowner.proofofdelivery');
    Route::get('/customers/datatables', [CustomerController::class, 'datatables'])->name('customers.datatables');
    Route::get('/fetch-fhf-notes/{customerLocationId}', [CustomerLocationController::class, 'fetchFhfNotes'])->name('fetch-notes');
    Route::get('/fetch-shipments/{shipmentstatus}/{customerId}', [CustomerController::class, 'fetchShipments'])->name('fetch-shipments');
    Route::get('/fetch-driver-shipments/{driverId}', [CarrierownerController::class, 'fetchShipments'])->name('fetch-shipmentssss');
    Route::get('/fetch-route-shipments/{routeId}', [SystemReportController::class, 'fetchShipments'])->name('fetch-route-shipment');
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index'); 

    Route::get('/carrierowner', [CarrierownerController::class, 'index'])->name('carrierowner.index');
    Route::get('/carrierowner/datatables', [CarrierownerController::class, 'datatables'])->name('carrierowner.datatables');
    Route::get('/carrierowner/create', [CarrierownerController::class, 'create'])->name('carrierowner.create');
    Route::post('/carrierowner/store', [CarrierownerController::class, 'store'])->name('carrierowner.store');
    
    Route::post('/carrierowner/storesubdriver/{id}', [CarrierownerController::class, 'storesubdriver'])->name('carrierowner.storesubdriver');
    
    Route::get('/carrierowner/createsubdriver/{id}', [CarrierownerController::class, 'createsubdriver'])->name('carrierowner.createsubdriver');
    Route::get('/addform', [ShipmentController::class, 'create'])->name('shipment.create');
    
    Route::get('/adddriver', [CarrierownerController::class, 'addsubdriver'])->name('carrierowner.addsubdriver');
    
    Route::post('shipments/restore', [ShipmentController::class, 'restore'])->name('shipments.restore');
    Route::patch('shipments/restoreshipment/{id}', [ShipmentController::class, 'restoreshipment'])->name('shipments.restoreshipment');
    
    Route::get('/get-user-info', [ShipmentController::class, 'getUserInfo'])->name('get-user-info');

      Route::post('/check-email-availability', [UserController::class, 'checkEmailAvailability'])
    ->name('check-email-availability');
    
    Route::get('/get-shipments/{id}', [CarrierownerController::class,'shipmentedit']);
    
    // Edit and Update routes
    Route::get('/carrierowner/{id}/edit', [CarrierownerController::class, 'edit'])->name('carrierowner.edit');
    Route::put('/carrierowner/{id}', [CarrierownerController::class, 'update'])->name('carrierowner.update');

     // Define a custom route for creating a new user (GET request)
    Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/validate-unique-user', [UserController::class, 'validateUniqueUser'])->name('validate.unique.user');
    
    Route::get('/users/{id}', [UserController::class, 'getUserDetails']);
    
    Route::post('users', [UserController::class, 'store'])->name('users.store');
        
    Route::get('/pickup-orders', [PickupOrderController::class, 'index'])->name('pickup-orders.index');
    Route::get('/pickup-orders/datatables', [PickupOrderController::class, 'datatables'])->name('pickup-orders.datatables');
    Route::delete('/pickup-orders/delete', [PickupOrderController::class, 'delete'])->name('pickup-orders.delete');
    
    Route::post('/unassign-location', [UserController::class, 'unassignLocation'])->name('users.unassign-location');
    Route::get('/fetch-proof-deilivery/{deliveryDetailId}', [CarrierownerController::class , 'showProof'])->name('carrierowner.getProof');

    Route::get('/trucks', [TruckController::class, 'index'])->name('trucks.index');
    Route::get('/trucks/datatables', [TruckController::class, 'datatables'])->name('trucks.datatables');
    
    Route::post('/save-truck', [TruckController::class, 'saveTruck'])->name('trucks.saveTruck');
    Route::post('/update-truck/{id}', [TruckController::class, 'updateTruck'])->name('trucks.update-truck');
    Route::get('/get-vin/{truckModel}', [TruckController::class, 'getVin']);
        
    Route::get('/routes/getactivitylog/{id}',[RoutesController::class,'getActivityLog'])->name('routes.getactivitylog');
    Route::post('/routes/updateLockStatus',[RoutesController::class,'updateLockStatus'])->name('routes.updateLockStatus');
    
    //////////////////////////////////////
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/createRoute', [ShipmentController::class, 'createRoute'])->name('shipments.createRoute');
    Route::post('/upload-profile-picture', [UserController::class, 'uploadProfilePicture']);
    Route::get('/view/{id}', [UserController::class, 'show'])->name('user.view');
    Route::get('/shipments/datatables', [ShipmentController::class, 'datatables'])->name('shipments.datatables');
    Route::get('/shipments/createDatatables', [ShipmentController::class, 'createDatatables'])->name('shipments.createDatatables'); 
    Route::get('/shipment-view/{id}/{deleted?}', [CustomerController::class, 'showShipment'])->name('customers.viewshipment');
    Route::get('/fetch-activity-log/{shipmentId}', [ShipmentController::class, 'fetchActivitylog'])->name('fetch-activitylog');
    Route::get('/export-shipment/{id}', [ShipmentController::class, 'exportShipment'])->name('shipments.exportShipment');
    Route::put('/update-notes/{shipment}', [ShipmentController::class, 'updateNotes'])->name('update.notes');
    Route::get('/fetch-inventory-detail/{shipmentId}', [ShipmentController::class, 'fetchInventorydetail'])->name('fetch-inventorydetail');
    Route::get('/fetch-delivery-proof-detail/{shipmentId}', [ShipmentController::class, 'fetchDeliveryProof'])->name('fetch-deliveryproof');
    Route::get('/fetch-delivery-instruction/{routeId}', [RoutesController::class, 'deliveryInstruction'])->name('delivery-instruction');
    Route::get('/fhf-notes/{shipmentId}', [ShipmentController::class, 'getFhfNotes'])->name('get-notes');

    Route::get('routes/getAvailableCapacityTruck', [RoutesController::class, 'availableCapacityTruck'])->name('routes.availableCapacityTruck');
    Route::post('routes/addNewShipmentToRoute', [RoutesController::class, 'addNewShipmentToRoute'])->name('routes.addNewShipmentToRoute');
    Route::get('routes/getUnallocatedInventoryRoute', [RoutesController::class, 'getUnallocatedInventoryRoute'])->name('shipments.getUnallocatedInventory');
    Route::get('routes/getUnallocatedPRTRoute', [RoutesController::class, 'getUnallocatedPRTRoute'])->name('routes.getUnallocatedPRTRoute');
    Route::post('routes/replaceNewShipmentToRoute', [RoutesController::class, 'replaceNewShipmentToRoute'])->name('routes.replaceNewShipmentToRoute');
    Route::delete('routes/deleteShipmentFromRoute/{id}', [RoutesController::class, 'deleteShipmentFromRoute'])->name('routes.deleteShipmentFromRoute');
    Route::delete('routes/cancelShipmentFromRoute/{id}', [RoutesController::class, 'cancelShipmentFromRoute'])->name('routes.cancelShipmentFromRoute');

    Route::get('/shipments/shipmentloads',[ShipmentController::class, 'Shipmentloads'])->name('shipments.shipmentloads');
    Route::get('/routes/map',[RoutesController::class,'map'])->name('routes.map');
    Route::get('/routes/trackRoute',[RoutesController::class,'trackRoute'])->name('routes.trackRoute');
    Route::get('/routes/mapFilter',[RoutesController::class,'mapFilter'])->name('routes.map.filter');
    Route::get('/routes/trackRouteFilter',[RoutesController::class,'trackRouteFilter'])->name('routes.map.trackRouteFilter');
    Route::post('/routes/updateDeliveryRoute', [RoutesController::class,'updateDeliveryRoute'])->name('routes.updatedeliveryroute');
    Route::post('/routes/create',[RoutesController::class,'create'])->name('routes.create');
    Route::get('/routes/getroutebyid/{id}',[RoutesController::class,'getRouteById'])->name('routes.getroutebyid');
    Route::get('/get-driver-details', [DriverController::class, 'getDriverDetails']);
    Route::post('/update-driver-route', [RoutesController::class, 'updateDriverRoute'])->name('routes.assigndriver');
    Route::post('/get-routes-data', [LoaderController::class, 'getRoutesData'])->name('routes.loaderdata');
    Route::post('/checkRoutesLoadboardData', [LoaderController::class, 'checkRoutesLoadboardData']);
    Route::post('/update-loadboard-routes', [LoaderController::class, 'updateLoadboardRoutes'])->name('routes.loadersave');
    Route::post('/withdraw-loadboard-routes', [LoaderController::class, 'withdrawLoadboardRoutes']);
    Route::get('/get-users-data',[UserController::class, 'getUsersData'])->name('users.driver');
    Route::get('/update/prices', [DriverController::class, 'updatePrices'])->name('update.prices');
    Route::delete('/route/delete', [RoutesController::class, 'routeDelete'])->name('route.delete');
    Route::post('/route/updaterouteno', [RoutesController::class,'updateRouteNo'])->name('route.updaterouteno');
    Route::delete('shipments/delete', [ShipmentController::class, 'delete'])->name('shipments.delete');
    Route::put('/route/pricesUpdate', [RoutesController::class, 'pricesUpdate'])->name('prices.update');
    Route::get('/location/map', [CustomerLocationController::class, 'showMap'])->name('map.showMap');
    Route::get('/shipmentdetail/{id}', [ShipmentController::class, 'getNotes'])->name('driver.Notes');
    Route::get('/notification',[NotificationController::class,'index'])->name('notification.index');
    Route::get('/notificationIconCount', [NotificationController::class,'NotificationiconCount']);
    Route::get('/search', [GlobelsearchController::class,'globalSearch']);

    Route::get('/getRouteData/{id}', [RoutesController::class, 'getRouteData']);
    Route::get('/checkLoadboardRoutesByRouteId/{routeId}', [RoutesController::class, 'checkLoadboardRoutesByRouteId']);
    Route::post('/route-download', [RoutesController::class, 'downloadRoute'])->name('route-download');

    //Rate Setting
    Route::get('/rate-setting/edit', [RateSettingController::class, 'edit'])->name('rate-setting.edit');
    Route::put('/rate-setting/{id}', [RateSettingController::class, 'update'])->name('rate-setting.update');
    Route::get('/routes/getRecommendedPrice', [RateSettingController::class, 'getRecommendedPrice'])->name('routes.getRecommendedPrice');

    Route::put('/rate-setting-calculate/{id}', [RateSettingController::class, 'calculateUpdate'])->name('rate-setting-calculate.edit');


});

Route::get('/otpverification/{token}', [PasswordResetLinkController::class, 'otp'])->name('password.otpverification');
// Route::post('/verify-otp', [PasswordResetLinkController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/verify-otp/{token}', [PasswordResetLinkController::class, 'verifyOtp'])->name('otp.verify');

Route::post('/chek-user-password', [UserController::class, 'chekUserPassword'])->name('chekUserPassword');

// Route::get('/password/otpverification/{token}', [PasswordResetLinkController::class, 'otp'])->name('password.otpverification');

Route::put('/updatePassword', [PasswordResetLinkController::class, 'updatePassword'])->name('password.updatePassword');
Route::post('/invite-user/{id}', [CarrierownerController::class, 'inviteuser'])->name('invite.email');
Route::get('/delivery-notification/{id}', [DeliveryNotificationController::class, 'view'])->name('delivery-notification');
Route::post('/save-rating', [DeliveryNotificationController::class, 'saveRating'])->name('save-rating');

Route::get('/resetPassword/{token}', [PasswordResetLinkController::class, 'resetpassword'])->name('password.resetpassword');

//cron to update diesel price
Route::get('/updateFuelPrice', [FuelPriceController::class, 'updateFuelPrice']);
Route::get('/calculateRecommendedPriceData', [RateSettingController::class, 'calculateRecommendedPriceData']);
Route::get('/download-bol/{id}', [CustomerController::class, 'showShipment']);
Route::post('/update-timezone', [UserController::class, 'updateTimezone']);
require __DIR__ . '/auth.php';