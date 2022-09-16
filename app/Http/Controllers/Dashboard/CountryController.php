<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Label;
use App\Models\Language;
use App\Models\Country;
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

class CountryController extends Controller
{
    protected $flag_image_uri = "";
    protected $flag_not_found_uri = "";
    protected $no_flag_image = "";

    public function __construct()
    {
        $this->middleware('auth');
        $this->setFlagImagePath();
        $this->flag_not_found_uri = asset('assets/dashboard/images/no_image.png');
        $this->no_flag_image = asset('assets/dashboard/images/flags.jpg');
    }

    public function getFlagImagePath(){
        return asset('assets/dashboard/images/flags');
    }

    public function setFlagImagePath(){
        $this->flag_image_uri = $this->getFlagImagePath();
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {        
        return view("dashboard.country.list");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $country = Country::find($id);
        return view('dashboard.country.show', compact('country'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.country.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
        $validation_messages = [
            'country_name.required' => 'Country Name is required.',
            'country_name.unique' => 'Country Name is already taken.',
            'country_name.encoded_unique' => 'Country Name is already taken.',
            'country_name.string' => 'Only string allowed',
            'country_name.max' => 'Max length exceeded',
            'country_code.required' => 'Country Code is required.',
            'currency_code.required' => 'Currency Code is required.',
            'currency_code.required' => 'Currency Code is required.',
            'currency_decimal_point.required' => 'Currency Decimal Point is required.',
        ];

        if($id !="" && empty($childId))
        {
            $validator = Validator::make($request->all(), [
                'country_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:countries,name,id,'.$id.',status,2'
                                    // Rule::unique('countries','name')->ignore($id)->where(function ($query) use ($request) {
                                    //     return $query->where('2','!=')->where('name','=',urlencode($request->country_name));
                                    // })
                                ],
                'country_code' => [
                                    'required',
                                    Rule::unique('countries','country_code')->ignore($id)->where(function ($query) use($request) {
                                        return $query->where('status','!=','2');
                                    })
                                ],
                'currency_code' => [
                                    'required',
                                    Rule::unique('countries','currency_code')->ignore($id)->where(function ($query) use($request) {
                                        return $query->where('status','!=','2');
                                    })
                                ],
                'currency_decimal_point' => 'required',
            ],
            $validation_messages
        );

        }
        else if( $lang_id!="" ){
            if($childId!=""){
                $validator = Validator::make($request->all(), [
                    'country_name' => [
                                        'required',
                                        'encoded_unique:countries,name,id,'.$childId.',status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
                );
            }
            else{
                $validator = Validator::make($request->all(), [
                    'country_name' => [
                                        'required',
                                        'encoded_unique:countries,name,status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
            );
            }
            
        } else{
            $validator = Validator::make($request->all(), [
                'country_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:countries,name,status,2'
                                    // Rule::unique('countries','name')->where( function ($query) use($request) {
                                    //     return $query->where('status','!=','2')->where('name','=',urlencode($request->country_name));
                                    // }),
                                ],
                'country_code' => 'required',
                'currency_code' => 'required',
                'currency_decimal_point' => 'required',
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
        $country = new Country();
        $validations = $this->validateRequest($request);

        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $country->name = urlencode($request->country_name);
        $country->country_code = $request->country_code;
        $country->currency_code = $request->currency_code;
        $country->currency_decimal_point = $request->currency_decimal_point;
        $country->status = 1;
        $country->parent_id = 0;
        $country->language_id = 1;
        $country->save(); 

        return redirect()->route('country')->with('doneMessage', __('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = Country::find($id);
        if($country->count() > 0){
            return view('dashboard.country.edit', compact('country'));
        }else{
            return redirect()->route('country')->with('errorMessage', __('backend.noDataFound'));
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
        $country = Country::find($id);
        $country->name = urlencode($request->country_name);
        $country->country_code = $request->country_code;
        $country->currency_code = $request->currency_code;
        $country->currency_decimal_point = $request->currency_decimal_point;
        $country->save(); 

        return redirect()->route('country')->with('success', __('backend.saveDone'));
    }


    public function multiLang(Request $request,$parentId,$langId){
        $country = Country::where('parent_id','=',$parentId)->where('language_id','=',$langId);
        $isCountryExists = $country->count();
        $country_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Country::find($parentId);
        $countryData = [];
        if($isCountryExists>0){
            $countryData = $country->first();
        }
        return view('dashboard.country.addLang',compact('languageData','parentData','countryData'));
    }

    public function storeLang(Request $request){

        $validations = $this->validateRequest($request,"",$request->country_language_id,$request->country_id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }
        $parentData = Country::find($request->country_parent_id);
        $newUser = Country::updateOrCreate([
            'language_id' =>  (int) $request->country_language_id,
            'parent_id' =>  (int) $parentData->id,
        ],[
            'name' => urlencode($request->country_name),
            'status' =>  1
        ]);
        return redirect()->route('country')->with('success', __('backend.saveDone'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::find($id);
        $country->status = 2;
        $country->save();
        
        return redirect()->route('country')
            ->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $active  = Country::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                Country::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                Country::wherein('id', $request->ids)
                    ->update(['status' => 2]);
            }
        }
        return redirect()->route('country')->with('doneMessage', __('backend.saveDone'));
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

        if ($columnIndex == 1) {
            $sort = 'name';
        } 
        elseif ($columnIndex==2) {
            $sort = 'country_code';
        } 
        elseif ($columnIndex == 3) {
            $sort = 'currency_code';
        } 
        elseif ($columnIndex == 4) {
            $sort = 'currency_decimal_point';
        }
        elseif ($columnIndex == 5) {
            $sort = 'status';
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }
        
        $totalAr = Country::where('status','!=',2)->where('parent_id','=',0);
        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('country_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('currency_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('currency_decimal_point', 'like', '%' . $searchValue . '%');
            });
        }

        // $totalAr = Country::with('childdata')->whereHas('childdata' , function ($childquery) use($lang_id) {
        //     return $childquery = $childquery->where('language_id','=',$lang_id);
        // })->where('status','=',1)->get();


        $setting = Setting::first();


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $country_name =    isset($data->name) ? $data->name : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $currency_code = isset($data->currency_code) ? $data->currency_code : '';
            $currency_decimal_point = isset($data->currency_decimal_point) ? $data->currency_decimal_point : '';

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('country.show', ['id' => $data->id]);
            $edit = route('country.edit', ['id' => $data->id]);
            $delete = route('country.delete', ['id'=>$data->id]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';
        
            $language = Language::where('id', ">", '1')->get();

            foreach ($language as $k => $lang) {
                $langEdit =  route('country.multiLang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $options .= '<a class="btn paddingset delete-country" onclick="deleteCountry(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($country_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            // $country_flag = $this->no_flag_image;
            // if($data->image){
            //     $checkFile = $this->flag_image_uri . '/' . $data->image;
            //     // if(file_exists($checkFile)){
            //         $country_flag = $checkFile;
            //     // }else{
            //     //     $country_flag = $this->flag_not_found_uri;
            //     // }
            // }

            // $country_flag_image = '<a href="' . $country_flag . '" alt="' . $country_name . '" target="_blank" style="cursor: pointer;"><img src="' . $country_flag . '" alt="' . $country_name . '" width="80" height="40"></a>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                // "country_flag" => $country_flag_image,
                "country_name" => urldecode($country_name),
                "country_code" => $country_code,
                "currency_code" => $currency_code,
                "currency_decimal_point" => $currency_decimal_point,
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
