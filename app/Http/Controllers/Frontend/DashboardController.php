<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Helper;
use Session;

class DashboardController extends Controller
{

    public function change_language(Request $request){
        $lang_id = $request->input('lang_id');
        $Language = Language::find($lang_id);
        \Session::put('lang', $Language->code);
        return true;
    }

    public function subscription_list() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = \Auth::guard('web')->id();

        // $subscription_plans = SubscriptionPlan::with(['childdata' => function ($child) use ($language_id){
        //         return $child->where('language_id', '=', $language_id);
        //     }])
        //     ->where('plan_type', '=', config('constants.AGENTS_TYPE.individual.value'))
        //     ->where('parent_id', '=', 0)
        //     ->where('status', '=', 1)
        //     ->get();

        $subscription_plans = UserSubscription::with(['propertiesSubscribed' => function ($properties) {
            return $properties->where('status', '!=', 2);
        }])
        ->where('user_id', '=', $user_id)
        ->where('status', '!=', 2)
        ->orderBy('id', 'desc')
        ->get();

        $PageTitle = $labels['my_subscription_plan'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.property.subscription_plans', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'subscription_plans'));
    }

    public function cancel_plan(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = \Auth::guard('web')->id();

        $usersubid = $request->input('plan_id');
        if($usersubid) {

            $user_sub = UserSubscription::find($usersubid);
            $user_sub->status = 2;
            $user_sub->save();

            $mainResult['statusCode'] = 200;
            $mainResult['message'] = $labels['plan_cancelled_successfully'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['plan_cancelled_successfully'];
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

}
