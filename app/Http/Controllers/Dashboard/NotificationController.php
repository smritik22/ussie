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

class NotificationController extends Controller
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
        return view("dashboard.notification.list");
    }


     public function show($id)
    {
        $id = base64_decode($id);
        // echo "<pre>";print_r($id);exit;
        $notification_data = DB::table('notification')
        ->leftjoin('customer as user','user.id','=','notification.from_id')
        ->leftjoin('customer as driver','driver.id','=','notification.to_id')
        ->select('notification.*','user.name as username','driver.name as drivername')
        ->where('notification.id','=',$id)
        ->get();

            return view('dashboard.notification.show', compact('notification_data'));
        // }else{
        //     return redirect()->route('ride')->with('errorMessage',__('backend.noDataFound'  ));
        // }
    }

    public function export_notification(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $ride_status = $request->get('ride_status');

        $notification = DB::table('notification')
        ->leftjoin('customer as user','user.id','=','notification.from_id')
        ->leftjoin('customer as driver','driver.id','=','notification.to_id')
        ->select('notification.*','user.name as username','driver.name as drivername');

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $notification->where('notification.created_date', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $notification->where('notification.created_date', '<=', $min_date . ' 23:59:59');
        }

        $get_currency = \Helper::getDefaultCurrency();

        if ($ride_status) {
            // $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $notification->where('notification.notification_type','=',$ride_status);
        }

        
        $notification = $notification->get();
        // echo "<pre>";print_r($notification->toArray());exit();
        $filename = 'notification_report' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','From','To','Notification Type','Notification Title','Notification Description','Read Status','Notification Date'));
        // 11

        $i = 1;
        foreach ($notification as $key => $data) {
            // $transaction_id =  isset($data->transaction_id)?:'';
            // echo "<pre>";print_r($transaction_id);exit();
           
            $passenger_name =  isset($data->username) ? urldecode($data->username) : '';
            $driver_name =  isset($data->drivername) ? urldecode($data->drivername) : '';
            $title =  isset($data->title) ? urldecode($data->title) : '';
            $description =  isset($data->description) ? urldecode($data->description) : '';

            $notification_type = \Helper::notification_type(isset($data->notification_type) ? $data->notification_type :'');

            $notification_type =  $notification_type;

            if ($data->read_status == 1) {
                $read_status = "Read";
            } else {
                $read_status = "Not Read";
            }
            
            // $admin_commission = isset($data->admin_commission) ? urldecode($data->admin_commission) : '';
            $created_date        = @$data->created_date ? Carbon::parse($data->created_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array($i,$passenger_name,$driver_name,$notification_type,$title,$description,$read_status,$created_date));

            $i++;
        }
    }


     public function anyData(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
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
        $ride_status = $request->get('ride_status');
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'notification.title';
        } 
        elseif($columnIndex==2){
            $sort = 'notification.description';
        }
        elseif ($columnIndex==3) {
            $sort = 'notification.notification_type';
        } 
        elseif ($columnIndex==4) {
            $sort = 'user.name';
        }elseif ($columnIndex==5) {
            $sort = 'driver.name';
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

        $totalAr = DB::table('notification')
        ->leftjoin('customer as user','user.id','=','notification.from_id')
        ->leftjoin('customer as driver','driver.id','=','notification.to_id')
        ->select('notification.*','user.name as username','driver.name as drivername');
        // ->where('notification.status','!=',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('notification.title', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('notification.description', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('user.name', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('driver.name', 'LIKE', '%'.urlencode($searchValue).'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        // $setting = Setting::first();

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('notification.created_date', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('notification.created_date', '<=', $min_date . ' 23:59:59');
        }

        if ($ride_status) {
            // $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('notification.notification_type','=',$ride_status);
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr);exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
          
           $title = isset($data->title) ? $data->title : '';
           $description = isset($data->description) ? $data->description : '';
           $username = isset($data->username) ? $data->username : '';
            $drivername = isset($data->drivername) ? $data->drivername : '';
            
            // $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            // if ($data->is_driver_approve == 1) {
            //     $is_driver = '<i class="text-success inline">Approve</i>';
            // } elseif ($data->is_driver_approve == 2) {
                
            //     $is_driver = '<i class="text-danger inline">Reject</i>';
            // } else{
            //     $is_driver = '<i class="text-danger inline">Pending</i>';
            // }
            $error = \Helper::notification_type(isset($data->notification_type) ? $data->notification_type :'');

             $notification_type = '<i class="">'.$error.'</i>';
            //  if ($data->notification_type == 1) {
            //     $notification_type = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            // } else {
            //     $notification_type = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            // }

            if ($data->read_status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('notification.show', ['id' => base64_encode($data->id)]);
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

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "title" => urldecode($title),
                "description" => urldecode($description),
                "username" => urldecode($username),
                "drivername" => urldecode($drivername),
                // "total_amount" => $total_amount,
                // // "mobile_number" => $phone,
                // "transaction_date" => Carbon::parse($data->transaction_date)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A'),
                // "is_driver" => $is_driver,
                "notification_type" => $notification_type,
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
