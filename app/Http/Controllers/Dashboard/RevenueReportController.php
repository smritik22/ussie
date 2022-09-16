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

class RevenueReportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view("dashboard.report.revenue_report");
    }

    public function export(Request $request)
    {
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $subscription_type = $request->get('subscription_type');
        $plan_type = $request->get('plan_type');

        $transactions = Transaction::with(['agentDetails' => function($agent){
            $agent->where('status', '!=', 2);
        }]);

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $transactions->where('created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $transactions->where('created_at', '<=', $min_date . ' 23:59:59');
        }
        
        if ($plan_type != "") {
            $transactions->where('plan_type', '=', (int) $plan_type );
        }

        if ($subscription_type != "") {
            $transactions->where('subscription_type', '=', (int) $subscription_type );
        }

        $transactions = $transactions->get();

        $filename = 'revenue_report_' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','Transaction ID', 'Subscription Type' , 'Agent Name', 'Agent Phone', 'Amount', 'Date Transferred', 'Status'));

        $i = 1;
        foreach ($transactions as $key => $data) {

            $transaction_id = $data->trans_no;
            $subscription_type = @config('constants.SUBSCRIPTION_TYPE.'.$data->subscription_type.'.label_key') ? Helper::getLabelValueByKey(config('constants.SUBSCRIPTION_TYPE.'.$data->subscription_type.'.label_key')) : "-";
            $agent_name = @$data->agentDetails->full_name?:"";
            $agent_contact = @urldecode($data->agentDetails->country_code) . ' ' . @$data->agentDetails->mobile_number;

            $currency = "KD";
            if ($data->area_id) {
                $area = Area::find($data->area_id);
                $country = Country::find($area->country_id);
                $currency = $country->currency_code;
            }

            $amount = @$data->amount ? $data->amount . ' ' . $currency : "-";
            $date = @$data->created_at ? Carbon::parse( $data->created_at )->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-";

            $status =  'Success';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Pending';
            }

            fputcsv($file, array( $i, $transaction_id, $subscription_type, @urldecode($agent_name), $agent_contact, $amount, $date, $status ));

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
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');

        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'transactions.trans_no';
        } elseif ($columnIndex == 2) {
            $sort = 'agent.full_name';
        } 
        else if($columnIndex == 3){
            $sort = 'agent.mobile_number';
        } 
        else if($columnIndex == 4){
            $sort = 'transactions.amount';
        } 
        else if($columnIndex == 5){
            $sort = 'transactions.created_at';
        } 
        else if($columnIndex == 6){
            $sort = 'transactions.status';
        } 
        else {
            $sort = 'transactions.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Transaction::select('transactions.*','agent.full_name as agent_name','agent.country_code', 'agent.mobile_number', 'user_subscriptions.plan_type as planType', 'user_subscriptions.total_price as TotalPrice')
            ->leftjoin('users as agent', 'agent.id','=','transactions.agent_id')
            ->leftjoin('user_subscriptions', 'user_subscriptions.transaction_id', '=', 'transactions.id')
            ->where('agent.status', '!=', 2);

        $totalRecords = $totalAr->get()->count();


        if ($searchValue != "") {

            $totalAr = $totalAr->where(function ($query)  use ($searchValue)  {
                $query->where('transactions.trans_no', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('agent.full_name', 'LIKE', '%' . urlencode($searchValue) . '%')
                        ->orWhere(DB::raw("CONCAT(agent.country_code, '+', agent.mobile_number)"), 'LIKE', '%' . urlencode($searchValue) . '%')
                        ->orWhere('transactions.amount', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('transactions.created_at', 'LIKE', '%' . $searchValue . '%');
            });
        }

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('transactions.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('transactions.created_at', '<=', $min_date . ' 23:59:59');
        }

        if ($request->get('subscription_type')!="") {
            $totalAr->where('transactions.subscription_type', '=', (int) $request->get('subscription_type'));
        }

        if ($request->get('plan_type')!="") {
            $totalAr->where('user_subscriptions.plan_type', '=', (int) $request->get('plan_type'));
        }

        $totalDiplayRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($totalAr as $key => $data) {

            $status = "";
            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $currency = "KD";
            if ($data->area_id) {
                $area = Area::find($data->area_id);
                $country = Country::find($area->country_id);
                $currency = $country->currency_code;
            }
            
            $subscription_type = @config('constants.AGENT_TYPE.'.$data->planType.'.label_key')?Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'.$data->planType.'.label_key')):"-";

            $data_arr[] = array(
                "transaction_number" => @$data->trans_no ?: "",
                "subscription_type" => @$subscription_type,
                "agent_name" => @urldecode($data->agentDetails->full_name)?:"",
                "agent_contact" => @urldecode($data->agentDetails->country_code) . ' ' . @$data->agentDetails->mobile_number,
                "amount" => @$data->amount?$data->amount . ' ' . $currency : "-",
                "date_listed" => @$data->created_at ? Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
                "status" => $status,
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalDiplayRecords,
            "aaData" => $data_arr
        );
        echo json_encode($response);
    }

    
} // RevenueReportController Class End
