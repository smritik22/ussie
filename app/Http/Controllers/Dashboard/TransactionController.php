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

class TransactionController extends Controller
{
    //
    // Define Default Variables

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
        return view("dashboard.transaction.list");
    }

     public function show($id)
    {
        $id = base64_decode($id);
        // echo "<pre>";print_r($id);exit;
        $user = MainUsers::where('user_type',2)->find($id);
        // $user = isset($user) ? $user : '';
        $ride_data = DB::table('transaction')
        ->leftjoin('ride_detail','ride_detail.id','=','transaction.ride_id')
        ->leftjoin('customer as user','user.id','=','transaction.user_id')
        ->leftjoin('customer as driver','driver.id','=','transaction.driver_id')
        ->select('transaction.*','user.name as username','user.mobile_number as usermobile','driver.mobile_number as drivermobile','driver.name as drivername')
        ->where('transaction.status','!=',2)->get();

        // $ride_stop_data = DB::table('ride_detail')
        // ->leftjoin('ride_route','ride_route.ride_id','=','ride_detail.id')
        // ->select('ride_detail.id','ride_route.*')
        // ->where('ride_detail.status','!=',2)
        // ->where('ride_detail.id',$id)
        // ->get();
        // echo "<pre>";print_r($ride_data->toArray());exit();
        // if($user->count() > 0){
        //     $image_url = isset($this->image_uri) ? $this->image_uri : '';
            return view('dashboard.transaction.show', compact('user','ride_data'));
        // }else{
        //     return redirect()->route('ride')->with('errorMessage',__('backend.noDataFound'  ));
        // }
    }



    public function export(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $subscription_type = $request->get('subscription_type');

        $transactions = DB::table('transaction')
        ->leftjoin('ride_detail','ride_detail.id','=','transaction.ride_id')
        ->leftjoin('customer as user','user.id','=','transaction.user_id')
        ->leftjoin('customer as driver','driver.id','=','transaction.driver_id')
        ->select('transaction.*','ride_detail.pickup_address','ride_detail.dest_address','user.name as username','user.mobile_number as user_mobile_number','user.mobile_number as usermobile','user.country_code as user_country_code','driver.country_code as driver_country_code','driver.mobile_number as drivermobile','driver.name as drivername')
        ->where('transaction.status','!=',2);

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

        
        $transactions = $transactions->get();
        // echo "<pre>";print_r($transactions->toArray());exit();
        $filename = 'transaction_report' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','Transaction Id','Passenger Name','Passenger Number','Driver Name','Driver Number','Pickup Location','Drop Location','Ride Amount','Transaction Date'));
        // 11

        $i = 1;
        foreach ($transactions as $key => $data) {
            // $transaction_id =  isset($data->transaction_id)?:'';
            // echo "<pre>";print_r($transaction_id);exit();
            $transaction_id =  isset($data->transaction_id) ? urldecode($data->transaction_id) : '';
            $passenger_name =  isset($data->username) ? urldecode($data->username) : '';
            $passenger_mobile = @urldecode($data->user_country_code) . ' ' . @$data->user_mobile_number;
          
            $driver_name =  isset($data->drivername) ? urldecode($data->drivername) : '';
            $driver_mobile = @urldecode($data->driver_country_code) . ' ' . @$data->drivermobile;
            
            $pickup_address = isset($data->pickup_address) ? urldecode($data->pickup_address) : '';
            $dest_address = isset($data->dest_address) ? urldecode($data->dest_address) : '';
           
            $customer_total_amount = isset($data->total_amount) ? urldecode($data->total_amount) : '';
            // $admin_commission = isset($data->admin_commission) ? urldecode($data->admin_commission) : '';
            $transaction_date        = @$data->transaction_date ? Carbon::parse($data->transaction_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array($i,$transaction_id,$passenger_name,$passenger_mobile,$driver_name,$driver_mobile,$pickup_address,$dest_address,$get_currency.''.$customer_total_amount,$transaction_date));

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
            $sort = 'transaction.transaction_id';
        } 
        elseif($columnIndex==2){
            $sort = 'user.name';
        }
        elseif ($columnIndex==3) {
            $sort = 'driver.name';
        } 
        elseif ($columnIndex==4) {
            $sort = 'transaction.total_amount';
        }elseif ($columnIndex==5) {
            $sort = 'transaction.transaction_date';
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
        $totalAr = DB::table('transaction')
        ->leftjoin('ride_detail','ride_detail.id','=','transaction.ride_id')
        ->leftjoin('customer as user','user.id','=','transaction.user_id')
        ->leftjoin('customer as driver','driver.id','=','transaction.driver_id')
        ->select('transaction.*','user.name as username','user.mobile_number as usermobile','driver.mobile_number as drivermobile','driver.name as drivername')
        ->where('transaction.status','!=',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('transaction.transaction_id', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('user.name', 'LIKE', '%'.urlencode($searchValue).'%')
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
            $transaction_id = isset($data->transaction_id) ? $data->transaction_id : '';
           $username = isset($data->username) ? $data->username : '';
            $drivername = isset($data->drivername) ? $data->drivername : '';
            $total_amount = isset($data->total_amount) ? $data->total_amount : '';
            $transaction_date = isset($data->transaction_date) ? $data->transaction_date : '';
            // $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            // if ($data->is_driver_approve == 1) {
            //     $is_driver = '<i class="text-success inline">Approve</i>';
            // } elseif ($data->is_driver_approve == 2) {
                
            //     $is_driver = '<i class="text-danger inline">Reject</i>';
            // } else{
            //     $is_driver = '<i class="text-danger inline">Pending</i>';
            // }

             $date = \Helper::converttimeTozone($data->transaction_date);

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('transaction.show', ['id' => base64_encode($data->id)]);
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
            $error = \Helper::getDefaultCurrency();

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "transaction_id" => urldecode($transaction_id),
                "username" => urldecode($username),
                "drivername" => urldecode($drivername),
                "total_amount" => $error.''.$total_amount,
                // "mobile_number" => $phone,
                "transaction_date" => $date,
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
