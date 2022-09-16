<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Amenity;
use App\Models\Area;
use App\Models\BathroomTypes;
use App\Models\BedroomTypes;
use App\Models\Country;
use App\Models\Language;
use App\Models\MainUsers;
use App\Models\Property;
use App\Models\PropertyCondition;
use App\Models\PropertyAmenities;
use App\Models\PropertyCompletionStatus;
use App\Models\PropertyType;
use App\Models\PropertyImages;
use App\Models\Setting;
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
use Mail;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;

class PropertiesController extends Controller
{
    // Define Default Variables
    protected $image_uri = "";
    protected $uploadPath;

    public function __construct()
    {
        $this->middleware('auth');
        $this->setUploadPath();
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath()
    {
        $this->uploadPath = "storage/property_images/";
    }


    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request,$property_for = "")
    {
        $propertyTypes = PropertyType::where('parent_id', '=', 0)->get();
        return view("dashboard.properties.list", compact('propertyTypes', 'property_for'));
    }


     /**
     * Display the specified resource.
     *
     * @param  \App\Models\MainUsers  $user
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $property = Property::where('slug',$id)->first();
        $country = @$property->areaDetails->country;
        $currency_code = config('constants.DEFAULT_CURRENCY');
        $favourite_count = $property->favouriteProperty->count();
        if($country){
            $currency_code = $country->currency_code?: config('constants.DEFAULT_CURRENCY');
        }
        return view('dashboard.properties.show', compact('property','currency_code','favourite_count'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property = Property::where('status','!=',2)->where('slug',$id)->first();
        $countries = Country::where('status','!=',2)->where('parent_id','=',0)->get();
        $property_types = PropertyType::where('parent_id','=',0)->get();
        $amenities = Amenity::where('parent_id','=',0)->where('status','!=',2)->get();
        $property_conditions = PropertyCondition::where('parent_id','=',0)->get();
        $property_completions = PropertyCompletionStatus::where('parent_id','=',0)->get();
        $bathroom_types = BathroomTypes::where('parent_id','=',0)->get();
        $bedroom_types = BedroomTypes::where('parent_id','=',0)->get();
        $maxImageUploads = \Helper::getMaxImagesUploadLimit();
        // $maxImageUploads = 10;
        if($property){
            $image_url =  public_path($this->uploadPath . $property->id);
            return view('dashboard.properties.edit', compact('property', 'image_url', 'countries', 'property_types', 'amenities', 'property_conditions', 'property_completions', 'bathroom_types', 'bedroom_types','maxImageUploads'));
        }else{
            return redirect()->route('properties')->with('errorMessage', __('backend.noDataFound'));
        }
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validations = $this->validateRequest($request,$id);
        
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $id = decrypt($id);

        $property_name_check = Property::where('property_name','=',$request->property_name)
                                ->where('id','!=',$id)
                                ->where('status','!=',2)->exists();
        $property_slug_check = Property::where('slug','=', Str::slug($request->property_name, '-'))
                                ->where('id','!=',$id)
                                ->where('status','!=',2)->exists();

        $property = Property::find($id);
        $property_name = @$request->property_name?:"";
        if($property_name_check || $property_slug_check){
            $property_name = @$request->property_name . " " . $property->property_id;
        }

        if($request->hasFile('property_images')){
            $property_images = [];
            foreach($request->file('property_images') as $key => $file)
            {
                $filename = 'tmp-'. time() . \Str::slug($file->getClientOriginalName(), '-'). '.'.$file->extension();
                $ext = $file->getClientOriginalExtension();
                Storage::disk('public')->put( 'tmp/' . $filename,  File::get($file));

                $source_imagebanner = public_path('/storage/tmp/'.$filename);
                $file_namebanner= "property-".time().'-'.str_pad(rand(0,1000), 4, '0', STR_PAD_LEFT).'.'.$ext;
                
                $upload_dir = public_path( $this->getUploadPath() . $property->id);
                $image_destinationbanner = $upload_dir.'/'.$file_namebanner;
                if (!file_exists($upload_dir)) {
                    \File::makeDirectory($upload_dir, 0777, true);
                }
                
                Helper::correctImageOrientation($source_imagebanner);
                $compress_image = Helper::compressImage($source_imagebanner, $image_destinationbanner);
                Helper::correctImageOrientation($image_destinationbanner);
                // remove temporary images uploaded in folder for resize
                unlink($source_imagebanner);

                $property_images[$key]['property_id'] = $property->id;
                $property_images[$key]['property_image'] = $file_namebanner;
            }

            PropertyImages::insert($property_images);
        }

        if($request->deleted_image){
            $property_images = PropertyImages::wherein('id', $request->deleted_image)->get();
            foreach($property_images as $image){
                // dd(public_path($this->getUploadPath() . $property->id . '/' . $image->property_image));
                File::delete( public_path($this->getUploadPath() . $property->id . '/' . $image->property_image) );
            }

            $property_images = PropertyImages::wherein('id', $request->deleted_image)->delete();

        }
        // if ($request->$formFileName != "") {

        //     // $newname = $_FILES[$formFileName]['name'];
        //     // $ext = pathinfo($newname, PATHINFO_EXTENSION);
        //     // $insertimage = 'usersImageDefault-'.time().'.'.$ext; 
        //     // $tmpfile = $_FILES[$formFileName]['tmp_name'];
        //     // $upload_dir =  public_path()."/" . $this->getUploadPath();

        //     // if (!file_exists($upload_dir)) {
        //     //   \File::makeDirectory($upload_dir, 0777, true);
        //     // }
        //     // move_uploaded_file($tmpfile, $upload_dir.$insertimage);
            
            
        // }

        // foreach ($request->file('property_images') as $key => $imagefile) {

        //     //$image      = $request->file('photo');
        //     $fileName   = "property-" . time() . '' . $key . '.' . $imagefile->getClientOriginalExtension();

        //     //dd();
        //     $path = $imagefile->store($destinationPath);
        //     //Storage::disk('local')->put('images/1/smalls'.'/'.$fileName, $img, 'public');

        //     $image = new PropertyImages;
        //     // $path = $imagefile->store('/images/resource/'.$property->property_id, ['disk' =>   'property_images']);
        //     $image->property_image = $fileName;
        //     $image->property_id = $property->id;
        //     $image->save();
        // }

        $property->property_name = $property_name;
        $property->slug = Str::slug($property_name, '-');
        $property->property_address = @$request->property_address?:"";
        $property->property_description = @$request->property_description?:"";
        $property->area_id = @$request->area?:null;
        $property->property_type = @$request->property_type?:null;
        $property->property_for = @$request->property_for?:null;
        $property->base_price = @$request->property_price?:null;
        $property->property_sqft_area = @$request->property_sqft_area?:null;
        $property->property_amenities_ids = @$request->amenities?implode(',',$request->amenities):"";
        $property->condition_type_id = @$request->condition?:null;
        $property->completion_status_id = @$request->completion_status?:null;
        $property->bedroom_type = @$request->bedroom_type?:null;
        $property->total_bedrooms = @$request->total_bedrooms?:null;
        $property->bathroom_type = @$request->bathroom_type?:null;
        $property->total_bathrooms = @$request->total_bathrooms?:null;
        $property->total_toilets = @$request->total_toilets?:null;
        $property->property_address_latitude = @$request->latitude?:null;
        $property->property_address_longitude = @$request->longitude?:null;
        $property->updated_by = 0;
        $property->ip_address = $request->getClientIp()?:null;
        $property->save();

        return redirect()->route('properties')->with('success', __('backend.saveDone'));
    }

    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {          
                $active  = Property::wherein('id', $request->ids);
                $notapprovedList = $active->where('is_approved', '=', 0)->get();

                $template_id = 12;
                foreach($notapprovedList as $property) {
                    $user_id = $property->agent_id;
                    $language_id = $property->language_id;
                    $user_email = @urldecode($property->agentDetails->email);
                    $full_name = @urldecode($property->agentDetails->full_name);
                    $phone = $property->agentDetails->mobile_number;
                    $country_code = @urldecode($property->agentDetails->country_code);

                    $this->sendEmailApproveProperty($language_id, $user_email, $full_name, $phone, $country_code, $template_id,$property);

                }
                $active->update(['status' => 1, 'is_approved' => 1]);
            } elseif ($request->action == "block") {
                Property::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {

                $property_images = PropertyImages::wherein('property_id', $request->ids)->get();
                foreach($property_images as $image){
                    File::delete( public_path($this->getUploadPath() . $image->property_id . '/' . $image->property_image) );
                }

                $property_images = PropertyImages::wherein('property_id', $request->ids)->delete();

                Property::wherein('id', $request->ids)
                    ->update(['status' => 2]);
            }
        }
        return redirect()->route('properties')->with('doneMessage', __('backend.saveDone'));
    }



    public function sendEmailApproveProperty($language_id, $agent_email, $full_name, $mobile_no, $country_code, $template_id,$property,$logo="", $url =""){
        $setting = Setting::find(1);
		// dd($setting);
		$templateData = Helper::getEmailTemplateData($language_id, $template_id);
		// dd($templateData);

		$from_email = $setting['from_email'];
		$data = array('email' => $agent_email, 'full_name' => $full_name, 'agent_email' => $agent_email, 'phone' => $mobile_no, 'country_code' => $country_code, 'url' => $url, 'id' =>  $template_id, 'language_id' => $language_id, 'logo' => $logo, 'from_email' => $from_email, 'property' => $property);
		try {
            Mail::send('emails.property_approved', $data, function ($message) use ($data, $templateData) {
                $message->to($data['email'], $templateData->title)->subject($templateData->subject);
                $message->from($data['from_email'], 'DOM - Properties');
            });
		} catch (\Throwable $th) {
			// throw $th;
		}
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $property = Property::where('slug', $id)->first();
        if($property){

            $property_images = PropertyImages::where('property_id', $property->id)->get();
            foreach($property_images as $image){
                File::delete( public_path($this->getUploadPath() . $property->id . '/' . $image->property_image) );
            }

            $property_images = PropertyImages::where('property_id', $property->id)->delete();

            $property->status = 2;
            $property->updated_by = 0;
            $property->save();
            
            return redirect()->route('properties')
                ->with('success', __('backend.deleteDone'));

        }else{
            return redirect()->route('properties')
                ->with('error', __('backend.noDataFound'));
        }
    }

    public function validateRequest($request,$id="")
    {
        $validation_messages = [
            'property_name.required' => 'Property Name is required.',
            'property_name.string' => 'Only string allowed',
            'property_name.max' => 'Max length exceeded',
            'property_address' => 'Address is required.',
            'country.required' => 'Country is required.',
            'governorate.required' => 'Governorate is required.',
            'area.required' => 'Area is required.',
            'property_type.required' => 'Property Type is required.',
            'property_for.required' => 'Property For is required.',
            'property_price.required' => 'Price is required',
            'property_sqft_area.required' => 'Area(in sqft.) is Required',
            'amenities.required' => 'Amenities Required',
            'condition.required' => 'Condition is Required',
            'completion_status.required' => 'Completion Status is Required',
            'bedroom_type.required' => 'Bedroom Type is Required',
            'total_bedrooms.required' => 'Total Bedrooms is Required',
            'bathroom_type.required' => 'Bathroom Type is Required',
            'total_bathrooms.required' => 'Total Bathrooms is Required',
            'total_toilets.required' => 'Total Toilets required.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
        ];

        if($id!="")
        {
            $id = decrypt($id);
            $validator = Validator::make($request->all(), [
                'property_name' => [
                                    'required',
                                    'string',
                                    'max:200',
                                ],
                'property_address' => [ 'required' ],
                'country' => ['required'],
                'governorate' => ['required'],
                'area' => ['required'],
                'property_type' => ['required'],
                'property_for' => ['required'],
                'property_price' => ['required'],
                'property_sqft_area' => ['required'],
                'amenities' => ['required'],
                'condition' => [],
                'completion_status' => [],
                'bedroom_type' => [],
                'total_bedrooms' => [],
                'bathroom_type' => [],
                'total_bathrooms' => [],
                'total_toilets' => [],
                'latitude' => ['required'],
                'longitude' => ['required'],
            ],
            $validation_messages
        );
        } else{
            $validator = Validator::make($request->all(), [
                'property_name' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'property_address' => [ 'required' ],
                'country' => ['required'],
                'governorate' => ['required'],
                'area' => ['required'],
                'property_type' => ['required'],
                'property_for' => ['required'],
                'property_price' => ['required'],
                'property_sqft_area' => ['required'],
                'amenities' => ['required'],
                'condition' => [],
                'completion_status' => [],
                'bedroom_type' => [],
                'total_bedrooms' => [],
                'bathroom_type' => [],
                'total_bathrooms' => [],
                'total_toilets' => [],
                'latitude' => ['required'],
                'longitude' => ['required'],
            ],
            $validation_messages
            );
        }

        return $validator;
    }


    // AJAX
    public function getPropertyImages(Request $request){
        $property_id = $request->property_id;
        $property = Property::find($property_id);
        $property_image_url = $this->uploadPath;
        
        $html = view('dashboard.properties.images',compact("property",'property_image_url'));
        return $html; 
    }

    public function anyData(Request $request)
    {
        // dd($request->all());
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
        if ($columnIndex == 0) {
            $sort = 'id';
        } elseif ($columnIndex == 1) {
            $sort = 'properties.property_name';
        } elseif ($columnIndex == 2) {
            $sort = 'users.full_name';
        }
        elseif ($columnIndex == 3) {
            $sort = 'users.mobile_number';
        }
        elseif ($columnIndex == 4) {
            $sort = 'property_types.type';
        }
        elseif ($columnIndex == 5) {
            $sort = 'properties.property_for';
        }
        elseif ($columnIndex == 6) {
            $sort = 'properties.property_address';
        }
        elseif ($columnIndex == 7) {
            $sort = 'properties.property_sqft_area';
        }
        elseif ($columnIndex == 8) {
            $sort = 'properties.base_price';
        }
        elseif ($columnIndex == 9) {
            $sort = 'properties.created_at';
        }
        elseif ($columnIndex == 10) {
            $sort = 'properties.property_subscription_enddate';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = DB::table('properties')
                    ->select( 'properties.*', 'properties.id as PropertyId', 'users.full_name as agent_name','users.country_code', 'users.mobile_number', 'property_types.type as property_type_text', 'area.name as property_area_name', 'property_conditions.condition_text as propertyConditionText', 'property_completion_statuses.completion_type as property_completion_status')
                    // ,\DB::raw("GROUP_CONCAT(amenity.amenity_name) as amenities") 
                    ->leftjoin('users','properties.agent_id','=','users.id')
                    ->leftjoin('property_types','properties.property_type','=','property_types.id')
                    // ->leftjoin("amenity",\DB::raw("FIND_IN_SET(amenity.id,properties.property_amenities_ids)"),">",\DB::raw("'0'"))
                    ->leftjoin('area','properties.area_id','=','area.id')
                    ->leftjoin('property_conditions','properties.condition_type_id','=','property_conditions.id')
                    ->leftjoin('property_completion_statuses','properties.completion_status_id','=','property_conditions.id')
                    ->leftjoin('property_for','properties.property_for','=', 'property_for.id')
                    ->where('properties.status','!=',2);

        $totalRecords = $totalAr->get()->count();

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('properties.property_name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('users.full_name', 'LIKE', '%' . urlencode($searchValue) . '%')
                    // ->orWhere('users.mobile_number', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere(DB::raw("CONCAT(users.country_code, '+', users.mobile_number)"), 'LIKE','%'.urlencode($searchValue).'%')
                    ->orWhere('property_types.type', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('property_for.for_text', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_address', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_sqft_area', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.base_price', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.created_at', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_subscription_enddate', 'LIKE', '%' . urlencode($searchValue) . '%');
            });
        }

        if($start_date){
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('properties.created_at', '>=', $min_date);
        }

        if($end_date){
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('properties.created_at', '<=', $min_date . ' 23:59:59');
        }

        if($request->get('property_type')){
            $totalAr->where('property_type', '=', $request->get('property_type'));
        }

        if($request->get('property_for')){
            $totalAr->where('property_for', '=', config('constants.PROPERTY_FOR_TEXT.'.$request->get('property_for').'.value'));
        }

        $totalDiplayRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            // ->groupBy("properties.id")
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $showPage =  route('property.show', ['id' => $data->slug]);
            $editPage =  route('property.edit', ['id' => $data->slug]);
            $delete   =  route('property.delete', ['id' => $data->slug]);

            $status = "";
            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.$data->property_name.'"> <i class="fa-solid fa-trash text-danger"></i> </a> ';
            // <i class="fa-solid fa-trash"></i>

            $currency = "KD";
            if($data->area_id){
                $area = Area::find($data->area_id);
                $country = Country::find($area->country_id);
                $currency = $country->currency_code;
            }

            $property_for = "-";
            if(@$data->property_for){
                $property_for = \Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.'.$data->property_for.'.label_key'));
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "property_name" => @$data->property_name?:"",
                "agent_name" => @urldecode($data->agent_name)?:"",
                "agent_contact" => @urldecode($data->country_code) . ' ' . @$data->mobile_number,
                "property_type" => @$data->property_type_text?:"-",
                "property_for" => $property_for,
                "property_address" => @$data->property_address ? \Str::limit($data->property_address,20,'...') : "-",
                "property_sqft_area" => @$data->property_sqft_area?:"-",
                "property_price" => @$data->base_price?$data->base_price." ".$currency:"-",
                "date_listed" => @$data->created_at ? Carbon::parse($data->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' H:i A') : "-",
                "subscription_expire_date" => @$data->property_subscription_enddate ? Carbon::parse($data->property_subscription_enddate)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') : "-",
                "status" => $status,
                "options" => isset($options) ? $options : '',
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalDiplayRecords,
            "aaData" => $data_arr
        );
        echo json_encode($response);
    }




}
