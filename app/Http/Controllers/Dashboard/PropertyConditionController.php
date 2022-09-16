<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Property;
use App\Models\PropertyCondition;
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

class PropertyConditionController extends Controller
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
        return view("dashboard.property_condition.list");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.property_condition.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $condition = new PropertyCondition();
        $this->validateRequest();
        $condition->condition_text = $request->condition;
        $condition->parent_id = 0;
        $condition->language_id = 1;
        $condition->save();

        return redirect()->route('condition')->with('success', __('backend.addDone'));
    }

    public function storeLang(Request $request)
    {

        $this->validateRequest('', $request->condition_parent_id, $request->condition_id,$request->condition_language_id);
        $parentData = PropertyCondition::find($request->condition_parent_id);
        $newUser = PropertyCondition::updateOrCreate([
            'language_id' =>  $request->condition_language_id,
            'parent_id' =>  $parentData->id,
        ], [
            'condition_text' => $request->condition
        ]);
        return redirect()->route('condition')->with('success', __('backend.saveDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $condition = PropertyCondition::find($id);
        return view('dashboard.property_condition.edit', compact('condition'));
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
        $condition = PropertyCondition::find($id);
        $condition->condition_text = $request->condition;
        $condition->save();
        return redirect()->route('condition')->with('success', __('backend.saveDone'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $removeChildren = PropertyCondition::where('parent_id','=',$id)->delete();
        $condition = PropertyCondition::find($id);
        $condition->delete();

        return redirect()->route('condition')->with('success', __('backend.deleteDone'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condition = PropertyCondition::find($id);
        return view('dashboard.property_condition.show', compact('condition'));
    }

    public function validateRequest($id = "", $parentId = "", $childId = "", $langId="")
    {
        $validation_messages = [
            'condition.required' => __('backend.condition') . ' is required.',
            'condition.unique' =>  'This ' . __('backend.condition') . ' is already taken.',
        ];

        if ($id != "" && empty($parentId && $langId)) {
            $validateData = request()->validate(
                [
                    'condition' => 'required|unique:App\Models\PropertyCondition,condition_text,' . $id . ',id'
                ],
                $validation_messages
            );
        } 
        else if($id=="" && !empty($childId)){
            $validateData = request()->validate(
                [
                    'condition' => 'required|unique:App\Models\PropertyCondition,condition_text,' . $childId . ',id',
                ],
                $validation_messages
            );
        }
        else if ($parentId != '' && $langId!="") {
            $validateData = request()->validate(
                [
                    'condition' => 'required|unique:App\Models\PropertyCondition,condition_text,' . $parentId . ',id'
                ],
                $validation_messages
            );
        }
         else {

            $validateData = request()->validate(
                [
                    'condition' => 'required|unique:App\Models\PropertyCondition,condition_text',
                ],
                $validation_messages
            );
        }

        return $validateData;
    }
    public function langedit(Request $request, $parentId, $langId)
    {
        $condition = PropertyCondition::where('parent_id', '=', $parentId)->where('language_id', '=', $langId);
        $isconditionExists = $condition->count();
        $condition_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = PropertyCondition::find($parentId);
        $conditionData = [];
        if ($isconditionExists > 0) {
            $conditionData = $condition->first();
        }

        $title = (isset($conditionData->condition_text) ? __('backend.edit_lang_condition') : __('backend.add_lang_condition'));
        return view('dashboard.property_condition.addLang', compact('languageData', 'parentData', 'conditionData','title'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {          
                $active  = PropertyCondition::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                PropertyCondition::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {

                PropertyCondition::wherein('parent_id', $request->ids)->delete();
                PropertyCondition::wherein('id', $request->ids)->delete();
            }
        }
        return redirect()->route('condition')->with('doneMessage', __('backend.saveDone'));
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
            $sort = 'condition_text';
        } elseif ($columnIndex == 2) {
            $sort = 'updated_at';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = PropertyCondition::with('childdata')->where('parent_id', '=', 0);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('condition_text', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('created_at', 'LIKE', '%' . urlencode($searchValue) . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $showPage =  route('condition.show', ['id' => $data->id]);
            $editPage =  route('condition.edit', ['id' => $data->id]);
            $delete   =  route('condition.delete', ['id' => $data->id]);

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.$data->condition_text.'"> <i class="fa-solid fa-trash text-danger"></i> </a> ';
            // <i class="fa-solid fa-trash"></i>
            $language = Language::where('id', ">", '1')->get();
            foreach ($language as $k => $lang) {
                $langEdit =  route('condition.editlang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "property_condition" => isset($data->condition_text) ? $data->condition_text : '',
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
