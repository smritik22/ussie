<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Language;
use App\Models\PropertyType;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Throwable;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;

class PropertyTypesController extends Controller
{
    // Define Default Variables

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
        return view("dashboard.property_types.list");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.property_types.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $property_type = new PropertyType();
        $this->validateRequest();
        $property_type->type = $request->property_type;
        $property_type->parent_id = 0;
        $property_type->language_id = 1;
        $property_type->save();

        return redirect()->route('property_type')->with('success', __('backend.addDone'));
    }

    public function storeLang(Request $request)
    {
        $this->validateRequest('', $request->property_type_parent_id, $request->property_type_id ,$request->property_type_language_id);
        $parentData = PropertyType::find($request->property_type_parent_id);
        $newUser = PropertyType::updateOrCreate([
            'language_id' =>  $request->property_type_language_id,
            'parent_id' =>  $parentData->id,
        ], [
            'type' => $request->property_type
        ]);
        return redirect()->route('property_type')->with('success', __('backend.saveDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property_type = PropertyType::find($id);
        return view('dashboard.property_types.edit', compact('property_type'));
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
        $this->validateRequest($id);
        $property_type = PropertyType::find($id);
        $property_type->type = $request->property_type;
        $property_type->save();
        return redirect()->route('property_type')->with('success', __('backend.saveDone'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $removeChildren = PropertyType::where('parent_id','=',$id)->delete();
        $property_type = PropertyType::find($id);
        $property_type->delete();

        return redirect()->route('property_type')->with('success', __('backend.deleteDone'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $property_type = PropertyType::find($id);
        return view('dashboard.property_types.show', compact('property_type'));
    }

    public function validateRequest($id = "", $parentId = "", $childId="", $langId="")
    {
        $validation_messages = [
            'property_type.required' => __('backend.property_type') . ' is required.',
            'property_type.unique' =>  'This ' . __('backend.property_type') . ' is already taken.',
        ];

        if ($id != "" && empty($parentId && $langId)) {
            $validateData = request()->validate(
                [
                    'property_type' => 'required|unique:App\Models\PropertyType,type,'.$id.',id',
                ],
                $validation_messages
            );
        } 
        else if($id=="" && !empty($childId)){
            $validateData = request()->validate(
                [
                    'property_type' => 'required|unique:App\Models\PropertyType,type,' . $childId . ',id',
                ],
                $validation_messages
            );
        }
        else if ($parentId != '' && $langId!="") {
            $validateData = request()->validate(
                [
                    'property_type' => 'required|unique:App\Models\PropertyType,type,0,id,parent_id,' . $parentId . ',language_id,'.$langId,
                ],
                $validation_messages
            );
        }
         else {

            $validateData = request()->validate(
                [
                    'property_type' => 'required|unique:App\Models\PropertyType,type',
                ],
                $validation_messages
            );
        }

        return $validateData;
    }
    public function langedit(Request $request, $parentId, $langId)
    {
        $property_type = PropertyType::where('parent_id', '=', $parentId)->where('language_id', '=', $langId);
        $isproperty_typeExists = $property_type->count();
        $property_type_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = PropertyType::find($parentId);
        $property_typeData = [];
        if ($isproperty_typeExists > 0) {
            $property_typeData = $property_type->first();
        }

        $title = (isset($property_typeData->type) ? __('backend.edit_lang_property_type') : __('backend.add_lang_property_type'));
        return view('dashboard.property_types.addLang', compact('languageData', 'parentData', 'property_typeData','title'));
    }

    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {          
                $active  = PropertyType::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                PropertyType::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                PropertyType::wherein('parent_id', $request->ids)->delete();
                PropertyType::wherein('id', $request->ids)->delete();
            }
        }
        return redirect()->route('property_type')->with('doneMessage', __('backend.saveDone'));
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
        if ($columnIndex == 0) {
            $sort = 'id';
        } elseif ($columnIndex == 1) {
            $sort = 'type';
        } elseif ($columnIndex == 2) {
            $sort = 'updated_at';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = PropertyType::with('childdata')->where('parent_id', '=', 0);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('type', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('updated_at', 'LIKE', '%' . urlencode($searchValue) . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $showPage = route('property_type.show', ['id' => $data->id]);
            $editPage = route('property_type.edit', ['id' => $data->id]);
            $delete   = route('property_type.delete', ['id' => $data->id]);

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.$data->type.'"> <span class="fa fa-trash text-danger"></span> </a> ';

            $language = Language::where('id', ">", '1')->get();
            foreach ($language as $k => $lang) {
                $langEdit =  route('property_type.editlang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "property_type" => isset($data->type) ? $data->type : '',
                "updated_at" => Carbon::parse($data->updated_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A'),
                "options" => isset($options) ? $options : '',
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
