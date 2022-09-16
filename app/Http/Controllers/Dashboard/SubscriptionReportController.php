<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\MainUsers;
use App\Models\Transaction;
use App\Models\SubscriptionPlan;
use App\Models\Area;
use App\Models\Country;
use Auth;
use File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use Storage;
use DB;
use Str;
use Session;
use Throwable;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;

class SubscriptionReportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view("dashboard.report.subscription_report");
    }

    // EXPORT CSV FILE
    public function export_property(Request $request)
    {
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');

        $subscriptions =  \DB::table('subscription_plans')
                        ->select('subscription_plans.*','userSubscriptions.totalSubs','users_bought.totalRevenue')
                        ->leftjoin(\DB::raw('(SELECT plan_id,user_id,COUNT(id) as totalSubs FROM user_subscriptions GROUP BY user_id,plan_id ) as userSubscriptions'),'subscription_plans.id','=','userSubscriptions.plan_id')
                        ->leftjoin(\DB::raw('(SELECT plan_id,SUM(total_price) as totalRevenue FROM user_subscriptions GROUP BY plan_id) as users_bought'),'subscription_plans.id','=','users_bought.plan_id')
                        ->where('subscription_plans.parent_id', '=', 0);
        
        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $subscriptions->where('subscription_plans.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $subscriptions->where('subscription_plans.created_at', '<=', $min_date . ' 23:59:59');
        }

        $subscriptions = $subscriptions->get();

        // dd($subscriptions->toArray());
        $filename = 'subscription_plans_report_' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.', 'Plan name', 'Plan duration', 'Number of ads', 'Total agents subscribed', 'Plan type', 'Plan price', 'Total plan revenue', 'Created on'));

        $i = 1;
        foreach ($subscriptions as $key => $data) {
            $plan_type = @$data->plan_type!="" ? Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'.$data->plan_type.'.label_key')) : '-';

            $plan_duration = "-";
            if($data->plan_duration_type && $data->plan_duration_value ){
                $plan_duration = Helper::getValidTillDate( date('Y-m-d H:i:s'), $data->plan_duration_value ,$data->plan_duration_type);
            }

            fputcsv($file, array($i,
                                $data->plan_name, 
                                $plan_duration['value'] . " " . $plan_duration['label_value'],
                                $data->no_of_plan_post,
                                $data->totalSubs, 
                                $plan_type, 
                                ((@$data->plan_price>0) ? number_format($data->plan_price,3) . ' KD' : '-'),
                                (@$data->totalRevenue>0 ? number_format($data->totalRevenue,3,'.',',') :0) . ' ' . \Helper::getDefaultCurrency(),Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A')
                            )
            );

            $i++;
        }

    }

    public function anyData(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 1) {
            $sort = 'subscription_plans.plan_name';
        } elseif ($columnIndex == 2) {
            $sort = 'subscription_plans.plan_duration_type, subscription_plans.plan_duration_value';
        } elseif ($columnIndex == 3) {
            $sort = 'subscription_plans.no_of_plan_post';
        }
        elseif ($columnIndex == 4) {
            $sort = 'userSubscriptions.totalSubs';
        }
        elseif ($columnIndex == 5) {
            $sort = 'subscription_plans.plan_type';
        }
        elseif ($columnIndex == 6) {
            $sort = 'subscription_plans.plan_price';
        }
        elseif ($columnIndex == 7) {
            $sort = 'users_bought.totalRevenue';
        }
        elseif ($columnIndex == 8) {
            $sort = 'subscription_plans.created_at';
        }
        else {
            $sort = 'subscription_plans.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // $totalAr = SubscriptionPlan::
        //             // with(['planSubscriptions' => function ($subscriptions){
        //             //     // $subscriptions;
        //             // }])
        //             select('subscription_plans.*', \DB::raw('COUNT(user_subscriptions.id) as total_subscriptions'))
        //             ->leftjoin('user_subscriptions','user_subscriptions.plan_id','=','subscription_plans.id')
        //             // ->where('subscription_plans.id','=', 2)
        //             ->where('subscription_plans.parent_id', '=', 0);

        $totalAr = \DB::table('subscription_plans')
                        ->select('subscription_plans.*','userSubscriptions.totalSubs','users_bought.totalRevenue')
                        ->leftjoin(\DB::raw('(SELECT plan_id,user_id,COUNT(id) as totalSubs FROM user_subscriptions GROUP BY user_id,plan_id ) as userSubscriptions'),'subscription_plans.id','=','userSubscriptions.plan_id')
                        ->leftjoin(\DB::raw('(SELECT plan_id,SUM(total_price) as totalRevenue FROM user_subscriptions GROUP BY plan_id) as users_bought'),'subscription_plans.id','=','users_bought.plan_id')
                        ->where('subscription_plans.parent_id', '=', 0);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('subscription_plans.plan_name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('subscription_plans.plan_duration', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('subscription_plans.plan_price', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('subscription_plans.no_of_plan_post', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('userSubscriptions.totalSubs', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('users_bought.totalRevenue', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('subscription_plans.created_at', 'LIKE', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
        ->skip($start)
        ->take($rowperpage)
        ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {

            // dd($data);
            $plan_type = @$data->plan_type!="" ? Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'.$data->plan_type.'.label_key')) : '-';

            $plan_duration = "-";
            if($data->plan_duration_type && $data->plan_duration_value){
                $plan_duration = Helper::getValidTillDate( date('Y-m-d H:i:s'), $data->plan_duration_value ,$data->plan_duration_type);
            }

            $data_arr[] = array(
                'id' => $data->id,
                'plan_name' => @$data->plan_name ? : '-',
                'plan_duration' => $plan_duration['value'] . " " . $plan_duration['label_value'],
                'number_of_ads' => @$data->no_of_plan_post ? : '0',
                'total_agents_subscribed' => @$data->totalSubs? : 0,
                'plan_type' => $plan_type,
                'plan_price' => ((@$data->plan_price>0) ? number_format($data->plan_price,3) . ' KD' : '-'),
                'total_plan_revenue' => (@$data->totalRevenue>0 ? number_format($data->totalRevenue,3) :0) . ' ' . \Helper::getDefaultCurrency(),
                'created_on' => Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A'),
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );
        echo json_encode($response);
    }


}
