<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Label;
use App\Models\Language;
use App\Models\Country;
use App\Models\Governorate;
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

class GovernorateController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.governorate.list");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $governorate = Governorate::find($id);
        return view('dashboard.governorate.show', compact('governorate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::where('status','!=',2)->where('parent_id','=',0)->get();
        return view("dashboard.governorate.create",compact('countries'));
    }


    public function validateRequest($request,$id="",$lang_id = "",$childId="")
    {
        $validation_messages = [
            'governorate_name.required' => 'Governorate Name is required.',
            'governorate_name.unique' => 'Governorate Name is already taken.',
            'governorate_name.encoded_unique' => 'Governorate Name is already taken.',
            'governorate_name.string' => 'Only string allowed',
            'governorate_name.max' => 'Max length exceeded',
            'country.required' => 'Country is required.',
        ];

        if($id !="" && empty($childId))
        {
            $validator = Validator::make($request->all(), [
                'governorate_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:governorate,name,id,'.$id.',status,2',
                                ],
                'country' => ['required'],
            ],
            $validation_messages
        );

        }
        else if( $lang_id!="" ){
            if($childId!=""){
                $validator = Validator::make($request->all(), [
                    'governorate_name' => [
                                        'required',
                                        'encoded_unique:governorate,name,id,'.$childId.',status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
                );
            }
            else{
                $validator = Validator::make($request->all(), [
                    'governorate_name' => [
                                        'required',
                                        'encoded_unique:governorate,name,status,2',
                                        'max:100'
                                    ],
                ],
                $validation_messages
            );
            }
            
        } else{
            $validator = Validator::make($request->all(), [
                'governorate_name' => [
                                    'required',
                                    'string',
                                    'max:100',
                                    'encoded_unique:governorate,name,status,2'
                                ],
                'country' => 'required'
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
        $governorate = new Governorate();
        $validations = $this->validateRequest($request);

        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }

        $governorate->name = urlencode($request->governorate_name);
        $governorate->country_id = trim($request->country);
        $governorate->status = 1;
        $governorate->parent_id = 0;
        $governorate->language_id = 1;
        $governorate->save(); 

        return redirect()->route('governorate')->with('doneMessage', __('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $governorate = Governorate::find($id);
        $countries = Country::where('status','!=',2)->where('parent_id','=',0)->get();

        if($governorate){
            return view('dashboard.governorate.edit', compact('governorate','countries'));
        }else{
            return redirect()->route('governorate')->with('errorMessage', __('backend.noDataFound'));
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
        $governorate = Governorate::find($id);
        $governorate->name = urlencode($request->governorate_name);
        $governorate->country_id = $request->country;
        $governorate->save(); 

        return redirect()->route('governorate')->with('success', __('backend.saveDone'));
    }


    public function multiLang(Request $request,$parentId,$langId){
        $governorate = Governorate::where('parent_id','=',$parentId)->where('language_id','=',$langId);
        $isGovernorateExists = $governorate->count();
        $gov_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Governorate::find($parentId);
        $governorateData = [];
        if($isGovernorateExists>0){
            $governorateData = $governorate->first();
        }
        return view('dashboard.governorate.addLang',compact('languageData','parentData','governorateData'));
    }

    public function storeLang(Request $request){

        $validations = $this->validateRequest($request,"",$request->governorate_language_id,$request->governorate_id);
        if ($validations->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validations)
                        ->withInput();
        }
        $parentData = Governorate::find($request->governorate_parent_id);
        $newUser = Governorate::updateOrCreate([
            'language_id' =>  (int) $request->governorate_language_id,
            'parent_id' =>  (int) $parentData->id,
        ],[
            'name' => urlencode($request->governorate_name),
            'status' =>  1
        ]);
        return redirect()->route('governorate')->with('success', __('backend.saveDone'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Governorate::find($id);
        $country->status = 2;
        $country->save();
        
        return redirect()->route('governorate')
            ->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $checkCountryInactive = Governorate::with('country')->wherein('id', $request->ids)->whereHas('country', function ($query){
                    $query->where('status','=',0);
                })->get()->count();
                if($checkCountryInactive > 0){
                    return redirect()->route('governorate')->with('error', __('backend.oneofgovs_country_inactive'));
                }
                $active  = Governorate::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                Governorate::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                Governorate::wherein('id', $request->ids)
                    ->update(['status' => 2]);
            }
        }
        return redirect()->route('governorate')->with('doneMessage', __('backend.saveDone'));
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
            $sort = 'countries.name';
        } 
        elseif ($columnIndex==2) {
            $sort = 'governorate.name';
        } 
        elseif ($columnIndex == 3) {
            $sort = 'governorate.status';
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }
        
        // // $totalAr = Country::where('status','!=',2)->where('parent_id','=',0);
        // // if ($searchValue != "") {
        // //     $totalAr = $totalAr->where(function ($query) use ($searchValue) {
        // //         $query->orWhere('name', 'like', '%' . $searchValue . '%')
        // //             ->orWhere('country_code', 'like', '%' . $searchValue . '%')
        // //             ->orWhere('currency_code', 'like', '%' . $searchValue . '%')
        // //             ->orWhere('currency_decimal_point', 'like', '%' . $searchValue . '%');
        // //     });
        // // }

        // $totalAr = Governorate::with('childdata','country');
        // if($searchValue!= ""){
        //     $totalAr = $totalAr->whereHas('country' , function ($countryQuery) use ($searchValue) {
        //         return $countryQuery = $countryQuery->where('language_id','=',$lang_id);
        //     })->where('status','=',1)->get();
        // }

        $totalAr = \DB::table('governorate')
                ->select('governorate.*','countries.name as country_name', 'countries.status as country_status')
                ->leftJoin('countries','governorate.country_id','=','countries.id')
                ->where('governorate.status','!=',2)
                ->where('governorate.parent_id','=',0)
                ->where('countries.status','!=',2);

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('governorate.name', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('countries.name', 'LIKE', '%'.$searchValue.'%');
            });
        }

        $setting = Setting::first();


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $country_name =    isset($data->country_name) ? $data->country_name : '';
            $governorate_name = isset($data->name) ? $data->name : '';

            if ($data->status == 1 && $data->country_status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('governorate.show', ['id' => $data->id]);
            $edit = route('governorate.edit', ['id' => $data->id]);
            $delete = route('governorate.delete', ['id'=>$data->id]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';
        
            $language = Language::where('id', ">", '1')->get();

            foreach ($language as $k => $lang) {
                $langEdit =  route('governorate.multiLang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $options .= '<a class="btn paddingset delete-governorate" onclick="deleteGovernorate(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($governorate_name).'"> <span class="fa fa-trash text-danger"></span> </a> ';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "governorate_name" => urldecode($governorate_name),
                "country_name" => urldecode($country_name),
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


    // end controller
}
