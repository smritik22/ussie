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
use App\Models\DriverDetailsModal;
use Redirect;
use Helper;
use Hash;
use Image;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\Language;

class DriverController extends Controller
{
    //

    private $uploadPath = "public/uploads/driver_user/";
    private $uploadDocumentPath = "public/uploads/driver_document/";
    private $uploadPath_Image = "uploads/driver_user/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Driver";

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $this->setImagePath();
        $this->setDocumentImagePath();
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

    //Laravel Developer 26-08-2022 Driver Documentaion
    public function getDocumentImagePath(){
        return asset($this->uploadDocumentPath);
    }

    public function setDocumentImagePath(){
        $this->image_uri_document = $this->getDocumentImagePath() . '/';
    }


     public function getUploadPath()
    {
        return $this->uploadPath_Image;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.driver.list");
    }

    public function reject_list($id)
    {
        $id = base64_decode($id);
        $driver_id = $id;
        return view("dashboard.driver.reject_list",compact("driver_id"));
    }

    public function riderejectanyData(Request $request){
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
        $driver_id = $request->get('driver_id');

        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'driver_ride_reject.id';
        } 
        elseif($columnIndex==2){
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
        // $totalAr = DB::table('ride_detail')
        // ->leftjoin('customer as user','user.id','=','ride_detail.user_id')
        // ->leftjoin('customer as driver','driver.id','=','ride_detail.driver_id')
        // ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.id','=','ride_detail.driver_vehicle_detail_id')
        // ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        // ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        // ->select('ride_detail.*','user.name as username','driver.name as drivername','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name')
        // ->where('ride_detail.status','!=',2);
        $totalAr = DB::table('driver_ride_reject')
        ->leftjoin('ride_detail','ride_detail.id','=','driver_ride_reject.ride_id')
        ->leftjoin('customer as driver','driver.id','=','driver_ride_reject.driver_id')
        ->select('driver_ride_reject.*','driver.name as drivername','driver.status as driver_status')
        ->where('driver_ride_reject.status','=',1)->where('driver_ride_reject.driver_id',$driver_id);
        // echo "<pre>";print_r($totalAr->toArray());exit();
        // $totalAr = MainUsers::where('status','!=',2)->where('user_type',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('driver.name', 'LIKE', '%'.$searchValue.'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        $setting = Setting::first();

        // if ($start_date) {
        //     $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
        //     $totalAr->where('ride_detail.created_date', '>=', $min_date);
        // }

        // if ($end_date) {
        //     $min_date = Carbon::parse($end_date)->format('Y-m-d');
        //     $totalAr->where('ride_detail.created_date', '<=', $min_date . ' 23:59:59');
        // }
        // $active_status = [2,4,5,6,7,8];

        // if ($ride_status) {
            // $min_date = Carbon::parse($end_date)->format('Y-m-d');
            // $totalAr->where('ride_detail.id',3);
        // }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            
            // $id = isset($data->ride_id) ? $data->ride_id : '';
            $ride_id = isset($data->ride_id) ? $data->ride_id : '';
           
        
            
            

            // $show = route('ride.show', ['id' => base64_encode($data->id)]);
            // $edit = route('driver.edit', ['id' => base64_encode($data->id)]);
            // $delete = route('driver.delete', ['id' => base64_encode($data->id)]);
            // $reject_list = route('passenger.ride_list', ['id' => base64_encode($data->id)]);
             if ($data->driver_status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }
            $options = "";

            // $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';
            $get_currency = \Helper::getDefaultCurrency();

           
            // $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            // $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                 
                
                "ride_id" => $ride_id,
                // "drivername" => urldecode($drivername),
                "status" => $status,
                // "options" => $options,
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

    public function show($id)
    {
        $id = base64_decode($id);
        $user = MainUsers::where('user_type',2)->find($id);
        // $driver_document = "";
        $driver_document = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->first();
        $driver_document_liciense = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->where('document_type',1)->select('driver_verification_document.document_image')->get();
        $driver_document_authority = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->where('document_type',2)->select('driver_verification_document.document_image')->get();
        $driver_document_territor = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->where('document_type',3)->select('driver_verification_document.document_image')->get();
        $driver_document_police = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->where('document_type',4)->select('driver_verification_document.document_image')->get();
        $driver_document_vehicle_documentaion = DB::table('driver_verification_document')->where('user_id',$id)->where('status','=',1)->where('document_type',5)->select('driver_verification_document.document_image')->get();
        // $driver_document_vehicle_documentaion = $driver_document_vehicle_documentaion->toArray();
        // echo "<pre>";print_r($driver_document_vehicle_documentaion);exit();
        // $List = implode(', ', $driver_document_vehicle_documentaion);
      

         $driver_vehicle = DB::table('driver_vehicle_detail')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->leftjoin('car_type','car_type.id','=','driver_vehicle_detail.car_type_id')
        ->select('driver_vehicle_detail.*','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name','car_type.car_type as car_type_name')
        ->where('driver_vehicle_detail.status','!=',3)->where('driver_id','=',$id)->get();

        $image_uri_document = isset($this->image_uri_document) ? $this->image_uri_document : '';
        // echo "<pre>";print_r($image_uri_document);exit();
        if($user){
            $image_url = isset($this->image_uri) ? $this->image_uri : '';
            return view('dashboard.driver.show', compact('user','image_url','driver_vehicle','driver_document','driver_document_liciense','image_uri_document','driver_document_authority','driver_document_territor','driver_document_police','driver_document_vehicle_documentaion'));
        }else{
            return redirect()->route('driver')->with('errorMessage',__('backend.noDataFound'  ));
        }
    }

    public function ride_list($id){
        $id = base64_decode($id);
        // echo "<pre>";print_r($id);exit();
        // $ride_list = DB::table('ride_detail')->where('user_id',$id)->get();
        // echo "<pre>";print_r($ride_list->toArray());exit();
        return view('dashboard.driver.ride_list', compact('id'));
    }

    public function rideanyData(Request $request){
         $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $id = $request->get('id');

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
        }elseif ($columnIndex==3) {
            $sort = 'ride_detail.pickup_address';
        }elseif ($columnIndex==4) {
            $sort = 'ride_detail.dest_address';
        }elseif ($columnIndex==5) {
            $sort = 'ride_detail.estimate_fare';
        }elseif ($columnIndex==6) {
            $sort = 'ride_detail.actual_fare'; 
        }elseif ($columnIndex==7) {
            $sort = 'ride_detail.ride_km'; 
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
        ->leftjoin('driver_vehicle_detail','driver_vehicle_detail.id','=','ride_detail.driver_vehicle_detail_id')
        ->leftjoin('vehicle_type','vehicle_type.id','=','driver_vehicle_detail.vehicle_type_id')
        ->leftjoin('vehicle_model','vehicle_model.id','=','driver_vehicle_detail.vehicle_model_id')
        ->select('ride_detail.*','user.name as username','driver.name as drivername','vehicle_type.name as vehicle_type_name','vehicle_model.name as vehicle_model_name')
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
        if ($id) {
            
            $totalAr->where('ride_detail.driver_id',$id);
        }
        $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr);exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            
            $id = isset($data->id) ? $data->id : '';
            $vehicle_type_name = isset($data->vehicle_type_name) ? $data->vehicle_type_name : '';
            $vehicle_model_name = isset($data->vehicle_model_name) ? $data->vehicle_model_name : '';
            $pickup_address = isset($data->pickup_address) ? $data->pickup_address : '';
            $dest_address = isset($data->dest_address) ? $data->dest_address : '';
            $start_date = isset($data->start_date) ? $data->start_date : '';
            $username = isset($data->username) ? $data->username : '';
            $drivername = isset($data->drivername) ? $data->drivername : '';
            $estimate_fare = isset($data->estimate_fare) ? $data->estimate_fare : '';
            $actual_fare = isset($data->actual_fare) ? $data->actual_fare : '';
            $ride_km = isset($data->ride_km) ? $data->ride_km : '';
            // $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            // if ($data->is_driver_approve == 1) {
            //     $is_driver = '<i class="text-success inline">Approve</i>';
            // } elseif ($data->is_driver_approve == 2) {
                
            //     $is_driver = '<i class="text-danger inline">Reject</i>';
            // } else{
            //     $is_driver = '<i class="text-danger inline">Pending</i>';
            // }

            
            // $error = \Helper::orderStatusadmin(isset($data->ride_status) ? $data->ride_status :'');
            if ($data->ride_status == 0) {
                $status = '<i class="text-warning inline ride_status_font">Pending</i>';
            } elseif ($data->ride_status == 1) {
                $status = '<i class="text-warning inline ride_status_font">Pending</i>';
            } elseif ($data->ride_status == 2) {
                $status = '<i class="text-success inline ride_status_font">Request Accept</i>';
            }elseif ($data->ride_status == 3) {
                $status = '<i class="text-primary inline ride_status_font">Reject</i>';
            }elseif ($data->ride_status == 4) {
                $status = '<i class="text-success inline ride_status_font">Arrived At Pickup</i>';
            }elseif ($data->ride_status == 5) {
                $status = '<i class="text-success inline ride_status_font">Picked Up Customer</i>';
            }elseif ($data->ride_status == 6) {
                $status = '<i class="text-info inline ride_status_font">Arrived At Destination</i>';
            }elseif ($data->ride_status == 7) {
                $status = '<i class="text-info inline ride_status_font">Complated</i>';
            }elseif ($data->ride_status == 8) {
                $status = '<i class="text-danger inline ride_status_font">Cancelled by customer</i>';
            }elseif ($data->ride_status == 9) {
                $status = '<i class="text-danger inline ride_status_font">Cancelled by admin</i>';  
            }
            else{
                $status = '<i class="text-success inline">Not Driver Avilable</i>';
            }

            $show = route('ride.show', ['id' => base64_encode($data->id)]);
            $edit = route('driver.edit', ['id' => base64_encode($data->id)]);
            $delete = route('driver.delete', ['id' => base64_encode($data->id)]);
            $reject_list = route('ride.reject_list', ['id' => base64_encode($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';
            $options .= '<a class="btn paddingset delete-data" href="' . $reject_list . '" title="Driver Ride Reject"> <span class="fa-solid fa-car-side"></span> </a> ';
             $get_currency = \Helper::getDefaultCurrency();
            // $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            // $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                
                "pickup_address" => urldecode($pickup_address),
                "dest_address" => urldecode($dest_address),
                "username" => urldecode($username),
                "drivername" => urldecode($drivername),
                 "estimate_fare" => $get_currency.''.$estimate_fare,
                "actual_fare" => $get_currency.''.$actual_fare,
                "ride_km" => $ride_km,
                // "is_driver" => $driver_id,
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

    public function edit($id)
    {
        $id = base64_decode($id);
        $user = MainUsers::where('user_type',2)->find($id);
        $image_url = isset($this->image_uri) ? $this->image_uri : '';

        if($user){
            return view('dashboard.driver.edit', compact('user', 'image_url'));
        }else{
            return redirect()->route('driver')->with('errorMessage', __('backend.noDataFound'));
        }
    }

    public function update(Request $request,$id)
    {
        // echo "<pre>";print_r($id);
        // echo "<pre>";print_r($request->toArray());exit();
        $validations = $this->validateRequest($request,$id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $user = MainUsers::where('user_type',2)->find($id);
        // echo "<pre>";print_r($user);exit();
        $formFileName = "photo";
        $fileFinalName_ar = "";

        if ($request->photo_delete == 1) {
            if ($user->customer_image != "") {
                File::delete($this->getUploadPath() . $user->customer_image);
            }

            $user->customer_image = "";
        }


        if ($request->$formFileName != "") {
                    
            $newname = $_FILES[$formFileName]['name'];
            $ext = pathinfo($newname, PATHINFO_EXTENSION);
            $insertimage = 'usersImageDefault-'.time().'.'.$ext; 
            $tmpfile = $_FILES[$formFileName]['tmp_name'];
            $upload_dir =  public_path()."/" . $this->getUploadPath();

            if (!file_exists($upload_dir)) {
              \File::makeDirectory($upload_dir, 0777, true);
            }
            move_uploaded_file($tmpfile, $upload_dir.$insertimage);
            $source_imagebanner = $upload_dir.$insertimage;
            $file_namebanner= "users-".time().'-'.str_pad(rand(0,1000), 4, '0', STR_PAD_LEFT).$ext;
            $image_destinationbanner = $upload_dir.$file_namebanner;
            Helper::correctImageOrientation($source_imagebanner);
            $compress_images = Helper::compressImage($source_imagebanner, $image_destinationbanner);
            Helper::correctImageOrientation($image_destinationbanner);
            unlink($source_imagebanner);
            $fileFinalName_ar=$file_namebanner;

            if (!file_exists($upload_dir)) {
              \File::makeDirectory($upload_dir, 0775, true);
            }
        }

        if ($fileFinalName_ar != "") {
            // Delete a User file
            if ($user->customer_image != "") {
                File::delete($this->getUploadPath() . $user->customer_image);
            }

            $user->customer_image = $fileFinalName_ar;
        }

        $user->name = urlencode($request->full_name);
        $user->email = urlencode($request->email);
        $user->country_code = urlencode($request->country_code);
        $user->mobile_number = urlencode($request->mobile_number);
        // $user->updated_by = Auth::user()->id;
        // echo "<pre>";print_r($user);exit();
        $user->save(); 

        return redirect()->route('driver')->with('success', $this->title.' '.__('backend.saveDone'));
    }

    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
       $validation_messages = [
            'full_name.required' => 'Full Name is required.',
            'mobile_number.unique' => 'Mobile Number is already taken.',
            'email.encoded_unique' => 'Email is already taken.',
            'email.email' => 'Valid Email Address.',
            'photo.photo' => 'Valid Image Here.',
           
        ];

        if( $id !="" )
        {
            $validator = Validator::make($request->all(), [
                'full_name' => [
                                    'required',
                                ],
                'email' => [
                            'required',
                            'encoded_unique:users,email,id,'.$id.',status,2',
                            'email:rfc,dns',
                        ],
                'mobile_number' => [ 
                                    'required',
                                    Rule::unique('App\Models\MainUsers','mobile_number')->ignore($id)->where(function ($query) use($request) {
                                        return $query->where('status', "!=", 2)->where('country_code','=',urlencode($request->country_code));
                                    }),
                                    // 'unique:App\Models\MainUsers,mobile_number,status,2,id'
                                    // 'unique:users,mobile_number,status,2',
                                    
                                ],
                'photo' => 'image|mimes:jpeg,jpg,png,gif|max:3072',
            ],
            $validation_messages
        );

        }
         else{
            $validator = Validator::make($request->all(), [
                'full_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                ],
                'email' => [ 
                            'required',
                            'email:rfc,dns',
                            'encoded_unique:users,email,status,2'
                        ],
                'mobile_number' => [ 
                                    'required',
                                    'unique:App\Models\MainUsers,mobile_number,status,2'
                                ],
                'photo' => 'image|mimes:jpeg,jpg,png,gif|max:3072',
            ],
            $validation_messages
        );
        }

        return $validator;
    }

    public function destroy($id)
    {
        $id = base64_decode($id);
        $user = MainUsers::find($id);

        if ($user->customer_image != "") {
            File::delete($this->getUploadPath() . $user->customer_image);
        }
        $user->customer_image = null;
        $user->status = 2;
        $user->save();
        
        return redirect()->route('driver')
            ->with('success', __('backend.deleteDone'));
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
                if($request->status==0){
                    $message = "Driver inactive successfully.";
                    MainUsers::whereIn('id',$ids)->update(['status' => $status]);
                }elseif ($request->status==1) {
                    $message = "Driver active successfully.";
                    MainUsers::whereIn('id',$ids)->update(['status' => $status]);
                }elseif($request->status=="approve"){
                    $message = "Driver Approve successfully.";
                    MainUsers::whereIn('id',$ids)->update(['is_driver_approve' => 1]);
                    DriverDetailsModal::wherein('user_id',$ids)->update(['is_approve' =>1]);
                }elseif($request->status=="reject"){
                    $message = "Driver Reject successfully.";
                    MainUsers::whereIn('id',$ids)->update(['is_driver_approve' => 2]);
                    DriverDetailsModal::wherein('user_id',$ids)->update(['is_approve' =>2]);
                }else{
                    $message = "Driver deleted successfully.";
                    MainUsers::whereIn('id',$ids)->update(['status' => $status]);
                }

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        // if ($request->row_ids != "") {
        //     if ($request->action == "activate") {          
        //         $active  = MainUsers::wherein('id', $request->ids);
        //         $active->update(['status' => 1]);
        //     } elseif ($request->action == "block") {
        //         MainUsers::wherein('id', $request->ids)
        //             ->update(['status' => 0]);
        //     } elseif ($request->action == "delete") {
        //         $users = MainUsers::wherein('id', $request->ids);
        //         foreach($users as $user){
        //             if ($user->customer_image != "") {
        //                 File::delete($this->getUploadPath() . $user->customer_image);
        //             }
        //         }

        //         MainUsers::wherein('id', $request->ids)
        //             ->update(['status' => 2,'customer_image' => null]);
        //     }elseif ($request->action == "approve") {
        //         // echo "string";exit();
        //         MainUsers::wherein('id', $request->ids)
        //             ->update(['is_driver_approve' => 1]);
        //     }elseif ($request->action == "reject") {
        //         MainUsers::wherein('id', $request->ids)
        //             ->update(['is_driver_approve' => 2]);
        //     }
        // }
        // return redirect()->route('driver')->with('doneMessage', __('backend.saveDone'));
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

            $show = route('driver.show', ['id' => base64_encode($data->id)]);
            $edit = route('driver.edit', ['id' => base64_encode($data->id)]);
            $delete = route('driver.delete', ['id' => base64_encode($data->id)]);
            $driver_list = route('driver.ride_list', ['id' => base64_encode($data->id)]);
            $reject_list = route('driver.reject_list', ['id' => base64_encode($data->id)]);

            $date = \Helper::converttimeTozone($data->created_at);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            $options .= '<a class="btn paddingset delete-data" href="' . $driver_list . '" title="Ride Details"> <span class="fa-solid fa-car-side"></span> </a> ';

            // $options .= '<a class="btn paddingset delete-data" href="' . $reject_list . '" title="Ride Details"> <span class="fa-solid fa-car-side"></span> </a> ';

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
