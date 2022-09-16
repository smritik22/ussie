<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MainUsers as MainUser;
use App\Models\MainUsers;
use App\Models\Property;
use App\Models\UserSubscription;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\AddressModal;
use App\Models\UserConversation;
use App\Models\UserFavouriteProperty;
use App\Models\PropertyImages;
use App\Models\ReportUser;

use App\Helpers\Helper;
use File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;
use Hash;
use PDO;
use DB;
use Carbon\Carbon;

class RideController extends Controller
{
   public function  my_ride(Request $request){
   	// echo "<pre>";print_r($request->toArray());exit();
   	$user_id = $request->user_id;
   	$token = $request->token;
   	$type = $request->type;

   	if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
	if(!empty($userToken)){

		if ($type==1) {
			$ride_data = Helper::getRideData($user_id,$type);
		}elseif ($type==2) {
			$ride_data = Helper::getRideData($user_id,$type);
			
		}elseif ($type==3) {
			$ride_data = Helper::getRideData($user_id,$type);
			
		}
		// echo "<pre>";print_r($ride_data);exit();
		if (!empty($ride_data)) {
			$responseArr = [];
			foreach ($ride_data as $ride) {
				$response['id'] = isset($ride->id) ? $ride->id : '';
				$response['user_id'] = isset($ride->user_id) ? $ride->user_id : '';
				$response['driver_id'] = isset($ride->driver_id) ? $ride->driver_id : '';
				$response['start_date'] = isset($ride->start_date) ? $ride->start_date : '';
				$response['estimate_fare'] = isset($ride->estimate_fare) ? $ride->estimate_fare : '';
				$response['actual_fare'] = isset($ride->actual_fare) ? $ride->actual_fare : '';
				$response['pickup_address'] = isset($ride->pickup_address) ? urldecode($ride->pickup_address) : '';
				$response['dest_address'] = isset($ride->dest_address) ? urldecode($ride->dest_address) : '';
				$responseArr[] = $response;
			}
			$result['code']     = (string) 1;
			$result['message']  = 'success';
			$result['result']   = $responseArr;
			
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'no_data_found';
			$result['result']   = [];
		}

		
	}else{
					
		$result['code']     = (string) 0;
		$result['message']  = 'invalid_token';
		$result['result']   = [];
				}

	}else{
		$result['code']     = (string) 0;
		$result['message']  = 'server_not_responding';
		$result['result']   = [];
	}

		$mainResult[] = $result;
		return response()->json($mainResult);

   }


   public function my_ride_detail(Request $request)
   {

   	// echo "<pre>";print_r($request->toArray());exit();
   	$user_id = $request->user_id;
   	$token = $request->token;
   	$ride_id = $request->ride_id;

   	if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
	if(!empty($userToken)){

		$my_ride_detail = Helper::getRideDetail($user_id,$ride_id);
		if ($my_ride_detail) {
			$response['id'] = isset($my_ride_detail->id) ? $my_ride_detail->id : '';
		$response['user_id'] = isset($my_ride_detail->user_id) ? $my_ride_detail->user_id : '';
		$response['driver_id'] = isset($my_ride_detail->driver_id) ? $my_ride_detail->driver_id : '';
		$response['start_date'] = isset($my_ride_detail->start_date) ? $my_ride_detail->start_date : '';
		$response['map_image'] = isset($my_ride_detail->map_image) ? $my_ride_detail->map_image : '';
		$response['pickup_address'] = isset($my_ride_detail->pickup_address) ? urldecode($my_ride_detail->pickup_address) : '';
		$response['dest_address'] = isset($my_ride_detail->dest_address) ? urldecode($my_ride_detail->dest_address) : '';
		$response['driver_image'] = asset('public/uploads/driver_user/' . $my_ride_detail->driver_image);
		$response['drivername'] = isset($my_ride_detail->drivername) ? urldecode($my_ride_detail->drivername) : '';
		$response['vehicle_type_name'] = isset($my_ride_detail->vehicle_type_name) ? urldecode($my_ride_detail->vehicle_type_name) : '';
		$response['vehicle_model_name'] = isset($my_ride_detail->vehicle_model_name) ? urldecode($my_ride_detail->vehicle_model_name) : '';
		$response['car_type_image'] = asset('public/uploads/car-type/' . $my_ride_detail->car_type_image);
		$response['actual_fare'] = isset($my_ride_detail->actual_fare) ? $my_ride_detail->actual_fare : '';
		// $response['map_image'] = isset($my_ride_detail->map_image) ? $my_ride_detail->map_image : '';
		$result['code']     = (string) 1;
		$result['message']  = 'success';
		$result['result']   = $response;
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'no_data_found';
			$result['result']   = [];
		}
		// echo "Data:<pre>";print_r($my_ride_detail);exit();
		
	}else{
					
		$result['code']     = (string) 0;
		$result['message']  = 'invalid_token';
		$result['result']   = [];
	}

	}else{
		$result['code']     = (string) 0;
		$result['message']  = 'server_not_responding';
		$result['result']   = [];
	}

   	$mainResult[] = $result;
	return response()->json($mainResult);
   }
}
