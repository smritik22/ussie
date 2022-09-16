<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\MainUsers;
use App\Models\Transaction;
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

class PaymentController extends Controller
{
    //
    public function index(Request $request)
    {
        return view("dashboard.payment.list");
    }

    public function show($id)
    {
        $id = base64_decode($id);
     
        
        $payment_data = DB::table('tbl_trip_payment')
        ->leftjoin('ride_detail','ride_detail.id','=','tbl_trip_payment.ride_id')
        ->leftjoin('customer as user','user.id','=','tbl_trip_payment.user_id')
        ->leftjoin('customer as driver','driver.id','=','tbl_trip_payment.driver_id')
        ->select('tbl_trip_payment.*','ride_detail.pickup_address','ride_detail.dest_address','ride_detail.start_date as ride_start_date','ride_detail.end_date as ride_end_date','ride_detail.ride_status as ride_detail_status','ride_detail.ride_km','ride_detail.estimate_fare','user.name as username','user.mobile_number as usermobile','driver.mobile_number as drivermobile','driver.name as drivername')
        ->where('tbl_trip_payment.status','!=',2)->where('tbl_trip_payment.id','=',$id)->get();

        
            return view('dashboard.payment.show', compact('payment_data'));
        // }else{
        //     return redirect()->route('ride')->with('errorMessage',__('backend.noDataFound'  ));
        // }
    }

     public function export_payment(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $subscription_type = $request->get('subscription_type');

        $payment_data = DB::table('tbl_trip_payment')
        ->leftjoin('ride_detail','ride_detail.id','=','tbl_trip_payment.ride_id')
        ->leftjoin('customer as user','user.id','=','tbl_trip_payment.user_id')
        ->leftjoin('customer as driver','driver.id','=','tbl_trip_payment.driver_id')
        ->select('tbl_trip_payment.*','ride_detail.pickup_address','ride_detail.dest_address','ride_detail.start_date as ride_start_date','ride_detail.end_date as ride_end_date','ride_detail.ride_status as ride_detail_status','ride_detail.ride_km','ride_detail.estimate_fare','ride_detail.actual_fare','user.name as username','user.mobile_number as usermobile','driver.mobile_number as drivermobile','driver.name as drivername')
        ->where('tbl_trip_payment.status','!=',2);

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $transactions->where('transaction.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $transactions->where('transaction.created_at', '<=', $min_date . ' 23:59:59');
        }

        $get_currency = \Helper::getDefaultCurrency();

        // if ($ride_status) {
        //     // $min_date = Carbon::parse($end_date)->format('Y-m-d');
        //     $properties->where('ride_detail.ride_status','=',$ride_status);
        // }

        
        $payment_data = $payment_data->get();
        // echo "<pre>";print_r($payment_data->toArray());exit();
        $filename = 'payment_report' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','Passenger Name','Driver Name','Ride Id','Pickup Location','Drop Location','Ride Start Date','Ride End Date','Ride Estimated Fare','Ride Actual Fare','Ride Km','Ride Amount','Payment Date'));
        // 11

        $i = 1;
        foreach ($payment_data as $key => $data) {
            // $transaction_id =  isset($data->transaction_id)?:'';
            // echo "<pre>";print_r($transaction_id);exit();
           
            $passenger_name =  isset($data->username) ? urldecode($data->username) : '';
            $driver_name =  isset($data->drivername) ? urldecode($data->drivername) : '';
            $ride_id = isset($data->ride_id) ? $data->ride_id : '';
            $pickup_address = isset($data->pickup_address) ? urldecode($data->pickup_address) : '';
            $dest_address = isset($data->dest_address) ? urldecode($data->dest_address) : '';
           
            $ride_start_date        = @$data->ride_start_date ? Carbon::parse($data->ride_start_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            $ride_end_date        = @$data->ride_end_date ? Carbon::parse($data->ride_end_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            $estimate_fare = isset($data->estimate_fare) ? urldecode($data->estimate_fare) : '';
            $actual_fare = isset($data->actual_fare) ? urldecode($data->actual_fare) : '';
            $ride_km = isset($data->ride_km) ? urldecode($data->ride_km) : '';
            $ride_total_amount = isset($data->total_amount) ? urldecode($data->total_amount) : '';
            // $admin_commission = isset($data->admin_commission) ? urldecode($data->admin_commission) : '';
            $payment_release_date        = @$data->payment_release_date ? Carbon::parse($data->payment_release_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array($i,$passenger_name,$driver_name,$ride_id,$pickup_address,$dest_address,$ride_start_date,$ride_end_date,$get_currency.''.$estimate_fare,$get_currency.''.$actual_fare,$ride_km,$get_currency.''.$ride_total_amount,$payment_release_date));

            $i++;
        }
    }


    public function anyData(Request $request)
    {
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
            $sort = 'user.name';
        } 
        elseif($columnIndex==2){
            $sort = 'driver.name';
        }
        elseif ($columnIndex==3) {
            $sort = 'tbl_trip_payment.total_amount';
        } 
        elseif ($columnIndex==4) {
            $sort = 'tbl_trip_payment.deducation';
        }elseif ($columnIndex==5) {
            $sort = 'tbl_trip_payment.payment_release_date';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // $totalAr = MainUsers::where('status','!=',2)->where('user_type',2);
        // $totalAr = Transaction::where('status','!=',2);
        // $totalAr = DB::table('transaction')
        // ->leftjoin('ride_detail','ride_detail.id','=','transaction.ride_id')
        // ->leftjoin('customer as user','user.id','=','transaction.user_id')
        // ->leftjoin('customer as driver','driver.id','=','transaction.driver_id')
        // ->select('transaction.*','user.name as username','driver.name as drivername')
        // ->where('transaction.status','!=',2);

        $totalAr = DB::table('tbl_trip_payment')
        ->leftjoin('ride_detail','ride_detail.id','=','tbl_trip_payment.ride_id')
        ->leftjoin('customer as user','user.id','=','tbl_trip_payment.user_id')
        ->leftjoin('customer as driver','driver.id','=','tbl_trip_payment.driver_id')
        ->select('tbl_trip_payment.*','user.name as username','driver.name as drivername')
        ->where('tbl_trip_payment.status','!=',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('user.name', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('driver.name', 'LIKE', '%'.urlencode($searchValue).'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        // $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            // $transaction_id = isset($data->transaction_id) ? $data->transaction_id : '';
           $username = isset($data->username) ? $data->username : '';
            $drivername = isset($data->drivername) ? $data->drivername : '';
            $total_amount = isset($data->total_amount) ? $data->total_amount : '';
            $deducation = isset($data->deducation) ? $data->deducation : '';
            $payment_release_date = isset($data->payment_release_date) ? $data->payment_release_date : '';
            // $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            // if ($data->is_driver_approve == 1) {
            //     $is_driver = '<i class="text-success inline">Approve</i>';
            // } elseif ($data->is_driver_approve == 2) {
                
            //     $is_driver = '<i class="text-danger inline">Reject</i>';
            // } else{
            //     $is_driver = '<i class="text-danger inline">Pending</i>';
            // }

            if ($data->payment_status == 1) {
                $status = '<i class="text-success inline">Approve</i>';
            } else {
                $status = '<i class="text-danger inline"><span class="hide">Pending</span></i>';
            }

            $show = route('payment.show', ['id' => base64_encode($data->id)]);
            $edit = route('vehicle.edit', ['id' => base64_encode($data->id)]);
            $delete = route('vehicle.delete', ['id' => base64_encode($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            // $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }
            $get_currency = \Helper::getDefaultCurrency();
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                // "transaction_id" => urldecode($transaction_id),
                "username" => urldecode($username),
                "drivername" => urldecode($drivername),
                "total_amount" => $get_currency.''.$total_amount,
                "deducation" => $get_currency.''.$deducation,
                "payment_release_date" => Carbon::parse($data->payment_release_date)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A'),
                // "is_driver" => $is_driver,
                "status" => $status,
                "options" => $options,
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
