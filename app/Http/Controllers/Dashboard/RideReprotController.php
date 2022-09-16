<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebmasterSection;
use Auth;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Config;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Models\MainUsers;
use App\Models\RideDetails;
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;

class RideReprotController extends Controller
{
    //
    private $uploadPath = "uploads/driver_user/";
    protected $image_uri = "";
    protected $no_image = "";

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $this->setImagePath();
        $this->no_image = asset('assets/dashboard/images/no_image.png');

        // Check Permissions
        if (@Auth::user()->permissions != 0 && Auth::user()->permissions != 1) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

     public function getImagePath(){
        return asset($this->uploadPath);
    }

    public function setImagePath(){
        $this->image_uri = $this->getImagePath() . '/';
    }


     public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function index()
    {
        return view("dashboard.ride-report.list");
    }

    public function show($id)
    {
        $id = decrypt($id);
    	// echo "<pre>";print_r($id);exit;
        $user = MainUsers::where('user_type',2)->find($id);
        // $user = isset($user) ? $user : '';
        $ride_data = DB::table('ride_detail')
        ->leftjoin('customer as user','user.id','=','ride_detail.user_id')
        ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.id','=','ride_detail.driver_vehicle_detail_id')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->leftjoin('tbl_ratings','tbl_ratings.ride_id','=','ride_detail.id')
        ->leftjoin('tbl_trip_payment','tbl_trip_payment.ride_id','=','ride_detail.id')
        ->select('ride_detail.*','user.name as username','driver.name as drivername','user.mobile_number as usermobile','driver.mobile_number as drivermobile','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name','driver_vehicle_detail.vehicle_number as vehicle_number','tbl_ratings.driver_ratings','tbl_ratings.driver_review','tbl_trip_payment.total_amount as customer_total_amount','tbl_trip_payment.deducation as admin_commission')
        ->where('ride_detail.status','!=',2)
        ->where('ride_detail.id',$id)->get();

        $ride_stop_data = DB::table('ride_detail')
        ->leftjoin('ride_route','ride_route.ride_id','=','ride_detail.id')
        ->select('ride_detail.id','ride_route.*')
        ->where('ride_detail.status','!=',2)
        ->where('ride_detail.id',$id)
        ->get();
        // echo "<pre>";print_r($ride_data->toArray());exit();
        // if($user->count() > 0){
        //     $image_url = isset($this->image_uri) ? $this->image_uri : '';
            return view('dashboard.ride-report.show', compact('user','ride_data','ride_stop_data'));
        // }else{
        //     return redirect()->route('ride')->with('errorMessage',__('backend.noDataFound'  ));
        // }
    }

    public function export_property(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $ride_status = $request->get('ride_status');

        // $properties = DB::table('ride_detail')
        // ->leftjoin('customer as user','user.id','=','ride_detail.user_id')
        // ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        // ->select('ride_detail.*','user.name as username','user.mobile_number as user_mobile_number','user.email as user_email','user.country_code as user_country_code','driver.name as drivername','driver.mobile_number as driver_mobile_number','driver.email as driver_email','driver.country_code as driver_country_code')
        // ->where('ride_detail.status','!=',2);

        $properties =DB::table('ride_detail')
        ->leftjoin('customer as user','user.id','=','ride_detail.user_id')
        ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.id','=','ride_detail.driver_vehicle_detail_id')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->leftjoin('tbl_ratings','tbl_ratings.ride_id','=','ride_detail.id')
        ->leftjoin('tbl_trip_payment','tbl_trip_payment.ride_id','=','ride_detail.id')
        ->select('ride_detail.*','user.name as username','user.mobile_number as user_mobile_number','user.email as user_email','user.country_code as user_country_code','driver.name as drivername','driver.mobile_number as driver_mobile_number','driver.email as driver_email','driver.country_code as driver_country_code','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name','driver_vehicle_detail.vehicle_number as vehicle_number','tbl_ratings.driver_ratings','tbl_ratings.driver_review','tbl_trip_payment.total_amount as customer_total_amount','tbl_trip_payment.deducation as admin_commission')
        ->where('ride_detail.status','!=',2);
        
        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $properties->where('ride_detail.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $properties->where('ride_detail.created_at', '<=', $min_date . ' 23:59:59');
        }

        if ($ride_status) {
            // $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $properties->where('ride_detail.ride_status','=',$ride_status);
        }

        
        $properties = $properties->get();
        // echo "<pre>";print_r($properties->toArray());exit();
        $filename = 'ride_report_' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','Passenger Name','Passenger Number','Passenger Email','Driver Name','Driver Number','Driver Email','Pickup Location','Drop Location','Vehicle Make','Vehicle NUmber','Vehicle Model','Ride Status','Driver Rate','Driver Review','Ride Amount','Ride Commission','Create Date'));
        // 11

        $i = 1;
        foreach ($properties as $key => $data) {
            // $property_id =  isset($data->property_id)?:'';
            $passenger_name =  isset($data->username) ? urldecode($data->username) : '';
            $passenger_mobile = @urldecode($data->user_country_code) . ' ' . @$data->user_mobile_number;
            $passenger_email = isset($data->user_email) ? urldecode($data->user_email) : '';
            $driver_name =  isset($data->drivername) ? urldecode($data->drivername) : '';
            $driver_mobile = @urldecode($data->driver_country_code) . ' ' . @$data->driver_mobile_number;
            $driver_email = isset($data->driver_email) ? urldecode($data->driver_email) : '';
            $pickup_address = isset($data->pickup_address) ? urldecode($data->pickup_address) : '';
            $dest_address = isset($data->dest_address) ? urldecode($data->dest_address) : '';
            $dest_address = isset($data->dest_address) ? urldecode($data->dest_address) : '';
            $vehicle_type_name = isset($data->vehicle_type_name) ? urldecode($data->vehicle_type_name) : '';
            $vehicle_number = isset($data->vehicle_number) ? urldecode($data->vehicle_number) : '';
            $vehicle_model_name = isset($data->vehicle_model_name) ? urldecode($data->vehicle_model_name) : '';
            $driver_ratings = isset($data->driver_ratings) ? urldecode($data->driver_ratings) : '';
            $driver_review = isset($data->driver_review) ? urldecode($data->driver_review) : '';
            $customer_total_amount = isset($data->customer_total_amount) ? urldecode($data->customer_total_amount) : '';
            $admin_commission = isset($data->admin_commission) ? urldecode($data->admin_commission) : '';
            $ride_create_date        = @$data->created_date ? Carbon::parse($data->created_date)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array($i,$passenger_name,$passenger_mobile,$passenger_email,$driver_name,$driver_mobile,$driver_email,$pickup_address,$dest_address,$dest_address,$vehicle_type_name,$vehicle_number,$vehicle_model_name,$driver_ratings,$driver_review,$customer_total_amount,$admin_commission,$ride_create_date));

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
         $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');

        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'ride_detail.id';
        } 
        elseif($columnIndex==2){
            $sort = 'ride_detail.start_date';
        }
        elseif ($columnIndex==3) {
            $sort = 'user.name';
        } 
        elseif ($columnIndex==4) {
            $sort = 'driver.name';
        }
        else {
            $sort = 'ride_detail.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // $totalAr = RideDetails::where('status','!=',2);
        $totalAr = DB::table('ride_detail')
        ->leftjoin('customer as user','user.id','=','ride_detail.user_id')
        ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        ->select('ride_detail.*','user.name as username','driver.name as drivername')
        ->where('ride_detail.status','!=',2);
        // echo "<pre>";print_r($totalAr->toArray());exit();
        // $totalAr = MainUsers::where('status','!=',2)->where('user_type',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('ride_detail.start_date', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('user.name', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('driver.name', 'LIKE', '%'.$searchValue.'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        $setting = Setting::first();

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('ride_detail.created_date', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('ride_detail.created_date', '<=', $min_date . ' 23:59:59');
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
        	
            $id = isset($data->id) ? $data->id : '';
            $start_date = isset($data->start_date) ? $data->start_date : '';
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

            // if ($data->status == 1) {
            //     $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            // } else {
            //     $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            // }

            $show = route('ride-report.show', ['id' => encrypt($data->id)]);
            $edit = route('driver.edit', ['id' => encrypt($data->id)]);
            $delete = route('driver.delete', ['id' => encrypt($data->id)]);

             $start_date = \Helper::converttimeTozone($data->start_date);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            // $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" => $id,
                "start_date" => $start_date,
                "username" => urldecode($username),
                "drivername" => urldecode($drivername),
                // "is_driver" => $driver_id,
                // "status" => $status,
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
