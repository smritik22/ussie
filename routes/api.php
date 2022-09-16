<?php

use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/', function(){ echo "Access Denied"; });

Route::group(['namespace'=> 'Api'], function () {
    Route::post('label', [SettingsController::class,'label']);
    Route::post('general', [SettingsController::class,'general']);

    // user
    Route::post('register', [UserController::class,'register']);
    Route::post('login', [UserController::class,'login']);
    Route::post('resend_otp', [UserController::class,'resend_otp']);
    Route::post('verify_otp', [UserController::class,'verify_otp']);
    Route::post('profile_setup', [UserController::class,'profile_setup']);
    Route::post('forgot_password', [UserController::class,'forgot_password']);
    Route::post('reset_password', [UserController::class,'reset_password']);
    Route::post('logout', [UserController::class,'logout']);
    Route::post('add_address', [UserController::class,'add_address']);
    Route::post('edit_address', [UserController::class,'edit_address']);
    Route::post('view_address', [UserController::class,'view_address']);
    Route::post('edit_profile', [UserController::class, 'edit_profile']);
    Route::post('promocode', [UserController::class, 'promocode']);

    //Ride Controller //
    Route::post('my_ride', [RideController::class, 'my_ride']);
    Route::post('my_ride_detail', [RideController::class, 'my_ride_detail']);

    //Driver Route //
    //Driver Login //
    Route::post('upload_document', [DriverController::class,'upload_document']);
    Route::post('car_details', [DriverController::class,'car_details']);
    Route::post('vehicle_details', [DriverController::class,'vehicle_details']);

    Route::post('home', [PropertyController::class,'home']);
    Route::post('get_filters', [PropertyController::class,'get_filters']);
    Route::post('property_list', [PropertyController::class,'property_list']);

    Route::post('spotlight_list', [AreaController::class,'spotlight_list']);

    Route::post('amenities', [SettingsController::class,'amenities']);
    Route::post('cms_page', [SettingsController::class,'cms_page']);

    Route::post('property_area_map', [AreaController::class, 'property_area_map']);
    Route::post('property_list_map', [PropertyController::class, 'property_list_map']);

    Route::post('property_details', [PropertyController::class, 'property_details']);
    Route::post('similar_property_list', [PropertyController::class, 'similar_property_list']);
    
    // Login required
    Route::group(['middleware' => 'loginauth','namespace'=> 'Api'], function () {
        // PROPERTY
        Route::post('add_property', [PropertyController::class, 'add_property']);
        Route::post('add_remove_favourite', [PropertyController::class, 'add_remove_favourite']);
        Route::post('delete_property', [PropertyController::class, 'delete_property']);
        Route::post('delete_property_image', [PropertyController::class, 'delete_property_image']);
        Route::post('edit_property', [PropertyController::class, 'edit_property']);
        
        Route::post('favourite_list', [UserController::class, 'favourite_list']);
        Route::post('my_ads', [UserController::class, 'my_ads']);
        
        // USER CHAT
        Route::post('user_chat_list', [UserController::class, 'user_chat_list']);
        Route::post('user_chat_messages', [UserController::class, 'user_chat_messages']);
        Route::post('send_message', [UserController::class, 'send_message']);
        
        // USER PROFILE
        Route::post('view_profile', [UserController::class, 'view_profile']);
        // Route::post('edit_profile', [UserController::class, 'edit_profile']);

        // CHAGE PASSWORD
        Route::post('change_password', [UserController::class, 'change_password']);
        
        // SUBSCRIPTION
        Route::post('get_active_plan_details', [UserController::class, 'getActivePlanDetails']);
    });
    Route::post('subscription_plan_listing', [SettingsController::class, 'subscription_plan_listing']);

    Route::post('report_user', [UserController::class, 'report_user']);
    Route::post('property_inquiry', [PropertyController::class, 'property_inquiry']);
});

Route::get('testEmail', [UserController::class,'testEmail']);