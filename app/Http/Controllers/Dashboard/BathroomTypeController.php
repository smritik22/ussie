<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Language;
use App\Models\BathroomTypes;
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

class BathroomTypeController extends Controller
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
        return view("dashboard.bathroom_types.list");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.bathroom_types.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bathroom_type = new BathroomTypes();
        $this->validateRequest();
        $bathroom_type->type = $request->type;
        $bathroom_type->parent_id = 0;
        $bathroom_type->language_id = 1;
        $bathroom_type->save();

        return redirect()->route('bathroom_type')->with('success', __('backend.addDone'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bathroom_type = BathroomTypes::find($id);
        return view('dashboard.bathroom_types.show', compact('bathroom_type'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bathroomType = BathroomTypes::find($id);
        return view('dashboard.bathroom_types.edit', compact('bathroomType'));
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
        $bathroom_type = BathroomTypes::find($id);
        $bathroom_type->type = $request->type;
        $bathroom_type->save();
        return redirect()->route('bathroom_type')->with('success', __('backend.saveDone'));
    }



    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $active  = BathroomTypes::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                BathroomTypes::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                BathroomTypes::wherein('parent_id', $request->ids)->delete();
                BathroomTypes::wherein('id', $request->ids)->delete();
            }
        }
        return redirect()->route('bathroom_type')->with('doneMessage', __('backend.saveDone'));
    }


    public function langedit(Request $request, $parentId, $langId)
    {
        $bathroom_type = BathroomTypes::where('parent_id', '=', $parentId)->where('language_id', '=', $langId);
        $isbathroomTypeExists = $bathroom_type->count();
        $bathroom_type_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = BathroomTypes::find($parentId);
        $bathroomTypeData = [];
        if ($isbathroomTypeExists > 0) {
            $bathroomTypeData = $bathroom_type->first();
        }

        $title = (isset($bathroomTypeData->type) ? __('backend.edit_lang_bathroom_type') : __('backend.add_lang_bathroom_type'));
        return view('dashboard.bathroom_types.addLang', compact('languageData', 'parentData', 'bathroomTypeData', 'title'));
    }

    public function storeLang(Request $request)
    {
        $this->validateRequest('', $request->bathroom_type_parent_id, $request->bathroom_type_id, $request->bathroom_type_language_id);
        $parentData = BathroomTypes::find($request->bathroom_type_parent_id);
        $newUser = BathroomTypes::updateOrCreate([
            'language_id' =>  $request->bathroom_type_language_id,
            'parent_id' =>  $parentData->id,
        ], [
            'type' => $request->type
        ]);
        return redirect()->route('bathroom_type')->with('success', __('backend.saveDone'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $removeChildren = BathroomTypes::where('parent_id', '=', $id)->delete();
        $bathroom_type = BathroomTypes::where('id', '=', $id)->delete();

        return redirect()->route('bathroom_type')->with('success', __('backend.deleteDone'));
    }

    // *******************************************************************************************
    // VALIDATIONS
    public function validateRequest($id = "", $parentId = "", $childId = "", $langId = "")
    {
        $validation_messages = [
            'type.required' => __('backend.bathroom_type') . ' is required.',
            'type.unique' =>  'This ' . __('backend.bathroom_type') . ' is already taken.',
        ];

        if ($id != "" && empty($parentId && $langId)) {
            $validateData = request()->validate(
                [
                    'type' => 'required|unique:App\Models\BathroomTypes,type,' . $id . ',id',
                ],
                $validation_messages
            );
        } else if ($id == "" && !empty($childId)) {
            $validateData = request()->validate(
                [
                    'type' => 'required|unique:App\Models\BathroomTypes,type,' . $childId . ',id',
                ],
                $validation_messages
            );
        } else if ($parentId != '' && $langId != "") {
            $validateData = request()->validate(
                [
                    'type' => 'required|unique:App\Models\BathroomTypes,type,'.$parentId.',parent_id' ,
                ],
                $validation_messages
            );
        } else {

            $validateData = request()->validate(
                [
                    'type' => 'required|unique:App\Models\BathroomTypes,type',
                ],
                $validation_messages
            );
        }

        return $validateData;
    }



    // AJAX

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

        $totalAr = BathroomTypes::with('childdata')->where('parent_id', '=', 0);

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
            $showPage = route('bathroom_type.show', ['id' => $data->id]);
            $editPage = route('bathroom_type.edit', ['id' => $data->id]);
            $delete   = route('bathroom_type.delete', ['id' => $data->id]);

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="' . $delete . '" title="Delete" data-name="' . $data->type . '"> <span class="fa fa-trash text-danger"></span> </a> ';

            $language = Language::where('id', ">", '1')->get();
            foreach ($language as $k => $lang) {
                $langEdit =  route('bathroom_type.editlang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "bathroom_type" => isset($data->type) ? $data->type : '-',
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
