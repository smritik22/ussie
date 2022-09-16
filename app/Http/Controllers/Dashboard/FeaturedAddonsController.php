<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAddons;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Auth;
use Redirect;
use Storage;
use DB;
use Str;
use Session;
use Illuminate\Support\Carbon;

class FeaturedAddonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('dashboard.featured_addons.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("dashboard.featured_addons.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validateRequest($request);

        $addon = new FeaturedAddons;
        $addon->no_of_extra_featured_post = $request->no_of_extra_featured_post ?: 0;
        $addon->extra_each_featured_post_price = $request->extra_each_featured_post_price ?: 0;
        $addon->status = 1;
        $addon->save();

        return redirect()->route('featured_addons')->with('doneMessage', __('backend.addDone'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $addon = FeaturedAddons::find($id);
        return view('dashboard.featured_addons.show', compact('addon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $addon = FeaturedAddons::find($id);
        return view('dashboard.featured_addons.edit', compact('addon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validateRequest($request, $id);

        $addon = FeaturedAddons::find($id);
        $addon->no_of_extra_featured_post = $request->no_of_extra_featured_post ?: 0;
        $addon->extra_each_featured_post_price = $request->extra_each_featured_post_price ?: 0;
        $addon->save();

        return redirect()->route('featured_addons')->with('doneMessage', __('backend.saveDone'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FeaturedAddons::where('id', '=', $id)->delete();
        return redirect()->route('featured_addons')->with('success', __('backend.deleteDone'));
    }


    public function updateAll(Request $request) {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $active  = FeaturedAddons::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                FeaturedAddons::wherein('id', $request->ids)->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                FeaturedAddons::wherein('id', $request->ids)->delete();
            }
        }
        return redirect()->route('featured_addons')->with('doneMessage', __('backend.saveDone'));
    }

    // validations
    private function validateRequest($request, $id = "") {
        $validation_messages = [
            "no_of_extra_featured_post.required" => __('backend.no_of_extra_featured_post') . ' is required.',
            "no_of_extra_featured_post.unique" => "This no. is already exists",
            "extra_each_featured_post_price.required" => __('backend.extra_each_featured_post_price') . ' is required.'
        ];

        if ($id != "") {
            $validations = [
                "no_of_extra_featured_post" => 'required|unique:App\Models\FeaturedAddons,no_of_extra_featured_post,'.$id.',id',
                "extra_each_featured_post_price" => 'required',
            ];
        }else {
            $validations = [
                "no_of_extra_featured_post" => 'required|unique:App\Models\FeaturedAddons,no_of_extra_featured_post',
                "extra_each_featured_post_price" => 'required',
            ];
        }

        $validateData = request()->validate(
            $validations,
            $validation_messages
        );

        return $validateData;
    }

    // ajax call get data 
    public function anyData(Request $request) {
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
        } 
        elseif ($columnIndex == 1) {
            $sort = 'no_of_extra_featured_post';
        }
        elseif ($columnIndex == 2) {
            $sort = 'extra_each_featured_post_price';
        }
        elseif ($columnIndex == 3) {
            $sort = 'status';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }


        $totalAr = FeaturedAddons::where('status', '!=', 2);
        $totalRecords = $totalAr->get()->count();
        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('no_of_extra_featured_post', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('extra_each_featured_post_price', 'LIKE', '%' . $searchValue . '%');
            });
        }

        $totalDiplayRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            // ->groupBy("properties.id")
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $currency = Helper::getDefaultCurrency();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $showPage =  route('featured_addon.show', ['id' => $data->id]);
            $editPage =  route('featured_addon.edit', ['id' => $data->id]);
            $delete   =  route('featured_addon.delete', ['id' => $data->id]);

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

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "no_of_extra_featured_post" => @$data->no_of_extra_featured_post ?: "",
                "extra_each_featured_post_price" => @$data->extra_each_featured_post_price ? $data->extra_each_featured_post_price . " " .  $currency : "-",
                "updated_at" => Carbon::parse($data->updated_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A'),
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
