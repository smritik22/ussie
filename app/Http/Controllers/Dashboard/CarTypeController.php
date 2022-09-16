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
use App\Models\VehicleModal;
use App\Models\Vehicle;
use App\Models\CarTypeModal;
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;

class CarTypeController extends Controller
{
    //
   private $uploadPath = "uploads/car-type/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Car-Type";

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
    	
        // $vehicle = count($vehicles);
        return view("dashboard.car-type.list");
    }

    public function create()
    {
    	// $vehicle_type = Vehicle::orderby('id','desc')->where('status',1)->get();

        return view("dashboard.car-type.create");
    }

    public function edit($id)
    {
    	// echo "<pre>";print_r($id);exit();
    	$id = base64_decode($id);
        // $vehicle_modal = DB::table('vehicle_model')
        // ->leftjoin('vehicle_type','vehicle_type.id','=','vehicle_model.vehicle_type_id')
        // ->select('vehicle_model.*','vehicle_type.name as vehiclename')
        // ->where('vehicle_model.id',$id)
        // ->get();

        $car_type = CarTypeModal::where('id',$id)->get();
        $image_url = isset($this->image_uri) ? $this->image_uri : '';
        // echo "<pre>";print_r($vehicle_modal);exit;
        return view('dashboard.car-type.edit', compact('car_type','image_url'));
    }

    public function show($id)
    {
    	$id = base64_decode($id);
    	// echo "<pre>";print_r($id);exit();
        $car_type = CarTypeModal::find($id);
        $image_url = isset($this->image_uri) ? $this->image_uri : '';
        // echo "<pre>";print_r($image_url);exit();
        return view('dashboard.car-type.show', compact('car_type','image_url'));
    }

    public function updateAll(Request $request)
    {
        if($request->ajax())
        {
            // echo "<pre>";print_r($request->toArray());exit();
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                if($request->status==0){
                    $message = "car-type inactive successfully.";
                }elseif ($request->status==1) {
                    $message = "car-type active successfully.";
                }else{
                    $message = "car-type deleted successfully.";
                }
                    CarTypeModal::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        // // echo "<pre>";print_r($request->toArray());exit();
        // if ($request->row_ids != "") {
        //     if ($request->action == "activate") {          
        //         $active  = CarTypeModal::wherein('id', $request->ids);
        //         $active->update(['status' => 1]);
        //     } elseif ($request->action == "block") {
        //         CarTypeModal::wherein('id', $request->ids)
        //             ->update(['status' => 2]);
        //     } elseif ($request->action == "delete") {
        //         $users = CarTypeModal::wherein('id', $request->ids)->update(['status' => 3]);;
                
        //     }
        // }
        // return redirect()->route('car-type')->with('doneMessage', __('backend.saveDone'));
    }

     public function destroy($id)
    {
    	$id = base64_decode($id);
        $vehicle_modal = CarTypeModal::find($id);
        $vehicle_modal->status = 3;
        $vehicle_modal->save();
        
        return redirect()->route('car-type')
            ->with('success', 'Car Type deleted successfully');
    }


    public function store(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
        $car_type = new CarTypeModal();
        $this->validateRequest();
         $formFileName = "image";
        $fileFinalName_ar = "";

        if (request()->has('image')) 
        {

            $avatarName = time().'.'.request()->image->getClientOriginalExtension();
            $file = request()->file('image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/car-type');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

            
        }

        $car_type->car_type = $request->car_type;
        $car_type->description = $request->description;
        $car_type->base_fare = $request->base_fare;
        $car_type->per_km_charge = $request->per_km_charge;
        $car_type->per_km_charge_pool = $request->per_km_charge_pool;
        $car_type->image = $avatarName;
        // $label->Label_value = $request->label_value;
        $car_type->status = 1;
        // $label->parentid = 0;
        // $label->language_id = 1;
        $car_type->created_at = date('Y-m-d H:i:s');
        $car_type->save(); 

        return redirect()->route('car-type')
            ->with('success', $this->title.' '.__('backend.addDone'));
    }

    public function update(Request $request,$id)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
    	// print_r($id);exit();
        $this->validateRequest($id);
        $car_type = CarTypeModal::find($id);

        $formFileName = "image";
        $fileFinalName_ar = "";

        if (request()->has('image')) 
        {

            $avatarName = time().'.'.request()->image->getClientOriginalExtension();
            $file = request()->file('image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/car-type');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

             $car_type->update([                 
                'image' => $avatarName,
            ]);
        }

        //$label->labelname = $request->label_key;
        $car_type->car_type = $request->car_type;
        $car_type->description = $request->description;
        $car_type->base_fare = $request->base_fare;
        $car_type->per_km_charge = $request->per_km_charge;
        $car_type->per_km_charge_pool = $request->per_km_charge_pool;
        // $car_type->image = $avatarName;
        $car_type->status = 1;
        $car_type->updated_at = date('Y-m-d H:i:s');
        $car_type->save(); 

        return redirect()->route('car-type')->with('success', $this->title.' '.__('backend.saveDone'));
    }

    public function validateRequest($id="",$childId="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'car_type' => 'required|regex:/^[A-Za-z0-9 ]+$/|max:50',
                'description' => 'required|max:200',
                'base_fare' => 'required|numeric|max:8',
                'per_km_charge' => 'required|numeric|max:8',
                'per_km_charge_pool' => 'required|numeric|max:8',
                'image' => 'mimes:png,jpeg,jpg,gif,svg',
            ]);

        }else if($childId != ''){
            $validateData =request()->validate([
                'car_type' => 'required|regex:/^[A-Za-z0-9 ]+$/|max:50',
                'description' => 'required|max:200',
                'base_fare' => 'required|numeric|max:8',
                'per_km_charge' => 'required|numeric|max:8',
                'per_km_charge_pool' => 'required|numeric|max:8',
                'image' => 'mimes:png,jpeg,jpg,gif,svg',
            ]);
        }
        else{

            $validateData =request()->validate([
                'car_type' => 'required|regex:/^[A-Za-z0-9 ]+$/|max:50',
                'description' => 'required|max:200',
                'base_fare' => 'required|numeric|max:8',
                'per_km_charge' => 'required|numeric|max:8',
                'per_km_charge_pool' => 'required|numeric|max:8',
                'image' => 'required|mimes:png,jpeg,jpg,gif,svg',
            ]);
            
        }

        return $validateData;
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
            $sort = 'car_type';
        } 
        elseif($columnIndex==2){
            $sort = 'description';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = CarTypeModal::where('status','!=',3);
        // $totalAr = VehicleModal::where('status','!=',3);
     //    $totalAr = DB::table('vehicle_model')
     //    ->leftjoin('vehicle_type','vehicle_type.id','=','vehicle_model.vehicle_type_id')
     //    ->select('vehicle_model.*','vehicle_type.name as vehiclename')
    	// ->where('vehicle_model.status','!=',3);
        // echo "<pre>";print_r($totalAr->toArray());exit();

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('car_type', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('description', 'LIKE', '%'.$searchValue.'%');
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
            $car_type = isset($data->car_type) ? $data->car_type : '';
            $description = isset($data->description) ? $data->description : '';
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

            $show = route('car-type.show', ['id' => base64_encode($data->id)]);
            $edit = route('car-type.edit', ['id' => base64_encode($data->id)]);
            $delete = route('car-type.delete', ['id' => base64_encode($data->id)]);

            $image = $this->no_image;
            if($data->image){
                $checkFile = $this->image_uri . '/' . $data->image;
                $image = $checkFile;
            }
            $image_show = '<a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a>';

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($car_type).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "car_type" => urldecode($car_type),
                "description" => urldecode($description),
                "image" => $image_show,
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
