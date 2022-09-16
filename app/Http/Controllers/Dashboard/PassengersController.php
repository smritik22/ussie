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

class PassengersController extends Controller
{
    //

    private $uploadPath = "uploads/passenger_user/";
    private $uploadPath_Image = "uploads/passenger_user/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Passenger";

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
        return $this->uploadPath_Image;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.passenger.list");
    }

    public function show($id)
    {
        $id = base64_decode($id);
        $locations = array();
        $user = MainUsers::where('user_type',1)->find($id);
        $markers = MainUsers::select('id','address','address_latitude','address_longitude')->where('id',$id)->get()->toArray();

        // $locations[] = array('latitude' => $markers, 'longitude' => $markers);
        // dd($locations);
        if($user){
            $image_url = isset($this->image_uri) ? $this->image_uri : '';
            return view('dashboard.passenger.show', compact('user','image_url','markers'));
        }else{
            return redirect()->route('passenger')->with('errorMessage',__('backend.noDataFound'  ));
        }
    }

    public function ride_list($id){
        $id = base64_decode($id);
        // echo "<pre>";print_r($id);exit();
        // $ride_list = DB::table('ride_detail')->where('user_id',$id)->get();
        // echo "<pre>";print_r($ride_list->toArray());exit();
        return view('dashboard.passenger.ride_list', compact('id'));
    }



    public function rideanyData(Request $request){

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
            
            $totalAr->where('ride_detail.user_id',$id);
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
                $status = '<i class="text-success inline ride_status_font">Not Driver Avilable</i>';
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
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" onchange="checkChange();" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden"  value="' . $data->id . '"> </label>',
                 
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
        $user = MainUsers::where('user_type',1)->find($id);
        // dd($user->hobbies);
        $checkbox=explode(",",$user->hobbies);
        // dd($string);
        $image_url = isset($this->image_uri) ? $this->image_uri : '';

        if($user){
            return view('dashboard.passenger.edit', compact('user', 'image_url','checkbox'));
        }else{
            return redirect()->route('passenger')->with('errorMessage', __('backend.noDataFound'));
        }
    }

    public function create()
    {

        return view("dashboard.passenger.create");
    }

    private function storeImage($user)
    {
        $formFileName = "customer_image";
        $fileFinalName_ar1 = "";
        if (request()->$formFileName != "") {
            $fileFinalName_ar1 = time().rand(1111,
            9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
            $uploadPath = "uploads/passenger_user";
            $path = public_path() . $uploadPath;
            request()->file($formFileName)->move($path, $fileFinalName_ar1);
            // echo "<pre>";print_r($fileFinalName_ar1);


           $user->update([
               'customer_image' => $fileFinalName_ar1,
           ]);
        }
    }
    private function storeImage1($user)
    {
        $formFileName = "file_1";
        $fileFinalName_ar2 = "";
        if (request()->$formFileName != "") {
            $fileFinalName_ar2 = time(). rand(1111,
            9999) .'.' . request()->file($formFileName)->getClientOriginalExtension();
            $uploadPath = "uploads/passenger_user";
            $path = public_path() . $uploadPath;
            request()->file($formFileName)->move($path, $fileFinalName_ar2);
            // echo "<pre>";print_r($fileFinalName_ar2);
            // dd($fileFinalName_ar2);

           $user->update([
               'file_1' => $fileFinalName_ar2,
           ]);
        }
    }
    private function storeImage2($user)
    {
        $formFileName = "file_2";
        $fileFinalName_ar3 = "";
        if (request()->$formFileName != "") {
            $fileFinalName_ar3 = time(). rand(1111,
            9999) .'.' . request()->file($formFileName)->getClientOriginalExtension();
            $uploadPath = "uploads/passenger_user";
            $path = public_path() . $uploadPath;
            request()->file($formFileName)->move($path, $fileFinalName_ar3);
            // echo "<pre>";print_r($fileFinalName_ar3);exit;


           $user->update([
               'file_2' => $fileFinalName_ar3,
           ]);
        }
    }

      public function store(Request $request)
    {

        // dd($request->all());
        // echo "Store:<pre>";print_r($request->toArray());exit();
        $validations = $this->validateRequest($request);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }
        $passenger = new MainUsers();
         $formFileName = "customer_image";
        $fileFinalName_ar = "";
        $avatarName = '';
        if (request()->has('customer_image')) 
        {

            $avatarName = time(). rand(1111,
            9999) .'.' . request()->customer_image->getClientOriginalExtension();
            $file = request()->file('customer_image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

            
        }

        $formFileName1 = "file_1";
        $fileFinalName_ar1 = "";
        $avatarName1 = '';
        if (request()->has('file_1')) 
        {

            $avatarName1 = time(). rand(1111,
            9999) .'.' . request()->file_1->getClientOriginalExtension();
            $file1 = request()->file('file_1');
            $name1=$avatarName1;
                $destinationPath1 = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath1. "/".  $name1;
                $file1->move($destinationPath1, $name1);

            
        }

        $formFileName2 = "file_2";
        $fileFinalName_ar2 = "";
        $avatarName2 = '';
        if (request()->has('file_2')) 
        {

            $avatarName2 = time(). rand(1111,
            9999) .'.' . request()->file_2->getClientOriginalExtension();
            $file2 = request()->file('file_2');
            $name2=$avatarName2;
                $destinationPath2 = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath2. "/".  $name2;
                $file2->move($destinationPath2, $name2);

            
        }

 

        $passenger->name = $request->name;
        $passenger->email = $request->email;
        $passenger->mobile_number = $request->mobile_number;
        $passenger->customer_image = $avatarName;
        $passenger->file_1 = $avatarName1;
        $passenger->file_2 = $avatarName2;
        $passenger->gender = $request->gender;
        $passenger->address = $request->address;
        $passenger->address_latitude = $request->address_latitude;
        $passenger->address_longitude = $request->address_longitude;
        $passenger['hobbies'] = implode(",",$request->hobbies); 
        $passenger->created_at = date('Y-m-d H:i:s');
        $passenger->save(); 

        // $this->storeImage($passenger);
        // $this->storeImage1($passenger);
        // $this->storeImage2($passenger);
        return redirect()->route('passenger')
            ->with('success', $this->title.' '.__('backend.addDone'));
    }

   
    public function update(Request $request,$id)
    {
        // dd($request->all());
        // echo "<pre>";print_r($id);
        // echo "update<pre>";print_r($request->toArray());exit();
        $validations = $this->validateRequest($request,$id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $user = MainUsers::where('user_type',1)->find($id);
        // echo "<pre>";print_r($user);exit();
        // $formFileName = "photo";
        // $fileFinalName_ar = "";

        // if ($request->photo_delete == 1) {
        //     if ($user->customer_image != "") {
        //         File::delete($this->getUploadPath() . $user->customer_image);
        //     }

        //     $user->customer_image = "";
        // }


        // if ($request->$formFileName != "") {
                    
        //     $newname = $_FILES[$formFileName]['name'];
        //     $ext = pathinfo($newname, PATHINFO_EXTENSION);
        //     $insertimage = 'usersImageDefault-'.time().'.'.$ext; 
        //     $tmpfile = $_FILES[$formFileName]['tmp_name'];
        //     $upload_dir =  public_path()."/" . $this->getUploadPath();
        //     // echo "<pre>";print_r($upload_dir);exit();
        //     if (!file_exists($upload_dir)) {
        //       \File::makeDirectory($upload_dir, 0777, true);
        //     }
        //     move_uploaded_file($tmpfile, $upload_dir.$insertimage);
        //     $source_imagebanner = $upload_dir.$insertimage;
        //     $file_namebanner= "users-".time().'-'.str_pad(rand(0,1000), 4, '0', STR_PAD_LEFT).$ext;
        //     $image_destinationbanner = $upload_dir.$file_namebanner;
        //     Helper::correctImageOrientation($source_imagebanner);
        //     $compress_images = Helper::compressImage($source_imagebanner, $image_destinationbanner);
        //     Helper::correctImageOrientation($image_destinationbanner);
        //     unlink($source_imagebanner);
        //     $fileFinalName_ar=$file_namebanner;

        //     if (!file_exists($upload_dir)) {
        //       \File::makeDirectory($upload_dir, 0775, true);
        //     }
        // }

        // if ($fileFinalName_ar != "") {
        //     // Delete a User file
        //     if ($user->customer_image != "") {
        //         File::delete($this->getUploadPath() . $user->customer_image);
        //     }

        //     $user->customer_image = $fileFinalName_ar;
        // }

        $formFileName = "customer_image";
        $fileFinalName_ar = "";

        if (request()->has('customer_image')) 
        {

            $avatarName = time().'.'.request()->customer_image->getClientOriginalExtension();
            $file = request()->file('customer_image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

             $user->customer_image = $avatarName;
            //  $user->save();
            // dd($user);
        }

        $formFileName1 = "file_1";
        $fileFinalName_ar1 = "";

        if (request()->has('file_1')) 
        {

            $avatarName1 = time(). rand(1111,
            9999) .'.' . request()->file_1->getClientOriginalExtension();
            $file1 = request()->file('file_1');
            $name1=$avatarName1;
                $destinationPath1 = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath1. "/".  $name1;
                $file1->move($destinationPath1, $name1);

             $user->update([                 
                'file_1' => $avatarName1,
            ]);
        }

        $formFileName2 = "file_2";
        $fileFinalName_ar2 = "";

        if (request()->has('file_2')) 
        {

            $avatarName2 = time(). rand(1111,
            9999) .'.' . request()->file_2->getClientOriginalExtension();
            $file2 = request()->file('file_2');
            $name2=$avatarName2;
                $destinationPath2 = public_path('uploads/passenger_user');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath2. "/".  $name2;
                $file2->move($destinationPath2, $name2);

             $user->update([                 
                'file_2' => $avatarName2,
            ]);
        }


        $user->name = urlencode($request->full_name);
        $user->email = urlencode($request->email);
        $user->country_code = urlencode($request->country_code);
        $user->mobile_number = urlencode($request->mobile_number);
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user['hobbies'] = implode(",",$request->hobbies);
        // $user->file_2 = $avatarName2;
        // $user->updated_by = Auth::user()->id;
        // echo "<pre>";print_r($user);exit();
        $user->save(); 

        return redirect()->route('passenger')->with('success', $this->title.' '.__('backend.saveDone'));
    }

    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
        $validation_messages = [
            'full_name.required' => 'Full Name is required.',
            'mobile_number.unique' => 'Mobile Number is already taken.',
            'email.encoded_unique' => 'Email is already taken.',
            'email.email' => 'Valid Email Address.',
            'photo.customer_image' => 'Valid Image Here.',
            'name.required' => 'Name is required.',
            'customer_image.mimes' => 'Valid Image Here.',
            'file_1.mimes' => 'Valid Image Here.',
            'file_2.mimes' => 'Valid Image Here.',
           
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
                'customer_image' => 'image|mimes:jpeg,jpg,png,gif|max:3072',
                'file_1' => 'mimes:jpeg,jpg,png,gif|max:3072',
                'file_2' => 'mimes:jpeg,jpg,png,gif|max:3072',
                
            ],
            $validation_messages
        );

        }
         else{
            $validator = Validator::make($request->all(), [
                'name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                ],
                'email' => [
                            'required',
                            'encoded_unique:users,email,status,2',
                            'email:rfc,dns',
                        ],
                'mobile_number' => [ 
                                    'required',
                                    'unique:App\Models\MainUsers,mobile_number,status,2'
                                ],
                 'customer_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:3072',
                 'file_1' => 'required|mimes:jpeg,jpg,png,gif|max:3072',
                 'file_2' => 'required|mimes:jpeg,jpg,png,gif|max:3072',
                 'gender' => 'required',
                 'hobbies' => 'required',
                 'address' => 'required',
                
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
        
        return redirect()->route('passenger')
            ->with('success', __('backend.deleteDone'));
    }

    public function updateAll(Request $request) {

       if($request->ajax())
        {
            // echo "<pre>";print_r($request->toArray());exit();
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                if($request->status==0){
                    $message = "Passenger inactive successfully.";
                }elseif ($request->status==1) {
                    $message = "Passenger active successfully.";
                }else{
                    $message = "Passenger deleted successfully.";
                }
                    MainUsers::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        // if ($request->row_ids != "") {
        //     if ($request->action == "activate") {  
        //     // echo "string";exit();        
        //         $active  = MainUsers::wherein('id', $request->ids);
        //         $active->update(['status' => 1]);
        //     } elseif ($request->action == "block") {
        //          $ids= explode(",", $request->ids);
        // // echo "<pre>";print_r($request->ids);exit();
        //     // echo "string1";exit();        
        //         MainUsers::wherein('id', $ids)
        //             ->update(['status' => 0]);
        //     } elseif ($request->action == "delete") {
        //     // echo "string2";exit();        
        //         $users = MainUsers::wherein('id', $request->ids);
        //         foreach($users as $user){
        //             if ($user->customer_image != "") {
        //                 File::delete($this->getUploadPath() . $user->customer_image);
        //             }
        //         }

        //         MainUsers::wherein('id', $request->ids)
        //             ->update(['status' => 2,'customer_image' => null]);
        //     }
        // // }
        // return redirect()->route('passenger')->with('doneMessage', __('backend.saveDone'));
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
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = MainUsers::where('status','!=',2)->where('user_type',1);

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

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            
            $date = \Helper::converttimeTozone($data->created_at);
                    

            $show = route('passenger.show', ['id' => base64_encode($data->id)]);
            $edit = route('passenger.edit', ['id' => base64_encode($data->id)]);
            $delete = route('passenger.delete', ['id' => base64_encode($data->id)]);
            $passenger_list = route('passenger.ride_list', ['id' => base64_encode($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            $options .= '<a class="btn paddingset delete-data" href="' . $passenger_list . '" title="Ride Details"> <span class="fa-solid fa-car-side"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onclick="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value"  name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "full_name" => urldecode($full_name),
                "email" => urldecode($email),
                "mobile_number" => $phone,
                "join_date" => $date,
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
