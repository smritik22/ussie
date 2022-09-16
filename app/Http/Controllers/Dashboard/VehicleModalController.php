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
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;

class VehicleModalController extends Controller
{
    //
     private $uploadPath = "uploads/driver_user/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Vehicle-Modal";

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
    	$vehicles = VehicleModal::orderby('id', 'desc')->get();

        $vehicle_type = Vehicle::orderby('id','desc')->where('status','=',1)->get();


        $vehicle = count($vehicles);
        return view("dashboard.vehicle-modal.list", compact("vehicle","vehicles","vehicle_type"));
    }

    public function create()
    {
    	$vehicle_type = Vehicle::orderby('id','desc')->where('status',1)->get();

        return view("dashboard.vehicle-modal.create" , compact("vehicle_type"));
    }

    public function store(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
        $vehicle = new VehicleModal();
        $this->validateRequest();
        $vehicle->name = $request->name;
        $vehicle->vehicle_type_id = $request->vehicle_type_id;
        // $label->Label_value = $request->label_value;
        $vehicle->status = 1;
        // $label->parentid = 0;
        // $label->language_id = 1;
        $vehicle->created_at = date('Y-m-d H:i:s');
        $vehicle->save(); 

        return redirect()->route('vehicle-modal')
            ->with('success', $this->title.' '.__('backend.addDone'));
    }

    public function update(Request $request,$id)
    {
    	// echo "<pre>";print_r($request->toArray());
    	// print_r($id);exit();
        $this->validateRequest($id);
        $vehicle = VehicleModal::find($id);
        //$label->labelname = $request->label_key;
        $vehicle->name = $request->name;
        $vehicle->vehicle_type_id = $request->vehicle_type_id;
        $vehicle->status = 1;
        $vehicle->updated_at = date('Y-m-d H:i:s');
        $vehicle->save(); 

        return redirect()->route('vehicle-modal')->with('success', $this->title.' '.__('backend.saveDone'));
    }

    public function validateRequest($id="",$childId="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'name' => 'required||max:30',
                'vehicle_type_id' => 'required',
            ]);

        }else if($childId != ''){
            $validateData =request()->validate([
                'name' => 'required||max:30',
                'vehicle_type_id' => 'required',
            ]);
        }
        else{

            $validateData =request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'name' => ['required','max:30',Rule::unique('vehicle_model')->where(function ($query){

            return $query->where('status','!=','3');

            })],
                'vehicle_type_id' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function show($id)
    {
    	$id = base64_decode($id);
        $vehicle_modal = DB::table('vehicle_model')
        ->leftjoin('vehicle_type','vehicle_type.id','=','vehicle_model.vehicle_type_id')
        ->select('vehicle_model.*','vehicle_type.name as vehiclename')
        ->where('vehicle_model.id',$id)
        ->get();
    	// echo "<pre>";print_r($vehicle_modal->toArray());exit();

        return view('dashboard.vehicle-modal.show', compact('vehicle_modal'));
    }

    public function edit($id)
    {
    	// echo "<pre>";print_r($id);exit();
    	$id = base64_decode($id);
        $vehicle_modal = DB::table('vehicle_model')
        ->leftjoin('vehicle_type','vehicle_type.id','=','vehicle_model.vehicle_type_id')
        ->select('vehicle_model.*','vehicle_type.name as vehiclename')
        ->where('vehicle_model.id',$id)
        ->get();

        $vehicle_type = Vehicle::orderby('id','desc')->where('status',1)->get();
        // echo "<pre>";print_r($vehicle_modal);exit;
        return view('dashboard.vehicle-modal.edit', compact('vehicle_modal','vehicle_type'));
    }

    public function destroy($id)
    {
    	$id = base64_decode($id);
        $vehicle_modal = VehicleModal::find($id);
        $vehicle_modal->status = 3;
        $vehicle_modal->save();
        
        return redirect()->route('vehicle-modal')
            ->with('success', 'Vehicle deleted successfully');
    }

    public function updateAll(Request $request)
    {
        if($request->ajax())
        {
            // echo "<pre>";print_r($request->toArray());exit();
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                if($request->status==2){
                    $message = "Vehicl-modal inactive successfully.";
                }elseif ($request->status==1) {
                    $message = "Vehicl-modal active successfully.";
                }else{
                    $message = "Vehicl-modal deleted successfully.";
                }
                    VehicleModal::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        // echo "<pre>";print_r($request->toArray());exit();
        // if ($request->row_ids != "") {
        //     if ($request->action == "activate") {          
        //         $active  = VehicleModal::wherein('id', $request->ids);
        //         $active->update(['status' => 1]);
        //     } elseif ($request->action == "block") {
        //         VehicleModal::wherein('id', $request->ids)
        //             ->update(['status' => 2]);
        //     } elseif ($request->action == "delete") {
        //         $users = VehicleModal::wherein('id', $request->ids)->update(['status' => 3]);;
                
        //     }
        // }
        // return redirect()->route('vehicle-modal')->with('doneMessage', __('backend.saveDone'));
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
        $ride_status = $request->get('ride_status');
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'vehicle_type.name';
        } 
        elseif($columnIndex==2){
            $sort = 'vehicle_model.name';
        }
        elseif ($columnIndex==3) {
            $sort = 'vehicle_model.status';
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
        // $totalAr = VehicleModal::where('status','!=',3);
        $totalAr = DB::table('vehicle_model')
        ->leftjoin('vehicle_type','vehicle_type.id','=','vehicle_model.vehicle_type_id')
        ->select('vehicle_model.*','vehicle_type.name as vehiclename')
    	->where('vehicle_model.status','!=',3);
        // echo "<pre>";print_r($totalAr->toArray());exit();

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('vehicle_model.name', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('vehicle_type.name', 'LIKE', '%'.$searchValue.'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        $setting = Setting::first();

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('vehicle_model.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('vehicle_model.updated_at', '<=', $min_date . ' 23:59:59');
        }

        if ($ride_status) {
           
           $totalAr->where('vehicle_type.id','=',$ride_status);
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $vehiclename = isset($data->vehiclename) ? $data->vehiclename : '';
            $name = isset($data->name) ? $data->name : '';
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

            $show = route('vehicle-modal.show', ['id' => base64_encode($data->id)]);
            $edit = route('vehicle-modal.edit', ['id' => base64_encode($data->id)]);
            $delete = route('vehicle-modal.delete', ['id' => base64_encode($data->id)]);

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
                "vehiclename" => urldecode($vehiclename),
                "name" => urldecode($name),
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
