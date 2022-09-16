<?php

namespace App\Helpers;

use App;
use App\Models\Area;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\Language;
use App\Models\WebmasterSetting;
use App\Models\EmailTemplate;
use App\Models\MainUsers;
use App\Models\RideDetails;
use App\Models\NotificationModal;
use App\Models\Property;
use App\Models\Label;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use GeoIP;
use Config;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Session;
use PhpParser\Builder\Class_;

class Helper
{

    static function createStatus($status = "")
    {
        $flag = 1;
        if (!empty($status)) {
            $flag = 0;
        }
        return $flag;
    }

    static function GeneralWebmasterSettings($var)
    {
        $WebmasterSetting = WebmasterSetting::find(1);
        return @$WebmasterSetting->$var?:20;
    }

    static function GeneralSiteSettings($var)
    {
        $Setting = Setting::find(1);
        return $Setting->$var;
    }

    static function Settings($var)
    {
        $Setting = Setting::find(1);
        return $Setting->$var;
    }

    static function LabelList($lang_id){
        $label_list = Label::with(['childdata' => function($childquery) use($lang_id){
            $childquery->where('language_id', '=', $lang_id);
        }])->where('parentid','=','0')->get();

        $labels = [];
        foreach($label_list as $label){
            $labels[$label->labelname] = ($lang_id!=1 && isset($label->childdata[0]->labelvalue) && !empty($label->childdata[0]->labelvalue))?$label->childdata[0]->labelvalue:$label->labelvalue;
        }
        return $labels;
    }

    static function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    static function get_day_name($timestamp) {

        $date = date('d/m/Y', strtotime($timestamp));
        if($date == date('d/m/Y')) {
          $date = 'Today ' . date('h:i A', strtotime($timestamp));
        } 
        else if($date == date('d/m/Y',(time() - (24 * 60 * 60))) ) {
          $date = 'Yesterday ' . date('h:i A', strtotime($timestamp));
        } 
        else {
            $date = date( env('DATE_FORMAT','Y-m-d') . ' h:i A', strtotime($timestamp));
        }
        return $date;
    }

    static function currentLanguage()
    {
        $locale = App::getLocale();
        if (\Session::has('lang')) {
            $locale = \Session::get('lang');
        }
        $Language = Language::where("code", $locale)->first();
        if (empty($Language)) {
            $Language = Language::where("code", env('DEFAULT_LANGUAGE', 'en'))->first();
        }
        return $Language;
    }

    static function LangFromCode($code)
    {
        return Language::where("code", $code)->first();
    }

    static function LangFromId($lang_id)
    {
        return Language::find($lang_id);
    }

    static function languagesList()
    {
        return Language::where("status", true)->orderby('id', 'asc')->get();
    }

    static function languageName($Language)
    {
        $language_title = "<span class='label light text-dark lang-label'>";
        if (!empty($Language)) {
            if ($Language->icon != "") {
                $language_title .= "<img src=\"" . asset('assets/dashboard/images/flags/' . $Language->icon . '.svg') . "\" alt=\"\">";
            }
            $language_title .= " <small>" . $Language->title . "</small></span>";
        }
        return $language_title;
    }

    static function changeDateFormate($date, $date_format)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);
    }

    static function productImagePath($image_name)
    {
        return public_path('images/products/' . $image_name);
    }

    static function getEmailTemplateData($id){
  //       if($language_id == 1){
		// 	return $template_data = EmailTemplate::find($id);
		// }else{
		// 	$template_data = EmailTemplate::with(['childdata' => function($childdata) use($language_id){
  //               $childdata->where('language_id', '=', $language_id);
  //           }])->find($id);

  //           return @$template_data->childdata[0] ? $template_data->childdata[0] : $template_data; 
		// }
        return $template_data = EmailTemplate::find($id);
    }



    static function getEmailtemplateContentForgotpassword($id, $email = "", $password = "", $name = "", $url = "", $logo = "")
    {
        // echo "string2";exit();
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        // dd($setting);
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Helper::getEmailTemplateData(18);
        // echo "<pre>";print_r($emailtemp);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$password}}' => $password,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$phoneNumber}' => isset($setting->phone) ? $setting->phone : '',
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        // echo $email;exit();
        return $email;
    }

    static function getEmailtemplateContentRegistration($id, $email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        // dd($setting);
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Helper::getEmailTemplateData(1,1);

        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$phoneNumber}' => isset($setting->phone) ? $setting->phone : '',
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        // echo $email;exit();
        return $email;
    }

    static function getEmailtemplateContentReportAgent($id, $language_id, $email, $user_email, $name, $phone, $country_code, $message, $agent_id, $url = "", $logo = "")
    {

        $setting = Setting::first();
        $agent_phone_number = "";
        $agent_email = "";
        $agent_name = "";

        if($agent_id) {
            $agent_details = MainUsers::find($agent_id);
            $agent_phone_number = urldecode($agent_details->country_code) . ' ' . $agent_details->mobile_number;
            $agent_email = urldecode($agent_details->email);
            $agent_name = urldecode($agent_details->full_name);
        }
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';


        $phone_number = $country_code . " " . $phone;
        
        $emailtemp = Helper::getEmailTemplateData($language_id,$id);

        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $user_email,
            '{{$full_name}}' => $name,
            '{{$phone}}' => $phone_number,
            '{{$report_message}}' => $message,
            '{{$agent_email}}' => @$agent_email ?: "",
            '{{$agent_name}}' => @$agent_name ?: "",
            '{{$agent_contact}}' => $agent_phone_number,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$phoneNumber}' => @$setting->phone ?: '',
            '{$supportemail}' => @$setting->support_email ?: '',
        );

        $email = strtr(urldecode($emailtemp->content), $vars);
        // echo $email;exit();
        return $email;
    }

    static function getEmailtemplateContentPropertyInquiry($id, $language_id, $email, $user_email, $name, $phone, $country_code, $message, $agent_id, $property_id, $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = WebmasterSetting::first();
        $agent_details = MainUsers::find($agent_id);
        $property_details = Property::find($property_id);
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';


        $phone_number = $country_code . " " . $phone;
        $agent_phone_number = urldecode($agent_details->country_code) . ' ' . $agent_details->mobile_number;
        $emailtemp = Helper::getEmailTemplateData($language_id,$id);

        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $user_email,
            '{{$full_name}}' => $name,
            '{{$phone}}' => $phone_number,
            '{{$inquiry_message}}' => $message,
            '{{$agent_name}}' => urldecode($agent_details->full_name),
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$property_name}}' => @$property_details->property_name ?: "",
            '{{$property_id}}' => @$property_details->property_id ?: "",
            '{{$property_address}}' => @$property_details->property_address ?: "",
            '{$phoneNumber}' => @$setting->phone ?: '',
            '{$supportemail}' => @$setting->support_email ?: '',
        );

        $email = strtr(urldecode($emailtemp->content), $vars);
        // echo $email;exit();
        return $email;
    }


    static function getEmailtemplateContentAddProperty($id, $language_id, $agent_email, $full_name, $phone, $country_code, $property, $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = WebmasterSetting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';


        $phone_number = $country_code . " " . $phone;
        // $agent_phone_number = urldecode($agent_details->country_code) . ' ' . $agent_details->mobile_number;
        $emailtemp = Helper::getEmailTemplateData($language_id,$id);

        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $agent_email,
            '{{$full_name}}' => $full_name,
            '{{$phone}}' => $phone_number,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$property_name}}' => @$property->property_name ?: "",
            '{{$property_id}}' => @$property->property_id ?: "",
            '{{$property_address}}' => @$property->property_address ?: "",
            '{$phoneNumber}' => @$setting->phone ?: '',
            '{$supportemail}' => @$setting->support_email ?: '',
            '{{$property_title}}' => @$property->property_name ?: "",
            '{{$property_for}}' => @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.'.$property->property_for.'.label_key'), $language_id) : "",
            '{{$property_price}}' => (@$property->base_price ?: 0) . ' ' . Helper::getDefaultCurrency(),
        );

        $email = strtr(urldecode($emailtemp->content), $vars);
        // echo $email;exit();
        return $email;
    }


    static function getEmailtemplateContentApprovedProperty($id, $language_id, $agent_email, $full_name, $phone, $country_code, $property, $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = WebmasterSetting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';


        $phone_number = $country_code . " " . $phone;
        // $agent_phone_number = urldecode($agent_details->country_code) . ' ' . $agent_details->mobile_number;
        $emailtemp = Helper::getEmailTemplateData($language_id,$id);

        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/logo.png');
        }
        $vars = array(
            '{{$email}}' => $agent_email,
            '{{$full_name}}' => $full_name,
            '{{$username}}' => $full_name,
            '{{$phone}}' => $phone_number,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$property_name}}' => @$property->property_name ?: "",
            '{{$property_id}}' => @$property->property_id ?: "",
            '{{$property_address}}' => @$property->property_address ?: "",
            '{$phoneNumber}' => @$setting->phone ?: '',
            '{$supportemail}' => @$setting->support_email ?: '',
            '{{$property_title}}' => @$property->property_name ?: "",
            '{{$property_for}}' => @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.'.$property->property_for.'.label_key'), $language_id) : "",
            '{{$property_price}}' => (@$property->base_price ?: 0) . ' ' . Helper::getDefaultCurrency(),
        );

        $email = strtr(urldecode($emailtemp->content), $vars);
        // echo $email;exit();
        return $email;
    }


    static function getCountryList()
    {
        return Country::with('childdata')->where('parent_id', '=', 0)->where('status', '!=', 2)->get();
    }

    static function getPropertyPriceById($property_id)
    {
        if ($property_id) {
            $property = Property::find($property_id);

            if ($property) {
                $setting = Setting::find(1);
                $service_charge = $setting->service_charge;
                $property_org_price = $property;
                return $property->base_price;
            }
        }

        return 0;
    }

    static function getPropertyPriceByPrice($price, $area_default_range="", $area_updated_range="")
    {
        if ($price) {
            $setting = Setting::find(1);
            $service_charge = $setting->service_charge;
            $property_org_price = Helper::tofloat($price);

            if( $area_updated_range ){
                $property_org_price = $property_org_price + ( ($property_org_price * $area_updated_range)/100 );
            }

            return number_format($property_org_price, 3, '.', ',');
        }

        return 0;
    }

    static function updateAreaWisePropertyPrice($area_id, $area_default_range, $area_updated_range) {
        if($area_id) {
            $area = Area::find($area_id);
            if($area_default_range && $area_updated_range) {
                if($area_updated_range != $area->updated_range ) {
                    $properties = Property::whereHas('agentDetails', function ($agent){
                        $agent->where('status', '!=', 2)->where('is_otp_varified', '=', 1);
                    })
                    ->where('area_id', '=', $area_id)
                    ->where('status', '!=', 2);
                }
            }
        }
    }

    static function getLabelValueByKey($label_key,$language_id=1)
    {
        $labelData = App\Models\Label::with(['childdata' => function($subquery) use($language_id){
            $subquery->where('language_id','=', $language_id);
        }])->where('labelname', '=', $label_key)->where('parentid', '=', 0)->where('status', '=', 1);
        if ($labelData->count() > 0) {
            $labelData = $labelData->first();

            if($language_id>1){
                return @$labelData->childdata[0]->labelvalue?:$labelData->labelvalue; 
            }
            return $labelData->labelvalue;
        } else {
            return "";
        }
    }

    // static function getDefaultCurrency(){
    //     return 'KD';
    // }

    static function getValidTill($start_date,$days){
        $end_date = ($start_date)->add(new DateInterval("P{$days}D") );
        // $dd = date_diff($start_date,$end_date);
        return $end_date;
    }

    static function getValidTillDate($start_date, $amount = 0, $type){
        $end_date = \Carbon\Carbon::parse($start_date);
        if($type == 1)
        {
            $end_date=  $end_date->addDays($amount);
            $text = 1;
            $label_key = "day";
        }
        elseif ($type == 2){
            $end_date=  $end_date->addDays($amount);
            $text =1;
            $label_key = "week";
        }
        // elseif ($days == 3){
        //     $end_date=  $end_date->addMonths(1);
        //     $text = 1;
        //     $label_key = "month";
        // }
        elseif ($type == 3){
            $end_date=  $end_date->addMonths($amount);
            $text = 3;
            $label_key = "months";
        }
        elseif ($type == 4){
            $end_date=  $end_date->addYear($amount);
            $text = 6;
            $label_key = "year";
        }
        // elseif ($days == 6){
        //     $end_date=  $end_date->addYear(1);
        //     $text = 1;
        //     $label_key = "year";
        // }
        
        // $dd = date_diff($start_date,$end_date);
        return array("enddate" => $end_date, "value" => $amount, "label_value" => Helper::getLabelValueByKey($label_key));
    }
    static function convert($convert) {
        // $years = floor($sum / 365);
        // $months = floor(($sum - ($years * 365))/30.5);
        // $days = ($sum - ($years * 365) - ($months * 30.5));

        $years = ($convert / 365) ; // days / 365 days
		$years = floor($years); // Remove all decimals

		$month = ($convert % 365) / 30.5; 
		$month = floor($month); // Remove all decimals

		$days = ($convert % 365) % 30.5; // the rest of days
        return array("days" => $days, "months" => $month, "years" => $years);
    }

    // Image upload compress

    static function correctImageOrientation($filename)
    {
        if (function_exists('exif_read_data')) {

            $exif = @exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    imagejpeg($img, $filename, 95);
                }
            }
        }
    }


    static function compressImage($source_image, $compress_image)
    {
        // dd($source_image, $compress_image);
        $image_info = getimagesize($source_image);
        if ($image_info['mime'] == 'image/jpeg' || $image_info['mime'] == 'image/jpg') {
            $source_image = imagecreatefromjpeg($source_image);
            imagejpeg($source_image, $compress_image, 15);
        } elseif ($image_info['mime'] == 'image/gif' || $image_info['mime'] == 'image/svg') {
            $source_image = imagecreatefromgif($source_image);
            imagegif($source_image, $compress_image, 15);
        } elseif ($image_info['mime'] == 'image/png') {
            $source_image = imagecreatefrompng($source_image);
            imagepng($source_image, $compress_image, 3);
        }
        return $compress_image;
    }
    // 
    // Image upload compress END

    static function getMaxImagesUploadLimit(){
        return Helper::GeneralWebmasterSettings('maximum_property_image_upload');
    }

    static function getOtpExpireTime(){
        return "+1 minutes";
    }

    static function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
      
        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }
    
        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }

    static function getMaxBedroomNumbers(){
        return 10;
    }

    static function getMaxBathroomNumbers(){
        return 10;
    }
    
    static function getMaxToiletNumbers(){
        return 10;
    }

    static function homeMaxPropertiesListCount(){
        return 9;
    }

    static function homeMaxAreaListCount(){
        return 10;
    }

    static function homeMaxAreaListCountWeb(){
        return 5;
    }

    static function getRoundedThousand($number){
        $number2 = Helper::tofloat($number);
        $strlen = strlen((string) $number2);
        if($strlen <= 3){
            return array(array("value" => (string) 1000));
        }

        $max_num = round($number2, -3);
        $sqft_arr = [];
        $diff = $max_num / 10;
        for($i=$max_num; $i >= $diff; $i-=$diff){
            $sqftArr = [];
            $sqftArr['value']  = (string) $i;
            $sqft_arr[] = $sqftArr;
        }
        return $sqft_arr;
    }

    static function getPropertyDeeplink($property_id, $property_slug_name) {
        return "";
    }

    static function getPlayStoreAppLink() {
        $url = "#";
        if(env('ANDROID_APP_ID')){
            $url = "http://play.google.com?id=". env('ANDROID_APP_ID');
        }
        return $url;
    }
    
    static function getAppStoreAppLink() {
        $url = "#";
        if(env('IOS_APP_NAME')) {
            $url = "http://itunes.com/apps/". env('IOS_APP_NAME');
        }
        return $url;
    }

    static function getKuwaitLatLong($var) {
        // 29.0023711,43.0197034,6z
        if($var == 'latitude') {
            return 29.2758538;
        }else if($var=='longitude') {
            return 46.8154797;
        }
    }

    static function sendOtp($phone, $country_code, $otp){
        return true;
    }

    static function getMaxDistanceCheck() {
        return 5;
    }

   static function  orderStatusadmin($id)
    {
        // echo "string";
        // print_r($id);
        // exit();
        // 1-pending,2-initited,3-Assign,4-on the way,5-waiting customer,6-pick-up,7-Arrived,8-cancle  
       
        $orderStatus = RideDetails::find($id);
        // echo "<pre>";print_r($orderStatus->toArray());exit();
        if($orderStatus->ride_status == 0)
        {
              return 'Pending';
        }
        else if($orderStatus->ride_status == 1)
        { 
                return 'Pending';
        } else if($orderStatus->ride_status == 2)
        { 
                return 'Request Accept';
        } else if($orderStatus->ride_status == 3)
        { 
                return 'Reject';
        } else if($orderStatus->ride_status == 4)
        { 
                return 'Arrived At Pickup';
        } else if($orderStatus->ride_status == 5)
        { 
                return 'Picked Up Customer';
        }else if($orderStatus->ride_status == 6)
        { 
                return 'Arrived At Destination';
        }
        else if($orderStatus->ride_status == 7)
        { 
                return 'Complated';
        }
        else if($orderStatus->ride_status == 8)
        { 
                return 'Cancelled by customer';
        }elseif($orderStatus->ride_status == 9){
            return 'Cancelled by admin';
        }else{
            return 'Not Driver Avilable';
        }
        
    }

    static function notification_type($id){
        $notification_order = NotificationModal::find($id);

        if ($notification_order->notification_type == 1) {
            return 'Notification1';
        }elseif ($notification_order->notification_type == 2) {
            return 'Notification2';
        }elseif ($notification_order->notification_type == 3) {
            return 'Notification3';
        }
    }


    static function converttimeTozone($datetime, $to_utc = 0)

    {

        $ip = \Request::ip(); //$_SERVER['REMOTE_ADDR']
        // echo "<pre>";print_r($_SERVER);exit();
        // $ip = $_SERVER['HTTP_CLIENT_IP'] 
        //        ? $_SERVER['HTTP_CLIENT_IP'] 
        //        : ($_SERVER['HTTP_X_FORWARDED_FOR'] 
        //             ? $_SERVER['HTTP_X_FORWARDED_FOR'] 
        //             : $_SERVER['REMOTE_ADDR']);

        $ipInfo = file_get_contents('http://ip-api.com/json/');

        $ipInfo = json_decode($ipInfo);

        $timezone = $ipInfo->timezone;

        date_default_timezone_set($timezone);

        $local_time_zone = date_default_timezone_get();



        $data = array(

            'fromTimezone' => ($to_utc ==1)? $local_time_zone : 'UTC',

            'toTimezone' => ($to_utc ==1)? 'UTC' : $local_time_zone,

            'dateTime' => $datetime,

            'dateTimeFormat' => env('DATE_FORMAT', 'Y-m-d') . ' h:i A'

        );

        $fromTimezone = $data['fromTimezone'];

        $toTimezone = $data['toTimezone'];

        $dateTime = $data['dateTime'];

        $dateTimeFormat = $data['dateTimeFormat'];

        $fromZoneDateTime = new \DateTime($dateTime, new \DateTimeZone($fromTimezone));



        // synchronizing with the to-Timezone

        $fromZoneDateTime->setTimezone(new \DateTimeZone($toTimezone));

        $returnDateTime = date($dateTimeFormat, strtotime($fromZoneDateTime->format('Y-m-d H:i:s')));



        return $returnDateTime;

    }

    // static function notification_type_option($id){
    //     $notification_order = NotificationModal::find($id);

    //     if ($notification_order->notification_type == 1) {
    //         $notification_type_option['notification_type'] = $notification_order->notification_type;
    //         $notification_type_option['notification_type'] = "Notification1";
    //         return $notification_type_option;
    //     }elseif ($notification_order->notification_type == 2) {
    //         $notification_type_option['notification_type'] = $notification_order->notification_type;
    //         $notification_type_option['notification_type'] = "Notification2";
    //         return $notification_type_option;
    //     }elseif ($notification_order->notification_type == 3) {
    //         $notification_type_option['notification_type'] = $notification_order->notification_type;
    //         $notification_type_option['notification_type'] = "Notification3";
    //         return $notification_type_option;
    //     }
    // }

    static function getDefaultCurrency(){

        $get_setting = Setting::find(1);
        // echo "<pre>";print_r($notification_order->toArray());exit();
        return $get_setting['currency'];
       
    }

    static function getuserData($mobile_number)
    {
        $userData = DB::table('customer')->where('mobile_number','=',$mobile_number)->get();
        $userData = $userData->toArray();
        // echo "<pre>";print_r($userData);exit();
        return $userData;
    }

    static function getLoginData($id)
    {
        $userData = DB::table('customer')->where('id',$id)->get();
        $userData = $userData->toArray();
        // echo "<pre>";print_r($userData);exit();
        return $userData;
    }

    static function getCustomerData($customer_address)
    {
        $customerData = DB::table('tbl_address_customer')->where('id',$customer_address)->where('status',1)->get();
        $customerData = $customerData->toArray();
        // echo "<pre>";print_r($userData);exit();
        return $customerData;
    }

    static function getCustomerViewData($user_id)
    {
        $view_address_data = DB::table('tbl_address_customer')->where('user_id',$user_id)->where('status',1)->get();
        $view_address_data = $view_address_data->toArray();
        // echo "<pre>";print_r($userData);exit();
        return $view_address_data;
    }

    static function getPromocode()
    {
        $get_promocode = DB::table('tbl_promocode')->where('status',1)->get();
        $get_promocode = $get_promocode->toArray();

        return $get_promocode;
    }

    static function getusercheckToken($user_id,$token)
    {
        $usertokenData = DB::table('customer')->where('id',$user_id)->where('token','=',$token)->first();
        // $usertokenData = $usertokenData->toArray();
        // echo "<pre>";print_r($usertokenData);exit();
        return $usertokenData;
    }

    static function getcmsData($cms_id)
    {
        $CmsData = DB::table('cms')->where('id',$cms_id)->where('status','=',1)->first();
        // $usertokenData = $usertokenData->toArray();
        // echo "<pre>";print_r($usertokenData);exit();
        return $CmsData;
    }


    static function getRideData($user_id,$type)
    {
        $type = $type;
        if ($type==1) {
            
        $rideData = DB::table('ride_detail')->where('user_id',$user_id)->where('ride_status',1)->get();
        }elseif ($type==2) {
        $rideData = DB::table('ride_detail')->where('user_id',$user_id)->where('ride_status',7)->get();
            
        }elseif ($type==3) {
        $rideData = DB::table('ride_detail')->where('user_id',$user_id)->wherein('ride_status',[8,9])->get();
            
        }

        $rideData = $rideData->toArray();
        
        return $rideData;
    }

    static function getRideDetail($user_id,$ride_id)
    {
        $rideDetail = DB::table('ride_detail')
        ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.id','=','ride_detail.driver_vehicle_detail_id')
        ->leftjoin('car_type','car_type.id','=','ride_detail.vehicle_type_id')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->leftjoin('tbl_ratings','tbl_ratings.ride_id','=','ride_detail.id')
        ->select('ride_detail.*','driver.name as drivername','driver.customer_image as driver_image','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name','car_type.image as car_type_image','tbl_ratings.customer_ratings as driver_rating','tbl_ratings.customer_review as driver_review')
        ->where('ride_detail.user_id',$user_id)->where('ride_detail.id',$ride_id)->first();
        // echo "<pre>";print_r($rideDetail);exit();
        return $rideDetail;
    }

    static function getCarTypeData()
    {
        $carTypeData = DB::table('car_type')->where('status',1)->get();
        $carTypeData = $carTypeData->toArray();

        return $carTypeData;
    }

    static function getVehicleData()
    {
        $getVehicleData = DB::table('vehicle_type')->where('status',1)->get();
        $getVehicleData = $getVehicleData->toArray();

        return $getVehicleData;
    }

    static function getVehicleModalData($vehicle_type_id)
    {
        $vehicleModalData = DB::table('vehicle_model')->where('vehicle_type_id',$vehicle_type_id)->where('status',1)->get();
        $vehicleModalData = $vehicleModalData->toArray();

        return $vehicleModalData;
    }

    static function getDriverVehicleData($driver_vehicle_id)
    {
        $driver_vehicle_data = DB::table('driver_vehicle_detail')->where('id',$driver_vehicle_detail)->where('status',1)->get();
        $driver_vehicle_data = $driver_vehicle_data->toArray();

        return $driver_vehicle_data;
    }

    //Helper class ends
}
