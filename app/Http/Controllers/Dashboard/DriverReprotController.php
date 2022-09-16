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
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;

class DriverReprotController extends Controller
{
    //
    private $uploadPath = "public/uploads/driver_user/";
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


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.driver-report.list");
    }

    public function show($id)
    {
        $id = base64_decode($id);
        $user = MainUsers::where('user_type',2)->find($id);
        if($user){
            $image_url = isset($this->image_uri) ? $this->image_uri : '';
            return view('dashboard.driver-report.show', compact('user','image_url'));
        }else{
            return redirect()->route('driver-report')->with('errorMessage',__('backend.noDataFound'  ));
        }
    }

    public function export_property(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');

        $properties = DB::table('customer')
        ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.driver_id','=','customer.id')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->select('customer.*','driver_vehicle_detail.vehicle_number','driver_vehicle_detail.vehicle_seat_capacity','driver_vehicle_detail.vehicle_name','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name')
        ->where('customer.status','!=',2)
        ->where('customer.user_type',2);
        
        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $properties->where('created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $properties->where('created_at', '<=', $min_date . ' 23:59:59');
        }

        
        $properties = $properties->get();
        // echo "<pre>";print_r($properties->toArray());exit();
        $filename = 'driver_report_' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.','Driver Name','Mobile Number','Email','Driver Vehicle Name','Driver Vehicle Number','Driver Vehicle Capacity','Driver Vehicle Type','Driver Vehicle Modal','Create Date','Status'));
        // 11

        $i = 1;
        foreach ($properties as $key => $data) {
            // $property_id =  isset($data->property_id)?:'';
            $passenger_name =  isset($data->name) ? urldecode($data->name) : '';
            $passenger_mobile = @urldecode($data->country_code) . ' ' . @$data->mobile_number;
            $passenger_email = isset($data->email) ? urldecode($data->email) : '';
            $vehicle_name = isset($data->vehicle_name) ? urldecode($data->vehicle_name) : '';
            $vehicle_number = isset($data->vehicle_number) ? urldecode($data->vehicle_number) : '';
            $vehicle_seat_capacity = isset($data->vehicle_seat_capacity) ? urldecode($data->vehicle_seat_capacity) : '';
            $vehicle_type_name = isset($data->vehicle_type_name) ? urldecode($data->vehicle_type_name) : '';
            $vehicle_model_name = isset($data->vehicle_model_name) ? urldecode($data->vehicle_model_name) : '';
            $passenger_create_date        = @$data->created_at ? Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array( $i,$passenger_name, $passenger_mobile, $passenger_email,$vehicle_name,$vehicle_number,$vehicle_seat_capacity,$vehicle_type_name,$vehicle_model_name, $passenger_create_date, $status));

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
            $sort = 'name';
        } 
        elseif($columnIndex==2){
            $sort = 'email';
        }
        elseif ($columnIndex==3) {
            $sort = 'mobile_number';
        } 
        elseif ($columnIndex==4) {
            $sort = 'created_at';
        }elseif ($columnIndex==5) {
            $sort = 'is_driver_approve';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = MainUsers::where('status','!=',2)->where('user_type',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('name', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere('email', 'LIKE', '%'.urlencode($searchValue).'%')
                            ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        $setting = Setting::first();

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('created_at', '<=', $min_date . ' 23:59:59');
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $full_name = isset($data->name) ? $data->name : '';
            $email = isset($data->email) ? $data->email : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $mobile_number = isset($data->mobile_number) ? $data->mobile_number : '';
            $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            if ($data->is_driver_approve == 1) {
                $is_driver = '<i class="text-success inline driver_status_font">Approve</i>';
            } elseif ($data->is_driver_approve == 2) {
                
                $is_driver = '<i class="text-danger inline driver_status_font">Reject</i>';
            } else{
                $is_driver = '<i class="text-danger inline driver_status_font">Pending</i>';
            }

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('driver-report.show', ['id' => base64_encode($data->id)]);
            $edit = route('driver.edit', ['id' => base64_encode($data->id)]);
            $delete = route('driver.delete', ['id' => base64_encode($data->id)]);

            $date = \Helper::converttimeTozone($data->created_at);

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
                "full_name" => urldecode($full_name),
                "email" => urldecode($email),
                "mobile_number" => $phone,
                "join_date" => $date,
                "is_driver" => $is_driver,
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
