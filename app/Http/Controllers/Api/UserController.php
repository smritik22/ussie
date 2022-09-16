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

class UserController extends Controller
{
	protected $chat_per_page;
	protected $messages_per_page;
	protected $fav_property_per_page;
	protected $my_ads_per_page;

	public function __construct()
	{
		$this->chat_per_page = 10;
		$this->messages_per_page = 15;
		$this->fav_property_per_page = 10;
		$this->my_ads_per_page = 10;
	}
	//
	public static function phoneExist($phone, $country_code, $edit = '')
	{
		if ($edit != '') {
			$data = MainUser::where([
				['mobile_number', '=', trim(urldecode($phone))],
				['country_code', '=', urlencode(trim($country_code))],
				['status', '!=', '2'],
				['is_otp_varified', '=', 1],
				['id', '!=', $edit],
			])->count();
		} else {
			$data = MainUser::where([
				['mobile_number', '=', trim(urldecode($phone))],
				['country_code', '=', urlencode(trim($country_code))],
				['is_otp_varified', '=', 1],
				['status', '!=', '2'],
			])->count();
		}
		return $data;
	}

	public static function emailExist($email, $edit = '')
	{
		if ($edit != '') {
			$data = MainUser::where([
				['email', '=', urlencode(trim($email))],
				['status', '!=', '2'],
				['is_otp_varified', '=', 1],
				['id', '!=', $edit],
			])->count();
		} else {
			$data = MainUser::where([
				['email', '=', urlencode(trim($email))],
				['is_otp_varified', '=', 1],
				['status', '!=', '2'],
			])->count();
		}
		return $data;
	}

	public function sendOtp($phone, $country_code, $otp)
	{
		Helper::sendOtp($phone, $country_code, $otp);
	}


	// user login process
	public function login(Request $request)
	{
		// echo "<pre>";print_r($request->toArray());
		$result = [];
		$mobile_number = $request->mobile_number;
		// echo "<pre>";print_r($mobile_number);exit();
		$userData = Helper::getuserData($mobile_number);
		// echo "<pre>";print_r($userData);exit();
		if (!empty($userData)) {
			// echo "string";exit();
			$otp_send = 0;
			$token = $this->generateToken();
			$otp = $this->generateOTP();
			$user_id = isset($userData[0]->id) ? $userData[0]->id : '';
			// echo "<pre>";print_r($user_id);exit();
			$user = MainUsers::find($user_id);
			// dd($user);
			// echo "<pre>";print_r($user);exit();
			$user->otp = $otp;
			$user->otp_expire_time = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));
			$user->token = $token;
			// $update['otp'] = $otp;
			$user->save();

			$token = $this->generateToken();
			$result['id'] = isset($userData[0]->id) ? urldecode($userData[0]->id) : '' ;
			$result['name'] = isset($userData[0]->name) ? urldecode($userData[0]->name) : '' ;
			$result['email'] = isset($userData[0]->email) ? urldecode($userData[0]->email) : '' ;
			$result['mobile_number'] = isset($userData[0]->mobile_number) ? $userData[0]->mobile_number : '' ;
			$result['country_code'] = isset($userData[0]->country_code) ? $userData[0]->country_code : '' ;
			// $result['otp'] = isset($userData[0]->otp) ? $userData[0]->otp : '' ;
			$result['user_type'] = isset($userData[0]->user_type) ? $userData[0]->user_type : '' ;
			$result['is_profile_setup'] = isset($userData[0]->is_profile_setup) ? $userData[0]->is_profile_setup : '' ;
			$result['secure_number'] = isset($userData[0]->secure_number) ? $userData[0]->secure_number : '' ;
			$result['token'] = isset($userData[0]->token) ? $userData[0]->token : '' ;
			$result['status'] = isset($userData[0]->status) ? $userData[0]->status : '' ;
			$result['device_token'] = isset($userData[0]->device_token) ? $userData[0]->device_token : '' ;
			$result['otp_expire_time'] = isset($user->otp_expire_time) ? $user->otp_expire_time :'';
			$result['otp'] =$otp;
			
			$customer_image = "";
				if (@$userData[0]->customer_image) {
					$customer_image = asset('public/uploads/passenger_user/' . $userData[0]->customer_image);
				}
				$result['customer_image'] = $customer_image;
			

		}else{
			// echo "string2";exit;
			$user = new MainUsers();
			$otp = $this->generateOTP();	
			$token = $this->generateToken();

			$user->mobile_number = $mobile_number;
			// $user->country_code = 
			$user->otp = $otp;
			$user->user_type = isset($request->user_type) ? $request->user_type : '';
			$user->is_profile_setup = 0;
			$user->status = 1;
			$user->token = $token;
			$user->otp_expire_time = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));
			$user->is_phone = isset($request->is_phone) ? $request->is_phone : '';
			$user->device_token = isset($request->device_token) ? $request->device_token : '';

			$user->save();
			// echo "<pre>";print_r($user);exit();
			if ($user->save()) {
				$user_id = $user->id;
				// echo "<pre>";print_r($user_id);exit();
			$userData = Helper::getLoginData($user_id);
			// echo "<pre>";print_r($userData);exit();
			$result['id'] = isset($userData[0]->id) ? urldecode($userData[0]->id) : '' ;
			$result['name'] = isset($userData[0]->name) ? urldecode($userData[0]->name) : '' ;
			$result['email'] = isset($userData[0]->email) ? urldecode($userData[0]->email) : '' ;
			$result['mobile_number'] = isset($userData[0]->mobile_number) ? $userData[0]->mobile_number : '' ;
			$result['country_code'] = isset($userData[0]->country_code) ? $userData[0]->country_code : '' ;
			// $result['otp'] = isset($userData[0]->otp) ? $userData[0]->otp : '' ;
			$result['user_type'] = isset($userData[0]->user_type) ? $userData[0]->user_type : '' ;
			$result['is_profile_setup'] = isset($userData[0]->is_profile_setup) ? $userData[0]->is_profile_setup : '' ;
			$result['secure_number'] = isset($userData[0]->secure_number) ? $userData[0]->secure_number : '' ;
			$result['token'] = isset($userData[0]->token) ? $userData[0]->token : '' ;
			$result['status'] = isset($userData[0]->status) ? $userData[0]->status : '' ;
			$result['device_token'] = isset($userData[0]->device_token) ? $userData[0]->device_token : '' ;
			$result['otp_expire_time'] = isset($userData[0]->otp_expire_time) ? $userData[0]->otp_expire_time : '';
			$result['otp'] =$otp;
			
			$customer_image = "";
				if (@$userData[0]->customer_image) {
					$customer_image = asset('public/uploads/passenger_user/' . $userData[0]->customer_image);
				}
				$result['customer_image'] = $customer_image;
			}
			// echo "string";exit();
		}

		$mainResult[] = $result;
		return response()->json($mainResult);
	}

	public function resend_otp(Request $request)
	{
		// echo "<pre>";print_r($request->toArray());exit();
		$user_id = $request->user_id;

		if (@$user_id) {

			$user_query = MainUser::find($user_id);
			// echo "<pre>";print_r($user_query->toArray());exit();
			if (@$user_query->exists()) {
				$userData = $user_query;
				// echo "<pre>";print_r($userData->toArray());exit();
				$otp_send = 0;
				$post = [];
				$otp = $this->generateOTP();
				if (@$userData->mobile_number) {
					$post['otp'] = $otp;
					// $otp_send = $this->sendOtp($userData->mobile_number, $userData->country_code, $post['otp']);
					$post['otp_expire_time'] = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));

					$userData->otp = $post['otp'];
					$userData->otp_expire_time = $post['otp_expire_time'];

					if (@$userData->save()) {

						$response = [];
						$response['id'] = isset($userData->id) ? urldecode($userData->id) : '' ;
						$response['name'] = isset($userData->name) ? urldecode($userData->name) : '' ;
						$response['email'] = isset($userData->email) ? urldecode($userData->email) : '' ;
						$response['mobile_number'] = isset($userData->mobile_number) ? $userData->mobile_number : '' ;
						$response['country_code'] = isset($userData->country_code) ? $userData->country_code : '' ;
						// $response['otp'] = isset($userData->otp) ? $userData->otp : '' ;
						$response['user_type'] = isset($userData->user_type) ? $userData->user_type : '' ;
						$response['is_profile_setup'] = isset($userData->is_profile_setup) ? $userData->is_profile_setup : '' ;
						$response['secure_number'] = isset($userData->secure_number) ? $userData->secure_number : '' ;
						$response['token'] = isset($userData->token) ? $userData->token : '' ;
						$response['status'] = isset($userData->status) ? $userData->status : '' ;
						$response['device_token'] = isset($userData->device_token) ? $userData->device_token : '' ;
						$response['otp_expire_time'] = isset($post['otp_expire_time']) ? $post['otp_expire_time'] :'';
						$response['otp'] =$otp;

						$customer_image = "";
						if (@$userData->customer_image) {
						$customer_image = asset('public/uploads/passenger_user/' . $userData->customer_image);
						}
						$response['customer_image'] = $customer_image;

						$result['code']     = (string) 1;
						$result['message']  = 'otp_sent_success';
						$result['result'][] = $response;
					} else {
						$result['code']     = (string) 0;
						$result['message']  = 'server_not_responding';
						$result['result']   = [];
					}
				} else {
					$result['code']    = (string) 0;
					$result['message'] = 'invalid_mobile_password';
					$result['result']  = [];
				}
			} else {
				$result['code']     = (string) 0;
				$result['message']  = 'no_data_found';
				$result['result']   = [];
			}
		} else {
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}

		$mainResult[] = $result;
		return response()->json($mainResult);
	}

	public function verify_otp(Request $request)
	{
		// echo "<pre>";print_r(date('Y-m-d H:i:s'));exit();
		$user_id = $request->user_id;
		$otp = $request->otp;
		

		if (@$user_id && @$otp) {
			$userData = MainUser::find($user_id);
			// echo "<pre>";print_r($userData->toArray());exit();
			if (@$userData) {

				if ($userData->otp == $otp) {

					if($userData->otp_expire_time < date('Y-m-d H:i:s')){
						$result['code'] = (string) 0;
						$result['message'] = "otp_expired";
						$result['result'] = [];

						$mainResult = $result;
						return response()->json($mainResult);
					}
					
					// $update = [];

					// $token = $this->generateToken();

					// $update['device_token'] = $request->device_token;
					// $update['token'] = $token;
					// $update['status'] = 1;
					// $userData->update($update);

					$user_arr = [];
					$user_arr['id'] = isset($userData->id) ? urldecode($userData->id) : '' ;
					$user_arr['name'] = isset($userData->name) ? urldecode($userData->name) : '' ;
					$user_arr['email'] = isset($userData->email) ? urldecode($userData->email) : '' ;
					$user_arr['mobile_number'] = isset($userData->mobile_number) ? $userData->mobile_number : '' ;
					$user_arr['country_code'] = isset($userData->country_code) ? $userData->country_code : '' ;
					// $user_arr['otp'] = isset($userData->otp) ? $userData->otp : '' ;
					$user_arr['user_type'] = isset($userData->user_type) ? $userData->user_type : '' ;
					$user_arr['is_profile_setup'] = isset($userData->is_profile_setup) ? $userData->is_profile_setup : '' ;
					$user_arr['secure_number'] = isset($userData->secure_number) ? $userData->secure_number : '' ;
					$user_arr['token'] = isset($userData->token) ? $userData->token : '' ;
					$user_arr['status'] = isset($userData->status) ? $userData->status : '' ;
					$user_arr['device_token'] = isset($userData->device_token) ? $userData->device_token : '' ;
					$user_arr['otp'] =$otp;

					$profile_image = "";
					if (@$userData->profile_image) {
						$profile_image = asset('public/uploads/passenger_user/' . $userData->profile_image);
					}
					$user_arr['profile_image'] = $profile_image;
					

					$result['code'] = (string) 1;
					$result['message'] = "success";
					$result['result'][] = $user_arr;
				} else {
					$result['code']     = (string) 0;
					$result['message']  = 'otp_not_matched';
					$result['result']   = [];
				}
			} else {
				$result['code']     = (string) 0;
				$result['message']  = 'no_data_found';
				$result['result']   = [];
			}
		} else {
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}

		$mainResult[] = $result;
		return response()->json($mainResult);
	}


	public function profile_setup(Request $request){
			// echo "<pre>";print_r($request->toArray());exit();
			$user_id = $request->user_id;
			$token = $request->token;
			$name = $request->name;
			$email = $request->email;

			if (@$user_id && @$token) {
				$userToken = Helper::getusercheckToken($user_id, $token);
				// echo "<pre>";print_r($userToken);exit();
				if(!empty($userToken)){

					$user = MainUsers::find($user_id);

					$user->name = urlencode($name);
					$user->email = urlencode($email);
					$user->is_profile_setup = 1;
					$user->save();

					$user_arr = [];
					$user_arr['id'] = isset($userToken->id) ? urldecode($userToken->id) : '' ;
					$user_arr['name'] = isset($user->name) ? urldecode($user->name) : '' ;
					$user_arr['email'] = isset($user->email) ? urldecode($user->email) : '' ;
					$user_arr['mobile_number'] = isset($userToken->mobile_number) ? $userToken->mobile_number : '' ;
					$user_arr['country_code'] = isset($userToken->country_code) ? $userToken->country_code : '' ;
					// $user_arr['otp'] = isset($userToken->otp) ? $userToken->otp : '' ;
					$user_arr['user_type'] = isset($userToken->user_type) ? $userToken->user_type : '' ;
					$user_arr['is_profile_setup'] = isset($userToken->is_profile_setup) ? $userToken->is_profile_setup : '' ;
					$user_arr['secure_number'] = isset($userToken->secure_number) ? $userToken->secure_number : '' ;
					$user_arr['token'] = isset($userToken->token) ? $userToken->token : '' ;
					$user_arr['status'] = isset($userToken->status) ? $userToken->status : '' ;
					$user_arr['device_token'] = isset($userToken->device_token) ? $userToken->device_token : '' ;
					$user_arr['otp'] =isset($userToken->otp) ? $userToken->otp : '' ;;

					$profile_image = "";
					if (@$userToken->profile_image) {
						$profile_image = asset('public/uploads/passenger_user/' . $userToken->profile_image);
					}
					$user_arr['profile_image'] = $profile_image;
					// echo "string";exit();
					// $user_arr = [];
					$result['code'] = (string) 1;
					$result['message'] = "success";
					$result['result'][] = $user_arr;
				}else{
					// echo "string1";exit();
					$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
				}
				// $userData = MainUser::find($user_id);
			}else{
				$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
			}
			$mainResult[] = $result;
		return response()->json($mainResult);
	}


	public function add_address(Request $request){
		// echo "<pre>";print_r($request->toArray());
		// $is_type = $request->is_type;
		$user_id = $request->user_id;
		$token = $request->token;
		$title = $request->title;
		$address = $request->address;
		$latitude = $request->latitude;
		$longitude = $request->longitude;
		if (@$user_id && @$token) {
			$userToken = Helper::getusercheckToken($user_id, $token);
			if(!empty($userToken)){
				$customer_address = new AddressModal();

				$customer_address->user_id = isset($user_id) ? $user_id :'';
				$customer_address->title = isset($title) ? $title :'';
				$customer_address->address = isset($address) ? $address :'';
				$customer_address->latitude = isset($latitude) ? $latitude :'';
				$customer_address->longitude = isset($longitude) ? $longitude :'';
				$customer_address->status = 1;
				$customer_address->save();
				
				if ($customer_address->save()) {

				$customer_address = $customer_address->id;
				$customer_address_data = Helper::getCustomerData($customer_address);
				// echo "<pre>";print_r($customer_address_data);exit();
				$response['id'] = isset($customer_address_data[0]->id) ? $customer_address_data[0]->id : '';
				$response['user_id'] = isset($customer_address_data[0]->user_id) ? $customer_address_data[0]->user_id : '';
				$response['title'] = isset($customer_address_data[0]->title) ? $customer_address_data[0]->title : '';
				$response['address'] = isset($customer_address_data[0]->address) ? $customer_address_data[0]->address : '';
				$response['latitude'] = isset($customer_address_data[0]->latitude) ? $customer_address_data[0]->latitude : '';
				$response['longitude'] = isset($customer_address_data[0]->longitude) ? $customer_address_data[0]->longitude : '';
				$response['status'] = isset($customer_address_data[0]->status) ? $customer_address_data[0]->status : '';
				}else{
					$result['code']     = (string) 0;
					$result['message']  = 'server_not_responding';
					$result['result']   = [];
				}


				$result['code']     = (string) 1;
				$result['message']  = 'add_address_success';
				$result['result'][] = $response;
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		} }else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
	}

	public function edit_address(Request $request){
		// echo "<pre>";print_r($request->toArray());
		$is_type = $request->is_type;
		$user_id = $request->user_id;
		$token = $request->token;
		$address_id = $request->address_id;
		$title = $request->title;
		$address = $request->address;
		$latitude = $request->latitude;
		$longitude = $request->longitude;

		if (@$user_id && @$token && @$address_id) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){

			if ($is_type==1) {
				// $customer_address_data = Helper::getCustomerData($address_id);
			// echo "<pre>";print_r($customer_address_data);exit();
			$customer_address = AddressModal::find($address_id);
			if (!empty($customer_address)) {
				// echo "string";exit();
				$customer_address->title = isset($title) ? $title : '';
				$customer_address->address = isset($address) ? $address : '';
				$customer_address->latitude = isset($latitude) ? $latitude : '';
				$customer_address->longitude = isset($longitude) ? $longitude : '';
				$customer_address->save();

				$customer_address_data = Helper::getCustomerData($address_id);
				if (!empty($customer_address_data)) {
					$response['id'] = isset($customer_address_data[0]->id) ? $customer_address_data[0]->id : '';
					$response['user_id'] = isset($customer_address_data[0]->user_id) ? $customer_address_data[0]->user_id : '';
					$response['title'] = isset($customer_address_data[0]->title) ? $customer_address_data[0]->title : '';
					$response['address'] = isset($customer_address_data[0]->address) ? $customer_address_data[0]->address : '';
					$response['latitude'] = isset($customer_address_data[0]->latitude) ? $customer_address_data[0]->latitude : '';
					$response['longitude'] = isset($customer_address_data[0]->longitude) ? $customer_address_data[0]->longitude : '';
					$response['status'] = isset($customer_address_data[0]->status) ? $customer_address_data[0]->status : '';

				$result['code']     = (string) 1;
				$result['message']  = 'edit_address_success';
				$result['result'][] = $response;
				}else{
					$result['code']     = (string) 0;
					$result['message']  = 'no_data_found';
					$result['result']   = [];
				}
			}else{
				$result['code']     = (string) 0;
				$result['message']  = 'no_data_found';
				$result['result']   = [];

			}
			}else{
				$customer_address = AddressModal::find($address_id);
				$customer_address->status = 2;
				$customer_address->save();

				$result['code']     = (string) 1;
				$result['message']  = 'delete_address_success';
				$result['result'] = [];
			}
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		} }else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
	}

	public function view_address(Request $request){
		$user_id = $request->user_id;
		$token = $request->token;
		if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){
			$view_address_data = Helper::getCustomerViewData($user_id);
			// echo "<pre>";print_r($view_address_data);exit();
			if (!empty($view_address_data)) {
			$responseArr = [];
			foreach ($view_address_data as $address_data) {

				$response['id'] = isset($address_data->id) ? $address_data->id : '';
				$response['user_id'] = isset($address_data->user_id) ? $address_data->user_id : '';
				$response['title'] = isset($address_data->title) ? $address_data->title : '';
				$response['address'] = isset($address_data->address) ? $address_data->address : '';
				$response['latitude'] = isset($address_data->latitude) ? $address_data->latitude : '';
				$response['longitude'] = isset($address_data->longitude) ? $address_data->longitude : '';
				$response['status'] = isset($address_data->status) ? $address_data->status : '';
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
		}
		else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
	}

	public function edit_profile(Request $request){
		// echo "<pre>";print_r($request->toArray());exit();
		$user_id = $request->user_id;
		$token = $request->token;
		$email = $request->email;
		$name = urldecode($request->name);
		$profile_image = $request->profile_image;
		// echo "<pre>";print_r($name);exit();

		if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){
			$user = MainUsers::find($user_id);

			$formFileName = "profile_image";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/passenger_user/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			$user->customer_image = $fileFinalName_ar;
			$user->name = urlencode($name);
			$user->email = urlencode($email);
			$user->save();

			if ($user->save()) {
				$user_id = $user->id;
				$userData = Helper::getLoginData($user_id);
			// echo "<pre>";print_r($userData);exit();
			$response['id'] = isset($userData[0]->id) ? urldecode($userData[0]->id) : '' ;
			$response['name'] = isset($userData[0]->name) ? urldecode($userData[0]->name) : '' ;
			$response['email'] = isset($userData[0]->email) ? urldecode($userData[0]->email) : '' ;
			$response['mobile_number'] = isset($userData[0]->mobile_number) ? $userData[0]->mobile_number : '' ;
			$response['country_code'] = isset($userData[0]->country_code) ? $userData[0]->country_code : '' ;
			// $response['otp'] = isset($userData[0]->otp) ? $userData[0]->otp : '' ;
			$response['user_type'] = isset($userData[0]->user_type) ? $userData[0]->user_type : '' ;
			$response['token'] = isset($userData[0]->token) ? $userData[0]->token : '' ;
			$response['status'] = isset($userData[0]->status) ? $userData[0]->status : '' ;
			$response['device_token'] = isset($userData[0]->device_token) ? $userData[0]->device_token : '' ;
			
			$customer_image = "";
				if (@$userData[0]->customer_image) {
					$customer_image = asset('public/uploads/passenger_user/' . $userData[0]->customer_image);
				}
				$response['customer_image'] = $customer_image;

			$result['code']     = (string) 1;
			$result['message']  = 'success';
			$result['result']   = $response;
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
			}
			// echo "<pre>";print_r($user);exit();
			
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		} }else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
	}

	public function promocode(Request $request){

		$user_id = $request->user_id;
		$token = $request->token;
		if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){

			$get_promocode = Helper::getPromocode();
			// echo "<pre>";print_r($get_promocode);exit();

			$responseArr = [];
			foreach ($get_promocode as $promocode) {
				$response['id'] = isset($promocode->id) ? $promocode->id :'';
				$response['promocode_name'] = isset($promocode->promocode_name) ? urldecode($promocode->promocode_name) :'';
				$response['promocode'] = isset($promocode->promocode) ? urldecode($promocode->promocode) :'';
				$response['page_content'] = isset($promocode->page_content) ? urldecode($promocode->page_content) :'';
				$response['promocode_percentage'] = isset($promocode->promocode_percentage) ? urldecode($promocode->promocode_percentage) :'';
				$response['start_date'] = (string) Carbon::parse($promocode->start_date)->format('Y-m-d h:i A');
				$response['end_date'] = (string) Carbon::parse($promocode->end_date)->format('Y-m-d h:i A');

				$responseArr[] = $response; 
			}
			$result['code']     = (string) 1;
			$result['message']  = 'success';
			$result['result']   = $responseArr;
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		} }else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);

	}


	public function logout(Request $request)
	{
		$userData = MainUser::where('id', $request->user_id)->where('status', '!=', 2)->where('is_otp_varified', '=', 1)->first();

		if (isset($userData) && !empty($userData)) {
			$userData->update(['remember_token' => '', 'device_token' => ' ', 'device_type' => '', 'device_id' => '']);

			$result = array();
			$result['code']     =  (string) 1;
			$result['message']  = "logout_success";
		} else {
			$result['code']     =  (string) 0;
			$result['message']  =   'no_data_found';
		}

		$mainResult[] = $result;
		return response()->json($mainResult);
	}

	



	


	/* 
	============================================= 
    | Some testings and common functions
	============================================= 
	*/

	public function testEmail()
	{
		$this->attachment_email(1, 'svapnil@mailinator.com', 'svapnil', '', '', 1);
	}

	//mail
	public function attachment_email($language_id, $email, $name, $url, $logo, $id)
	{

		$setting = Setting::find(1);
		// dd($setting);
		$templateData = Helper::getEmailTemplateData($language_id, $id);
		// dd($templateData);

		$from_email = $setting['from_email'];
		$data = array('email' => $email, 'name' => $name, 'url' => $url, 'id' =>  $id, 'logo' => $logo, 'from_email' => $from_email);
		// try {
		Mail::send('emails.registration', $data, function ($message) use ($data, $templateData) {
			$message->to($data['email'], $templateData->title)->subject($templateData->subject);
			$message->from($data['from_email'], 'DOM - Properties');
		});
		// } catch (\Throwable $th) {
		// 	// throw $th;
		// }
	}

	public function generateToken()
	{
		return md5(rand(1, 10) . microtime());
	}

	public function generateOTP()
	{
		return rand(1000, 9999);
	}


	/*
	==================================================================================
	|
	|   USER CHAT/CONVERSATION  
	|
	==================================================================================
	*/


	public function user_chat_list(Request $request)
	{
		$user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$page_no = @$request->input('page_no') ?: 1;

		$chat_list = UserConversation::from('user_conversation as m1')
			->select('m1.*')
			->join(DB::raw(
				'(
                SELECT
                    LEAST(from_id, to_id) AS from_id,
                    GREATEST(from_id, to_id) AS to_id,
                    MAX(id) AS max_id
                FROM user_conversation 
                GROUP BY
                    LEAST(from_id, to_id),
                    GREATEST(from_id, to_id)
            ) AS m2'
			), fn ($join) => $join
				->on(DB::raw('LEAST(m1.from_id, m1.to_id)'), '=', 'm2.from_id')
				->on(DB::raw('GREATEST(m1.from_id, m1.to_id)'), '=', 'm2.to_id')
				->on('m1.id', '=', 'm2.max_id'))
			->where('m1.from_id', $user_id)
			->orWhere('m1.to_id', $user_id)
			->orderByDesc('m1.created_at', 'desc');
			// ->take($this->chat_per_page)
			// ->skip( (($page_no * $this->chat_per_page) - 1) )
			// ->get();
		
		$total_records = $chat_list->count();
		$chat_list = $chat_list->paginate($this->chat_per_page, ['*'], 'page', $page_no);
		
		if($chat_list) {
			$chat_arr = [];
			foreach($chat_list as $key => $value) {
				$chat_data = [];
				$chat_user_id = "";
				if($value->from_id == $user_id) {
					$chat_user_id = $value->to_id;
					$chat_user_detail = $value->receiverDetails;
				}else {
					$chat_user_id = $value->from_id;
					$chat_user_detail = $value->senderDetails;
				}

				$profile_image = "";
				if (@$chat_user_detail->profile_image) {
					$profile_image = asset('uploads/general_users/' . $chat_user_detail->profile_image);
				}
				$user_arr['profile_image'] = $profile_image;

				$chat_data['user_id'] = (string) $user_id;
				$chat_data['chat_user_id'] = (string) $chat_user_id;
				$chat_data['sender_id'] =  (string) $value->from_id;
				$chat_data['receiver_id'] = (string) $value->to_id;
				$chat_data['name'] = urldecode($chat_user_detail->full_name);
				$chat_data['image'] = $profile_image;
				$chat_data['last_message'] = $value->message;

				$unread_count = 0;
				$unread_count = UserConversation::where('to_id', '=', $user_id)->where('read_status', '=', 0)->count();

				$chat_data['unread_message_counter'] = (string) $unread_count;
				$chat_data['date_time'] = date("H:i", strtotime($value->created_at));

				$chat_arr[] = $chat_data;
			}

			$response = [];
			$response['chat_list'] = $chat_arr;
			
			$result['code']     = (string) 1;
			$result['message']  = 'success';
			$result['total_records']  = (int) $total_records;
        	$result['per_page'] = (int) $this->messages_per_page;
			$result['result'][] = $response;
			$mainResult[] = $result;
			return response()->json($mainResult);
		}
		else {
			$result['code']     =   (string) -6;
			$result['message']  =   'no_data_found';
			$result['total_records']  = (int) $total_records;
        	$result['per_page'] = (int) $this->messages_per_page;
			$result['result']   =   [];
			$mainResult[] = $result;
			return response()->json($mainResult);
		}
			
	}

	/*
	==================================================================================
	|| USER CONVERSATION
	==================================================================================
	*/

	public function user_chat_messages(Request $request)
	{
		$user_id = $request->input('user_id');
		$chat_user_id = $request->input('chat_user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$page_no = @$request->input('page_no') ?: 1;

		$chatData = UserConversation::where(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $user_id)
				  ->where('to_id', '=', $chat_user_id);
		})->orWhere(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $chat_user_id)
				  ->where('to_id', '=', $user_id);
		});

		$total_records = $chatData->count();

		$chatData = $chatData->latest()->paginate($this->messages_per_page, ['*'], 'page', $page_no);

		$chat = [];
		foreach ($chatData as $key => $value) {
			$message = [];
			$message['message_id'] = (string) ($value->id ?: "");
			$message['user_id'] = (string) ($user_id ?: "");
			$message['sender_id'] = (string) ($value->from_id ?: "");
			$message['receiver_id'] = (string) ($value->to_id ?: "");
			$message['message'] = (string) ($value->message ?: "");

			$messageTime = Helper::get_day_name($value->created_at);
			$message['message_time'] = $messageTime;
			$message['message_time_stamp'] = date('Y-m-d H:i:s', strtotime($value->created_at));

			$chat[] = $message;
		}

		$response = [];
		$response['chat_user_id'] = (string) $chat_user_id;
		$response['message_chat'] = $chat;
		
		$result['code']     = (string) 1;
		$result['message']  = 'success';
		$result['total_records']  = (int) $total_records;
        $result['per_page'] = (int) $this->messages_per_page;
		$result['result'][] = $response;
		
		$mainResult[] = $result;
		return response()->json($mainResult);
	}


	/*
	==================================================================================
	|| SEND MESSAGE
	==================================================================================
	*/

	public function send_message(Request $request) {
		$user_id = $request->input('user_id');
		$chat_user_id = $request->input('chat_user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$message = $request->input('message');

		if($message) {
			$conversation = new UserConversation;
			$conversation->from_id = $user_id;
			$conversation->to_id   = $chat_user_id;
			$conversation->message = $message;
			$conversation->read_status = 0;

			if($conversation->save()) {

				$response = [];
				$response['message_id'] = (string) $conversation->id;
				$response['message'] = $conversation->message;
				$response['message_time'] = Helper::get_day_name($conversation->created_at);

				$result['code']     = (string) 1;
				$result['message']  = 'success';
				$result['result'][] = $response;
				
				$mainResult[] = $result;
				return response()->json($mainResult);
			}
			else{
				$result['code']     = (string) 0;
				$result['message']  = 'failure';
				$result['result']   = [];
				
				$mainResult[] = $result;
				return response()->json($mainResult);
			}
		}
		else{
			$result['code']     = (string) -5;
			$result['message']  = 'enter_message';
			$result['result']   = [];
			
			$mainResult[] = $result;
			return response()->json($mainResult);
		}
	}

	/*
	==================================================================================
	|
	|   USER PROFILE  
	|
	==================================================================================
	*/


	public function view_profile(Request $request) {
		$user_id = $request->input('user_id');
		$user_type = $request->input('user_type');
        $language_id = @$request->input('language_id') ?: 1;
		
		$user = MainUser::find($user_id);

		$response = [];
		$response['user_id'] = (string) $user_id;
		$response['full_name'] = urldecode($user->full_name) ?: "";
		$response['mobile_no'] = (string) $user->mobile_number ?: "";
		$response['email'] = urldecode($user->email) ?: "";
		$response['user_type'] = (string) $user->user_type;
		$response['agent_type'] = (string) $user->agent_type;
		$response['country_code'] = (string) urldecode($user->country_code) ?: "";
		$response['about_user'] = @$user->about_user ?: "";
		$response['short_address'] = @$user->user_short_address ?: "";

		$profile_image = "";
		if (@$user->profile_image) {
			// if($user->user_type == config('constants.USER_TYPE_AGENT')) {
			// 	$folder = 'uploads/agents/';
			// }else{
			// 	$folder = 'uploads/general_users/';
			// }
			$profile_image = asset( 'uploads/general_users/' . $user->profile_image );
		}

		$response['profile_image'] = $profile_image;

		
		$property_listing = [];
		
		if($user->user_type == config('constants.USER_TYPE_AGENT')) {
			$properties = Property::where('agent_id', '=', $user_id)->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))->paginate(5);
			foreach($properties as $key => $value) {
	
				$property_details = [];
				
				$is_fav = "0";
				if ($user_id) {
					$is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $value->id)->exists();
				}
	
				$area_name = "";
				$country_name = "";
				if ($value->area_id) {
					if ($language_id == 1) {
						$area_name = @urldecode($value->areaDetails->name) ?: "";
						$country_name = @urldecode($value->areaDetails->country->name) ?: "";
					} else {
						if (@$value->areaDetails->childdata[0]->name) {
							$area_name = urldecode($value->areaDetails->childdata[0]->name) ?: "";
						} else {
							$area_name = urldecode($value->areaDetails->name) ?: "";
						}
	
						if (@$value->areaDetails->country->childdata[0]->name) {
							$country_name = @urldecode($value->areaDetails->country->childdata[0]->name) ?: "";
						} else {
							$country_name = @urldecode($value->areaDetails->country->name) ?: "";
						}
					}
				}
	
				$short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
				$currency = @$value->areaDetails->country->currency_code ?: "KD";
	
				$property_image_url = PropertyImages::where('property_id', '=', $value->id)->orderBy('id', 'asc')->first();
	
				$image_url = "";
				if ($property_image_url) {
					$image_url = asset("storage/property_images/" . $value->id . '/' . $property_image_url->property_image);
				}
	
				$property_details['id'] = (string) $value->id;
				$property_details['property_id'] = (string) $value->property_id;
				$property_details['property_title'] = @$value->property_name ?: "";
				$property_details['property_image'] = $image_url;
				$property_details['property_price'] = (string) (@$value->price_area_wise ? (number_format(Helper::tofloat($value->price_area_wise), ($value->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
				$property_details['property_for'] =  @$value->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $value->property_for . '.label_key'), $language_id) : "";
				$property_details['property_for_id'] = (string) @$value->property_for ?: "";
	
				$property_details['is_favourite'] = (string) ($is_fav ?: "0");
				$property_details['property_short_address'] = $short_address;
	
				$property_listing[] = $property_details;
			}
		}

		$response['property_listing'] = $property_listing;

		$result['code']     = (string) 1;
		$result['message']  = 'success';
		$result['result'][] = $response;
		
		$mainResult[] = $result;
		return response()->json($mainResult);
	}



	public function change_password(Request $request) {

		$user_id = $request->input('user_id');
		$new_password = $request->input('new_password');

		$user = MainUser::where('id', $user_id)->where('status', '=', 1)->where('is_otp_varified', '=', 1)->first();

		if ($user) {

			if ($new_password) {
				
				$oldMatch = Hash::check($request->input('old_password'), $user->password);
				if(!$oldMatch) {
					$result['code']     = (string) -8;
					$result['message']  = 'old_password_not_matched';

					$mainResult[] = $result;
					return response()->json($mainResult);
				}

				$checkOld = Hash::check($new_password, $user->password);
				if ($checkOld) {
					$result['code']     = (string) -8;
					$result['message']  = 'you_have_entered_old_password';

					$mainResult[] = $result;
					return response()->json($mainResult);
				}

				$user->update(['password' => Hash::make($new_password)]);

				$result['code']     = (string) 1;
				$result['message']  = 'password_changed_successfully';
			} else {
				$result['code']     = (string) 0;
				$result['message']  = 'new_password_required';
			}
		} else {
			$result['code']     =   (string) -7;
			$result['message']  =   'account_not_exists';
		}
		$mainResult[] = $result;
		return response()->json($mainResult);
	}

	public function favourite_list(Request $request) {
		$user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$page_no = $request->input('page_no') ?: 1;

		$favList = UserFavouriteProperty::whereHas('PropertyDetails', function ($property) {
				$property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
			})
			->whereHas('UserDetails', function ($agent) {
				$agent->where('status', '=', 1);
			})
			->where('user_id', '=', $user_id);
		
		$total_records = $favList->count();

		$favList = $favList->latest()->paginate($this->fav_property_per_page, ['*'], 'page', $page_no);
		
		$propertyArr = [];
		foreach($favList as $key => $property) {
			$propertyDetails = [];
			$propertyDetails['id'] = (string) $property->PropertyDetails->id;
			$propertyDetails['property_id'] = (string) $property->PropertyDetails->property_id;
			$propertyDetails['title'] =  @$property->PropertyDetails->property_name ?: "";

			$propertyDetails['property_for']  = @$property->PropertyDetails->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->PropertyDetails->property_for . '.label_key'), $language_id) : "";
            $propertyDetails['property_for_id']  = (string) (($property->PropertyDetails->property_for != "" && $property->PropertyDetails->property_for != null) ? $property->PropertyDetails->property_for : "");

			$currency = @$property->PropertyDetails->areaDetails->country->currency_code ?: "KD";

			$area_name = "";
            $country_name = "";
            if ($property->PropertyDetails->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->PropertyDetails->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->PropertyDetails->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->PropertyDetails->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->PropertyDetails->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->PropertyDetails->areaDetails->name) ?: "";
                    }

                    if (@$property->PropertyDetails->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->PropertyDetails->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->PropertyDetails->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";

			$image = @$property->PropertyDetails->propertyImages[0]->property_image ?: "";
			$property_image = "";
			if($image) {
				$property_image = asset("storage/property_images/" . $property->PropertyDetails->id . '/' . $image);
			}

			$property_price = (@$property->PropertyDetails->price_area_wise ? (number_format(Helper::tofloat($property->PropertyDetails->price_area_wise), ($property->PropertyDetails->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
			
			$propertyDetails['property_price'] = (string) $property_price;
			$propertyDetails['image_url'] = $property_image;
			$propertyDetails['bathroom_count'] =  @$property->PropertyDetails->property_name ?: "0";
            $propertyDetails['bedroom_count'] = (string) @$property->PropertyDetails->total_bedrooms ?: "0";
            $propertyDetails['toilet_count'] = (string) @$property->PropertyDetails->total_toilets ?: "0";
            $propertyDetails['area_sqft'] = (string) @$property->PropertyDetails->property_sqft_area ?: "0";
			$propertyDetails['latitude'] = (string) @$property->PropertyDetails->property_address_latitude ?: "";
            $propertyDetails['longitude'] = (string) @$property->PropertyDetails->property_address_longitude ?: "";

			$area_value = "";
            if ($property->PropertyDetails->areaDetails->updated_range > $property->PropertyDetails->areaDetails->default_range) {
                // green
                $area_value = 2;
            } else if ($property->PropertyDetails->areaDetails->updated_range < $property->PropertyDetails->areaDetails->default_range) {
                // red
                $area_value = 1;
            } else if ($property->PropertyDetails->areaDetails->updated_range == $property->PropertyDetails->areaDetails->default_range) {
                // yellow
                $area_value = 0;
            }

            $property_arr['area_value'] = (string) $area_value;

            $propertyDetails['area_value'] = (string) $area_value;
			$propertyDetails['is_fav'] = "1";
			$propertyDetails['is_featured'] = (string) ($property->is_featured ?: 0);
			$propertyDetails['short_address'] = $short_address;

			$propertyArr[] = $propertyDetails;
		}
		
		$response = ["property_list" => $propertyArr];

		$result['code']     = (string) 1;
        $result['message']  = "success";
        $result['total_records']  = (int) $total_records;
        $result['per_page'] = (int) $this->fav_property_per_page;
        $result['result'][] = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
	}

	public function my_ads(Request $request) {
		$user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$page_no = $request->input('page_no');

		$my_ads = Property::with('agentDetails')
			->where('status', '!=', 2)
			->where('agent_id', '=', $user_id);
			// ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

		$total_records = $my_ads->count();

		$my_ads = $my_ads->latest()->paginate($this->my_ads_per_page, ['*'], 'page', $page_no);
		$property_list = [];

		foreach ($my_ads as $key => $property) {
			$property_arr = [];
            $property_arr['id']  = (string) $property->id;
            $property_arr['property_id']  = (string) @$property->property_id ?: "";
            $property_arr['title']  = @$property->property_name ?: "";
            $property_arr['property_for']  = @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
            $property_arr['property_for_id']  = (string) (($property->property_for != "" && $property->property_for != null) ? $property->property_for : "");

            $area_name = "";
            $country_name = "";
            if ($property->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->areaDetails->name) ?: "";
                    }

                    if (@$property->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $property_arr['short_address']  = $short_address;

            $currency = @$property->areaDetails->country->currency_code ?: "KD";

            $property_arr['property_price'] = (string) (@$property->price_area_wise ? (number_format(Helper::tofloat($property->price_area_wise), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
            $property_arr['bathroom_count'] = (string) @$property->total_bathrooms ?: "0";
            $property_arr['bedroom_count'] = (string) @$property->total_bedrooms ?: "0";
            $property_arr['toilet_count'] = (string) @$property->total_toilets ?: "0";
            $property_arr['area_sqft'] = (string) @$property->property_sqft_area ?: "0";

            $property_arr['latitude'] = (string) @$property->property_address_latitude ?: "";
            $property_arr['longitude'] = (string) @$property->property_address_longitude ?: "0";
            $property_arr['area_value'] = (string) @$property->areaDetails->updated_range ?: "";

            $property_image = "";
            if ($property->propertyImages->count() > 0) {
                $property_image = asset("storage/property_images/" . $property->id . '/' . $property->propertyImages[0]->property_image);
            }
            $property_arr['image_url'] = (string) $property_image;

            $is_fav = 0;
            if ($user_id) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
            }
            $property_arr['is_favourite'] = (string) $is_fav;
            $property_arr['is_featured'] = (string) ($property->is_featured ?: 0);

			$is_expired = 0;
			if($property->property_subscription_enddate <  date('Y-m-d H:i:s')) {
				$is_expired = 1;
			}
			$property_arr['is_plan_expired'] = (string) $is_expired;
			$property_arr['plan_expiry_date'] = date('Y-m-d', strtotime($property->property_subscription_enddate));

            $property_list[] = $property_arr;
		}

		$response = ["my_ads" => $property_list];

		$result['code']     = (string) 1;
        $result['message']  = "success";
        $result['total_records']  = (int) $total_records;
        $result['per_page'] = (int) $this->my_ads_per_page;
        $result['result'][] = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
	}


	public function report_user(Request $request) {
		$user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
		$agent_id = $request->input('agent_id');

		$mobile_no = $request->input('mobile_no');
		$email = $request->input('email');
		$full_name = $request->input('full_name');
		$country_code = $request->input('country_code');
		$message = $request->input('message');

		$setting = Setting::find(1);
		$admin_email = $setting->email;

		if($agent_id) {

			$report = new ReportUser;
			$report->uname = $full_name ?: "";
			$report->user_id = $user_id ?: "";
			$report->agent_id = $agent_id;
			$report->email = $email ?: "";
			$report->phone = $mobile_no ?: "";
			$report->country_code = $country_code ?: "";
			$report->report_message = $message ?: "";
	
			$report->save();
			$template_id = 5;
	
			$this->sendEmail($language_id, $admin_email, $email, $full_name, $mobile_no, $country_code, $message, $agent_id, "", "", $template_id);
	
			$result['code']     = (string) 1;
			$result['message']  = "user_reported";
		}
		else {
			$template_id = 9;
			$this->sendEmail($language_id, $admin_email, $email, $full_name, $mobile_no, $country_code, $message, "", "", "", $template_id);
	
			$result['code']     = (string) 1;
			$result['message']  = "mail_sent_to_admin";
		}

        $mainResult[] = $result;
        return response()->json($mainResult);
	}


	//mail
	public function sendEmail($language_id, $email, $user_email, $name, $mobile_no, $country_code, $report_message, $agent_id ="", $url = "", $logo = "", $id)
	{

		$setting = Setting::find(1);
		// dd($setting);
		$templateData = Helper::getEmailTemplateData($language_id, $id);
		// dd($templateData);

		$from_email = $setting['from_email'];
		$data = array('email' => $email, "user_email" => $user_email, 'name' => $name, 'url' => $url, "phone" => $mobile_no, "country_code" => $country_code, "language_id" => $language_id, 'id' =>  $id, "agent_id" => $agent_id, 'logo' => $logo, 'from_email' => $from_email, "report_message" => $report_message);	
		// try {
		Mail::send('emails.report_agent', $data, function ($message) use ($data, $templateData) {
			$message->to($data['email'], $templateData->title)->subject($templateData->subject);
			$message->from($data['from_email'], 'DOM - Properties');
		});
		// } catch (\Throwable $th) {
		// 	// throw $th;
		// }
	}



	// subscription plan details


    public function getActivePlanDetails(Request $request) {
		$user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;

		$planDetails = UserSubscription::where('user_id', '=', $user_id)
					->where('end_date', '>=', date('Y-m-d H:i:s'))
					->latest('id')
					->first();
		
		$plan = [];
		if($planDetails) {
			$plan['plan_id'] = (string) $planDetails->plan_id;
			$plan['plan_price'] = (string) $planDetails->total_price;
			$subPlanData = $planDetails->subscriptionPlanDetails;
			// dd($subPlanData);
			$plan['plan_name'] = ($language_id == 2 && $planDetails->plan_name_ar) ? $planDetails->plan_name_ar : $planDetails->plan_name;
			$plan['plan_description'] = ($language_id ==2 && $planDetails->plan_description_ar) ? $planDetails->plan_description_ar : $planDetails->plan_description;

			$plan_duration = "-";
            if($planDetails->plan_duration_value && $planDetails->plan_duration_type){
                $plan_duration = Helper::getValidTillDate( date('Y-m-d H:i:s'), $planDetails->plan_duration_value ,$planDetails->plan_duration_type);
            }

			$plan['plan_duration'] = ($plan_duration != '-') ? $plan_duration['value'] . " " . $plan_duration['label_value'] : "-";

			$plan_for = @$planDetails->plan_type!="" ? Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'.$planDetails->plan_type.'.label_key')) : '-';

			$total_posts = $planDetails->no_of_plan_post?: 0;
			// $total_posts = (int) $total_posts + (int) ($planDetails->no_of_extra_featured_post ?: 0);
			$plan['number_of_total_ads'] = (string) $total_posts;
			$plan['subscription_type'] = "1";
			$plan['plan_for'] = $plan_for;
			$plan['plan_for_id'] = (string) (@$planDetails->plan_type ?: 1);
			$plan['is_free'] = (string) ($planDetails->is_free_plan ?: 0);
			$plan['start_date'] = (string) Carbon::parse($planDetails->start_date)->format('Y-m-d h:i A');
			$plan['end_date'] = (string) Carbon::parse($planDetails->end_date)->format('Y-m-d h:i A');
			$plan['total_ads_posted'] = (string) $planDetails->propertiesSubscribed->count();
			$plan['is_expired'] = (string) ((time() > strtotime($planDetails->end_date)) ? 1 : 0);
			$plan['is_featured'] = (string) ($planDetails->is_featured ?: 0);
			$plan['no_of_default_featured_post'] = (string) ($planDetails->no_of_default_featured_post ?: 0);
			$plan['no_of_extra_featured_post'] = (string) ($planDetails->no_of_extra_featured_post ?: 0);
			$plan['extra_each_normal_post_price'] = (string) ($planDetails->extra_each_normal_post_price ?: 0);
		}

		$planResult = [];
		if($plan) {
			$planResult[] = $plan;
		}
		$result['code']     = (string) 1;
		$result['message']  = "success";
		$result['result'][] = ['plan_detail' => $planResult];

		$mainResult[] = $result;
        return response()->json($mainResult);
    }



}
