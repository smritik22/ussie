<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\AreaController;
use App\Http\Controllers\Frontend\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Frontend\DashboardController as FrontDashboardController;
use App\Http\Controllers\Frontend\LoginController;
use App\Http\Controllers\Frontend\PropertyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Frontend\MessageController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/admin', [\App\Http\Controllers\Auth\LoginController::class, 'showMainuserLoginForm'])->name('admin');


// No Permission
Route::get('/403', function () {
    return view('errors.403');
})->name('frontend.NoPermission');

Route::get('/phpinfo', function () {
    phpinfo();
    exit;
});

// Not Found
Route::get('404', function () {
    return view('frontEnd.404');
})->name('NotFound');

Route::get('404-not-found', function () {
    return view('frontEnd.404');
})->name('frontend.not_found');


Route::get('admin/app-version', function(){
    echo 'The current Laravel version is '. app()->version();
});

Route::Group(['prefix' => env('BACKEND_PATH')], function () {
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\LoginController::class, 'forgotpass']);
    Route::post('/forgot/user', [\App\Http\Controllers\Auth\LoginController::class, 'mainuserforgot']);

    Route::middleware(['preventBackHistory'])->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showMainuserLoginForm'])->name('admin.login');
        Route::post('/adminLogin', [\App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])->name('adminLogin');
        Route::post('/main-user-logout', [\App\Http\Controllers\Auth\LoginController::class, 'logoutMainUser'])->name('main-user-logout');
    });
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::Group(['namespace' => 'Frontend', 'as' => 'frontend.','middleware'=>'userauth'], function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('homePage');
    
    Route::get('/login', [UserController::class,'index'])->name('login');
    Route::post('/login/submit', [UserController::class,'login'])->name('login.submit');
    Route::get('/signup', [RegisterController::class,'showSignupForm'])->name('signup');
    Route::post('/signup/submit', [UserController::class,'signup'])->name('signup.submit');

    Route::get('/forget-password', [UserController::class,'forget_password'])->name('forget_password');
    Route::post('/forget-password/submit', [UserController::class,'forgotPassword_submit'])->name('forgot_password.submit');

    // Route::get('properties/{id?}/{property_for?}/{area_name?}', [PropertyController::class, 'property_list'])->name('propertylist');
    Route::get('properties/{id?}', [PropertyController::class, 'property_list'])->name('propertylist');
    Route::post('properties/get-property-list', [PropertyController::class, 'getData'])->name('fetchPropertyList');
    Route::post('change_language', [FrontDashboardController::class, 'change_language'])->name('change_language');
    Route::get('property-details/{id?}', [PropertyController::class, 'property_details'])->name('property_details');
    Route::post('/add-remove-fav-property', [PropertyController::class, 'addRemfavProperty'])->name('addRem_favProp');

    Route::get('property-areas', [AreaController::class, 'index'])->name('area_list');
    Route::post('get-property-areas', [AreaController::class, 'getData'])->name('getAreas');

    Route::post('get-map_data', [PropertyController::class, 'getMap'])->name('getPropMapData');
    Route::post('report-user', [PropertyController::class, 'report_user'])->name('report_user');
    Route::post('contact-user', [PropertyController::class, 'contact_user'])->name('contact_user');

    Route::get('thank-you', [HomeController::class,'thank_you'])->name('thankyou');

    Route::get('about-us', [HomeController::class, 'about_us'])->name('about_us');
    Route::get('terms-and-conditions', [HomeController::class, 'terms_and_conditions'])->name('terms_and_conditions');
    Route::get('privacy-policy', [HomeController::class, 'privacy_policy'])->name('privacy_policy');
    Route::get('faqs', [HomeController::class, 'faqs'])->name('faqs');
    Route::get('contact-us', [HomeController::class, 'contact_us'])->name('contact_us');
    Route::post('contact-us/submit', [HomeController::class, 'submit_contactus'])->name('contactus.submit');

    Route::get('varify-otp', [UserController::class,'otp_varify'])->name('varify_otp');
    Route::post('varify-otp/submit', [UserController::class,'varifyOTP'])->name('varify_otp.submit');
    Route::post('resend-otp', [UserController::class,'resend_otp'])->name('resend_otp');

    Route::get('reset-password', [UserController::class, 'reset_password'])->name('reset_password');
    Route::post('reset-password/submit', [UserController::class, 'submit_reset_password'])->name('reset_password.submit');

    Route::get('logout-user', [UserController::class, 'logoutUser'])->name('logout');
    Route::get('view-agent/{id}', [PropertyController::class, 'view_agent'])->name('agent.view');
    
    Route::get('view-similar-properties/{id}', [PropertyController::class, 'similar_properties'])->name('property.similiar_properties');
    Route::post('/view-similar-properties/get-data', [PropertyController::class, 'fetch_similar_properties'])->name('property.similiar_properties.fetch');

    // login required
    Route::Group(['middleware' => ['frontloginrequired', 'preventBackHistory']], function (){

        Route::get('/account', [UserController::class,'account'])->name('account');
        Route::post('/account/update', [UserController::class,'update_profile'])->name('account.update');
        Route::post('/account/remove-profile', [UserController::class, 'removeImage'])->name('user.removeimage');
        Route::post('/change-password/submit', [UserController::class,'change_password'])->name('change_password.submit');

        Route::post('/my-favourites', [UserController::class, 'getFavourites'])->name('user.favourites.list');
        Route::get('/my-subscriptions', [FrontDashboardController::class, 'subscription_list'])->name('subscriptionplans.list');
        Route::post('/my-subscriptions/cancel-plan', [FrontDashboardController::class, 'cancel_plan'])->name('usersubscription.cancelplan');

        Route::get('/add-edit-property/{id?}', [PropertyController::class, 'add_property'])->name('property.add');
        Route::post('/add-property/submit', [PropertyController::class, 'add_property_submit'])->name('property.submit');
        Route::get('/add-property/success', [PropertyController::class,'thank_you_page'])->name('property.add.thank_you');
        Route::post('/delete-property', [PropertyController::class, 'delete_property'])->name('property.delete');

        Route::get('/my-ads', [PropertyController::class, 'my_ads'])->name('property.my_ads');
        Route::post('/my-ads/get-data', [PropertyController::class, 'fetch_my_ads'])->name('property.my_ads.fetch');

        Route::get('chat-list', [MessageController::class, 'index'])->name('chat.list');
        Route::post('chat-list/fetch', [MessageController::class, 'fetchChatList'])->name('chat.list.fetch');
        Route::get('conversation/{id}', [MessageController::class, 'conversation'])->name('conversation.list');
        Route::post('conversation/fetch', [MessageController::class, 'fetchConversationList'])->name('conversation.list.fetch');
        Route::post('send-message', [MessageController::class, 'sendMessage'])->name('conversation.message.submit');
    });

    Route::Group(['middleware' => 'auth:main_user'], function () {
        Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    });

    Route::post('payment',[PaymentController::class, 'payment'])->name('payment');
    Route::get('payment/success/{id?}',[PaymentController::class, 'payment_success'])->name('payment.success');
    Route::get('payment/error/{id?}',[PaymentController::class, 'payment_error'])->name('payment.error');
});



// Clear Cache
Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return redirect()->back()->with('doneMessage', __('backend.cashClearDone'));
})->name('cacheClear');

Route::get('/route-clear', function () {
    // Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('route:list');
    return redirect()->back()->with('doneMessage', 'Routes cleared');
})->name('routeClear');
