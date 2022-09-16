<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Area;
use App\Models\Cms;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\Setting;
use App\Models\WebmasterSetting;

use Auth;
use Mail;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->route('adminHome');
        // $language_id = Helper::currentLanguage()->id;
        // $labels = Helper::LabelList($language_id);

        // Session::forget('isForgotPass');
        // Session::forget('user_id_1');

        // $property_type = PropertyType::with(['childdata' => function($child) use($language_id) {
        //         return $child->where('language_id', '=', $language_id);
        //     }])
        //     ->where('parent_id', '=', 0)
        //     ->get();
        
        // $area_list = Area::with([
        //         'childdata' => function ($child) use ($language_id) {
        //             $child->where('language_id', '=', $language_id);
        //         },
        //         'properties' => function ($properties_a) use ($language_id) {
        //             $properties_a = $properties_a->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
        //         }
        //     ])
        //     ->whereHas('properties', function ($property) {
        //         $property = $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
        //     })
        //     ->where('parent_id', '=', 0)
        //     ->orderBy('name')
        //     ->skip(0)
        //     ->take(Helper::homeMaxAreaListCountWeb())
        //     ->get();
        
        // $properties = Property::with([
        //         'areaDetails' => function ($area) {
        //             $area->where('status', '=', 1)->where('parent_id', '=', 0);
        //         },
        //         'areaDetails.country' => function ($country) {
        //             $country->where('status', '=', 1)->where('parent_id', '=', 0);
        //         },
        //         'areaDetails.childdata' => function ($areaChild) use ($language_id) {
        //             $areaChild->where('language_id', '=', $language_id);
        //         },
        //         'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
        //             $countryChild->where('language_id', '=', $language_id);
        //         },
        //     ])
        //     // ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
        //     //     $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
        //     // })
        //     ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
        //         $areaDetailsQuery->where('status', '=', 1);
        //     })
        //     ->whereHas('agentDetails', function ($agentQuery) {
        //         $agentQuery->where('status', '=', 1);
        //     })
        //     ->where('status', '=', 1)
        //     ->where('is_featured', '=', 1)
        //     ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

        // $properties = $properties->orderBy('id', 'desc')->paginate(Helper::homeMaxPropertiesListCount());

        // $PageTitle = $labels['home_page_title']; 
        // $PageDescription = "";
        // $PageKeywords = "";
        // $WebmasterSettings = "";
        // $headerFill = 1;
        
        // return view('frontEnd.home', compact('PageTitle', 'PageDescription', 'PageKeywords', 'labels', 'property_type', 'language_id', 'area_list', 'properties', 'headerFill'));
    }

    public function about_us(Request $request ) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $cms_id = 6;
        $cms_data = Cms::find($cms_id);

        if($cms_data) {

            $PageTitle = ( $language_id != 1 && @$cms_data->childdata[0]->page_name ) ? $cms_data->childdata[0]->page_name : $cms_data->page_name;
            $PageDescription = ( $language_id != 1 && @$cms_data->childdata[0]->description ) ? strip_tags($cms_data->childdata[0]->description) : strip_tags($cms_data->description);
            $PageKeywords = "";
            $WebmasterSettings = "";
    
            return view('frontEnd.cms.about', compact('cms_data', 'language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        } else {
            return redirect()->route('frontend.not_found');
        }
    }

    public function terms_and_conditions(Request $request ) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $cms_id = 3;
        $cms_data = Cms::find($cms_id);

        if($cms_data) {

            $PageTitle = ( $language_id != 1 && @$cms_data->childdata[0]->page_name ) ? $cms_data->childdata[0]->page_name : $cms_data->page_name;
            $PageDescription = ( $language_id != 1 && @$cms_data->childdata[0]->description ) ? strip_tags($cms_data->childdata[0]->description) : strip_tags($cms_data->description);
            $PageKeywords = "";
            $WebmasterSettings = "";
    
            return view('frontEnd.cms.terms', compact('cms_data', 'language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        } else {
            return redirect()->route('frontend.not_found');
        }
    }


    public function privacy_policy(Request $request ) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $cms_id = 1;
        $cms_data = Cms::find($cms_id);

        if($cms_data) {

            $PageTitle = ( $language_id != 1 && @$cms_data->childdata[0]->page_name ) ? $cms_data->childdata[0]->page_name : $cms_data->page_name;
            $PageDescription = ( $language_id != 1 && @$cms_data->childdata[0]->description ) ? strip_tags($cms_data->childdata[0]->description) : strip_tags($cms_data->description);
            $PageKeywords = "";
            $WebmasterSettings = "";
    
            return view('frontEnd.cms.privancy', compact('cms_data', 'language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        } else {
            return redirect()->route('frontend.not_found');
        }
    }

    public function faqs(Request $request ) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $cms_id = 5;
        $cms_data = Cms::find($cms_id);

        if($cms_data) {

            $PageTitle = ( $language_id != 1 && @$cms_data->childdata[0]->page_name ) ? $cms_data->childdata[0]->page_name : $cms_data->page_name;
            $PageDescription = ( $language_id != 1 && @$cms_data->childdata[0]->description ) ? strip_tags($cms_data->childdata[0]->description) : strip_tags($cms_data->description);
            $PageKeywords = "";
            $WebmasterSettings = "";
    
            return view('frontEnd.cms.faqs', compact('cms_data', 'language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
        } else {
            return redirect()->route('frontend.not_found');
        }
    }


    public function contact_us() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $setting = Setting::first();

        $PageTitle = $labels['contact_us'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = WebmasterSetting::first();
        return view('frontEnd.contact', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'setting'));
    }

    public function submit_contactus(Request $request) {
        $user_id = "";
        if(Auth::check()) {
            $user_id = Auth::id();
        }
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $full_name = $request->input('name');
        $email = $request->input('email');
        $mobile_no = $request->input('mobile');
        $country_code = $request->input('country_code');
        $message = $request->input('message');

        $setting = Setting::find(1);
		$admin_email = $setting->email;

        $template_id = 9;
        $this->sendEmail($language_id, $admin_email, $email, $full_name, $mobile_no, $country_code, $message, "", "", "", $template_id);

        $response = [];
        $response['statusCode'] = 200;
        $response['message'] = $labels['mail_sent_to_admin'];
        $response['title'] = $labels['mail_sent_to_admin'];
        $response['url'] = route('frontend.thankyou');

        return response()->json($response);
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
		try {
		Mail::send('emails.report_agent', $data, function ($message) use ($data, $templateData) {
			$message->to($data['email'], $templateData->title)->subject($templateData->subject);
			$message->from($data['from_email'], 'DOM - Properties');
		});
		} catch (\Throwable $th) {
			throw $th;
		}
	}

    public function thank_you() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $PageTitle = $labels['contact_us'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.thank_you', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
    }

}
