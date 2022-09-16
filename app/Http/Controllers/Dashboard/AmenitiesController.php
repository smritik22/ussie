<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Label;
use App\Models\Language;
use App\Models\Amenity;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Redirect;
use DB;
use Session;
use Throwable;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use Illuminate\Support\MessageBag;

class AmenitiesController extends Controller
{
    //
    protected $_image_uri = "";
    protected $__not_found_uri = "";
    protected $no_image = "";
    protected $uploadPath = "";


    public function __construct()
    {
        $this->middleware('auth');
        $this->setImagePath();
        $this->__not_found_uri = asset('uploads/amenities/no_image.svg');
        $this->no_image = asset('uploads/amenities/no_image.svg');
        $this->uploadPath = "uploads/amenities";
    }

    public function getUploadPath(){
        return $this->uploadPath;
    }

    public function getImagePath(){
        return asset('uploads/amenities');
    }

    public function setImagePath(){
        $this->_image_uri = $this->getImagePath();
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.amenity.list");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $amenity = Amenity::find($id);
        $image_url = $this->_image_uri;
        return view('dashboard.amenity.show', compact('amenity','image_url'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.amenity.create");
    }


    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
        $validation_messages = [
            'amenity_name.required' => 'Amenity Name is required.',
            'amenity_name.unique' => 'Amenity Name is already taken.',
            'amenity_name.encoded_unique' => 'Amenity Name is already taken.',
            'amenity_name.string' => 'Only string allowed',
            'amenity_name.max' => 'Max length exceeded',
            'photo.mimes' => 'Not valid image extension',
        ];

        if($id !="" && empty($childId))
        {
            $validator = Validator::make($request->all(), [
                'amenity_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:amenity,amenity_name,id,'.$id.',status,2',
                                ],
                'image' => 'mimes:svg',
            ],
            $validation_messages
        );

        }
        else if( $lang_id!="" ){
            if($childId!=""){
                $validator = Validator::make($request->all(), [
                    'amenity_name' => [
                                        'required',
                                        'encoded_unique:amenity,amenity_name,id,'.$childId.',status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
                );
            }
            else{
                $validator = Validator::make($request->all(), [
                    'amenity_name' => [
                                        'required',
                                        'encoded_unique:amenity,amenity_name,status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
            );
            }
            
        } else{
            $validator = Validator::make($request->all(), [
                'amenity_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:amenity,amenity_name,status,2'
                                ],
                'image' => 'mimes:svg',
            ],
            $validation_messages
        );
        }

        return $validator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       
        $amenity = new Amenity();
        $validations = $this->validateRequest($request);

        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        // Start of Upload Files
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadPath();
            $uploadPath = public_path() . '/' . $path;

            // try{
                $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
            // }catch(Throwable $e){
            //     throw $e;
            // }
        }
        // End of Upload Files

        $amenity->amenity_name = urlencode(trim($request->amenity_name));
        $amenity->image = $fileFinalName_ar;
        $amenity->status = 1;
        $amenity->parent_id = 0;
        $amenity->language_id = 1;
        $amenity->save(); 

        return redirect()->route('amenity')->with('doneMessage', __('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $amenity = Amenity::find($id);
        $image_url = $this->_image_uri;
        if($amenity){
            return view('dashboard.amenity.edit', compact('amenity','image_url'));
        }else{
            return redirect()->route('amenity')->with('errorMessage', __('backend.noDataFound'));
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

        $amenity = Amenity::find($id);

        // Start of Upload Files
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111, 9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            
            $path = $this->getUploadPath();
            $uploadPath = public_path(). '/' . $path;

            //$path = $this->getUploadPath();

            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
        }
        // End of Upload Files

        if ($request->photo_delete == 1) {
            // Delete a User file
            if ($amenity->image != "") {
                File::delete($this->getUploadPath() . $amenity->image);
            }

            $amenity->image = "";
        }
        if ($fileFinalName_ar != "") {
            // Delete a User file
            if ($amenity->image != "") {
                File::delete($this->getUploadPath() . $amenity->image);
            }

            $amenity->image = $fileFinalName_ar;
        }

        $amenity->amenity_name = urlencode($request->amenity_name);
        $amenity->save(); 

        return redirect()->route('amenity')->with('success', __('backend.saveDone'));
    }


    public function multiLang(Request $request,$parentId,$langId){
        $amenity = Amenity::where('parent_id','=',$parentId)->where('language_id','=',$langId);
        $isExists = $amenity->count();
        $amenity_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Amenity::find($parentId);
        $amenityData = [];
        if($isExists>0){
            $amenityData = $amenity->first();
        }
        return view('dashboard.amenity.addLang',compact('languageData','parentData','amenityData'));
    }

    public function storeLang(Request $request){

        $validations = $this->validateRequest($request,"",$request->amenity_language_id,$request->amenity_id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }
        $parentData = Amenity::find($request->amenity_parent_id);
        $newUser = Amenity::updateOrCreate([
            'language_id' =>  (int) $request->amenity_language_id,
            'parent_id' =>  (int) $parentData->id,
        ],[
            'amenity_name' => urlencode($request->amenity_name),
            'status' =>  1
        ]);
        return redirect()->route('amenity')->with('success', __('backend.saveDone'));
        
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $amenity = Amenity::find($id);
        $amenity->status = 2;
        $amenity->save();

        return redirect()->route('amenity')
            ->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $active  = Amenity::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                Amenity::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                Amenity::wherein('id', $request->ids)
                    ->update(['status' => 2]);
            }
        }
        return redirect()->route('amenity')->with('doneMessage', __('backend.saveDone'));
    }


    // AJAX call server side pagination
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

        if ($columnIndex == 2) {
            $sort = 'amenity_name';
        } 
        elseif ($columnIndex==3) {
            $sort = 'status';
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Amenity::with('childdata')->where('status','!=',2)->where('parent_id','=',0);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('amenity_name', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }
        $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // dd($totalAr->toSql());

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $amenity_name = isset($data->amenity_name) ? $data->amenity_name : '';

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('amenity.show', ['id' => $data->id]);
            $edit = route('amenity.edit', ['id' => $data->id]);
            $delete = route('amenity.delete', ['id' => $data->id]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';
        
            $language = Language::where('id', ">", '1')->get();

            foreach ($language as $k => $lang) {
                $langEdit =  route('amenity.multiLang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $options .= '<a class="btn paddingset delete-amenity" onclick="deleteAmenity(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($amenity_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            $aminity_image = $this->no_image;
            if($data->image){
                $checkFile = $this->_image_uri . '/' . $data->image;
                $aminity_image = $checkFile;
            }

            $aminity_image_show = '<a href="' . $aminity_image . '" alt="' . $amenity_name . '" target="_blank" style="cursor: pointer;"><img src="' . $aminity_image . '" alt="' . $amenity_name . '" width="80" height="40"></a>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "amenity_image" => $aminity_image_show,
                "amenity_name" => urldecode($amenity_name),
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