<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// use App\Models\FavouriteProperty;
use App\Models\Area;
use App\Models\Label;
use App\Models\MainUsers as MainUser;
use App\Models\Property;

use Helper;
use DB;

class AreaController extends Controller
{
    //
    protected $spotlight_per_page;
    protected $indicator_image_url;

    public function __construct()
    {
        $this->spotlight_per_page = 10;
        $this->indicator_image_url = "assets/indicators/";
    }

    // spotlight list || area list
    public function spotlight_list(Request $request) {
        $language_id = $request->input('language_id',1);
        $user_id = $request->input('user_id');

        if ($user_id) {
            $user = MainUser::where('id', $user_id)->where('is_otp_varified', '=', 1)->first();

            if ($user) {

                if ($user->status == 0) {
                    $result['code']     = (string) -3;
                    $result['message']  = 'inactive_account';
                    $result['result']   = [];

                    $mainResult[] = $result;
                    return response()->json($mainResult);
                }

                if ($user->status == 2) {
                    $result['code']     = (string) -2;
                    $result['message']  = 'account_deleted_contact_to_admin';
                    $result['result']   = [];

                    $mainResult[] = $result;
                    return response()->json($mainResult);
                }

                // if($user->remember_token != $token){
                // 	$result['code']     = (string) -7;
                // 	$result['message']  = 'invalid_token';
                // 	$result['result']   = [];

                // 	$mainResult[]=$result;
                // 	return response()->json($mainResult); 
                // }
            }else{
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $page = isset($request->page_no) ?  $request->page_no : 1;

        $spotlight = Area::with(['childdata' => function ($child) use ($language_id) {
                $child->where('language_id', '=', $language_id);
            }])
            ->whereHas('properties', function($property) {
                $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
            })
            ->where('parent_id', '=', 0)
            ->where('status', 1);
        
        $total_spotlight = $spotlight->get()->count();
        $spotlight = $spotlight->orderby('id','desc')
                        ->paginate($this->spotlight_per_page, ['*'], 'page', $page);

        $spotlight_arr = [];
        foreach ($spotlight as $key => $value) {
            $area_arr = [];
            $area_arr['id'] = (string) $value->id;
            $area_name = $value->name;
            if ($language_id > 1) {
                $area_name = @$value->childdata[0]->name ?: $value->name;
            }
            $area_arr['area_name'] = (string) urldecode($area_name);
            $image_url = "";
            if($value->image){
                $image_url = asset('assets/dashboard/images/areas/'. $value->image);
            }
            $area_arr['image_url'] = $image_url;
            $spotlight_arr[] = $area_arr;
        }
        $response['spotlight'] = $spotlight_arr;

        $result['code']           = (string) 1;
        $result['message']        = 'success';
        $result['total_records']  = (int) $total_spotlight;
        $result['per_page']       = (int) $this->spotlight_per_page;
        $result['result'][]       = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
    }

    // Map listing for area
    public function property_area_map(Request $request) {
        
        $language_id = $request->input('language_id');
        $user_id   = $request->input('user_id');
        $south_lat = $request->input('south_lat');
        $north_lat = $request->input('north_lat');
        $west_long = $request->input('west_long');
        $east_long = $request->input('east_long');

        if ($user_id) {
            $user = MainUser::where('id', $user_id)->where('is_otp_varified', '=', 1)->first();

            if ($user) {

                if ($user->status == 0) {
                    $result['code']     = (string) -3;
                    $result['message']  = 'inactive_account';
                    $result['result']   = [];

                    $mainResult[] = $result;
                    return response()->json($mainResult);
                }

                if ($user->status == 2) {
                    $result['code']     = (string) -2;
                    $result['message']  = 'account_deleted_contact_to_admin';
                    $result['result']   = [];

                    $mainResult[] = $result;
                    return response()->json($mainResult);
                }

                // if($user->remember_token != $token){
                // 	$result['code']     = (string) -7;
                // 	$result['message']  = 'invalid_token';
                // 	$result['result']   = [];

                // 	$mainResult[]=$result;
                // 	return response()->json($mainResult); 
                // }
            }else{
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $areas = Area::with(['childdata' => function ($childData) use($language_id) {
                        $childData->where('language_id', '=', $language_id);
                    }])
                    ->where('parent_id', '=', 0)
                    ->where('status', '=', 1)
                    ->whereHas('properties', function ($property) {
                        $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date("Y-m-d H:i:s"));
                    });
                    
        if($south_lat && $north_lat && $east_long && $west_long) {
            $areas = $areas->whereRaw(DB::raw("(CASE WHEN '".$south_lat."' < '".$north_lat."'
                        THEN latitude BETWEEN '".$south_lat."' AND '".$north_lat."'
                        ELSE latitude BETWEEN '".$north_lat."' AND '".$south_lat."' 
                        END) 
                        AND
                        (CASE WHEN '".$east_long."' < '".$west_long."'
                            THEN longitude BETWEEN '".$east_long."' AND '".$west_long."'
                            ELSE longitude BETWEEN '".$west_long."' AND '".$east_long."'
                        END)")
                    );
        }
        
        $areas = $areas->get();

        $area_list = [];
        foreach($areas as $key => $value) {
            $areaDetail = [];
            
            $area_name = $value->name;
            if($language_id != 1){
                $area_name = @$value->childdata[0]->name ?: $value->name;
            }
            
            $indicator_image = "";
            $area_value = "";
            if($value->updated_range > $value->default_range){
                // green
                $indicator_image = asset($this->indicator_image_url . 'Grren_nav.png');
                $area_value = 1;
            }
            else if($value->updated_range < $value->default_range){
                // red
                $indicator_image = asset($this->indicator_image_url . 'Red_Nav.png');
                $area_value = 2;
            }
            else if($value->updated_range == $value->default_range){
                // yellow
                $indicator_image = asset($this->indicator_image_url . 'Yellow_nav.png');
                $area_value = 0;
            }

            $areaDetail['id'] = (string) $value->id;
            $areaDetail['area_name'] = urldecode($area_name);
            $areaDetail['latitude'] = (string) $value->latitude;
            $areaDetail['longitude'] = (string) $value->longitude;
            $areaDetail['image_url'] = $indicator_image;
            $areaDetail['area_value'] = (string) $area_value;

            $area_list[] = $areaDetail;
        }

        $result['code']       = (string) 1;
        $result['message']    = 'success';
        $result['result'][]   = array("property_area" => $area_list);

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


}
