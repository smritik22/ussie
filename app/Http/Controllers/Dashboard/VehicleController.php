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
use App\Models\Vehicle;
use App\Models\VehicleModal;
use App\Models\PromoCode;
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;


class VehicleController extends Controller
{
    //

     private $uploadPath = "uploads/driver_user/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Vehicle-Make";
    private $promocode = "Promocode";

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
    	$vehicles = Vehicle::orderby('id', 'desc')->get();

        $vehicle = count($vehicles);
        return view("dashboard.vehicle.list", compact("vehicle","vehicles"));
    }

    public function create()
    {
        // if( !(\Helper::check_permission(5,2)) ){
        //     return Redirect::to(route('NoPermission'))->send();
        // }

          // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        return view("dashboard.vehicle.create");
    }

    public function store(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
        $vehicle = new Vehicle();
        $this->validateRequest();
        $vehicle->name = $request->name;
        // $label->Label_value = $request->label_value;
        $vehicle->status = 1;
        // $label->parentid = 0;
        // $label->language_id = 1;
        $vehicle->created_at = date('Y-m-d H:i:s');
        $vehicle->save(); 

        return redirect()->route('vehicle')
            ->with('success', $this->title.' '.__('backend.addDone'));
    }

    public function edit($id)
    {
    	// echo "<pre>";print_r($id);exit();
    	$id = base64_decode($id);
        $vehicle = Vehicle::find($id);
        // echo "<pre>";print_r($vehicle)
        return view('dashboard.vehicle.edit', compact('vehicle'));
    }

    public function update(Request $request,$id)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
    	// print_r($id);exit();
        $this->validateRequest($id);
        $vehicle = Vehicle::find($id);
        //$label->labelname = $request->label_key;
        $vehicle->name = $request->name;
        $vehicle->status = 1;
        $vehicle->updated_at = date('Y-m-d H:i:s');
        $vehicle->save(); 

        return redirect()->route('vehicle')->with('success', $this->title.' '.__('backend.saveDone'));
    }

    public function update_data(Request $request,$id)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        // print_r($id);exit();
        $this->validateRequest_data($id);
        $promocode = PromoCode::find($id);

        $formFileName = "promocode";
        $fileFinalName_ar = "";

        if (request()->has('promocode_image')) 
        {

            $avatarName = time().'.'.request()->promocode_image->getClientOriginalExtension();
            $file = request()->file('promocode_image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/promocode');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

             $promocode->update([                 
                'promocode_image' => $avatarName,
            ]);
        }

        $promocode->promocode_name = $request->promocode_name;
        $promocode->promocode = $request->promocode;
        $promocode->promocode_percentage = $request->promocode_percentage;
        $promocode->page_content = $request->page_content;
        // $promocode->promocode_image = $avatarName;
        // $promoCode->start_date = $request->start_date;
        $promocode->start_date = date("Y-m-d h:i:s", strtotime($request->start_date));
        $promocode->end_date = date("Y-m-d h:i:s", strtotime($request->end_date));
        //$label->labelname = $request->label_key;
    
        $promocode->save(); 

        return redirect()->route('promocode')->with('success', $this->promocode.' '.__('backend.saveDone'));
    }

    public function validateRequest_data($id="",$childId="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'promocode_name' => 'required',
                'promocode' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'page_content' => 'required',
                // 'promocode_image' => 'required',
            ]);

        }else if($childId != ''){
            $validateData =request()->validate([
                'promocode_name' => 'required',
                'promocode' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'page_content' => 'required',
                // 'promocode_image' => 'required',
            ]);
        }
        else{

            $validateData =request()->validate([
                'promocode_name' => 'required|unique:tbl_promocode',
                'promocode' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'page_content' => 'required',
                // 'promocode_image' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function validateRequest($id="",$childId="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'name' => 'required|regex:/^[A-Za-z0-9 ]+$/|max:50',
            ]);

        }else if($childId != ''){
            $validateData =request()->validate([
                'name' => 'required|regex:/^[A-Za-z0-9 ]+$/|max:50',
            ]);
        }
        else{

            $validateData =request()->validate([
                'name' => ['required','regex:/^[A-Za-z0-9 ]+$/','max:50',Rule::unique('vehicle_type')->where(function ($query){

            return $query->where('status','!=','3');

            })],
                // 'label_value' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function show($id)
    {
    	$id = base64_decode($id);
    	// echo "<pre>";print_r($id);exit();
        $vehicle = Vehicle::find($id);
        return view('dashboard.vehicle.show', compact('vehicle'));
    }

    public function destroy($id)
    {
    	$id = base64_decode($id);
        $driver_vehicle = DB::table('vehicle_model')->where('vehicle_type_id',$id)->where('status',1)->get();
        $driver_vehicle = $driver_vehicle->toArray();
        // echo "<pre>";print_r($driver_vehicle->toArray());exit();
        // $driver_vehicle = $driver_vehicle->toArray();
        if (empty($driver_vehicle)) {
            
            // echo "string";exit();
        $vehiclemodal=VehicleModal::where('vehicle_type_id',$id)->first();
        if (!empty($vehiclemodal)) {
            
        $vehiclemodal->status = 3;
        $vehiclemodal->save();
        }
        $vehicle = Vehicle::find($id);
        $vehicle->status = 3;
        $vehicle->save();
        
        return redirect()->route('vehicle')
            ->with('success', 'Vehicle deleted successfully');
        }else{
            // echo "string1";exit();
            return redirect()->route('vehicle')
            ->with('danger', 'Already Vehicle Modal Create');
        }
    }

     public function updateAll(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        if($request->ajax())
        {
            // echo "<pre>";print_r($request->toArray());exit();
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                if($request->status==2){
                    VehicleModal::whereIn('vehicle_type_id',$ids)->update(['status' => $status]);
                    $message = "Vehicle inactive successfully.";
                }elseif ($request->status==1) {
                    VehicleModal::whereIn('vehicle_type_id',$ids)->update(['status' => $status]);
                    $message = "Vehicle active successfully.";
                }else{
                    VehicleModal::whereIn('vehicle_type_id',$ids)->update(['status' => $status]);
                    $message = "Vehicle deleted successfully.";
                }
                    Vehicle::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        // if ($request->row_ids != "") {
        //     if ($request->action == "activate") {          
        //         $active  = Vehicle::wherein('id', $request->ids);
        //         $active->update(['status' => 1]);
        //     } elseif ($request->action == "block") {
        //         Vehicle::wherein('id', $request->ids)
        //             ->update(['status' => 2]);
        //     } elseif ($request->action == "delete") {
        //         $users = Vehicle::wherein('id', $request->ids)->update(['status' => 3]);;
                
        //     }
        // }
        // return redirect()->route('vehicle')->with('doneMessage', __('backend.saveDone'));
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

        // $totalAr = MainUsers::where('status','!=',2)->where('user_type',2);
        $totalAr = Vehicle::where('status','!=',3);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('name', 'LIKE', '%'.urlencode($searchValue).'%');
                            // ->orWhere('email', 'LIKE', '%'.urlencode($searchValue).'%')
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $name = isset($data->name) ? $data->name : '';
            // $email = isset($data->email) ? $data->email : '';
            // $country_code = isset($data->country_code) ? $data->country_code : '';
            // $mobile_number = isset($data->mobile_number) ? $data->mobile_number : '';
            // $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            // if ($data->is_driver_approve == 1) {
            //     $is_driver = '<i class="text-success inline">Approve</i>';
            // } elseif ($data->is_driver_approve == 2) {
                
            //     $is_driver = '<i class="text-danger inline">Reject</i>';
            // } else{
            //     $is_driver = '<i class="text-danger inline">Pending</i>';
            // }

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('vehicle.show', ['id' => base64_encode($data->id)]);
            $edit = route('vehicle.edit', ['id' => base64_encode($data->id)]);
            $delete = route('vehicle.delete', ['id' => base64_encode($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "name" => urldecode($name),
                // "email" => urldecode($email),
                // "mobile_number" => $phone,
                // "join_date" => Carbon::parse($data->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A'),
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
