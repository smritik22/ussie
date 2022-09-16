<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule; 
use App\Helpers\Helper;
use Auth;
use App\Models\MainUsers;
use App\Models\UserFavouriteProperty;
use App\Models\Property;
use App\Models\Setting;
use Redirect;
use Hash;
use Session;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Image;
use Mail;
use File;

class UserController extends Controller
{
    protected $fav_property_per_page;

    public function __construct()
    {
        $this->fav_property_per_page = 6;
    }
    //

    public function index() {
      
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminHome');
        // if (auth()->guard('main_user')->check()) {
        //     echo "fds";exit;
        //     return redirect(route('frontend.homePage'));
        } 
        else if(auth()->guard('web')->check()) {
            return redirect()->route('frontend.homePage');
        }
        else{
            // echo "string";exit();
            return redirect()->route('admin');
            // $language_id = Helper::currentLanguage()->id;
            // $labels = Helper::LabelList($language_id);
            // $PageTitle = $labels['login'];
            // $PageDescription = "";
            // $PageKeywords = "";
            // $WebmasterSettings = "";
            // return view('frontEnd.login', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        }
    }

    public function login(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        
        $user = MainUsers::where('is_otp_varified', '=', 1)->where('mobile_number', '=', $request->mobile_number);
		if (@$user->exists()) {
			$user_data = $user->first();

			if ($user_data->status == 0) {

                $mainResult['statusCode'] = 204;
                $mainResult['message'] = $labels['inactive_account'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['inactive_account'];
                return response()->json($mainResult);
			} 
            else if ($user_data->status == 2) {
                $mainResult['statusCode'] = 204;
                $mainResult['message'] = $labels['account_deleted_contact_to_admin'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['account_deleted_contact_to_admin'];
                return response()->json($mainResult);
			}

			$validate = Hash::check($request->input('password'), $user_data->password);

            if (auth()->guard('web')->attempt(['mobile_number' => $request->input('mobile_number'), 'password' => $request->input('password')])) {
                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['logged_in_successfully'];
                $mainResult['url'] = route('frontend.homePage');
                $mainResult['title'] = $labels['logged_in_successfully'];
                return response()->json($mainResult);
            }
            else{
                $mainResult['statusCode'] = 202;
                $mainResult['message'] = $labels['password_is_incorrect'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['password_is_incorrect'];
                return response()->json($mainResult);
			}
		} else {
			$mainResult['statusCode'] = 203;
            $mainResult['message'] = $labels['mobile_not_registered_with_us'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['mobile_not_registered_with_us'];
            return response()->json($mainResult);
		}
    }

    public function logoutUser(){
        Auth::logout();
        $language_id = Session::has('lang') ? Session::get('lang') : 1;
        Session::flush();
        Session::put('lang', $language_id);
        return redirect()->route('frontend.homePage');
    }

    public function signup(Request $request) {
        
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        Session::forget('isForgotPass');

        // $validation_messages = [
        //     'full_name.required' => $labels['please_enter_name'],
        //     'mobile_number.unique' => $labels['mobile_number_already_taken'],
        //     'email.required' => $labels['please_enter_email'],
        //     'email.email' => $labels['please_enter_valid_email'],
        //     'email.encoded_unique' => $labels['email_already_taken'],
        //     'password.required' => $labels['please_enter_password'],
        // ];

        // $this->validate(
        //     $request,
        //     [
        //         'full_name' => 'required',
        //         'email' =>  'required|email|encoded_unique:users,email,status,2',
        //         'mobile_number' => 'required|unique:App\Models\MainUsers,mobile_number,status,2',
        //         'password' => 'required'
        //     ],
        //     $validation_messages
        // );

        $check_email = MainUsers::where('email', '=', urlencode($request->email))
        ->where('status', '!=', 2)
        ->where('is_otp_varified', '=', 1)->exists();
        
        $check_phone = MainUsers::where('mobile_number', '=', urlencode($request->mobile_number))
        ->where('status', '!=', 2)
        ->where('is_otp_varified', '=', 1)->exists();
        
        if($check_email) {
            $mainResult['statusCode'] = 202;
            $mainResult['message'] = $labels['email_already_taken'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['email_already_taken'];
            return response()->json($mainResult);
        }

        if($check_phone) {
            $mainResult['statusCode'] = 202;
            $mainResult['message'] = $labels['mobile_number_already_taken'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['mobile_number_already_taken'];
            return response()->json($mainResult);
        }

        $user = new MainUsers;
        $user->full_name = @urlencode($request->full_name);
        $user->email = urlencode($request->email);
        $user->mobile_number = $request->mobile_number;
        $user->country_code = urlencode($request->country_code);
        $user->password = Hash::make($request->password);
        $user->status = 2;
        $user->user_type = config('constants.USER_TYPE_GENERAL');
        
        $otp = $this->generateOTP();
        $user->otp = $otp;
        $user->is_otp_varified = 0;
        
        $otp_send = 0;
        if (@$request->mobile_number && @$request->country_code) {
            $otp_send = $this->sendOtp($request->mobile_number, $request->country_code, $user->otp);
            $user->otp_expire_time = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));
        }

        if($user->save()) {
            \Session::put('user_id_1',$user->id);
            
            $mainResult['statusCode'] = 200;
            $mainResult['message'] = $labels['registered_successfully'] . " - " . $user->otp;
            $mainResult['url'] = route('frontend.varify_otp');
            $mainResult['title'] = $labels['registered_successfully'];
            return response()->json($mainResult);

            // return redirect()->route('frontend.varify_otp')->with('doneMessage', $labels['registered_successfully']);
        }
        else{
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
            // return redirect()->route('frontend.signup')->with('errorMessage', $labels['something_went_wrong']);
        }
    }

    public function otp_varify(){
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $PageTitle = $labels['verify_otp'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        $id = \Session::get('user_id_1');
        $users = MainUsers::where('id',$id)->first();
        if(!empty($users))
        {
            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime($users->otp_expire_time);
            $interval = $datetime1->diff($datetime2);

            $elapsed = $interval->format('%I:%S');
            $invert  = $interval->invert;
            if($invert) {
                if(\Session::get('isForgotPass') == 1) {
                    return redirect()->route('frontend.forget_password');
                }
            }
            
            return view('frontEnd.users.otp', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings','users', 'elapsed', 'invert'));
        }
        return redirect()->route('frontend.signup')->with('errorMessage', $labels['something_went_wrong']);
        
    }

    public function resend_otp(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $id = \Session::get('user_id_1');
        if(!$id) {
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = route('frontend.signup');
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }
        $user = MainUsers::where('id',$id)->first();
        if($user) {
            $otp = $this->generateOTP();
            $user->otp = $otp;
            $user->is_otp_varified = 0;
            
            $otp_send = 0;
            $otp_send = $this->sendOtp($user->mobile_number, $user->country_code, $user->otp);
            $user->otp_expire_time = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));

            if($user->save()) {
                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['otp_sent_successfully'] . " - " . $user->otp;
                $mainResult['url'] = route('frontend.varify_otp');
                $mainResult['title'] = $labels['otp_sent_successfully'];
                return response()->json($mainResult);
            }
            else{
                $mainResult['statusCode'] = 201;
                $mainResult['message'] = $labels['something_went_wrong'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['something_went_wrong'];
                return response()->json($mainResult);
            }
        }
        else{
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = route('frontend.signup');
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }
    }

    public function varifyOTP(Request $request) {

        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $otp_verify_type = $request->input('otp_verify_type');

        $user_id = \Session::get('user_id_1');
        $isForgotPass = \Session::get('isForgotPass');
        
        if(!$user_id) {
            $mainResult['statusCode'] = 205;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = route('frontend.signup');
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }

        if(!@$request->digit || count(@$request->digit) != 4){
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['please_enter_otp'];
            $mainResult['url'] = route('frontend.varify_otp');
            $mainResult['title'] = $labels['please_enter_otp'];
            return response()->json($mainResult);
        }

        $otp = implode('',$request->digit);
        $otp = trim($otp);

        $userData = MainUsers::find($user_id);
        if (@$userData) {
            if ($userData->otp == $otp) {

                if($userData->otp_expire_time < date('Y-m-d H:i:s')){
                    $mainResult['statusCode'] = 203;
                    $mainResult['message'] = $labels['otp_expired'];
                    $mainResult['url'] = "";
                    $mainResult['title'] = $labels['otp_expired'];
                    return response()->json($mainResult);
                }
                
                $update = [];
                $update['is_otp_varified'] = 1;
                $update['status'] = 1;
                $userData->update($update);

                if ($otp_verify_type == config('constants.otp_varify_type_register') && @$userData->email) {

                    $logo = asset('assets/frontend/logo/logo.png');
                    $url = '';
                    $email = urldecode($userData->email);
                    $name = urldecode($userData->full_name);
                    $template_id = 1;
                    $this->attachment_email($language_id, $email, $name, '', $logo, $template_id);
                }
                if($otp_verify_type == config('constants.otp_varify_type_register') ) {
                    \Session::forget('user_id_1');
                    $url = route('frontend.login');
                }
                else {
                    $url = route('frontend.reset_password');
                }

                $mainResult['statusCode'] = 200;
                $mainResult['message'] = ($otp_verify_type == config('constants.otp_varify_type_register')) ? $labels['otp_varified_successfully'] : $labels['otp_varified'];
                $mainResult['url'] = $url;
                $mainResult['title'] = ($otp_verify_type == config('constants.otp_varify_type_register')) ? $labels['otp_varified_successfully'] : $labels['otp_varified'];
                return response()->json($mainResult);
            } 
            else {
                $mainResult['statusCode'] = 204;
                $mainResult['message'] = $labels['otp_not_matched'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['otp_not_matched'];
                return response()->json($mainResult);
            }
        }
        else{
            $mainResult['statusCode'] = 206;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = route('frontend.homePage');
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }
    }

    public function email_validate(Request $request) {

        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        
        $email = $this->test_input($request->input('email'));

        if(!$email) {
            return json_encode($labels['please_enter_email']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode($labels['please_enter_valid_email']);
        }

        $check_email = MainUsers::where('status', "!=", 2)
                    ->where('is_otp_varified', '=', 1)
                    ->where('email', '=', urlencode($email))
                    ->exists();

        return  $check_email ? json_encode($labels['email_already_taken']) : json_encode(true);
        
    }


    public function forget_password() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $PageTitle = $labels['forgot_Password'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.forgot_password', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
    }

    public function forgotPassword_submit(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $userCheck = MainUsers::where('mobile_number', $request->mobile_number)->where('country_code', urlencode(trim($request->country_code)))->where('is_otp_varified', '=', 1);

		if ($userCheck->exists()) {
			$userData = $userCheck->first();

			if ($userData->status == 0) {
                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['inactive_account'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['inactive_account'];
                return response()->json($mainResult);
			} else if ($userData->status == 2) {
                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['account_deleted_contact_to_admin'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['account_deleted_contact_to_admin'];
                return response()->json($mainResult);
			} else {
                
				$otp_send = 0;
				$otp = $this->generateOTP();
				$post = [];
				$post['otp'] = $otp;
				$otp_send = $this->sendOtp($request->mobile_number, $request->country_code, $post['otp']);
				$post['otp_expire_time'] = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));

				$userData->update($post);
                \Session::put('isForgotPass', 1);
                \Session::put('user_id_1', $userData->id);

                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['otp_sent_to_this_number'] . ' - ' . $post['otp'];
                $mainResult['url'] = route('frontend.varify_otp');
                $mainResult['title'] = $labels['otp_sent_to_this_number'] . ' - ' . $post['otp'];
                return response()->json($mainResult);
			}
		} else {
            $mainResult['statusCode'] = 204;
            $mainResult['message'] = $labels['mobile_not_registered_with_us'];
            $mainResult['url'] = route('frontend.signup');
            $mainResult['title'] = $labels['mobile_not_registered_with_us'];
            return response()->json($mainResult);
		}
    }

    public function reset_password() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        // dd('HHHHHH');
        $user_id = \Session::get('user_id_1');
        $isForgotPass = \Session::get('isForgotPass');

        if($user_id && $isForgotPass) {
            $PageTitle = $labels['reset_password'];
            $PageDescription = "";
            $PageKeywords = "";
            $WebmasterSettings = "";
            return view('frontEnd.reset_password', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        }
        else {
            return redirect()->route('frontend.forget_password');
        }
    }

    public function submit_reset_password(Request $request) {
        
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $user_id = \Session::get('user_id_1');
		$new_password = $request->input('password');

		$user = MainUsers::where('id', $user_id)->where('status', '!=', 2)->where('is_otp_varified', '=', 1)->first();

		if ($user) {

			if ($user->status == 0) {

                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['inactive_account'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['inactive_account'];
                return response()->json($mainResult);

			} else if ($user->status == 2) {
                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['account_deleted_contact_to_admin'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['account_deleted_contact_to_admin'];
                return response()->json($mainResult);
			}

			if ($new_password) {

				$checkOld = Hash::check($request->input('password'), $user->password);

				if ($checkOld) {

                    $mainResult['statusCode'] = 203;
                    $mainResult['message'] = $labels['your_new_password_must'];
                    $mainResult['url'] = "";
                    $mainResult['title'] = $labels['your_new_password_must'];
                    return response()->json($mainResult);
				}

				$user->update(['password' => Hash::make($new_password)]);

                \Session::forget('isForgotPass');
                \Session::forget('user_id_1');

                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['password_reset_success'];
                $mainResult['url'] = route('frontend.login');
                $mainResult['title'] = $labels['password_reset_success'];
                return response()->json($mainResult);

			} else {
				$mainResult['statusCode'] = 201;
                $mainResult['message'] = $labels['new_password_required'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['new_password_required'];
                return response()->json($mainResult);
			}
		} else {
            $mainResult['statusCode'] = 205;
            $mainResult['message'] = $labels['no_data_is_available'];
            $mainResult['url'] = route('frontend.homePage');
            $mainResult['title'] = $labels['no_data_is_available'];
            return response()->json($mainResult);
		}
    }

    public function account() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $PageTitle = $labels['my_account'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.users.profile', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
    }

    public function change_password(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = \Auth::guard('web')->id();
		$new_password = $request->input('new_password');

		$user = MainUsers::where('id', $user_id)->where('status', '=', 1)->where('is_otp_varified', '=', 1)->first();

		if ($user) {

			if ($new_password) {
				
				$oldMatch = Hash::check($request->input('old_password'), $user->password);
				if(!$oldMatch) {
                    $mainResult['statusCode'] = 203;
                    $mainResult['message'] = $labels['old_password_not_matched'];
                    $mainResult['url'] = "";
                    $mainResult['title'] = $labels['old_password_not_matched'];
                    return response()->json($mainResult);
				}

				$checkOld = Hash::check($new_password, $user->password);
				if ($checkOld) {
                    $mainResult['statusCode'] = 203;
                    $mainResult['message'] = $labels['you_have_entered_old_password'];
                    $mainResult['url'] = "";
                    $mainResult['title'] = $labels['you_have_entered_old_password'];
                    return response()->json($mainResult);
				}

				$user->update(['password' => Hash::make($new_password)]);

                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['password_changed_successfully'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['password_changed_successfully'];
                return response()->json($mainResult);
			} else {
                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['new_password_required'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['new_password_required'];
                return response()->json($mainResult);
			}
		} else {

            Auth::guard('web')->logout();

            $mainResult['statusCode'] = 206;
            $mainResult['message'] = $labels['account_not_exists'];
            $mainResult['url'] = route('frontend.homePage');
            $mainResult['title'] = $labels['account_not_exists'];
            return response()->json($mainResult);
		}
    }

    /* ---------------------------------------------------------------------------- */
    // Profile update
    /* ---------------------------------------------------------------------------- */

    public static function phoneExist($phone, $country_code, $edit = '')
	{
		if ($edit != '') {
			$data = MainUsers::where([
				['mobile_number', '=', trim(urldecode($phone))],
				['country_code', '=', urlencode(trim($country_code))],
				['status', '!=', '2'],
				['is_otp_varified', '=', 1],
				['id', '!=', $edit],
			])->count();
		} else {
			$data = MainUsers::where([
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
			$data = MainUsers::where([
				['email', '=', urlencode(trim($email))],
				['status', '!=', '2'],
				['is_otp_varified', '=', 1],
				['id', '!=', $edit],
			])->count();
		} else {
			$data = MainUsers::where([
				['email', '=', urlencode(trim($email))],
				['is_otp_varified', '=', 1],
				['status', '!=', '2'],
			])->count();
		}
		return $data;
	}

    public function update_profile(Request $request) {
        // dd($request->all());
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = \Auth::guard('web')->id();

        if (!empty($request->input('mobile_number'))) {
            $detail2 = $this->phoneExist($request->input('mobile_number'), $request->input('country_code'), $user_id);
            if ($detail2 > 0) {
                $mainResult['statusCode'] = 204;
                $mainResult['message'] = $labels['mobile_already_taken'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['mobile_already_taken'];
                return response()->json($mainResult);
            }
        }
        if (!empty($request->input('email'))) {
            $emailExists = $this->emailExist($request->input('email'), $user_id);
            if ($emailExists > 0) {
                $mainResult['statusCode'] = 204;
                $mainResult['message'] = $labels['email_already_taken'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['email_already_taken'];
                return response()->json($mainResult);
            }
        }

        $user = MainUsers::find($user_id);

        $user->full_name = isset($request->full_name) ? urlencode($request->full_name) : "";
        $user->mobile_number = isset($request->mobile_number) ? $request->mobile_number : '';
        $user->email = isset($request->email) ? urlencode($request->email) : '';
        $user->country_code = isset($request->country_code) ? urlencode($request->country_code) : '';
        $user->user_short_address = isset($request->short_adress) ? $request->short_adress : '';
        $user->about_user = isset($request->about_user) ? $request->about_user : '';

        $formFileName = "profile_image";
        $fileFinalName_ar = "";

        if ($request->$formFileName) {

            try {
                $image = $request->file($formFileName);
                $fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
                // $destinationPath = public_path( '/' . 'uploads/general_users/' );
                $pathImg =  public_path( 'uploads/general_users/' );
                //$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
                $request->file($formFileName)->move($pathImg, $fileFinalName_ar);
                // $img = Image::make($image->getRealPath());
                // $img->resize(500, null, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->save($destinationPath , $fileFinalName_ar);
            } 
            catch (\Throwable $th) {
                throw $th;
            }

        }

        if ($fileFinalName_ar != "") {
            // Delete a User file
            if ($user->profile_image != "") {
                \File::delete( asset('uploads/general_users/'. $user->profile_image));
            }

            $user->profile_image = $fileFinalName_ar;
        }

        if ($user->save()) {
            $mainResult['statusCode'] = 200;
            $mainResult['message'] = $labels['profile_updated_successfully'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['profile_updated_successfully'];
            $mainResult['profileimage'] = $user->profile_image ? asset('uploads/general_users/'. $user->profile_image) : "";
            return response()->json($mainResult);
        } 
        else {
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }
    }

    public function getFavourites(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = \Auth::guard('web')->id();
        $page_no = $request->input('page_no') ?: 1;

        $properties = UserFavouriteProperty::whereHas('PropertyDetails', function ($property) {
            $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
        })
        ->whereHas('UserDetails', function ($agent) {
            $agent->where('status', '=', 1);
        })
        ->where('user_id', '=', $user_id);

        $total_records = $properties->count();

        $properties = $properties->latest()->paginate($this->fav_property_per_page, ['*'], 'page', $page_no);

        $html = view('frontEnd.users.profile.favourites', compact('properties', 'labels', 'language_id', 'user_id'))->render();

        $mainResult['statusCode'] = 200;
        $mainResult['html'] = $html;
        $mainResult['total_page'] = $properties->lastPage();
        $mainResult['total_records'] = $total_records;
        return response()->json($mainResult);
    }

    private function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    // =================================================

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

    private function generateOTP()
	{
		return rand(1000, 9999);
	}

    private function sendOtp($phone, $country_code, $otp)
	{
		Helper::sendOtp($phone, $country_code, $otp);
	}

}
