<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Label;
use App\Models\Language;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Area;
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
use Str;

class AreaController extends Controller
{
    //

    protected $flag_image_uri = "";
    protected $flag_not_found_uri = "";
    protected $no_flag_image = "";
    protected $uploadPath;

    public function __construct()
    {
        $this->middleware('auth');
        $this->setFlagImagePath();
        $this->flag_not_found_uri = asset('assets/dashboard/images/no_image.png');
        $this->no_flag_image = asset('assets/dashboard/images/no_image.png');
        $this->uploadPath = "assets/dashboard/images/areas/";
    }

    public function getFlagImagePath()
    {
        return asset('assets/dashboard/images/areas');
    }

    public function setFlagImagePath()
    {
        $this->flag_image_uri = $this->getFlagImagePath() . '/';
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view("dashboard.area.list");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $area = Area::find($id);
        $image_url = $this->flag_image_uri;
        return view('dashboard.area.show', compact('area', 'image_url'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::where('status', '!=', 2)->where('parent_id', '=', 0)->get();
        return view("dashboard.area.create", compact('countries'));
    }

    public function governorate_list(Request $request)
    {
        $country_id = $request->country_id;
        $governorate = Governorate::where('country_id', '=', $country_id)->where('status', '!=', 2)->where('parent_id', '=', 0)->get();

        $govrnorate_data = [];
        foreach ($governorate as $key => $value) {
            $govrnorate_arr = [];
            $govrnorate_arr['id'] = $value->id;
            $govrnorate_arr['name'] = @$value->name ? urldecode($value->name) : "";

            $govrnorate_data[] = $govrnorate_arr;
        }

        echo json_encode($govrnorate_data);
    }

    public function area_list(Request $request)
    {
        $governorate = $request->governorate;
        $area = Area::where('governorate_id', '=', $governorate)->where('status', '!=', 2)->where('parent_id', '=', 0)->get();

        $area_data = [];
        foreach ($area as $key => $value) {
            $area_arr = [];
            $area_arr['id'] = $value->id;
            $area_arr['name'] = @$value->name ? urldecode($value->name) : "";

            $area_data[] = $area_arr;
        }

        echo json_encode($area_data);
    }


    public function validateRequest($request, $id = "", $lang_id = "", $childId = "")
    {
        $validation_messages = [
            'area_name.required' => 'Area Name is required.',
            'area_name.unique' => 'Area Name is already taken.',
            'area_name.encoded_unique' => 'Area Name is already taken.',
            'area_name.string' => 'Only string allowed',
            'area_name.max' => 'Max length exceeded',
            'governorate.required' => 'Governorate is required.',
            'country.required' => 'Country is required.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
        ];

        if ($id != "" && empty($childId)) {
            $validator = Validator::make(
                $request->all(),
                [
                    'area_name' => [
                        'required',
                        'string',
                        'max:100',
                        'encoded_unique:area,name,id,' . $id . ',status,2',
                    ],
                    'country' => ['required'],
                    'governorate' => ['required'],
                    'latitude' => ['required'],
                    'longitude' => ['required'],
                ],
                $validation_messages
            );
        } else if ($lang_id != "") {
            if ($childId != "") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'area_name' => [
                            'required',
                            'encoded_unique:area,name,id,' . $childId . ',status,2',
                            'max:100'
                        ],
                    ],
                    $validation_messages
                );
            } else {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'area_name' => [
                            'required',
                            'encoded_unique:area,name,status,2',
                            'max:100'
                        ],
                    ],
                    $validation_messages
                );
            }
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'area_name' => [
                        'required',
                        'string',
                        'max:100',
                        'encoded_unique:area,name,status,2'
                    ],
                    'country' => 'required',
                    'latitude' => ['required'],
                    'longitude' => ['required'],
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
        $area = new Area();
        $validations = $this->validateRequest($request);

        if ($validations->fails()) {
            return redirect()
                ->back()
                ->withErrors($validations)
                ->withInput();
        }

        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar =  'area-' . time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->uploadPath;
            $request->file($formFileName)->move($path, $fileFinalName_ar);

            $area->image = $fileFinalName_ar;
        }

        $area->name = urlencode(trim($request->area_name));
        $area->slug = Str::slug($request->area_name, '-');
        $area->country_id = trim($request->country);
        $area->governorate_id = trim($request->governorate);
        $area->latitude = trim($request->latitude);
        $area->longitude = trim($request->longitude);
        $area->default_range = trim($request->default_range);
        $area->updated_range = trim($request->updated_range);
        $area->status = 1;
        $area->parent_id = 0;
        $area->language_id = 1;
        $area->save();

        return redirect()->route('area')->with('doneMessage', __('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $area = Area::find($id);
        $countries = Country::where('status', '!=', 2)->where('parent_id', '=', 0)->get();
        $image_url = $this->flag_image_uri;

        if ($area) {
            return view( 'dashboard.area.edit', compact( 'area', 'countries', 'image_url' ) );
        } else {
            return redirect()->route('area')->with('errorMessage', __('backend.noDataFound'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validations = $this->validateRequest($request, $id);
        if ($validations->fails()) {
            return redirect()
                ->back()
                ->withErrors($validations)
                ->withInput();
        }

        $area = Area::find($id);

        // Start of Upload Files
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->uploadPath;
            $request->file($formFileName)->move($path, $fileFinalName_ar);

        }

        if ($request->photo_delete == 1) {
            // Delete a User file
            if ($area->image != "") {
                File::delete(public_path($this->uploadPath . '/' . $area->image));
            }

            $area->image = "";
        }
        if ($fileFinalName_ar != "") {
            // Delete a User file
            if ($area->image != "") {
                File::delete(public_path($this->uploadPath . '/' . $area->image));
            }

            $area->image = $fileFinalName_ar;
        }

        $area->name = urlencode($request->area_name);
        $area->slug = Str::slug($request->area_name, '-');
        $area->governorate_id = urlencode($request->governorate);
        $area->country_id = trim($request->country);
        $area->latitude = trim($request->latitude);
        $area->longitude = trim($request->longitude);
        $area->default_range = trim($request->default_range);
        $area->updated_range = trim($request->updated_range);
        $area->save();

        return redirect()->route('area')->with('success', __('backend.saveDone'));
    }


    public function multiLang(Request $request, $parentId, $langId)
    {
        $area = Area::where('parent_id', '=', $parentId)->where('language_id', '=', $langId);
        $isAreaExists = $area->count();
        $gov_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Area::find($parentId);
        $areaData = [];
        if ($isAreaExists > 0) {
            $areaData = $area->first();
        }
        return view('dashboard.area.addLang', compact('languageData', 'parentData', 'areaData'));
    }

    public function storeLang(Request $request)
    {

        $validations = $this->validateRequest($request, "", $request->area_language_id, $request->area_id);
        if ($validations->fails()) {
            return redirect()
                ->back()
                ->withErrors($validations)
                ->withInput();
        }
        $parentData = Area::find($request->area_parent_id);
        $newUser = Area::updateOrCreate([
            'language_id' =>  (int) $request->area_language_id,
            'parent_id' =>  (int) $parentData->id,
        ], [
            'name' => urlencode($request->area_name),
            'status' =>  1
        ]);
        return redirect()->route('area')->with('success', __('backend.saveDone'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area = Area::find($id);
        if($area->image){
            File::delete( $this->flag_image_uri . '/' . $area->photo );
        }
        $area->status = 2;
        $area->save();

        return redirect()->route('area')
            ->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {

                $checkCountryInactive = Area::with('country')->wherein('id', $request->ids)->whereHas('country', function ($query) {
                    $query->where('status', '=', 0);
                })->get()->count();

                $checkGovernoratesInactive = Area::with('governorate')->wherein('id', $request->ids)->whereHas('governorate', function ($query) {
                    $query->where('status', '=', 0);
                })->get()->count();

                if ($checkGovernoratesInactive > 0) {
                    return redirect()->route('area')->with('error', __('backend.oneofareas_govs_inactive'));
                }

                if ($checkCountryInactive > 0) {
                    return redirect()->route('area')->with('error', __('backend.oneofgovs_country_inactive'));
                }

                $active  = Area::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                Area::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                Area::wherein('id', $request->ids)
                    ->update(['status' => 2]);
            }
        }
        return redirect()->route('area')->with('doneMessage', __('backend.saveDone'));
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
            $sort = 'area.name';
        } elseif ($columnIndex == 3) {
            $sort = 'governorate.name';
        } elseif ($columnIndex == 4) {
            $sort = 'countries.name';
        }
        elseif ($columnIndex == 5) {
            $sort = 'area.name';
        }
        elseif ($columnIndex == 6) {
            $sort = 'area.name';
        }
        elseif ($columnIndex == 7) {
            $sort = 'area.default_range';
        }
        elseif ($columnIndex == 8) {
            $sort = 'area.updated_range';
        }
        elseif ($columnIndex == 9) {
            $sort = 'area.status';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = \DB::table('area')
            ->select('area.*', 'countries.name as country_name', 'countries.status as country_status', 'governorate.name as governorate_name', 'governorate.status as governorate_status')
            ->leftJoin('governorate', 'area.governorate_id', '=', 'governorate.id')
            ->leftJoin('countries', 'area.country_id', '=', 'countries.id')
            ->where('area.status', '!=', 2)
            ->where('area.parent_id', '=', 0)
            ->where('governorate.status', '!=', 2)
            ->where('countries.status', '!=', 2);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                return $query->where('area.name', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('governorate.name', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('area.latitude', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('area.longitude', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('area.default_range', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('area.updated_range', 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('countries.name', 'LIKE', '%' . urlencode($searchValue) . '%');
            });
        }

        // $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $country_name =    isset($data->country_name) ? $data->country_name : '';
            $governorate_name = isset($data->governorate_name) ? $data->governorate_name : '';
            $area_name = isset($data->name) ? $data->name : '';

            if ($data->status == 1 && $data->country_status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('area.show', ['id' => $data->id]);
            $edit = route('area.edit', ['id' => $data->id]);
            $delete = route('area.delete', ['id' => $data->id]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $language = Language::where('id', ">", '1')->get();

            foreach ($language as $k => $lang) {
                $langEdit =  route('area.multiLang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $options .= '<a class="btn paddingset delete-area" onclick="deleteArea(this);" href="javascript:void(0)" data-href="' . $delete . '" title="Delete" data-name="' . urldecode($area_name) . '"> <span class="fa fa-trash text-danger"></span> </a> ';

            $country_flag = $this->no_flag_image;
            if ($data->image) {
                $checkFile = $this->flag_image_uri . '/' . $data->image;
                // if(file_exists($checkFile)){
                $country_flag = $checkFile;
                // }else{
                //     $country_flag = $this->flag_not_found_uri;
                // }
            }

            $area_image = '<a href="' . $country_flag . '" alt="' . $country_name . '" target="_blank" style="cursor: pointer;"><img src="' . $country_flag . '" alt="' . $country_name . '" width="80" height="40"></a>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "area_image" => $area_image,
                "area_name" => urldecode($area_name),
                "governorate_name" => urldecode($governorate_name),
                "country_name" => urldecode($country_name),
                "latitude" => @$data->latitude?:'-',
                "longitude" => @$data->longitude?:'-',
                "default_range" =>  @$data->default_range?:'-',
                "updated_range" =>  @$data->updated_range?:'-',
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
