<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Language;
use App\Models\SubscriptionPlan;
use Auth;
use DateTime;
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

class SubscriptionPlanController extends Controller
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
        return view("dashboard.subscription_plans.list");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd(Helper::getValidTillDate("2022-02-01",4));
        // dd(Helper::convert(2));
        return view("dashboard.subscription_plans.create");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription_plan = SubscriptionPlan::find($id);
        $plan_duration = Helper::getValidTillDate( date('Y-m-d H:i:s'), $subscription_plan->plan_duration_value ,$subscription_plan->plan_duration_type);
        return view('dashboard.subscription_plans.show', compact('subscription_plan', 'plan_duration'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subscription_plan = new SubscriptionPlan();
        
        $this->validateRequest($request);

        $subscription_plan->parent_id = 0;
        $subscription_plan->language_id = 1;
        $subscription_plan->plan_name = @$request->plan_name?:"";
        $subscription_plan->plan_description = @$request->plan_description?:"";
        $subscription_plan->plan_type = @$request->plan_type?:"";
        $subscription_plan->no_of_plan_post = @$request->no_of_plan_post?:"";
        $subscription_plan->is_free_plan = @$request->is_free ?: 0;
        $subscription_plan->plan_price = ( $request->plan_price && @$request->is_free != '1' )?$request->plan_price:"";
        $subscription_plan->plan_duration_value = @$request->plan_duration_value?:0;
        $subscription_plan->plan_duration_type = @$request->plan_duration_type?:0;
        $subscription_plan->extra_each_normal_post_price = @$request->extra_each_normal_post_price?:0;
        $subscription_plan->is_featured = @$request->is_featured ?: 0;
        $subscription_plan->no_of_default_featured_post = @$request->no_of_default_featured_post ?: 0;
        $subscription_plan->status = 1; 
        $subscription_plan->save();

        return redirect()->route('subscription_plans')->with('success', __('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subscription_plan = SubscriptionPlan::find($id);
        return view('dashboard.subscription_plans.edit', compact('subscription_plan'));
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
        $this->validateRequest($request,$id);
        $subscription_plan = SubscriptionPlan::find($id);
        $subscription_plan->plan_name = @$request->plan_name?:"";
        $subscription_plan->plan_description = @$request->plan_description?:"";
        $subscription_plan->plan_type = @$request->plan_type?:"";
        $subscription_plan->no_of_plan_post = @$request->no_of_plan_post?:"";
        $subscription_plan->is_free_plan = @$request->is_free ?: 0;
        $subscription_plan->plan_price = ( $request->plan_price && @$request->is_free != '1' )?$request->plan_price:"";
        $subscription_plan->plan_duration_value = @$request->plan_duration_value?:0;
        $subscription_plan->plan_duration_type = @$request->plan_duration_type?:0;
        $subscription_plan->extra_each_normal_post_price = @$request->extra_each_normal_post_price?:0;
        $subscription_plan->is_featured = @$request->is_featured ?: 0;
        $subscription_plan->no_of_default_featured_post = @$request->no_of_default_featured_post ?: 0;
        $subscription_plan->save();
        return redirect()->route('subscription_plans')->with('success', __('backend.saveDone'));
    }


    public function updateAll(Request $request)
    {
        if ($request->row_ids != "") {
            if ($request->action == "activate") {
                $active  = SubscriptionPlan::wherein('id', $request->ids);
                $active->update(['status' => 1]);
            } elseif ($request->action == "block") {
                SubscriptionPlan::wherein('id', $request->ids)
                    ->update(['status' => 0]);
            } elseif ($request->action == "delete") {
                SubscriptionPlan::wherein('parent_id', $request->ids)->delete();
                SubscriptionPlan::wherein('id', $request->ids)->delete();
            }
        }
        return redirect()->route('subscription_plans')->with('doneMessage', __('backend.saveDone'));
    }


    public function langedit(Request $request, $parentId, $langId)
    {
        $subscription_plan = SubscriptionPlan::where('parent_id', '=', $parentId)->where('language_id', '=', $langId);
        $issubscriptionplanExists = $subscription_plan->count();
        $subscription_plan_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = SubscriptionPlan::find($parentId);
        $subscriptionplanData = [];
        if ($issubscriptionplanExists > 0) {
            $subscriptionplanData = $subscription_plan->first();
        }

        $title = (isset($subscriptionplanData->type) ? __('backend.edit_lang_subscription_plan') : __('backend.add_lang_subscription_plan'));
        return view('dashboard.subscription_plans.addLang', compact('languageData', 'parentData', 'subscriptionplanData', 'title'));
    }

    public function storeLang(Request $request)
    {
        $this->validateRequest($request,'', $request->subscription_plan_parent_id, $request->subscription_plan_id, $request->subscription_plan_language_id);
        $parentData = SubscriptionPlan::find($request->subscription_plan_parent_id);
        $newUser = SubscriptionPlan::updateOrCreate([
            'language_id' =>  $request->subscription_plan_language_id,
            'parent_id' =>  $parentData->id,
        ], [
            'plan_name' => $request->plan_name,
            'plan_description' => $request->plan_description,
        ]);
        return redirect()->route('subscription_plans')->with('success', __('backend.saveDone'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SubscriptionPlan::where('parent_id', '=', $id)->delete(); // delete child data
        SubscriptionPlan::where('id', '=', $id)->delete();
        return redirect()->route('subscription_plans')->with('success', __('backend.deleteDone'));
    }


    // *******************************************************************************************
    // VALIDATIONS
    public function validateRequest($request,$id = "", $parentId = "", $childId = "", $langId = "")
    {
        $validation_messages = [
            'plan_name.required' => __('backend.subscription_plan_name') . ' is required.',
            'plan_name.unique' => __('backend.subscription_plan_name') . ' is already taken.',
            'plan_description.required' => __('backend.subscription_plan_description') . ' is required.',
            'plan_duration.required' => __('backend.subscription_plan_duration') . ' is required.',
            'no_of_plan_post.required' => __('backend.number_of_ads') . ' is required.',
            'plan_type.required' => __('backend.subscription_plan_type') . ' is required.',
            'plan_price.required' => __('backend.subscription_plan_price') . ' is required.',
            'plan_price.required_if' => __('backend.subscription_plan_price') . ' is required.',
            'plan_duration_value.required' => __('backend.plan_duration_value'). ' is required.',
            'plan_duration_type.required' => __('backend.plan_duration_type'). ' is required.',
            'no_of_default_featured_post.required' => __('backend.no_of_default_featured_post'). ' is required.',
            'extra_each_normal_post_price.required' => __('backend.extra_each_normal_post_price'). ' is required.',
            'plan_type.required' => __('backend.plan_type'). ' is required.',
        ];

        if ($id != "" && empty($parentId && $langId)) {
            $validations = [
                'plan_name' => 'required|unique:App\Models\SubscriptionPlan,plan_name,' . $id . ',id',
                'plan_description' => 'required',
                // 'plan_duration' => 'required',
                'no_of_plan_post' => 'required',
                'plan_duration_value' => 'required',
                'plan_duration_type' => 'required',
                'no_of_default_featured_post' => 'required',
                'extra_each_normal_post_price' => 'required',
                'plan_type' => 'required',
            ];

            if(!request('is_free')){
                $validations['plan_price'] = 'required';
            }

            $validateData = request()->validate(
                $validations,
                $validation_messages
            );

        } else if ($id == "" && !empty($childId)) {
            $validateData = request()->validate(
                [
                    'plan_name' => 'required|unique:App\Models\SubscriptionPlan,plan_name,' . $childId . ',id',
                    'plan_description' => 'required',
                ],
                $validation_messages
            );
        } else if ($parentId != '' && $langId != "") {
            $validateData = request()->validate(
                [
                    'plan_name' => 'required|unique:App\Models\SubscriptionPlan,plan_name,'.$parentId.',parent_id' ,
                    'plan_description' => 'required',
                ],
                $validation_messages
            );
        } else {

            // dd(request('is_free'));sss

            $validations = [
                'plan_name' => 'required|unique:App\Models\SubscriptionPlan,plan_name',
                'plan_description' => 'required',
                // 'plan_duration' => 'required',
                'no_of_plan_post' => 'required',
                'plan_duration_value' => 'required',
                'plan_duration_type' => 'required',
                'no_of_default_featured_post' => 'required',
                'extra_each_normal_post_price' => 'required',
                'plan_type' => 'required',
                // 'plan_price' => 'required_if:is_free,!==,1',
            ];
            
            if(!request('is_free')){
                $validations['plan_price'] = 'required';
            }

            $validateData = request()->validate(
                $validations,
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
            $sort = 'plan_name';
        } elseif ($columnIndex == 2) {
            $sort = 'duration_type';
        }
        elseif ($columnIndex == 3) {
            $sort = 'number_of_ads';
        }
        elseif ($columnIndex == 4) {
            $sort = 'subscription_type';
        }
        elseif ($columnIndex == 5) {
            $sort = 'subscription_price';
        }
        elseif ($columnIndex == 6) {
            $sort = 'updated_at';
        }
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = DB::table('subscription_plans')
                    ->select('subscription_plans.*')
                    // ->leftjoin('user_subscriptions','user_subscriptions.plan_id','=','subscription_plans.id')
                    ->where('subscription_plans.parent_id','=',0);
                    // ->where('subscription_plans.status','!=',2);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('plan_name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('no_of_plan_post', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('updated_at', 'LIKE', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $showPage = route('subscription_plan.show', ['id' => $data->id]);
            $editPage = route('subscription_plan.edit', ['id' => $data->id]);
            $delete   = route('subscription_plan.delete', ['id' => $data->id]);

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="' . $delete . '" title="Delete" data-name="' . $data->plan_name . '"> <span class="fa fa-trash text-danger"></span> </a> ';

            $language = Language::where('id', ">", '1')->get();
            foreach ($language as $k => $lang) {
                $langEdit =  route('subscription_plan.editlang', [$data->id, $lang->id]);
                $options .= '<a class="btn" href="' . $langEdit . '" title="' . $lang->title . '">' . strtoupper($lang->code) . '</a>';
            }

            $plan_type = @$data->plan_type!="" ? Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'.$data->plan_type.'.label_key')) : '-';

            $plan_duration = "-";
            if($data->plan_duration_value && $data->plan_duration_type){
                $plan_duration = Helper::getValidTillDate( date('Y-m-d H:i:s'), $data->plan_duration_value ,$data->plan_duration_type);
            }

            $status = "";
            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }


            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "plan_name" => @$data->plan_name ? : '-',
                "plan_duration" => ($plan_duration != '-') ? $plan_duration['value'] . " " . $plan_duration['label_value'] : "-",
                "number_of_ads" => @$data->no_of_plan_post ? : '0',
                "plan_type" => $plan_type,
                "plan_price" => ((@$data->plan_price>0) ? $data->plan_price . ' KD' : '-'),
                "updated_at" => Carbon::parse($data->updated_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A'),
                "status" => $status,
                "options" => @$options ?? '',
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
