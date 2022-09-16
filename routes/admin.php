<?php

use App\Http\Controllers\Dashboard\AgentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\EmailTemplateController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\Dashboard\LabelController;
use App\Http\Controllers\Dashboard\CmsController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\GovernorateController;
use App\Http\Controllers\Dashboard\AreaController;
use App\Http\Controllers\Dashboard\AmenitiesController;
use App\Http\Controllers\Dashboard\BathroomTypeController;
use App\Http\Controllers\Dashboard\BedroomTypeController;
use App\Http\Controllers\Dashboard\FeaturedAddonsController;
use App\Http\Controllers\Dashboard\GeneralUsersController;
use App\Http\Controllers\Dashboard\PassengersController;
use App\Http\Controllers\Dashboard\DriverController;
use App\Http\Controllers\Dashboard\RideController;
use App\Http\Controllers\Dashboard\VehicleController;
use App\Http\Controllers\Dashboard\VehicleModalController;
use App\Http\Controllers\Dashboard\CarTypeController;
use App\Http\Controllers\Dashboard\PromoCodeController;
use App\Http\Controllers\Dashboard\PassengerReprotController;
use App\Http\Controllers\Dashboard\DriverReprotController;
use App\Http\Controllers\Dashboard\RideReprotController;
use App\Http\Controllers\Dashboard\RevenueReprotController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\PropertiesController;
use App\Http\Controllers\Dashboard\PropertyCompletionStatusController as CompletionController;
use App\Http\Controllers\Dashboard\PropertyCompletionStatusController;
use App\Http\Controllers\Dashboard\PropertyConditionController;
use App\Http\Controllers\Dashboard\PropertyReportController;
use App\Http\Controllers\Dashboard\PropertyTypesController;
use App\Http\Controllers\Dashboard\RevenueReportController;
use App\Http\Controllers\Dashboard\SubscriptionPlanController;
use App\Http\Controllers\Dashboard\SubscriptionReportController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\WebmasterSettingsController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\SubCategoryController;
use App\Models\BedroomTypes;
use App\Models\Property;
use App\Models\MainUsers;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

// Admin Home
Route::get('/admin-home', [DashboardController::class, 'index'])->name('adminHome');
Route::post('/filter', [DashboardController::class, 'index'])->name('dashboardfilter');
//Search
Route::get('/search', [DashboardController::class, 'search'])->name('adminSearch');
Route::post('/find', [DashboardController::class, 'find'])->name('adminFind');

// users
Route::get('/users', [UsersController::class, 'index'])->name('users');
Route::get('/users/create/', [UsersController::class, 'create'])->name('usersCreate');
Route::post('/users/store', [UsersController::class, 'store'])->name('usersStore');
Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('usersEdit');
Route::post('/users/{id}/update', [UsersController::class, 'update'])->name('usersUpdate');
Route::get('/users/destroy/{id}', [UsersController::class, 'destroy'])->name('usersDestroy');
Route::post('/users/updateAll', [UsersController::class, 'updateAll'])->name('usersUpdateAll');


// Users & Permissions
Route::get('/change-password', [UsersController::class, 'changePassword'])->name('admin-change-password');
Route::post('/update-password', [UsersController::class, 'updatePassword'])->name('admin-update-password');

// Labels Management
Route::get('/label', [LabelController::class,'index'])->name('label');
Route::get('/label/create', [LabelController::class,'create'])->name('label.create');
Route::post('/label/store', [LabelController::class,'store'])->name('label.store');
Route::get('/label/delete/{id}', [LabelController::class,'destroy'])->name('label.delete');
Route::get('/label/show/{id}', [LabelController::class,'show'])->name('label.show');
Route::get('/label/edit/{id}', [LabelController::class,'edit'])->name('label.edit');
Route::post('/label/update/{id}', [LabelController::class,'update'])->name('label.update');
Route::post('/label/anyData', [LabelController::class,'anyData'])->name('label.anyData');
Route::get('/label/lang-edit/{parentId}/{langId}', [LabelController::class,'langedit'])->name('label.editlang');
Route::post('/label/storeLang', [LabelController::class,'storeLang'])->name('label.storeLang');


// emailtemplate Management
Route::get('/emailtemplate', [EmailTemplateController::class,'index'])->name('emailtemplate');
Route::get('/emailtemplate/create', [EmailTemplateController::class,'create'])->name('emailtemplate.create');
Route::post('/emailtemplate/store', [EmailTemplateController::class,'store'])->name('emailtemplate.store');
Route::get('/emailtemplate/edit/{id}',[EmailTemplateController::class,'edit'])->name('emailtemplate.edit');
Route::post('/emailtemplate/update/{id}',[EmailTemplateController::class,'update'])->name('emailtemplate.update');
Route::get('/emailtemplate/show/{id}',[EmailTemplateController::class,'show'])->name('emailtemplate.show');
Route::post('/emailtemplate/anyData',[EmailTemplateController::class,'anyData'])->name('emailtemplate.anyData');
Route::get('/emailtemplate/{parentId}/addlang/{langId}', [EmailTemplateController::class,'multiLang'])->name('emailtemplate.multiLang');
Route::post('emailtemplate/storeLang', [EmailTemplateController::class,'storeLang'])->name('emailtemplate.storeLang');


// CMS Management
Route::get('/cms', [CmsController::class,'index'])->name('cms');
Route::get('/cms/create', [CmsController::class,'create'])->name('cms.create');
Route::post('/cms/store', [CmsController::class,'store'])->name('cms.store');
Route::get('/cms/delete/{id}', [CmsController::class,'destroy'])->name('cms.delete');
Route::get('/cms/show/{id}', [CmsController::class,'show'])->name('cms.show');
Route::get('cms/edit/{id}', [CmsController::class,'edit'])->name('cms.edit');
Route::post('cms/update/{id}', [CmsController::class,'update'])->name('cms.update');
Route::post('cms/anyData', [CmsController::class,'anyData'])->name('cms.anyData');
Route::get('cms/cms-edit/{parentId}/{langId}', [CmsController::class,'cmsedit'])->name('cms.editCms');
Route::post('cms/storeLang', [CmsController::class,'storeLang'])->name('cms.storeLang');

// Locations Management
// Country Management
Route::get('/country', [CountryController::class,'index'])->name('country');
Route::get('/country/create', [CountryController::class,'create'])->name('country.create');
Route::post('/country/store', [CountryController::class,'store'])->name('country.store');
Route::get('/country/delete/{id}', [CountryController::class,'destroy'])->name('country.delete');
Route::get('/country/show/{id}', [CountryController::class,'show'])->name('country.show');
Route::get('/country/edit/{id}', [CountryController::class,'edit'])->name('country.edit');
Route::post('/country/update/{id}', [CountryController::class,'update'])->name('country.update');
Route::post('/country/updateAll', [CountryController::class, 'updateAll'])->name('country.updateAll');
Route::post('/country/anyData', [CountryController::class,'anyData'])->name('country.anyData');
Route::get('/country/{parentId}/addlang/{langId}', [CountryController::class,'multiLang'])->name('country.multiLang');
Route::post('/country/addLanguage', [CountryController::class,'storeLang'])->name('country.storeLang');


// Governorate Management
Route::get('/governorate', [GovernorateController::class,'index'])->name('governorate');
Route::get('/governorate/create', [GovernorateController::class,'create'])->name('governorate.create');
Route::post('/governorate/store', [GovernorateController::class,'store'])->name('governorate.store');
Route::get('/governorate/delete/{id}', [GovernorateController::class,'destroy'])->name('governorate.delete');
Route::get('/governorate/show/{id}', [GovernorateController::class,'show'])->name('governorate.show');
Route::get('/governorate/edit/{id}', [GovernorateController::class,'edit'])->name('governorate.edit');
Route::post('/governorate/update/{id}', [GovernorateController::class,'update'])->name('governorate.update');
Route::post('/governorate/updateAll', [GovernorateController::class, 'updateAll'])->name('governorate.updateAll');
Route::post('/governorate/anyData', [GovernorateController::class,'anyData'])->name('governorate.anyData');
Route::get('/governorate/{parentId}/addlang/{langId}', [GovernorateController::class,'multiLang'])->name('governorate.multiLang');
Route::post('/governorate/addLanguage', [GovernorateController::class,'storeLang'])->name('governorate.storeLang');


// Area Management
Route::get('/area', [AreaController::class,'index'])->name('area');
Route::get('/area/create', [AreaController::class,'create'])->name('area.create');
Route::post('/area/store', [AreaController::class,'store'])->name('area.store');
Route::get('/area/delete/{id}', [AreaController::class,'destroy'])->name('area.delete');
Route::get('/area/show/{id}', [AreaController::class,'show'])->name('area.show');
Route::get('/area/edit/{id}', [AreaController::class,'edit'])->name('area.edit');
Route::post('/area/update/{id}', [AreaController::class,'update'])->name('area.update');
Route::post('/area/updateAll', [AreaController::class, 'updateAll'])->name('area.updateAll');
Route::post('/area/anyData', [AreaController::class,'anyData'])->name('area.anyData');
Route::get('/area/{parentId}/addlang/{langId}', [AreaController::class,'multiLang'])->name('area.multiLang');
Route::post('area/addLanguage', [AreaController::class,'storeLang'])->name('area.storeLang');
Route::post('area/fetch-governorate-list', [AreaController::class,'governorate_list'])->name('area.governorateList');
Route::post('area/fetch-area-list', [AreaController::class,'area_list'])->name('area.areaList');


// Amenity Management
Route::get('/amenity', [AmenitiesController::class,'index'])->name('amenity');
Route::get('/amenity/create', [AmenitiesController::class,'create'])->name('amenity.create');
Route::post('/amenity/store', [AmenitiesController::class,'store'])->name('amenity.store');
Route::get('/amenity/delete/{id}', [AmenitiesController::class,'destroy'])->name('amenity.delete');
Route::get('/amenity/show/{id}', [AmenitiesController::class,'show'])->name('amenity.show');
Route::get('/amenity/edit/{id}', [AmenitiesController::class,'edit'])->name('amenity.edit');
Route::post('/amenity/update/{id}', [AmenitiesController::class,'update'])->name('amenity.update');
Route::post('/amenity/updateAll', [AmenitiesController::class, 'updateAll'])->name('amenity.updateAll');
Route::post('/amenity/anyData', [AmenitiesController::class,'anyData'])->name('amenity.anyData');
Route::get('/amenity/{parentId}/addlang/{langId}', [AmenitiesController::class,'multiLang'])->name('amenity.multiLang');
Route::post('amenity/addLanguage', [AmenitiesController::class,'storeLang'])->name('amenity.storeLang');
Route::post('amenity/fetch-governorate-list', [AmenitiesController::class,'governorate_list'])->name('amenity.governorateList');


// General user management
Route::get('/generalusers', [GeneralUsersController::class,'index'])->name('generalusers');
Route::get('/generaluser/delete/{id}', [GeneralUsersController::class,'destroy'])->name('generaluser.delete');
Route::get('/generaluser/show/{id}', [GeneralUsersController::class,'show'])->name('generaluser.show');
Route::get('/generaluser/edit/{id}', [GeneralUsersController::class,'edit'])->name('generaluser.edit');
Route::post('/generaluser/update/{id}', [GeneralUsersController::class,'update'])->name('generaluser.update');
Route::post('/generaluser/updateAll', [GeneralUsersController::class, 'updateAll'])->name('generaluser.updateAll');
Route::post('/generaluser/anyData', [GeneralUsersController::class,'anyData'])->name('generaluser.anyData');


//Passenger user management

Route::get('/passenger', [PassengersController::class,'index'])->name('passenger');
Route::get('/passenger/delete/{id}', [PassengersController::class,'destroy'])->name('passenger.delete');
Route::get('/passenger/show/{id}', [PassengersController::class,'show'])->name('passenger.show');
Route::get('/passenger/edit/{id}', [PassengersController::class,'edit'])->name('passenger.edit');
Route::get('/passenger/create', [PassengersController::class,'create'])->name('passenger.create');
Route::post('/passenger/store', [PassengersController::class,'store'])->name('passenger.store');
Route::post('/passenger/update/{id}', [PassengersController::class,'update'])->name('passenger.update');
Route::post('/passenger/updateAll', [PassengersController::class, 'updateAll'])->name('passenger.updateAll');
Route::post('/passenger/anyData', [PassengersController::class,'anyData'])->name('passenger.anyData');
Route::get('/passenger/ride_list/{id}', [PassengersController::class,'ride_list'])->name('passenger.ride_list');
Route::post('/passenger/rideanyData', [PassengersController::class,'rideanyData'])->name('passenger.rideanyData');


//Driver Management //
Route::get('/driver', [DriverController::class,'index'])->name('driver');
Route::get('/driver/delete/{id}', [DriverController::class,'destroy'])->name('driver.delete');
Route::get('/driver/show/{id}', [DriverController::class,'show'])->name('driver.show');
Route::get('/driver/edit/{id}', [DriverController::class,'edit'])->name('driver.edit');
Route::post('/driver/update/{id}', [DriverController::class,'update'])->name('driver.update');
Route::post('/driver/updateAll', [DriverController::class, 'updateAll'])->name('driver.updateAll');
Route::post('/driver/anyData', [DriverController::class,'anyData'])->name('driver.anyData');
Route::get('/driver/ride_list/{id}', [DriverController::class,'ride_list'])->name('driver.ride_list');
Route::post('/driver/rideanyData', [DriverController::class,'rideanyData'])->name('driver.rideanyData');
Route::get('/driver/reject_list/{id}', [DriverController::class,'reject_list'])->name('driver.reject_list');
Route::post('/driver/riderejectanyData', [DriverController::class,'riderejectanyData'])->name('driver.riderejectanyData');


//Ride Management //
Route::get('/ride', [RideController::class,'index'])->name('ride');
// Route::get('/driver/delete/{id}', [DriverController::class,'destroy'])->name('driver.delete');
Route::get('/ride/show/{id}', [RideController::class,'show'])->name('ride.show');
// Route::get('/driver/edit/{id}', [DriverController::class,'edit'])->name('driver.edit');
// Route::post('/driver/update/{id}', [DriverController::class,'update'])->name('driver.update');
Route::post('/ride/updateAll', [RideController::class, 'updateAll'])->name('ride.updateAll');
Route::post('/ride/anyData', [RideController::class,'anyData'])->name('ride.anyData');
Route::post('/ride/export-ride-report',[RideController::class,'export_property'])->name('ride.export');
Route::get('/ride/active_ride', [RideController::class,'active_ride_list'])->name('ride.active_ride_list');
Route::post('/driver/activeAnydata', [RideController::class,'activeAnydata'])->name('ride.activeAnydata');
Route::get('/ride/reject_list/{id}', [RideController::class,'reject_list'])->name('ride.reject_list');
Route::post('/ride/riderejectanyData', [RideController::class,'riderejectanyData'])->name('ride.riderejectanyData');

//Vehical Type //
Route::get('/vehicle', [VehicleController::class,'index'])->name('vehicle');
Route::get('/vehicle/create', [VehicleController::class,'create'])->name('vehicle.create');
Route::post('/vehicle/store', [VehicleController::class,'store'])->name('vehicle.store');
Route::get('/vehicle/delete/{id}', [VehicleController::class,'destroy'])->name('vehicle.delete');
Route::get('/vehicle/show/{id}', [VehicleController::class,'show'])->name('vehicle.show');
Route::get('/vehicle/edit/{id}', [VehicleController::class,'edit'])->name('vehicle.edit');
Route::post('/vehicle/update/{id}', [VehicleController::class,'update'])->name('vehicle.update');
Route::post('/vehicle/updateAll', [VehicleController::class, 'updateAll'])->name('vehicle.updateAll');
Route::post('/vehicle/anyData', [VehicleController::class,'anyData'])->name('vehicle.anyData');


//Vehical Modal //
Route::get('/vehicle-modal', [VehicleModalController::class,'index'])->name('vehicle-modal');
Route::get('/vehicle-modal/create', [VehicleModalController::class,'create'])->name('vehicle-modal.create');
Route::post('/vehicle-modal/store', [VehicleModalController::class,'store'])->name('vehicle-modal.store');
Route::get('/vehicle-modal/delete/{id}', [VehicleModalController::class,'destroy'])->name('vehicle-modal.delete');
Route::get('/vehicle-modal/show/{id}', [VehicleModalController::class,'show'])->name('vehicle-modal.show');
Route::get('/vehicle-modal/edit/{id}', [VehicleModalController::class,'edit'])->name('vehicle-modal.edit');
Route::post('/vehicle-modal/update/{id}', [VehicleModalController::class,'update'])->name('vehicle-modal.update');
Route::post('/vehicle-modal/updateAll', [VehicleModalController::class, 'updateAll'])->name('vehicle-modal.updateAll');
Route::post('/vehicle-modal/anyData', [VehicleModalController::class,'anyData'])->name('vehicle-modal.anyData');

//Promocode Management //
Route::get('/promocode', [PromoCodeController::class,'index'])->name('promocode');
Route::get('/promocode/create', [PromoCodeController::class,'create'])->name('promocode.create');
Route::post('/promocode/store', [PromoCodeController::class,'store'])->name('promocode.store');
Route::get('/promocode/delete/{id}', [PromoCodeController::class,'destroy'])->name('promocode.delete');
Route::get('/promocode/show/{id}', [PromoCodeController::class,'show'])->name('promocode.show');
Route::get('/promocode/edit/{id}', [PromoCodeController::class,'edit'])->name('promocode.edit');
Route::post('/promocode/update/{id}', [VehicleController::class,'update_data'])->name('promocode.update');
Route::get('/promocode/update_route/{id}', [PromoCodeController::class,'update_route'])->name('promocode.update_route');

Route::post('/promocode/updateAll', [PromoCodeController::class, 'updateAll'])->name('promocode.updateAll');
Route::post('/promocode/anyData', [PromoCodeController::class,'anyData'])->name('promocode.anyData');

// Transactions
Route::get('/transactions', [TransactionController::class,'index'])->name('transaction');
Route::get('/transactions/show/{id}', [TransactionController::class,'show'])->name('transaction.show');
Route::post('/transactions/transactions-list',[TransactionController::class,'anyData'])->name('transaction.anyData');
Route::post('/transaction/export-report', [TransactionController::class,'export'])->name('transaction.export');

//Car Type Management
Route::get('/car-type', [CarTypeController::class,'index'])->name('car-type');
Route::get('/car-type/create', [CarTypeController::class,'create'])->name('car-type.create');
Route::post('/car-type/store', [CarTypeController::class,'store'])->name('car-type.store');
Route::get('/car-type/delete/{id}', [CarTypeController::class,'destroy'])->name('car-type.delete');
Route::get('/car-type/show/{id}', [CarTypeController::class,'show'])->name('car-type.show');
Route::get('/car-type/edit/{id}', [CarTypeController::class,'edit'])->name('car-type.edit');
Route::post('/car-type/update/{id}', [CarTypeController::class,'update'])->name('car-type.update');
Route::post('/car-type/updateAll', [CarTypeController::class, 'updateAll'])->name('car-type.updateAll');
Route::post('/car-type/anyData', [CarTypeController::class,'anyData'])->name('car-type.anyData');

//Category Management
Route::get('/category', [CategoryController::class,'index'])->name('category');
Route::get('/category/create', [CategoryController::class,'create'])->name('category.create');
Route::post('/category/store', [CategoryController::class,'store'])->name('category.store');
Route::get('/category/delete/{id}', [CategoryController::class,'destroy'])->name('category.delete');
Route::get('/category/show/{id}', [CategoryController::class,'show'])->name('category.show');
Route::get('/category/edit/{id}', [CategoryController::class,'edit'])->name('category.edit');
Route::post('/category/update/{id}', [CategoryController::class,'update'])->name('category.update');
Route::post('/category/updateAll', [CategoryController::class, 'updateAll'])->name('category.updateAll');
Route::post('/category/anyData', [CategoryController::class,'anyData'])->name('category.anyData');

//SubCategory Management
Route::get('/subCategory', [SubCategoryController::class,'index'])->name('subCategory');
Route::get('/subCategory/create', [SubCategoryController::class,'create'])->name('subCategory.create');
Route::post('/subCategory/store', [SubCategoryController::class,'store'])->name('subCategory.store');
Route::get('/subCategory/delete/{id}', [SubCategoryController::class,'destroy'])->name('subCategory.delete');
Route::get('/subCategory/show/{id}', [SubCategoryController::class,'show'])->name('subCategory.show');
Route::get('/subCategory/edit/{id}', [SubCategoryController::class,'edit'])->name('subCategory.edit');
Route::post('/subCategory/update/{id}', [SubCategoryController::class,'update'])->name('subCategory.update');
Route::post('/subCategory/updateAll', [SubCategoryController::class, 'updateAll'])->name('subCategory.updateAll');
Route::post('/subCategory/anyData', [SubCategoryController::class,'anyData'])->name('subCategory.anyData');

//Passenger Report Management //
Route::get('/passenger-report', [PassengerReprotController::class,'index'])->name('passenger-report');
Route::get('/passenger-report/show/{id}', [PassengerReprotController::class,'show'])->name('passenger-report.show');
Route::post('/passenger-report/anyData', [PassengerReprotController::class,'anyData'])->name('passenger-report.anyData');
Route::post('/passenger-report/export-passenger-report',[PassengerReprotController::class,'export_property'])->name('report.passenger.export');

//Driver Report Management //
Route::get('/driver-report', [DriverReprotController::class,'index'])->name('driver-report');
Route::get('/driver-report/show/{id}', [DriverReprotController::class,'show'])->name('driver-report.show');
Route::post('/driver-report/anyData', [DriverReprotController::class,'anyData'])->name('driver-report.anyData');
Route::post('/driver-report/export-driver-report',[DriverReprotController::class,'export_property'])->name('report.driver.export');

//Ride Report Management //
Route::get('/ride-report', [RideReprotController::class,'index'])->name('ride-report');
Route::get('/ride-report/show/{id}', [RideReprotController::class,'show'])->name('ride-report.show');
Route::post('/ride-report/anyData', [RideReprotController::class,'anyData'])->name('ride-report.anyData');
Route::post('/ride-report/export-ride-report',[RideReprotController::class,'export_property'])->name('report.ride.export');

//Revenue Report Management //
Route::get('/revenue-report', [RevenueReprotController::class,'index'])->name('revenue-report');
Route::get('/revenue-report/show/{id}', [RevenueReprotController::class,'show'])->name('revenue-report.show');
Route::post('/revenue-report/anyData', [RevenueReprotController::class,'anyData'])->name('revenue-report.anyData');
Route::post('/revenue-report/export-revenue-report',[RevenueReprotController::class,'export_property'])->name('report.revenue.export');

//Notificatoin Management //
Route::get('/notification', [NotificationController::class,'index'])->name('notification');
Route::get('/notification/show/{id}', [NotificationController::class,'show'])->name('notification.show');
Route::post('/notification/notification-list',[NotificationController::class,'anyData'])->name('notification.anyData');
Route::post('/notification/export-notification-report', [NotificationController::class,'export_notification'])->name('notification.export');


//Payment Management //
Route::get('/payment', [PaymentController::class,'index'])->name('payment');
Route::get('/payment/show/{id}', [PaymentController::class,'show'])->name('payment.show');
Route::post('/payment/transactions-list',[PaymentController::class,'anyData'])->name('payment.anyData');
Route::post('/payment/export-payment-report', [PaymentController::class,'export_payment'])->name('payment.export');


// Agents Management
Route::get('/agents/{agent_type?}', [AgentController::class,'index'])->name('agents');
Route::get('/agent/delete/{id}', [AgentController::class,'destroy'])->name('agent.delete');
Route::get('/agent/show/{id}', [AgentController::class,'show'])->name('agent.show');
Route::get('/agent/edit/{id}', [AgentController::class,'edit'])->name('agent.edit');
Route::post('/agent/update/{id}', [AgentController::class,'update'])->name('agent.update');
Route::post('/agent/updateAll', [AgentController::class, 'updateAll'])->name('agent.updateAll');
Route::post('/agent/anyData', [AgentController::class,'anyData'])->name('agent.anyData');



// Property Conditions management 
Route::get('/property-condition', [PropertyConditionController::class,'index'])->name('condition');
Route::get('/property-condition/create', [PropertyConditionController::class,'create'])->name('condition.create');
Route::post('/property-condition/store', [PropertyConditionController::class,'store'])->name('condition.store');
Route::get('/property-condition/delete/{id}', [PropertyConditionController::class,'destroy'])->name('condition.delete');
Route::get('/property-condition/show/{id}', [PropertyConditionController::class,'show'])->name('condition.show');
Route::get('/property-condition/edit/{id}', [PropertyConditionController::class,'edit'])->name('condition.edit');
Route::post('/property-condition/update/{id}', [PropertyConditionController::class,'update'])->name('condition.update');
Route::post('/property-condition/anyData', [PropertyConditionController::class,'anyData'])->name('condition.anyData');
Route::post('/property-condition/updateAll', [PropertyConditionController::class, 'updateAll'])->name('condition.updateAll');
Route::get('/property-condition/lang-edit/{parentId}/{langId}', [PropertyConditionController::class,'langedit'])->name('condition.editlang');
Route::post('/property-condition/storeLang', [PropertyConditionController::class,'storeLang'])->name('condition.storeLang');


// property completion status
Route::get('/property-completion-status', [PropertyCompletionStatusController::class,'index'])->name('completion');
Route::get('/property-completion-status/create', [PropertyCompletionStatusController::class,'create'])->name('completion.create');
Route::post('/property-completion-status/store', [PropertyCompletionStatusController::class,'store'])->name('completion.store');
Route::get('/property-completion-status/delete/{id}', [PropertyCompletionStatusController::class,'destroy'])->name('completion.delete');
Route::get('/property-completion-status/show/{id}', [PropertyCompletionStatusController::class,'show'])->name('completion.show');
Route::get('/property-completion-status/edit/{id}', [PropertyCompletionStatusController::class,'edit'])->name('completion.edit');
Route::post('/property-completion-status/update/{id}', [PropertyCompletionStatusController::class,'update'])->name('completion.update');
Route::post('/property-completion-status/anyData', [PropertyCompletionStatusController::class,'anyData'])->name('completion.anyData');
Route::post('/property-completion-status/updateAll', [PropertyCompletionStatusController::class, 'updateAll'])->name('completion.updateAll');
Route::get('/property-completion-status/lang-edit/{parentId}/{langId}', [PropertyCompletionStatusController::class,'langedit'])->name('completion.editlang');
Route::post('/property-completion-status/storeLang', [PropertyCompletionStatusController::class,'storeLang'])->name('completion.storeLang');

// Property Type
Route::get('/property-type', [PropertyTypesController::class,'index'])->name('property_type');
Route::get('/property-type/create', [PropertyTypesController::class,'create'])->name('property_type.create');
Route::post('/property-type/store', [PropertyTypesController::class,'store'])->name('property_type.store');
Route::get('/property-type/delete/{id}', [PropertyTypesController::class,'destroy'])->name('property_type.delete');
Route::get('/property-type/show/{id}', [PropertyTypesController::class,'show'])->name('property_type.show');
Route::get('/property-type/edit/{id}', [PropertyTypesController::class,'edit'])->name('property_type.edit');
Route::post('/property-type/update/{id}', [PropertyTypesController::class,'update'])->name('property_type.update');
Route::post('/property-type/anyData', [PropertyTypesController::class,'anyData'])->name('property_type.anyData');
Route::post('/property-type/updateAll', [PropertyTypesController::class, 'updateAll'])->name('property_type.updateAll');
Route::get('/property-type/lang-edit/{parentId}/{langId}', [PropertyTypesController::class,'langedit'])->name('property_type.editlang');
Route::post('/property-type/storeLang', [PropertyTypesController::class,'storeLang'])->name('property_type.storeLang');



// Properties management 
Route::get('/properties/{property_for?}', [PropertiesController::class,'index'])->name('properties');
// Route::get('/property/create', [PropertiesController::class,'create'])->name('property.create');
// Route::post('/property/store', [PropertiesController::class,'store'])->name('property.store');
Route::get('/property/delete/{id}', [PropertiesController::class,'destroy'])->name('property.delete');
Route::get('/property/show/{id}', [PropertiesController::class,'show'])->name('property.show');
Route::get('/property/edit/{id}', [PropertiesController::class,'edit'])->name('property.edit');
Route::post('/property/update/{id}', [PropertiesController::class,'update'])->name('property.update');
Route::post('/property/anyData', [PropertiesController::class,'anyData'])->name('property.anyData');
Route::post('/properties/updateAll', [PropertiesController::class, 'updateAll'])->name('property.updateAll');
Route::post('property/get-property-images',[PropertiesController::class,'getPropertyImages'])->name('property.getPropertyImages');

// Bathroom type management
Route::get('/bathroom-type', [BathroomTypeController::class,'index'])->name('bathroom_type');
Route::get('/bathroom-type/create', [BathroomTypeController::class,'create'])->name('bathroom_type.create');
Route::post('/bathroom-type/store', [BathroomTypeController::class,'store'])->name('bathroom_type.store');
Route::get('/bathroom-type/delete/{id}', [BathroomTypeController::class,'destroy'])->name('bathroom_type.delete');
Route::get('/bathroom-type/show/{id}', [BathroomTypeController::class,'show'])->name('bathroom_type.show');
Route::get('/bathroom-type/edit/{id}', [BathroomTypeController::class,'edit'])->name('bathroom_type.edit');
Route::post('/bathroom-type/update/{id}', [BathroomTypeController::class,'update'])->name('bathroom_type.update');
Route::post('/bathroom-type/anyData', [BathroomTypeController::class,'anyData'])->name('bathroom_type.anyData');
Route::post('/bathroom-type/updateAll', [BathroomTypeController::class, 'updateAll'])->name('bathroom_type.updateAll');
Route::get('/bathroom-type/lang-edit/{parentId}/{langId}', [BathroomTypeController::class,'langedit'])->name('bathroom_type.editlang');
Route::post('/bathroom-type/storeLang', [BathroomTypeController::class,'storeLang'])->name('bathroom_type.storeLang');

// Bedroom type management
Route::get('/bedroom-type', [BedroomTypeController::class,'index'])->name('bedroom_type');
Route::get('/bedroom-type/create', [BedroomTypeController::class,'create'])->name('bedroom_type.create');
Route::post('/bedroom-type/store', [BedroomTypeController::class,'store'])->name('bedroom_type.store');
Route::get('/bedroom-type/delete/{id}', [BedroomTypeController::class,'destroy'])->name('bedroom_type.delete');
Route::get('/bedroom-type/show/{id}', [BedroomTypeController::class,'show'])->name('bedroom_type.show');
Route::get('/bedroom-type/edit/{id}', [BedroomTypeController::class,'edit'])->name('bedroom_type.edit');
Route::post('/bedroom-type/update/{id}', [BedroomTypeController::class,'update'])->name('bedroom_type.update');
Route::post('/bedroom-type/anyData', [BedroomTypeController::class,'anyData'])->name('bedroom_type.anyData');
Route::post('/bedroom-type/updateAll', [BedroomTypeController::class, 'updateAll'])->name('bedroom_type.updateAll');
Route::get('/bedroom-type/lang-edit/{parentId}/{langId}', [BedroomTypeController::class,'langedit'])->name('bedroom_type.editlang');
Route::post('/bedroom-type/storeLang', [BedroomTypeController::class,'storeLang'])->name('bedroom_type.storeLang');


// Property Reports
Route::get('/property-report',[PropertyReportController::class,'index'])->name('propertyReport');
Route::post('/property-report/get-property-report-list',[PropertyReportController::class,'anyData'])->name('report.property.anyData');
Route::post('/property-report/export-property-report',[PropertyReportController::class,'export_property'])->name('report.property.export');





// Subscription plans
Route::get('/subscription-plans', [SubscriptionPlanController::class,'index'])->name('subscription_plans');
Route::get('/subscription-plan/create', [SubscriptionPlanController::class,'create'])->name('subscription_plan.create');
Route::post('/subscription-plan/store', [SubscriptionPlanController::class,'store'])->name('subscription_plan.store');
Route::get('/subscription-plan/delete/{id}', [SubscriptionPlanController::class,'destroy'])->name('subscription_plan.delete');
Route::get('/subscription-plan/show/{id}', [SubscriptionPlanController::class,'show'])->name('subscription_plan.show');
Route::get('/subscription-plan/edit/{id}', [SubscriptionPlanController::class,'edit'])->name('subscription_plan.edit');
Route::post('/subscription-plan/update/{id}', [SubscriptionPlanController::class,'update'])->name('subscription_plan.update');
Route::post('/subscription-plan/anyData', [SubscriptionPlanController::class,'anyData'])->name('subscription_plan.anyData');
Route::post('/subscription-plan/updateAll', [SubscriptionPlanController::class, 'updateAll'])->name('subscription_plan.updateAll');
Route::get('/subscription-plan/lang-edit/{parentId}/{langId}', [SubscriptionPlanController::class,'langedit'])->name('subscription_plan.editlang');
Route::post('/subscription-plan/storeLang', [SubscriptionPlanController::class,'storeLang'])->name('subscription_plan.storeLang');


// subscription report
Route::get('/subscription-report',[SubscriptionReportController::class,'index'])->name('subscription_report');
Route::post('/subscription-report/get-subscription-report-list',[SubscriptionReportController::class,'anyData'])->name('report.subscription.anyData');
Route::post('/subscription-report/export-subscription-report',[SubscriptionReportController::class,'export_property'])->name('report.subscription.export');

// revenue report
// Route::get('/revenue-report',[RevenueReportController::class,'index'])->name('revenue_report');
// Route::post('/revenue-report/get-revenue-report-list',[RevenueReportController::class,'anyData'])->name('report.revenue.anyData');
// Route::post('/revenue-report/export-revenue-report',[RevenueReportController::class,'export'])->name('report.revenue.export');



// Webmaster
Route::get('/webmaster', [WebmasterSettingsController::class, 'edit'])->name('webmasterSettings');
Route::post('/webmaster', [WebmasterSettingsController::class, 'update'])->name('webmasterSettingsUpdate');
Route::post('/webmaster/languages/store', [WebmasterSettingsController::class, 'language_store'])->name('webmasterLanguageStore');
Route::post('/webmaster/languages/store', [WebmasterSettingsController::class, 'language_store'])->name('webmasterLanguageStore');
Route::post('/webmaster/languages/update', [WebmasterSettingsController::class, 'language_update'])->name('webmasterLanguageUpdate');
Route::get('/webmaster/languages/destroy/{id}', [WebmasterSettingsController::class, 'language_destroy'])->name('webmasterLanguageDestroy');
Route::get('/webmaster/seo/repair', [WebmasterSettingsController::class, 'seo_repair'])->name('webmasterSEORepair');

Route::post('/webmaster/mail/smtp', [WebmasterSettingsController::class, 'mail_smtp_check'])->name('mailSMTPCheck');
Route::post('/webmaster/mail/test', [WebmasterSettingsController::class, 'mail_test'])->name('mailTest');



// featured addons
Route::get('/featured-addons', [FeaturedAddonsController::class,'index'])->name('featured_addons');
Route::get('/featured-addon/delete/{id}', [FeaturedAddonsController::class,'destroy'])->name('featured_addon.delete');
Route::get('/featured-addon/show/{id}', [FeaturedAddonsController::class,'show'])->name('featured_addon.show');
Route::get('/featured-addon/create', [FeaturedAddonsController::class,'create'])->name('featured_addon.create');
Route::post('/featured-addon/store', [FeaturedAddonsController::class,'store'])->name('featured_addon.store');
Route::get('/featured-addon/edit/{id}', [FeaturedAddonsController::class,'edit'])->name('featured_addon.edit');
Route::post('/featured-addon/update/{id}', [FeaturedAddonsController::class,'update'])->name('featured_addon.update');
Route::post('/featured-addon/updateAll', [FeaturedAddonsController::class, 'updateAll'])->name('featured_addon.updateAll');
Route::post('/featured-addon/anyData', [FeaturedAddonsController::class,'anyData'])->name('featured_addon.anyData');