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

class GeneralUsersController extends Controller
{
    // Define Default Variables
    private $uploadPath = "uploads/general_users/";
    protected $image_uri = "";
    protected $no_image = "";

    public function __construct()
    {
        $this->middleware('auth');
        $this->setImagePath();
        $this->no_image = asset('assets/dashboard/images/no_image.png');
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
        return view("dashboard.general_users.list");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MainUsers  $user
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $id = decrypt($id);
        $user = MainUsers::where('user_type','=',config('constants.USER_TYPE_GENERAL'))->find($id);
        if($user->count() > 0){
            $image_url = $this->image_uri;
            return view('dashboard.general_users.show', compact('user','image_url'));
        }else{
            return redirect()->route('generalusers')->with('errorMessage',__('backend.noDataFound'  ));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MainUsers  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $user = MainUsers::where('user_type','=',config('constants.USER_TYPE_GENERAL'))->find($id);
        $image_url = $this->image_uri;

        if($user->count() > 0){
            return view('dashboard.general_users.edit', compact('user', 'image_url'));
        }else{
            return redirect()->route('generalusers')->with('errorMessage', __('backend.noDataFound'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validations = $this->validateRequest($request,$id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $user = MainUsers::where('user_type','=',config('constants.USER_TYPE_GENERAL'))->find($id);
        
        $formFileName = "photo";
        $fileFinalName_ar = "";

        if ($request->photo_delete == 1) {
            if ($user->profile_image != "") {
                File::delete($this->getUploadPath() . $user->profile_image);
            }

            $user->profile_image = "";
        }

        // if ($request->$formFileName != "") {
        //     $image = $request->file($formFileName);
        //     $input['imagename'] = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
            
        //     $fileFinalName_ar =  $input['imagename'];
        //     $destinationPath = public_path( '/' . $this->uploadPath );

        //     $img = Image::make($image->getRealPath());
        //     $img->resize(500, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })->save($destinationPath . $input['imagename']);

        // }

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
            if ($user->profile_image != "") {
                File::delete($this->getUploadPath() . $user->profile_image);
            }

            $user->profile_image = $fileFinalName_ar;
        }

        $user->full_name = urlencode($request->full_name);
        $user->email = urlencode($request->email);
        $user->country_code = urlencode($request->country_code);
        $user->mobile_number = urlencode($request->mobile_number);
        $user->updated_by = Auth::user()->id;
        $user->save(); 

        return redirect()->route('generalusers')->with('success', __('backend.saveDone'));
    }

    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
        $validation_messages = [
            'full_name.required' => 'Full Name is required.',
            'full_name.string' => 'Only string allowed',
            'full_name.max' => 'Max length exceeded',
            'mobile_number.unique' => 'Mobile Number is already taken.',
            'email.encoded_unique' => 'Email is already taken.',
            'photo.mimes' => 'Not valid image extension',
            'photo.max' => 'Size is not valid',
            'photo.image' => 'Not good image',
        ];

        if( $id !="" )
        {
            $validator = Validator::make($request->all(), [
                'full_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                ],
                'email' => [
                            'required',
                            'encoded_unique:users,email,id,'.$id.',status,2',
                        ],
                'country_code' => [
                                    'required'
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
                            'email',
                            'encoded_unique:users,email,status,2'
                        ],
                'mobile_number' => [ 
                                    'required',
                                    'unique:App\Models\MainUsers,mobile_number,status,2'
                                ],
                'photo' => 'image|mimes:jpeg,jpg,png,gif',
            ],
            $validation_messages
        );
        }

        return $validator;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $user = MainUsers::find($id);

        if ($user->profile_image != "") {
            File::delete($this->getUploadPath() . $user->profile_image);
        }
        $user->profile_image = null;
        $user->status = 2;
        $user->save();
        
        return redirect()->route('generalusers')
            ->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {          
                $active  = MainUsers::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                MainUsers::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                $users = MainUsers::wherein('id', $request->ids);
                foreach($users as $user){
                    if ($user->profile_image != "") {
                        File::delete($this->getUploadPath() . $user->profile_image);
                    }
                }

                MainUsers::wherein('id', $request->ids)
                    ->update(['status' => 2,'profile_image' => null]);
            }
        }
        return redirect()->route('generalusers')->with('doneMessage', __('backend.saveDone'));
    }


    /* 
    *
    * AJAX call server side pagination
    *
    */

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
            $sort = 'full_name';
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

        $totalAr = MainUsers::where('status','!=',2)->where('user_type','=',config('constants.USER_TYPE_GENERAL'));

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('full_name', 'LIKE', '%'.urlencode($searchValue).'%')
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
            $full_name = isset($data->full_name) ? $data->full_name : '';
            $email = isset($data->email) ? $data->email : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $mobile_number = isset($data->mobile_number) ? $data->mobile_number : '';
            $phone = urldecode($country_code) . ' ' . urldecode($mobile_number);

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('generaluser.show', ['id' => encrypt($data->id)]);
            $edit = route('generaluser.edit', ['id' => encrypt($data->id)]);
            $delete = route('generaluser.delete', ['id' => encrypt($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($full_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "full_name" => urldecode($full_name),
                "email" => urldecode($email),
                "mobile_number" => $phone,
                "join_date" => Carbon::parse($data->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A'),
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
