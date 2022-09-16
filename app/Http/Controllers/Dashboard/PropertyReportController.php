<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Setting;
use App\Models\WebmasterSetting;
use App\Models\Area;
use App\Models\Governorate;
use App\Models\Country;
use App\Models\Language;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Yajra\Datatables\Datatables;
use \Carbon\Carbon;

class PropertyReportController extends Controller
{
    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $propertyTypes = \App\Models\PropertyType::where('parent_id', '=', 0)->get();
        return view("dashboard.report.property_report",compact('propertyTypes'));
    }


    public function export_property(Request $request)
    {
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');

        $properties = DB::table('properties')
            ->select('properties.*', 'properties.id as PropertyId', 'users.full_name as agent_name', 'users.country_code', 'users.mobile_number', 'property_types.type as property_type_text', 'area.name as property_area_name', 'property_conditions.condition_text as propertyConditionText', 'property_completion_statuses.completion_type as property_completion_status')
            // ,\DB::raw("GROUP_CONCAT(amenity.amenity_name) as amenities") 
            ->leftjoin('users', 'properties.agent_id', '=', 'users.id')
            ->leftjoin('property_types', 'properties.property_type', '=', 'property_types.id')
            // ->leftjoin("amenity",\DB::raw("FIND_IN_SET(amenity.id,properties.property_amenities_ids)"),">",\DB::raw("'0'"))
            ->leftjoin('area', 'properties.area_id', '=', 'area.id')
            ->leftjoin('property_conditions', 'properties.condition_type_id', '=', 'property_conditions.id')
            ->leftjoin('property_completion_statuses', 'properties.completion_status_id', '=', 'property_conditions.id')
            ->leftjoin('property_for','properties.property_for','=', 'property_for.id')
            ->where('properties.status', '!=', 2);
        
        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $properties->where('properties.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $properties->where('properties.created_at', '<=', $min_date . ' 23:59:59');
        }

        if ($request->get('properties.property_type')) {
            $properties->where('properties.property_type', '=', $request->get('properties.property_type'));
        }

        if ($request->get('property_for') != "") {
            $properties->where('properties.property_for', '=', $request->get('property_for'));
        }
        
        $properties = $properties->get();

        $filename = 'property_report_' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No.',/* 'Property ID', */ 'Property Name', 'Agent Name', 'Agent Contact', 'Property Type', 'Property For', 'Property Address', 'Area (sqft)', 'Price', 'Date Listed', 'Expire on','Status'));
        // 11

        $i = 1;
        foreach ($properties as $key => $data) {
            // $property_id =  isset($data->property_id)?:'';
            $property_name =  isset($data->property_name)?:'';
            $agent_name = isset($data->agent_name)?:"";
            $agent_contact = @urldecode($data->country_code) . ' ' . @$data->mobile_number;
            $property_type =  @$data->property_type_text ?: "-";
            
            $currency = "KD";
            if ($data->area_id) {
                $area = Area::find($data->area_id);
                $country = Country::find($area->country_id);
                $currency = $country->currency_code;
            }

            $property_for = "-";
            if (@$data->property_for) {
                $property_for = \Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $data->property_for . '.label_key'));
            }

            $property_address   = @$data->property_address?:"";
            $property_sqft_area = @$data->property_sqft_area ?: "-";
            $property_sqft_area = @$data->property_sqft_area ?: "-";
            $property_price     = @$data->base_price ? Helper::getPropertyPriceById($data->id) . " " . $currency : "-";
            $date_listed        = @$data->created_at ? Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";
            $subscription_expire_date = @$data->property_subscription_enddate ? Carbon::parse($data->property_subscription_enddate)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-";

            $status =  'Active';
            if (isset($data->status) && ($data->status == 0)) {
                $status = 'Deactive';
            }

            fputcsv($file, array( $i, /* $property_id, */ $property_name, $agent_name, $agent_contact, $property_type, $property_for, $property_address, $property_sqft_area, $property_price, $date_listed, $subscription_expire_date, $status));

            $i++;
        }
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
        } elseif ($columnIndex == 3) {
            $sort = 'users.mobile_number';
        } elseif ($columnIndex == 4) {
            $sort = 'property_types.type';
        } elseif ($columnIndex == 5) {
            $sort = 'property_for.for_text';
        } elseif ($columnIndex == 6) {
            $sort = 'properties.property_address';
        } elseif ($columnIndex == 7) {
            $sort = 'properties.property_sqft_area';
        } elseif ($columnIndex == 8) {
            $sort = 'properties.base_price';
        } elseif ($columnIndex == 9) {
            $sort = 'properties.created_at';
        } elseif ($columnIndex == 10) {
            $sort = 'properties.property_subscription_enddate';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = DB::table('properties')
            ->select('properties.*', 'properties.id as PropertyId', 'users.full_name as agent_name', 'users.country_code', 'users.mobile_number', 'property_types.type as property_type_text', 'area.name as property_area_name', 'property_conditions.condition_text as propertyConditionText', 'property_completion_statuses.completion_type as property_completion_status')
            // ,\DB::raw("GROUP_CONCAT(amenity.amenity_name) as amenities") 
            ->leftjoin('users', 'properties.agent_id', '=', 'users.id')
            ->leftjoin('property_types', 'properties.property_type', '=', 'property_types.id')
            // ->leftjoin("amenity",\DB::raw("FIND_IN_SET(amenity.id,properties.property_amenities_ids)"),">",\DB::raw("'0'"))
            ->leftjoin('area', 'properties.area_id', '=', 'area.id')
            ->leftjoin('property_conditions', 'properties.condition_type_id', '=', 'property_conditions.id')
            ->leftjoin('property_completion_statuses', 'properties.completion_status_id', '=', 'property_conditions.id')
            ->leftjoin('property_for','properties.property_for','=', 'property_for.id')
            ->where('properties.status', '!=', 2);

        $totalRecords = $totalAr->get()->count();

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('properties.property_name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('users.full_name', 'LIKE', '%' . urlencode($searchValue) . '%')
                    // ->orWhere('users.mobile_number', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere(DB::raw("CONCAT(users.country_code, '+', users.mobile_number)"), 'LIKE', '%' . urlencode($searchValue) . '%')
                    ->orWhere('property_types.type', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('property_for.for_text', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_address', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_sqft_area', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.base_price', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.created_at', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('properties.property_subscription_enddate', 'LIKE', '%' . $searchValue . '%');
            });
        }

        if ($start_date) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('properties.created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('properties.created_at', '<=', $min_date . ' 23:59:59');
        }

        if ($request->get('properties.property_type')) {
            $totalAr->where('properties.property_type', '=', $request->get('properties.property_type'));
        }

        if ($request->get('property_for') != "") {
            $totalAr->where('properties.property_for', '=', $request->get('property_for'));
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
            // $editPage =  route('property.edit', ['id' => $data->slug]);
            // $delete   =  route('property.delete', ['id' => $data->slug]);

            $status = "";
            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $options = "";

            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $showPage . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="' . $editPage . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            // $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="' . $delete . '" title="Delete" data-name="' . $data->property_name . '"> <i class="fa-solid fa-trash text-danger"></i> </a> ';

            $currency = "KD";
            if ($data->area_id) {
                $area = Area::find($data->area_id);
                $country = Country::find($area->country_id);
                $currency = $country->currency_code;
            }

            $property_for = "-";
            if (@$data->property_for) {
                $property_for = \Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $data->property_for . '.label_key'));
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "property_name" => @$data->property_name ?: "",
                "agent_name" => @urldecode($data->agent_name) ?: "",
                "agent_contact" => @urldecode($data->country_code) . ' ' . @$data->mobile_number,
                "property_type" => @$data->property_type_text ?: "-",
                "property_for" => $property_for,
                "property_address" => @$data->property_address ? \Str::limit($data->property_address, 20, '...') : "-",
                "property_sqft_area" => @$data->property_sqft_area ?: "-",
                // "property_price" => @$data->base_price ? $data->base_price . " " . $currency : "-",
                "property_price" => @$data->base_price ?  Helper::getPropertyPriceById($data->id) . " " . $currency : "-",
                "date_listed" => @$data->created_at ? Carbon::parse($data->created_at)->format(env('DATE_FORMAT', 'Y-m-d') . ' H:i A') : "-",
                "subscription_expire_date" => @$data->property_subscription_enddate ? Carbon::parse($data->property_subscription_enddate)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
