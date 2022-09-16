<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

// use App\Models\FavouriteProperty;
use App\Models\Area;
use App\Models\Label;
use App\Models\MainUsers as MainUser;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\BathroomTypes;
use App\Models\BedroomTypes;
use App\Models\FavouriteProperty;
use App\Models\PropertyAmenities;
use App\Models\PropertyCompletionStatus;
use App\Models\PropertyCondition;
use App\Models\PropertyImages;
use App\Models\PropertyFor;
use App\Models\SubscriptionPlan;
use App\Models\UserFavouriteProperty;
use App\Models\UserSubscription;
use App\Models\Transaction;
use App\Models\Setting;

use Mail;
use File;
use Storage;
use Str;
use Helper;
use DB;
use Carbon\Carbon;

class PropertyController extends Controller
{
    protected $property_per_page;
    protected $sort_property_by;
    protected $property_details_similar_product_limit;
    //
    // protected function getMaxMinPrice(){
    //     $properties = Property::max('base_price')->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
    //                     ->where('status', '=', 1);

    //     return array("max_price" => $properties->base_price);
    // }

    public function __construct()
    {
        $this->property_per_page = 10;
        $this->sort_property_by = array(
            1 => array("field" => "id", "sort_type" => "desc"),
            2 => array("field" => "price_area_wise", "sort_type" => "desc"),
            3 => array("field" => "price_area_wise", "sort_type" => "asc"),
        );
        $this->property_details_similar_product_limit = 10;
    }

    protected function getAreaSqftList()
    {

        $max_area_sqft = Property::where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->whereHas('agentDetails', function ($agent) {
                $agent->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->max('property_sqft_area');

        $max_area_sqft = @$max_area_sqft ?: 0;
        return Helper::getRoundedThousand($max_area_sqft);
    }

    private function getPropertyMaxPrice()
    {
        $max_price = Property::where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->whereHas('agentDetails', function ($agent) {
                $agent->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->max('price_area_wise');
        return @$max_price ?: 0;
    }

    private function getPropertyMinPrice()
    {
        // $min_price = Property::where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
        //     ->whereHas('agentDetails', function($agent) {
        //         $agent->where('status', '=', 1);
        //     })
        //     ->where('status', '=', 1)
        //     ->min('base_price');
        // return @$min_price ?: 0;
        return 1;
    }


    public function get_filters(Request $request)
    {

        // if($request->input('check_me') == 1){
        //     $result = $this->getPropertyMaxPrice();
        //     dd($result);
        //     $res = Helper::getRoundedThousand($result);
        //     dd($res);
        // }

        $language_id = $request->input('language_id', 1);
        $user_id = $request->input('user_id');

        $filter_data = [];
        $property_for = [];

        foreach (config('constants.PROPERTY_FOR') as $key => $value) {
            $property_for_arr = [];
            $property_for_arr['type']       = Helper::getLabelValueByKey($value['label_key'], $language_id);
            $property_for_arr['value']      = (string) $value['value'];
            $property_for_arr['label_key']  = (string) $value['label_key'];

            $property_for[] = $property_for_arr;
        }

        $filter_data['property_for'] = $property_for;

        // Property types list
        $property_types = PropertyType::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $property_type_list = [];
        foreach ($property_types as $key => $value) {
            $property_type_arr = [];
            $property_type_arr['id'] = (string) $value->id;

            $type = $value->type;
            if ($language_id > 1) {
                $type = @$value->childdata[0]->type ?: $value->type;
            }

            $property_type_arr['type'] = (string) $type;

            $property_type_list[] = $property_type_arr;
        }
        $filter_data['property_types'] = $property_type_list;

        // bedroom type list
        $bedroom_types = BedroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $bedroom_type_list = [];
        foreach ($bedroom_types as $key => $value) {
            $bedroom_type_arr = [];
            $bedroom_type_arr['id'] = (string) $value->id;

            $type = $value->type;
            if ($language_id > 1) {
                $type = @$value->childdata[0]->type ?: $value->type;
            }

            $bedroom_type_arr['type'] = (string) $type;

            $bedroom_type_list[] = $bedroom_type_arr;
        }
        $filter_data['bedroom_types'] = $bedroom_type_list;

        // Bathroom type list
        $bathroom_types = BathroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $bathroom_type_list = [];
        foreach ($bathroom_types as $key => $value) {
            $bathroom_type_arr = [];
            $bathroom_type_arr['id'] = (string) $value->id;
            $type = $value->type;
            if ($language_id > 1) {
                $type = @$value->childdata[0]->type ?: $value->type;
            }
            $bathroom_type_arr['type'] = (string) $type;
            $bathroom_type_list[] = $bathroom_type_arr;
        }
        $filter_data['bathroom_types'] = $bathroom_type_list;

        $filter_data['bedroom_number'] = (string) Helper::getMaxBedroomNumbers();
        $filter_data['bathroom_number'] = (string) Helper::getMaxBathroomNumbers();
        $filter_data['toilet_number'] = (string) Helper::getMaxToiletNumbers();

        // property conditions types list
        $condition_types = PropertyCondition::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $condition_type_list = [];
        foreach ($condition_types as $key => $value) {
            $condition_type_arr = [];
            $condition_type_arr['id'] = (string) $value->id;
            $type = $value->condition_text;
            if ($language_id > 1) {
                $type = @$value->childdata[0]->condition_text ?: $value->condition_text;
            }
            $condition_type_arr['type'] = (string) $type;
            $condition_type_list[] = $condition_type_arr;
        }
        $filter_data['condition_types'] = $condition_type_list;

        // Property completion statuses list
        $completion_status = PropertyCompletionStatus::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $completion_status_list = [];
        foreach ($completion_status as $key => $value) {
            $completion_status_arr = [];
            $completion_status_arr['id'] = (string) $value->id;
            $type = $value->completion_type;
            if ($language_id > 1) {
                $type = @$value->childdata[0]->completion_type ?: $value->completion_type;
            }
            $completion_status_arr['status'] = (string) $type;
            $completion_status_list[] = $completion_status_arr;
        }
        $filter_data['completion_status'] = $completion_status_list;

        // Area list
        $areas = Area::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $area_list = [];
        foreach ($areas as $key => $value) {
            $area_arr = [];
            $area_arr['id'] = (string) $value->id;
            $area_name = $value->name;
            if ($language_id > 1) {
                $area_name = @$value->childdata[0]->name ?: $value->name;
            }
            $area_arr['area_name'] = (string) urldecode($area_name);
            $area_list[] = $area_arr;
        }
        $filter_data['area_list'] = $area_list;
        $filter_data['area_sqft_list'] = $this->getAreaSqftList();

        $max_property_price = $this->getPropertyMaxPrice();
        $min_property_price = $this->getPropertyMinPrice();

        $filter_data['max_price'] = (string) number_format(Helper::toFloat(Helper::getPropertyPriceByPrice(Helper::toFloat($max_property_price))), 3, '.', '');
        $filter_data['min_price'] = (string) Helper::getPropertyPriceByPrice(Helper::toFloat($min_property_price));

        $result['code']     = (string) 1;
        $result['message']  = 'success';
        $result['result'][] = $filter_data;

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


    public function home(Request $request)
    {
        // params
        $user_id = $request->input('user_id');
        $language_id = $request->input('language_id', 1);
        $token = $request->input('token');

        // for filter
        // $latitude = $request->input('latitude');
        // $longitude = $request->input('longitude');
        // $property_for = $request->input('property_for');
        // $max_price = $request->input('max_price');
        // $min_price = $request->input('min_price');
        // $property_type_id = $request->input('property_type_id');
        // $bedroom_type_id = $request->input('bedroom_type_id');
        // $bedroom_number = $request->input('bedroom_number');
        // $bathroom_type_id = $request->input('bathroom_type_id');
        // $bathroom_number = $request->input('bathroom_number');
        // $condition_type_id = $request->input('condition_type_id');
        // $completion_status_id = $request->input('completion_status_id');
        // $area_id = $request->input('area_id');
        // $max_area_sqft = $request->input('max_area_sqft');

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
            } else {
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $applied_filters = [];
        // $applied_filters['latitude'] = (string) @$request->input('latitude') ?: "";
        // $applied_filters['longitude'] = (string) @$request->input('longitude') ?: "";

        $applied_filters['property_for_id'] = (string) ($request->input('property_for_id') != "" && $request->input('property_for_id') != null) ? $request->input('property_for_id') : "";
        $applied_filters['max_price'] = (string) @$request->input('max_price') ?: "";
        $applied_filters['min_price'] = (string) @$request->input('min_price') ?: "";
        $applied_filters['property_type_id'] = (string) @$request->input('property_type_id') ?: "";
        $applied_filters['bedroom_type_id'] = (string) @$request->input('bedroom_type_id') ?: "";
        $applied_filters['bedroom_number'] = (string) @$request->input('bedroom_number') ?: "";
        $applied_filters['bathroom_type_id'] = (string) @$request->input('bathroom_type_id') ?: "";
        $applied_filters['bathroom_number'] = (string) @$request->input('bathroom_number') ?: "";
        $applied_filters['toilet_number'] = (string) @$request->input('toilet_number') ?: "";
        $applied_filters['condition_type_id'] = (string) @$request->input('condition_type_id') ?: "";
        $applied_filters['completion_status_id'] = (string) @$request->input('completion_status_id') ?: "";
        $applied_filters['area_id'] = (string) @$request->input('area_id') ?: "";
        $applied_filters['max_area_sqft'] = (string) @$request->input('max_area_sqft') ?: "";

        $response['applied_filters'] = $applied_filters;

        $properties = Property::with([
            'areaDetails' => function ($area) {
                $area->where('status', '=', 1)->where('parent_id', '=', 0);
            },
            'areaDetails.country' => function ($country) {
                $country->where('status', '=', 1)->where('parent_id', '=', 0);
            },
            'areaDetails.childdata' => function ($areaChild) use ($language_id) {
                $areaChild->where('language_id', '=', $language_id);
            },
            'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
                $countryChild->where('language_id', '=', $language_id);
            },
        ])
            // ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            //     $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
            // })
            ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                $areaDetailsQuery->where('status', '=', 1);
            })
            ->whereHas('agentDetails', function ($agentQuery) {
                $agentQuery->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->where('is_featured', '=', 1)
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

        if ($request->input('property_for_id') != "" && $request->input('property_for_id') != null) {
            $properties->where('property_for', '=', $request->input('property_for_id'));
        }

        if ($request->input('property_type_id')) {
            $properties->where('property_type', '=', $request->input('property_type_id'));
        }

        if ($request->input('bedroom_type_id')) {
            $properties->where('bedroom_type', '=', $request->input('bedroom_type_id'));
        }

        if ($request->input('bathroom_type_id')) {
            $properties->where('bathroom_type', '=', $request->input('bathroom_type_id'));
        }

        if ($request->input('bedroom_number')) {
            $properties->where('total_bedrooms', '=', $request->input('bedroom_number'));
        }

        if ($request->input('bathroom_number')) {
            $properties->where('total_bathrooms', '=', $request->input('bathroom_number'));
        }

        if ($request->input('toilet_number')) {
            $properties->where('total_toilets', '=', $request->input('toilet_number'));
        }

        if ($request->input('condition_type_id')) {
            $properties->where('condition_type_id', '=', $request->input('condition_type_id'));
        }

        if ($request->input('completion_status_id')) {
            $properties->where('completion_status_id', '=', $request->input('completion_status_id'));
        }

        if ($request->input('completion_status_id')) {
            $properties->where('completion_status_id', '=', $request->input('completion_status_id'));
        }

        if ($request->input('area_id')) {
            $properties->where('area_id', '=', $request->input('area_id'));
        }

        if ($request->input('max_area_sqft')) {
            $properties->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
        }

        if ($request->input('max_price')) {
            $properties->where('price_area_wise', '<=', $request->input('max_price'));
        }

        if ($request->input('min_price')) {
            $properties->where('price_area_wise', '>=', $request->input('min_price'));
        }


        $properties = $properties->orderBy('id', 'desc')->paginate(Helper::homeMaxPropertiesListCount());

        // dd($properties->toArray());

        $property_list = [];
        $max_price = 0;
        $min_price = "";

        foreach ($properties as $key => $property) {

            $property_arr = [];
            $property_arr['id']  = (string) $property->id;
            $property_arr['property_id']  = (string) @$property->property_id ?: "";
            $property_arr['title']  = @$property->property_name ?: "";
            $property_arr['property_for']  = @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
            $property_arr['property_for_id']  = !empty($property->property_for) ? (string) $property->property_for : "";

            $area_name = "";
            $country_name = "";
            if ($property->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->areaDetails->name) ?: "";
                    }

                    if (@$property->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $property_arr['short_address']  = $short_address;

            $currency = @$property->areaDetails->country->currency_code ?: "KD";

            $base_price = $property->base_price;
            $property_price = Helper::getPropertyPriceByPrice($base_price, $property->areaDetails->default_range, $property->areaDetails->updated_range) ?: 0;

            // set max price and min price
            $max_price = Helper::tofloat($property_price) > $max_price ? Helper::tofloat($property_price) : $max_price;

            if (!isset($min_price) || $min_price == "") {
                $min_price = Helper::tofloat($property_price);
            }

            $min_price = (Helper::tofloat($property_price) < $min_price) ? Helper::tofloat($property_price) : $min_price;

            $base_price = number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3)) . ' ' . $currency;

            $property_arr['property_price'] = (string) (@$property->price_area_wise ? (number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
            $property_arr['bathroom_count'] = (string) @$property->total_bathrooms ?: "0";
            $property_arr['bedroom_count'] = (string) @$property->total_bedrooms ?: "0";
            $property_arr['toilet_count'] = (string) @$property->total_toilets ?: "0";
            $property_arr['area_sqft'] = (string) @$property->property_sqft_area ?: "0";

            $property_arr['latitude'] = (string) @$property->property_address_latitude ?: "";
            $property_arr['longitude'] = (string) @$property->property_address_longitude ?: "0";
            $property_arr['area_value'] = (string) @$property->areaDetails->updated_range ?: "";

            $property_image = "";
            if ($property->propertyImages->count() > 0) {
                $property_image = asset("storage/property_images/" . $property->id . '/' . $property->propertyImages[0]->property_image);
            }
            $property_arr['image_url'] = (string) $property_image;

            $is_fav = "0";
            if ($user_id) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
            }
            $property_arr['is_fav'] = (string) $is_fav ?: "0";
            $property_arr['is_featured'] = (string) ($property->is_featured ?: 0);

            $property_list[] = $property_arr;
        }

        $response['featured'] = $property_list;
        // $response['filters'] = array("max_price" => (string) $max_price, "min_price" => (string) @$min_price ?: "0");

        // $spotlight = Area::where('lagnauge')

        // Area list
        $spotlight = Area::with([
            'childdata' => function ($child) use ($language_id) {
                $child->where('language_id', '=', $language_id);
            },
            'properties' => function ($properties_a) use ($language_id) {
                $properties_a = $properties_a->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
            }
        ])
            ->whereHas('properties', function ($property) use ($request) {
                $property = $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

                if ($request->input('property_for_id') != "" && $request->input('property_for_id') != null) {
                    $property->where('property_for', '=', $request->input('property_for_id'));
                }

                if ($request->input('property_type_id')) {
                    $property->where('property_type', '=', $request->input('property_type_id'));
                }

                if ($request->input('bedroom_type_id')) {
                    $property->where('bedroom_type', '=', $request->input('bedroom_type_id'));
                }

                if ($request->input('bathroom_type_id')) {
                    $property->where('bathroom_type', '=', $request->input('bathroom_type_id'));
                }

                if ($request->input('bedroom_number')) {
                    $property->where('total_bedrooms', '=', $request->input('bedroom_number'));
                }

                if ($request->input('bathroom_number')) {
                    $property->where('total_bathrooms', '=', $request->input('bathroom_number'));
                }

                if ($request->input('toilet_number')) {
                    $property->where('total_toilets', '=', $request->input('toilet_number'));
                }

                if ($request->input('condition_type_id')) {
                    $property->where('condition_type_id', '=', $request->input('condition_type_id'));
                }

                if ($request->input('completion_status_id')) {
                    $property->where('completion_status_id', '=', $request->input('completion_status_id'));
                }

                if ($request->input('area_id')) {
                    $property->where('area_id', '=', $request->input('area_id'));
                }

                if ($request->input('max_area_sqft')) {
                    $property->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
                }

                if ($request->input('max_price')) {
                    $property->where('price_area_wise', '<=', $request->input('max_price'));
                }

                if ($request->input('min_price')) {
                    $property->where('price_area_wise', '>=', $request->input('min_price'));
                }
            })
            ->where('parent_id', '=', 0)
            ->orderBy('name')
            ->skip(0)
            ->take(Helper::homeMaxAreaListCount())
            ->get();
        // ->paginate(Helper::homeMaxAreaListCount());

        // if($request->input('check_me') == 1){
        //     dd($spotlight->toArray());
        // }

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
            if ($value->image) {
                $image_url = asset('assets/dashboard/images/areas/' . $value->image);
            }
            $area_arr['image_url'] = $image_url;
            $spotlight_arr[] = $area_arr;
        }
        $response['spotlight'] = $spotlight_arr;

        $result['code']     = (string) 1;
        $result['message']  = "success";
        $result['result'][] = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


    /* 
    |======================================
    || Property listing with filter
    |=================================
    */

    public function property_list(Request $request)
    {

        // params
        $user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
        $token = $request->input('token');
        $page_no = $request->input('page_no');
        $agent_id = $request->input('agent_id');
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');
        $is_featured = $request->input('is_featured');

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
            } else {
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $applied_filters = [];
        // $applied_filters['latitude'] = (string) @$request->input('latitude') ?: "";
        // $applied_filters['longitude'] = (string) @$request->input('longitude') ?: "";

        $applied_filters['property_for_id'] = (string) (($request->input('property_for_id') != "" && $request->input('property_for_id') != null) ? $request->input('property_for_id') : "");
        $applied_filters['max_price'] = (string) @$request->input('max_price') ?: "";
        $applied_filters['min_price'] = (string) @$request->input('min_price') ?: "";
        $applied_filters['property_type_id'] = (string) @$request->input('property_type_id') ?: "";
        $applied_filters['bedroom_type_id'] = (string) @$request->input('bedroom_type_id') ?: "";
        $applied_filters['bedroom_number'] = (string) @$request->input('bedroom_number') ?: "";
        $applied_filters['bathroom_type_id'] = (string) @$request->input('bathroom_type_id') ?: "";
        $applied_filters['bathroom_number'] = (string) @$request->input('bathroom_number') ?: "";
        $applied_filters['toilet_number'] = (string) @$request->input('toilet_number') ?: "";
        $applied_filters['condition_type_id'] = (string) @$request->input('condition_type_id') ?: "";
        $applied_filters['completion_status_id'] = (string) @$request->input('completion_status_id') ?: "";
        $applied_filters['area_id'] = (string) @$request->input('area_id') ?: "";
        $applied_filters['max_area_sqft'] = (string) @$request->input('max_area_sqft') ?: "";

        $response['applied_filters'] = $applied_filters; // set to response array
        $response['sort_by'] = (string) @$request->input('sort_by') ?: "";

        $sort_by = @$request->input('sort_by') ?: 1;
        $sorting = $this->sort_property_by[$sort_by];

        $properties = Property::with([
            'areaDetails' => function ($area) {
                $area->where('status', '=', 1)->where('parent_id', '=', 0);
            }, 'areaDetails.country' => function ($country) {
                $country->where('status', '=', 1)->where('parent_id', '=', 0);
            }, 'areaDetails.childdata' => function ($areaChild) use ($language_id) {
                $areaChild->where('language_id', '=', $language_id);
            }, 'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
                $countryChild->where('language_id', '=', $language_id);
            },
        ])
            // ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            //     $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
            // })
            ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                $areaDetailsQuery->where('status', '=', 1);
            })
            ->whereHas('agentDetails', function ($agentQuery) {
                $agentQuery->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

        if ($is_featured) {
            $properties = $properties->where('is_featured', '=', 1);
        }

        if ($agent_id) {
            $properties = $properties->where('agent_id', '=', $agent_id);
        }

        if ($lat && $lon) {
            // $properties->select( '*', DB::raw("((ACOS(SIN($lat  PI() / 180)  SIN(property_address_latitude * PI() / 180) + 
            // COS($lat  PI() / 180)  COS(property_address_latitude  PI() / 180)  COS(($lon - property_address_longitude) * 
            // PI() / 180))  180 / PI())  60 * 1.1515) AS distance"));
            $miles = Helper::getMaxDistanceCheck();
            // $miles = $request->input('distance');
            $properties = $properties->whereRaw("property_address_latitude BETWEEN ({$lat} - ({$miles}*0.018)) AND ({$lat} + ({$miles}*0.018)) AND property_address_longitude BETWEEN ({$lon} - ({$miles}*0.018)) AND ({$lon} + ({$miles}*0.018))");
        }

        if ($request->input('property_for_id') != "" || $request->input('property_for_id') != null) {
            $properties = $properties->where('property_for', '=', $request->input('property_for_id'));
        }

        if ($request->input('property_type_id')) {
            $properties = $properties->where('property_type', '=', $request->input('property_type_id'));
        }

        if ($request->input('bedroom_type_id')) {
            $properties = $properties->where('bedroom_type', '=', $request->input('bedroom_type_id'));
        }

        if ($request->input('bathroom_type_id')) {
            $properties = $properties->where('bathroom_type', '=', $request->input('bathroom_type_id'));
        }

        if ($request->input('bedroom_number')) {
            $properties = $properties->where('total_bedrooms', '=', $request->input('bedroom_number'));
        }

        if ($request->input('bathroom_number')) {
            $properties = $properties->where('total_bathrooms', '=', $request->input('bathroom_number'));
        }

        if ($request->input('toilet_number')) {
            $properties = $properties->where('total_toilets', '=', $request->input('toilet_number'));
        }

        if ($request->input('condition_type_id')) {
            $properties = $properties->where('condition_type_id', '=', $request->input('condition_type_id'));
        }

        if ($request->input('completion_status_id')) {
            $properties = $properties->where('completion_status_id', '=', $request->input('completion_status_id'));
        }

        if ($request->input('area_id')) {
            $properties = $properties->where('area_id', '=', $request->input('area_id'));
        }

        if ($request->input('max_area_sqft')) {
            $properties = $properties->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
        }

        if ($request->input('max_price')) {
            $properties = $properties->where('price_area_wise', '<=', $request->input('max_price'));
        }

        if ($request->input('min_price')) {
            $properties = $properties->where('price_area_wise', '>=', $request->input('min_price'));
        }

        // if($lat && $lon) {
        //     $properties = $properties->havingRaw("distance <= '" . Helper::getMaxDistanceCheck() . "'");
        // }

        $total_records = $properties->get()->count();

        $properties = $properties->orderBy($sorting['field'], $sorting['sort_type']);

        if ($page_no == 0) {
            $properties = $properties->get();
        } else {
            $properties = $properties->paginate($this->property_per_page, ['*'], 'page', $page_no);
        }
        // dd($properties->toArray());

        $property_list = [];
        $max_price = 0;
        $min_price = "";

        foreach ($properties as $key => $property) {

            $property_arr = [];
            $property_arr['id']  = (string) $property->id;
            $property_arr['property_id']  = (string) @$property->property_id ?: "";
            $property_arr['title']  = @$property->property_name ?: "";
            $property_arr['property_for']  = @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
            $property_arr['property_for_id']  = (string) (($property->property_for != "" && $property->property_for != null) ? $property->property_for : "");

            $area_name = "";
            $country_name = "";
            if ($property->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->areaDetails->name) ?: "";
                    }

                    if (@$property->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $property_arr['short_address']  = $short_address;

            $currency = @$property->areaDetails->country->currency_code ?: "KD";

            $base_price = $property->base_price;
            $property_price = Helper::getPropertyPriceByPrice($base_price, $property->areaDetails->default_range, $property->areaDetails->updated_range) ?: 0;

            // set max price and min price
            $max_price = Helper::tofloat($property_price) > $max_price ? Helper::tofloat($property_price) : $max_price;

            if (!isset($min_price) || $min_price == "") {
                $min_price = Helper::tofloat($property_price);
            }

            $min_price = (Helper::tofloat($property_price) < $min_price) ? Helper::tofloat($property_price) : $min_price;

            $base_price = number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3)) . ' ' . $currency;

            $property_price_res = (@$property->price_area_wise ? (number_format(Helper::tofloat($property->price_area_wise), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0");
            if ($user_id == $property->agent_id && env('SHOW_AGENT_ORIGINAL_PRICE') == 1) {
                $property_price_res = (@$property->base_price ? (number_format(Helper::tofloat($property->base_price), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0");
            }
            $property_arr['property_price'] = (string)  $property_price_res . ' ' . $currency;
            $property_arr['bathroom_count'] = (string) @$property->total_bathrooms ?: "0";
            $property_arr['bedroom_count'] = (string) @$property->total_bedrooms ?: "0";
            $property_arr['toilet_count'] = (string) @$property->total_toilets ?: "0";
            $property_arr['area_sqft'] = (string) @$property->property_sqft_area ?: "0";

            $property_arr['latitude'] = (string) @$property->property_address_latitude ?: "";
            $property_arr['longitude'] = (string) @$property->property_address_longitude ?: "0";

            $area_value = "";
            if ($property->areaDetails->updated_range > $property->areaDetails->default_range) {
                // green
                $area_value = 1;
            } else if ($property->areaDetails->updated_range < $property->areaDetails->default_range) {
                // red
                $area_value = 2;
            } else if ($property->areaDetails->updated_range == $property->areaDetails->default_range) {
                // yellow
                $area_value = 0;
            }

            $property_arr['area_value'] = (string) $area_value;

            $property_image = "";
            if ($property->propertyImages->count() > 0) {
                $property_image = asset("storage/property_images/" . $property->id . '/' . $property->propertyImages[0]->property_image);
            }
            $property_arr['image_url'] = (string) $property_image;

            $is_fav = 0;
            if ($user_id) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
            }
            $property_arr['is_fav'] = (string) $is_fav;
            $property_arr['is_featured'] = (string) ($property->is_featured ?: 0);

            $property_list[] = $property_arr;
        }

        $response['property_list'] = $property_list;

        $result['code']     = (string) 1;
        $result['message']  = "success";
        $result['total_records']  = (int) $total_records;
        $result['per_page'] = (int) $this->property_per_page;
        $result['result'][] = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


    /* 
    |======================================
    || Property listing with filter in map
    |=================================
    */

    public function property_list_map(Request $request)
    {

        // params
        $user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
        $token = $request->input('token');
        $area_id = $request->input('area_id');

        $south_lat = $request->input('south_lat');
        $north_lat = $request->input('north_lat');
        $west_long = $request->input('west_long');
        $east_long = $request->input('east_long');

        $page_no = $request->input('page_no');

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
            } else {
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $applied_filters = [];
        // $applied_filters['latitude'] = (string) @$request->input('latitude') ?: "";
        // $applied_filters['longitude'] = (string) @$request->input('longitude') ?: "";

        $applied_filters['property_for_id'] = (string) (($request->input('property_for_id') != "" && $request->input('property_for_id') != null) ? $request->input('property_for_id') : "");
        $applied_filters['max_price'] = (string) @$request->input('max_price') ?: "";
        $applied_filters['min_price'] = (string) @$request->input('min_price') ?: "";
        $applied_filters['property_type_id'] = (string) @$request->input('property_type_id') ?: "";
        $applied_filters['bedroom_type_id'] = (string) @$request->input('bedroom_type_id') ?: "";
        $applied_filters['bedroom_number'] = (string) @$request->input('bedroom_number') ?: "";
        $applied_filters['bathroom_type_id'] = (string) @$request->input('bathroom_type_id') ?: "";
        $applied_filters['bathroom_number'] = (string) @$request->input('bathroom_number') ?: "";
        $applied_filters['toilet_number'] = (string) @$request->input('toilet_number') ?: "";
        $applied_filters['condition_type_id'] = (string) @$request->input('condition_type_id') ?: "";
        $applied_filters['completion_status_id'] = (string) @$request->input('completion_status_id') ?: "";
        $applied_filters['area_id'] = (string) @$request->input('area_id') ?: "";
        $applied_filters['max_area_sqft'] = (string) @$request->input('max_area_sqft') ?: "";

        $response['applied_filters'] = $applied_filters; // set to response array
        $response['sort_by'] = (string) @$request->input('sort_by') ?: "";

        $sort_by = @$request->input('sort_by') ?: 1;
        $sorting = $this->sort_property_by[$sort_by];

        $properties = Property::with([
            'areaDetails' => function ($area) {
                $area->where('status', '=', 1)->where('parent_id', '=', 0);
            }, 'areaDetails.country' => function ($country) {
                $country->where('status', '=', 1)->where('parent_id', '=', 0);
            }, 'areaDetails.childdata' => function ($areaChild) use ($language_id) {
                $areaChild->where('language_id', '=', $language_id);
            }, 'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
                $countryChild->where('language_id', '=', $language_id);
            },
        ])
            // ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            //     $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
            // })
            ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                $areaDetailsQuery->where('status', '=', 1);
            })
            ->whereHas('agentDetails', function ($agentQuery) {
                $agentQuery->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

        if ($area_id) {
            $properties = $properties->where('area_id', '=', $area_id);
        }

        $total_records = $properties->get()->count();

        if ($south_lat && $north_lat && $east_long && $west_long) {
            $properties = $properties->whereRaw(
                DB::raw("(CASE WHEN '" . $south_lat . "' < '" . $north_lat . "'
                        THEN property_address_latitude BETWEEN '" . $south_lat . "' AND '" . $north_lat . "'
                        ELSE property_address_latitude BETWEEN '" . $north_lat . "' AND '" . $south_lat . "' 
                        END) 
                        AND
                        (CASE WHEN '" . $east_long . "' < '" . $west_long . "'
                            THEN property_address_longitude BETWEEN '" . $east_long . "' AND '" . $west_long . "'
                            ELSE property_address_longitude BETWEEN '" . $west_long . "' AND '" . $east_long . "'
                        END)")
            );
        }

        if ($request->input('area_id')) {
            $properties->where('area_id', '=', $request->input('area_id'));
        }

        if ($request->input('property_for_id') != "" && $request->input('property_for_id') != null) {
            $properties->where('property_for', '=', $request->input('property_for_id'));
        }

        if ($request->input('property_type_id')) {
            $properties->where('property_type', '=', $request->input('property_type_id'));
        }

        if ($request->input('bedroom_type_id')) {
            $properties->where('bedroom_type', '=', $request->input('bedroom_type_id'));
        }

        if ($request->input('bathroom_type_id')) {
            $properties->where('bathroom_type', '=', $request->input('bathroom_type_id'));
        }

        if ($request->input('bedroom_number')) {
            $properties->where('total_bedrooms', '=', $request->input('bedroom_number'));
        }

        if ($request->input('bathroom_number')) {
            $properties->where('total_bathrooms', '=', $request->input('bathroom_number'));
        }

        if ($request->input('toilet_number')) {
            $properties->where('total_toilets', '=', $request->input('toilet_number'));
        }

        if ($request->input('condition_type_id')) {
            $properties->where('condition_type_id', '=', $request->input('condition_type_id'));
        }

        if ($request->input('completion_status_id')) {
            $properties->where('completion_status_id', '=', $request->input('completion_status_id'));
        }

        if ($request->input('max_area_sqft')) {
            $properties->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
        }

        if ($request->input('max_price')) {
            $properties->where('price_area_wise', '<=', $request->input('max_price'));
        }

        if ($request->input('min_price')) {
            $properties->where('price_area_wise', '>=', $request->input('min_price'));
        }

        $properties = $properties->orderBy($sorting['field'], $sorting['sort_type']);

        if ($page_no == 0) {
            $properties = $properties->get();
        } else {
            $properties = $properties->paginate($this->property_per_page, ['*'], 'page', $page_no);
        }

        $property_list = [];
        $max_price = 0;
        $min_price = "";

        foreach ($properties as $key => $property) {

            $property_arr = [];
            $property_arr['id']  = (string) $property->id;
            $property_arr['property_id']  = (string) @$property->property_id ?: "";
            $property_arr['title']  = @$property->property_name ?: "";
            $property_arr['property_for']  = @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
            $property_arr['property_for_id']  = (string) (($property->property_for != "" && $property->property_for != null) ? $property->property_for : "");

            $area_name = "";
            $country_name = "";
            if ($property->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->areaDetails->name) ?: "";
                    }

                    if (@$property->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $property_arr['short_address']  = $short_address;

            $currency = @$property->areaDetails->country->currency_code ?: "KD";

            $base_price = $property->base_price;
            $property_price = Helper::getPropertyPriceByPrice($base_price, $property->areaDetails->default_range, $property->areaDetails->updated_range) ?: 0;

            // set max price and min price
            $max_price = Helper::tofloat($property_price) > $max_price ? Helper::tofloat($property_price) : $max_price;

            if (!isset($min_price) || $min_price == "") {
                $min_price = Helper::tofloat($property_price);
            }

            $min_price = (Helper::tofloat($property_price) < $min_price) ? Helper::tofloat($property_price) : $min_price;

            $base_price = number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3)) . ' ' . $currency;

            $property_arr['property_price'] = (string) (@$property->price_area_wise ? (number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
            $property_arr['bathroom_count'] = (string) @$property->total_bathrooms ?: "0";
            $property_arr['bedroom_count'] = (string) @$property->total_bedrooms ?: "0";
            $property_arr['toilet_count'] = (string) @$property->total_toilets ?: "0";
            $property_arr['area_sqft'] = (string) @$property->property_sqft_area ?: "0";

            $property_arr['latitude'] = (string) @$property->property_address_latitude ?: "";
            $property_arr['longitude'] = (string) @$property->property_address_longitude ?: "0";

            $area_value = "";
            if ($property->areaDetails->updated_range > $property->areaDetails->default_range) {
                // green
                $area_value = 1;
            } else if ($property->areaDetails->updated_range < $property->areaDetails->default_range) {
                // red
                $area_value = 2;
            } else if ($property->areaDetails->updated_range == $property->areaDetails->default_range) {
                // yellow
                $area_value = 0;
            }

            $property_arr['area_value'] = (string) $area_value;
            $property_image = "";
            if ($property->propertyImages->count() > 0) {
                $property_image = asset("storage/property_images/" . $property->id . '/' . $property->propertyImages[0]->property_image);
            }
            $property_arr['image_url'] = (string) $property_image;

            $is_fav = 0;
            if ($user_id) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
            }
            $property_arr['is_fav'] = (string) $is_fav;
            $property_arr['is_featured'] = (string) ($property->is_featured ?: 0);

            $property_list[] = $property_arr;
        }

        $response['property_list'] = $property_list;

        $result['code']     = (string) 1;
        $result['message']  = "success";
        $result['total_records']  = (int) $total_records;
        $result['per_page'] = (int) $this->property_per_page;
        $result['result'][] = $response;

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


    /* 
    |======================================
    || Add Property
    |=================================
    */

    public function add_property(Request $request)
    {

        $user_id = $request->input('user_id');
        $language_id = @$request->input('language_id') ?: 1;
        $token = $request->input('token');

        $property_title = $request->input('property_title');
        $property_for = $request->input('property_for');
        $property_type_id = $request->input('property_type_id');
        $property_price = $request->input('property_price');
        $area_sqft = $request->input('area_sqft');
        $bedroom_type_id = $request->input('bedroom_type_id');
        $condition_type_id = $request->input('condition_type_id');
        $completion_status_id = $request->input('completion_status_id');
        $bedroom_numbers = $request->input('bedroom_numbers');
        $bathroom_type_id = $request->input('bathroom_type_id');
        $bathroom_numbers = $request->input('bathroom_numbers');
        $toilet_numbers = $request->input('toilet_numbers');
        $amenities_ids = $request->input('amenities_ids');
        $area_id = $request->input('area_id');
        $property_description = $request->input('property_description');
        $property_address = $request->input('property_address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $images = $request->input('images');
        $is_renew = $request->input('is_renew'); // 1- renew plan, 2 - add new plan, 3 - add property in plan

        $plan_id = @$request->input('plan_id');

        if(!$plan_id) {
            $result['code']     = (string) 15;
            $result['message']  = "plan_required";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
        // $subscription_id = $request->input('subscription_id');
        $agent_type = $request->input('agent_type');

        if (!$property_title) {
            $result['code']     = (string) -5;
            $result['message']  = "property_title_required";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }

        $planDetails = SubscriptionPlan::where('parent_id', '=', 0)
            // ->where('status', '=', 1)
            ->find($plan_id);

        if(!$planDetails) {
            $result['code']     = (string) 16;
            $result['message']  = "plan_not_found";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }

        $plan_duration = Helper::getValidTillDate(date('Y-m-d H:i:s'), $planDetails->plan_duration_value, $planDetails->plan_duration_type);

        $userSubscriptionData = UserSubscription::where('user_id', '=', $user_id)->where('plan_id', '=', $plan_id)->latest('id')->first();

        $userSubscriptionId = "";
        $now = date('Y-m-d H:i:s');

        if(($is_renew == 1 || $is_renew == 3) && !$userSubscriptionData) {
            $result['code']     = (string) 12;
            $result['message']  = "not_subscribed_to_any_plan";
            $result['result']   = [];

            if ($request->input('is_testing') == 1) {
                $mainResult = $result;
            } else {
                $mainResult[] = $result;
            }
            return response()->json($mainResult);
        }

        if($planDetails->status != 1) {
            $result['code']     = (string) 14;
            $result['message']  = "plan_is_inactive";
            $result['result']   = [];

            if ($request->input('is_testing') == 1) {
                $mainResult = $result;
            } else {
                $mainResult[] = $result;
            }
            return response()->json($mainResult);
        }

        if ($is_renew == 1) {
            if($planDetails->is_free_plan == 1){
                $result['code']     = (string) 13;
                $result['message']  = "not_renewable_plan";
                $result['result']   = [];

                if ($request->input('is_testing') == 1) {
                    $mainResult = $result;
                } else {
                    $mainResult[] = $result;
                }
                return response()->json($mainResult);
            }
            $userSubscriptionId = $userSubscriptionData->id;

            $userSubscriptionData->plan_price = $planDetails->plan_price;
            $userSubscriptionData->plan_type = $planDetails->plan_type;
            $userSubscriptionData->no_of_plan_post = $planDetails->no_of_plan_post;
            $userSubscriptionData->plan_duration_value = $planDetails->plan_duration_value;
            $userSubscriptionData->plan_duration_type = $planDetails->plan_duration_type;
            $userSubscriptionData->extra_each_normal_post_price = $planDetails->extra_each_normal_post_price;
            $userSubscriptionData->is_featured = $planDetails->is_featured;
            $userSubscriptionData->no_of_default_featured_post = $planDetails->no_of_default_featured_post;
            $userSubscriptionData->start_date = $now;
            $userSubscriptionData->end_date = Carbon::parse($plan_duration['enddate'])->format('Y-m-d H:i:s');
            $userSubscriptionData->total_price = $planDetails->plan_price;
            $userSubscriptionData->save();

            $propertyFiledsUpdate = [
                "property_subscription_enddate" => Carbon::parse($plan_duration['enddate'])->format('Y-m-d H:i:s'),
                "updated_by" => $user_id
            ];
            $updateProperties = Property::where('plan_id', '=', $userSubscriptionId)
                ->where('status', '=', 1)
                ->update($propertyFiledsUpdate);
        } else if ($is_renew == 2) {
            $userSubscriptionData = new UserSubscription;

            $userSubscriptionData->transaction_id =  "";
            $userSubscriptionData->user_id = $user_id;
            $userSubscriptionData->plan_id = $plan_id;
            $userSubscriptionData->plan_name = @$planDetails->plan_name ?: "";
            $userSubscriptionData->plan_name_ar = @$planDetails->childdata[0]->plan_name ?: "";
            $userSubscriptionData->plan_description =  @$planDetails->plan_description ?: "";
            $userSubscriptionData->plan_description_ar = @$planDetails->childdata[0]->plan_description ?: "";
            $userSubscriptionData->plan_type = $planDetails->plan_type;
            $userSubscriptionData->no_of_plan_post = $planDetails->no_of_plan_post;
            $userSubscriptionData->is_free_plan = $planDetails->is_free_plan ?: 0;
            $userSubscriptionData->plan_price = $planDetails->plan_price;
            $userSubscriptionData->plan_duration_value = $planDetails->plan_duration_value;
            $userSubscriptionData->plan_duration_type = $planDetails->plan_duration_type;
            $userSubscriptionData->extra_each_normal_post_price = $planDetails->extra_each_normal_post_price;
            $userSubscriptionData->is_featured = $planDetails->is_featured ?: 0;
            $userSubscriptionData->no_of_default_featured_post = $planDetails->no_of_default_featured_post;

            $userSubscriptionData->start_date = $now;
            $userSubscriptionData->end_date = Carbon::parse($plan_duration['enddate'])->format('Y-m-d H:i:s');
            $userSubscriptionData->no_of_extra_featured_post = $planDetails->no_of_default_featured_post;
            $userSubscriptionData->extra_each_featured_post_price = $planDetails->extra_each_normal_post_price;
            $userSubscriptionData->total_price = $planDetails->plan_price;

            $userSubscriptionData->save();

            $userSubscriptionId = $userSubscriptionData->id;
        } else if($is_renew == 3) {
            if($userSubscriptionData->end_date < date('Y-m-d H:i:s')){
                $result['code']     = (string) -4;
                $result['message']  = "plan_expired";
                $result['result']   = [];
    
                if ($request->input('is_testing') == 1) {
                    $mainResult = $result;
                } else {
                    $mainResult[] = $result;
                }
                return response()->json($mainResult);
            }
        }

        $userSubscriptionId = $userSubscriptionData->id;

        $user = MainUser::find($user_id);
        if ($user->user_type != config('constants.USER_TYPE_AGENT')) {
            $user->user_type = config('constants.USER_TYPE_AGENT');
            $user->agent_joined_date = date('Y-m-d H:i:s');
        }
        if ($agent_type != "" || $agent_type != null) {
            $user->agent_type = $agent_type;
        }
        $user->save();

        $areaDetails = Area::find($area_id);
        $property = [];

        $org_property_price = Helper::getPropertyPriceByPrice($property_price, $areaDetails->default_range, $areaDetails->updated_range);
        // dd($org_property_price);
        $property_price_area_wise = number_format(Helper::tofloat($org_property_price), 3, '.', '');

        // $property['property_name'] = $property_title;
        $property['agent_id'] = $user_id;
        $property['property_description'] = @$property_description ?: "";
        $property['property_for'] = $property_for;
        $property['property_type'] = @$property_type_id ?: "";
        $property['area_id'] = $area_id ?: "";
        $property['property_address'] = $property_address ?: "";
        $property['property_address_latitude'] = $latitude ?: "";
        $property['property_address_longitude'] = $longitude ?: "";
        $property['property_amenities_ids'] = $amenities_ids ?: "";
        $property['bedroom_type'] = $bedroom_type_id ?: "";
        $property['total_bedrooms'] = $bedroom_numbers ?: "";
        $property['bathroom_type'] = $bathroom_type_id ?: "";
        $property['total_bathrooms'] = $bathroom_numbers ?: "";
        $property['total_toilets'] = $toilet_numbers ?: "";
        $property['property_sqft_area'] = $area_sqft ?: "";
        $property['base_price'] = $property_price ?: "";
        $property['price_area_wise'] = $property_price_area_wise;
        $property['condition_type_id'] = $condition_type_id ?: "";
        $property['completion_status_id'] = $completion_status_id ?: "";
        $property['plan_id'] = $userSubscriptionId;
        $property['property_subscription_enddate'] = $userSubscriptionData->end_date;
        // $property['property_subscription_enddate'] = $property_price_area_wise;
        // $property['plan_id'] = $property_price_area_wise;
        $property['status'] = 0;
        $property['created_by'] = $user_id;
        $property['updated_by'] = $user_id;
        $property['ip_address'] = $request->getClientIp() ?: null;

        $createProperty = Property::create($property);

        $addProperty = Property::find($createProperty->id);

        $property_id = '#' . str_pad($addProperty->id, 4, '0', STR_PAD_LEFT);
        $addProperty->property_id = $property_id;
        $property_name_exists = Property::where('property_name', '=', $property_title)->where('status', '!=', 2)->exists();
        $property_slug_exists = Property::where('slug', '=', Str::slug($property_title, '-'))->where('status', '!=', 2)->exists();

        if ($property_name_exists || $property_slug_exists) {
            $property_title = $property_title . " " . $property_id;
        }
        $addProperty->property_name = $property_title;
        $addProperty->slug = Str::slug($property_title, '-');

        if ($addProperty->save()) {

            $fetchProperty = Property::find($addProperty->id);
            if ($request->hasFile('images')) {
                $property_images = [];
                // try {

                foreach ($request->file('images') as $key => $file) {
                    // $filename = $file->getClientOriginalName();
                    $filename = 'tmp-' . time() . \Str::slug($file->getClientOriginalName(), '-') . '.' . $file->extension();
                    $ext = $file->getClientOriginalExtension();
                    Storage::disk('public')->put('tmp/' . $filename,  File::get($file));

                    $source_imagebanner = public_path('/storage/tmp/' . $filename);
                    $file_namebanner = "property-" . time() . '-' . str_pad(rand(0, 1000), 4, '0', STR_PAD_LEFT) . '.' . $ext;

                    $upload_dir = public_path('storage/property_images/' . $addProperty->id);
                    $image_destinationbanner = $upload_dir . '/' . $file_namebanner;
                    if (!file_exists($upload_dir)) {
                        \File::makeDirectory($upload_dir, 0777, true);
                    }

                    Helper::correctImageOrientation($source_imagebanner);
                    $compress_image = Helper::compressImage($source_imagebanner, $image_destinationbanner);
                    Helper::correctImageOrientation($image_destinationbanner);
                    // remove temporary images uploaded in folder for resize
                    unlink($source_imagebanner);

                    $property_images[$key]['property_id'] = $addProperty->id;
                    $property_images[$key]['property_image'] = $file_namebanner;
                }

                PropertyImages::insert($property_images);
                // } catch (\Throwable $th) {
                //     dd($th->getMessage());
                // }

            }

            $response = [];
            $propertyDetails = [];
            $plantDetails_arr = [];

            // $plan = $planDetails->subscriptionPlanDetails;

            $propertyDetails['id'] = (string) $fetchProperty->id;
            $propertyDetails['property_id'] = (string) $fetchProperty->property_id;
            $propertyDetails['property_title'] = $fetchProperty->property_name;
            $property_image_url = PropertyImages::where('property_id', '=', $fetchProperty->id)->orderBy('id', 'asc')->first();

            $image_url = "";
            if ($property_image_url) {
                $image_url = asset("storage/property_images/" . $fetchProperty->id . '/' . $property_image_url->property_image);
            }

            $area_name = "";
            $country_name = "";
            if ($fetchProperty->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($fetchProperty->areaDetails->name) ?: "";
                    $country_name = @urldecode($fetchProperty->areaDetails->country->name) ?: "";
                } else {
                    if (@$fetchProperty->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($fetchProperty->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($fetchProperty->areaDetails->name) ?: "";
                    }

                    if (@$fetchProperty->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($fetchProperty->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($fetchProperty->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $propertyDetails['short_address']  = $short_address;

            $propertyDetails['property_image_url'] = $image_url;
            $propertyDetails['property_for'] = @$fetchProperty->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $fetchProperty->property_for . '.label_key'), $language_id) : "";
            $propertyDetails['property_for_id'] = (string) $fetchProperty->property_for;
            $propertyDetails['property_price'] = (string) $fetchProperty->base_price ?: "";
            $propertyDetails['area_sqft'] = (string) $fetchProperty->property_sqft_area ?: "";
            $propertyDetails['is_fav'] = (string) "0";
            $propertyDetails['property_address'] = $fetchProperty->property_address ?: "";
            $propertyDetails['bedroom_numbers'] = (string) $fetchProperty->total_bedrooms ?: "";
            $propertyDetails['bathroom_numbers'] = (string) $fetchProperty->total_bathrooms ?: "";
            $propertyDetails['toilet_numbers'] = (string) $fetchProperty->total_toilets ?: "";

            $response["property_details"] = $propertyDetails;

            $plantDetails_arr['plan_name'] = ($language_id != 1 && $planDetails->childdata[0]->plan_name) ? $planDetails->childdata[0]->plan_name : $planDetails->plan_name;
            $plantDetails_arr['plan_duration'] = ($plan_duration != '-') ? $plan_duration['value'] . " " . $plan_duration['label_value'] : "-";
            $plantDetails_arr['plan_price'] = $planDetails->plan_price;
            $plantDetails_arr['plan_expiry_date'] = Carbon::parse($plan_duration['enddate'])->format('Y-m-d H:i:s');

            $response["plan_details"] = $plantDetails_arr;

            $result['code']     = (string) 1;
            $result['message']  = "success";
            $result['result'][] = $response;

            if ($request->input('is_testing') == 1) {
                // $result['result'] = $response;
                $mainResult = $result;
            } else {
                // $result['result'][] = $response;
                $mainResult[] = $result;
            }
            return response()->json($mainResult);
        } else {
            $result['code']     = (string) 0;
            $result['message']  = "something_went_wrong";
            $result['result']   = [];

            if ($request->input('is_testing') == 1) {
                // $result['result'] = $response;
                $mainResult = $result;
            } else {
                // $result['result'][] = $response;
                $mainResult[] = $result;
            }
            return response()->json($mainResult);
        }
    }

    /* 
    ================================================ 
    ||  ADD REMOVE FAVOURITE PROPERTY
    ================================================
    */

    public function add_remove_favourite(Request $request)
    {

        $language_id = $request->input('language_id') ?: 1;
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');
        $is_favourite = $request->input('is_favourite');

        if (!$property_id) {
            $result['code']     = (string) 0;
            $result['message']  = "failure";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }

        if ($is_favourite != "" || $is_favourite != null) {

            $response = [];
            $response['property_id'] = $property_id;
            $response['is_favourite'] = (string) $is_favourite;

            $userFavourite = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property_id);

            if ($is_favourite == 0) {
                $userFavourite->delete();

                $result['code']     = (string) 1;
                $result['message']  = "removed_from_favourite";
                $result['result'][] = $response;

                $mainResult[] = $result;
                return response()->json($mainResult);
            } else if (!$userFavourite->exists() && $is_favourite == 1) {

                $addFav = new UserFavouriteProperty;
                $addFav->user_id = $user_id;
                $addFav->property_id = $property_id;
                if ($addFav->save()) {

                    $result['code']     = (string) 1;
                    $result['message']  = "added_to_favourite";
                    $result['result'][] = $response;
                } else {
                    $result['code']     = (string) 0;
                    $result['message']  = "failure";
                    $result['result']   = [];
                }

                $mainResult[] = $result;
                return response()->json($mainResult);
            } else {
                // else if( $userFavourite->exists() && $is_favourite == 1 ){
                $result['code']     = (string) 1;
                $result['message']  = "already_addeed";
                $result['result'][] = $response;

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } else {
            $result['code']     = (string) 0;
            $result['message']  = "failure";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
    }

    /* 
    ================================================ 
    ||  PROPERTY DETAILS WITH SIMILAR PROPERTIES
    ================================================
    */

    public function property_details(Request $request)
    {
        $language_id = $request->input('language_id') ?: 1;
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');

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
            } else {
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $property = Property::with([
            'bedroomTypeDetails.childdata' => function ($bedType) use ($language_id) {
                $bedType->where('language_id', '=', $language_id);
            },
            'bathroomTypeDetails.childdata' => function ($bathType) use ($language_id) {
                $bathType->where('language_id', '=', $language_id);
            }
        ])
            ->with('subscribedPlanDetails', 'subscribedPlanDetails.subscriptionPlanDetails', 'propertyImages')
            // ->whereHas('subscribedPlanDetails.subscriptionPlanDetails', function ($planQuery) {
            //     $planQuery->where('status', '=', 1);
            // })
            ->where('id', '=', $property_id)
            ->first();

        // if($request->input('is_testing') == 1) {
        //     dd($property->toSql());
        // }
        if ($property) {

            if (@$property->status == 2) {
                $result['code']     = (string) -7;
                $result['message']  = 'property_deleted';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->status == 0) {
                $result['code']     = (string) -6;
                $result['message']  = 'property_is_inactive';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->property_subscription_enddate  < date('Y-m-d H:i:s')) {
                $result['code']     = (string) -5;
                $result['message']  = 'property_subscription_is_expired';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->agentDetails->status != 1) {
                $result['code']     = (string) 5;
                $result['message']  = 'agent_is_not_available';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            $property_images = [];
            foreach ($property->propertyImages as $key => $value) {
                $image_data = [];

                $image_data['image_id'] = (string) $value->id;
                $image_data['image_url'] = asset("storage/property_images/" . $property->id . '/' . $value->property_image);

                $property_images[] = $image_data;
            }

            $response = [];

            $currency = @$property->areaDetails->country->currency_code ?: "KD";

            $response['id'] = (string) $property->id;
            $response['property_images'] = $property_images;
            $response['property_image_count'] = (string) $property->propertyImages->count();
            $response['property_for'] =  @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
            $response['property_for_id'] = (string) @$property->property_for ?: "";
            $response['property_price'] = (string) (@$property->price_area_wise ?: 0) . " " . $currency;
            $response['property_id'] = (string) @$property->property_id ?: "";
            $response['property_type'] = (string) ($language_id != 1 && @$property->propertyTypeDetails->childdata[0]->type) ? ($property->propertyTypeDetails->childdata[0]->type) : $property->propertyTypeDetails->type;
            $response['property_title'] = @$property->property_name ?: "";

            $area_name = "";
            $country_name = "";
            if ($property->area_id) {
                if ($language_id == 1) {
                    $area_name = @urldecode($property->areaDetails->name) ?: "";
                    $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                } else {
                    if (@$property->areaDetails->childdata[0]->name) {
                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                    } else {
                        $area_name = urldecode($property->areaDetails->name) ?: "";
                    }

                    if (@$property->areaDetails->country->childdata[0]->name) {
                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                    } else {
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    }
                }
            }

            $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
            $deeplink = Helper::getPropertyDeeplink($property->id, $property->slug);
            $is_fav = "0";
            if ($user_id) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
            }


            $response['property_short_address'] = $short_address;
            $response['area_id'] = (string) @$property->area_id ?: "";
            $response['area_name'] = (string) @$area_name;
            $response['bedroom_count'] = (string) @$property->total_bedrooms ?: "";
            $response['bedroom_type_id'] = (string) @$property->bedroom_type ?: "";
            $response['bathroom_type_id'] = (string) @$property->bathroom_type ?: "";
            $response['bedroom_type'] = (string) ($language_id != 1 && @$property->bedroomTypeDetails->childdata[0]->type) ? $property->bedroomTypeDetails->childdata[0]->type : $property->bedroomTypeDetails->type;
            $response['bathroom_type'] = (string) ($language_id != 1 && @$property->bathroomTypeDetails->childdata[0]->type) ? $property->bathroomTypeDetails->childdata[0]->type : $property->bathroomTypeDetails->type;
            $response['property_type_id'] = (string) @$property->property_type ?: "";
            $response['bathroom_count'] = (string) @$property->total_bathrooms ?: "";
            $response['toilet_count'] = (string) @$property->total_toilets ?: "";
            $response['area_sqft'] = (string) @$property->property_sqft_area ?: "";
            $response['property_latitude'] = (string) @$property->property_address_latitude ?: "";
            $response['is_favourite'] = (string) ($is_fav ?: "0");
            $response['is_featured'] = (string) ($property->is_featured ?: 0);
            $response['property_longitude'] = (string) @$property->property_address_longitude ?: "";
            $response['description'] = @$property->property_description ?: "";
            $response['deeplink'] = @$deeplink ?: "";
            $response['property_long_address'] = @$property->property_address ?: "";

            // Amenities list
            $property_amenities = [];
            if (@$property->property_amenities_ids) {
                $property_amenities = explode(',', $property->property_amenities_ids);
                $amenities = Amenity::whereIn('id', $property_amenities)->where('status', '=', 1)->where('parent_id', '=', 0)->get();

                $property_amenities = [];
                foreach ($amenities as $key => $value) {
                    $data = [];
                    $data['id'] = (string) $value->id;
                    $amenity_name = $value->amenity_name;
                    if ($language_id > 1) {
                        $amenity_name = @$value->childdata[0]->amenity_name ?: $value->amenity_name;
                    }
                    $data['amenities_title'] = (string) urldecode($amenity_name);

                    $property_amenities[]  = $data;
                }
            }

            $response['amenities'] = $property_amenities;

            $agent_details = [];
            $agent_profile = "";
            if (@$property->agentDetails->profile_image) {
                $agent_profile = asset('/uploads/general_users/' . $property->agentDetails->profile_image);
            }

            $agent_details['agent_id'] = (string) @$property->agentDetails->id ?: "";
            $agent_details['agent_avatar_url'] = (string) $agent_profile;
            $agent_details['agent_name'] = (string) @urldecode($property->agentDetails->full_name) ?: "";

            $response['agent_details'] = $agent_details;

            $similar_properties = [];
            $similar = Property::with([
                'areaDetails' => function ($area) {
                    $area->where('status', '=', 1)->where('parent_id', '=', 0);
                },
                'areaDetails.country' => function ($country) {
                    $country->where('status', '=', 1)->where('parent_id', '=', 0);
                },
                'areaDetails.childdata' => function ($areaChild) use ($language_id) {
                    $areaChild->where('language_id', '=', $language_id);
                },
                'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
                    $countryChild->where('language_id', '=', $language_id);
                },
            ])
                ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
                    $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
                })
                ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                    $areaDetailsQuery->where('status', '=', 1);
                })
                ->whereHas('agentDetails', function ($agentQuery) {
                    $agentQuery->where('status', '=', 1);
                })
                ->where('status', '=', 1)
                ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
                ->where('property_for', '=', $property->property_for)
                ->where('property_type', '=', $property->property_type)
                ->where('bedroom_type', '=', $property->bedroom_type)
                ->where('bathroom_type', '=', $property->bathroom_type)
                // ->where('total_bedrooms', '=', $property->total_bedrooms)
                // ->where('total_bathrooms', '=', $property->total_bathrooms)
                // ->where('total_toilets', '=', $property->total_toilets)
                ->where('condition_type_id', '=', $property->condition_type_id)
                ->where('completion_status_id', '=', $property->completion_status_id)
                ->where('area_id', '=', $property->area_id)
                ->where('id', '!=', $property_id);

            $similar_properties_query = $similar->orderBy('id', 'desc')->paginate($this->property_details_similar_product_limit);


            foreach ($similar_properties_query as $key => $value) {
                $similar_property = [];

                $area_name = "";
                $country_name = "";
                if ($value->area_id) {
                    if ($language_id == 1) {
                        $area_name = @urldecode($value->areaDetails->name) ?: "";
                        $country_name = @urldecode($value->areaDetails->country->name) ?: "";
                    } else {
                        if (@$value->areaDetails->childdata[0]->name) {
                            $area_name = urldecode($value->areaDetails->childdata[0]->name) ?: "";
                        } else {
                            $area_name = urldecode($value->areaDetails->name) ?: "";
                        }

                        if (@$value->areaDetails->country->childdata[0]->name) {
                            $country_name = @urldecode($value->areaDetails->country->childdata[0]->name) ?: "";
                        } else {
                            $country_name = @urldecode($value->areaDetails->country->name) ?: "";
                        }
                    }
                }

                $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
                $currency = @$value->areaDetails->country->currency_code ?: "KD";
                $image_url = "";
                if (@$value->propertyImages[0]->property_image) {
                    $image_url = asset("storage/property_images/" . $value->id . '/' . $value->propertyImages[0]->property_image);
                }

                $similar_property['id'] = (string) $value->id;
                $similar_property['property_title'] = $value->property_name;
                $similar_property['property_for'] =  @$value->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $value->property_for . '.label_key'), $language_id) : "";
                $similar_property['property_for_id'] = (string) $value->property_for;
                $similar_property['property_short_address'] = $short_address;
                $similar_property['property_price'] = (string) (@$value->price_area_wise ?: 0) . " " . $currency;;
                $similar_property['image_url'] = $image_url;

                $is_fav = "0";
                if ($user_id) {
                    $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $value->id)->exists();
                }

                $similar_property['is_favourite'] = (string) ($is_fav ?: "0");
                $similar_property['is_featured'] = (string) ($value->is_featured ?: 0);

                $similar_properties[] = $similar_property;
            }

            $response['similar_properties'] = $similar_properties;

            $result['code']     = (string) 1;
            $result['message']  = 'success';
            $result['result'][] = $response;

            $mainResult[] = $result;
            return response()->json($mainResult);
        } else {
            $result['code']     = (string) 0;
            $result['message']  = 'property_not_found';
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
    }


    /* 
    ================================================ 
    ||  DELETE PROPERTY
    ================================================
    */

    public function delete_property(Request $request)
    {
        // $language_id = $request->input('language_id') ?: 1;
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');

        $property = Property::find($property_id);
        if ($property) {
            $property_images = PropertyImages::where('property_id', $property_id)->get();
            foreach ($property_images as $image) {
                File::delete(asset('storage/property_images/' . $property_id . '/' . $image->property_image));
            }

            $property_images = PropertyImages::where('property_id', $property_id)->delete();

            $property->status = 2;
            $property->updated_by = $user_id;
            if ($property->save()) {
                $result['code']     = (string) 1;
                $result['message']  = "success";

                $mainResult[] = $result;
                return response()->json($mainResult);
            } else {
                $result['code']     = (string) 0;
                $result['message']  = "failure";

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } else {
            $result['code']     = (string) -6;
            $result['message']  = "property_not_found";

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
    }


    /* 
    ================================================ 
    ||  EDIT PROPERTY
    ================================================
    */

    public function edit_property(Request $request)
    {
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');
        $language_id = @$request->input('language_id') ?: 1;
        $token = $request->input('token');

        $property_title = $request->input('property_title');
        $property_for = $request->input('property_for');
        $property_type_id = $request->input('property_type_id');
        $property_price = $request->input('property_price');
        $area_sqft = $request->input('area_sqft');
        $bedroom_type_id = $request->input('bedroom_type_id');
        $condition_type_id = $request->input('condition_type_id');
        $completion_status_id = $request->input('completion_status_id');
        $bedroom_numbers = $request->input('bedroom_numbers');
        $bathroom_type_id = $request->input('bathroom_type_id');
        $bathroom_numbers = $request->input('bathroom_numbers');
        $toilet_numbers = $request->input('toilet_numbers');
        $amenities_ids = $request->input('amenities_ids');
        $deleted_images = $request->input('deleted_images');
        $area_id = $request->input('area_id');
        $property_description = $request->input('property_description');
        $property_address = $request->input('property_address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $images = $request->input('images');


        if (!$property_title) {
            $result['code']     = (string) -5;
            $result['message']  = "property_title_required";
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }

        // $subscription = UserSubscription::where('property_id', '=', $property_id)->where('user_id', '=', $user_id)->first();
        // $plan_id = @$subscription->plan_id ?: "";

        // if($plan_id){

        //     $planDetails = SubscriptionPlan::where('parent_id', '=', 0)->where('status', '=', 1)->find($plan_id);


        //     if(!$planDetails->is_free ){
        //         $transactions = Transaction::where('agent_id', '=', $user_id)->where('subscription_plan_id', '=', $plan_id)->where('status', '=', 1);

        //         if($transactions->exists()){
        //             // $total_ads_count = Property::where('status', '!=', 2)->where('agent_id', '=', $user_id)->where('plan_id', '=', $)->count();
        //         }
        //         else{
        //             $add_plan = 1;
        //         }
        //     }else{
        //         $userSubscribed_plan_query = UserSubscription::with('subscriptionPlanDetails')->where("plan_id", '=', $plan_id)->where('user_id', '=', $user_id)->where('status', '=', 1);
        //     }

        //  //  THIS IS COMMENTED 
        //     if($planDetails){

        //         $userSubscribed_plan_query = UserSubscription::with('subscriptionPlanDetails')->where("plan_id", '=', $plan_id)->where('user_id', '=', $user_id)->where('status', '=', 1)->where('end_date', '<=', date('Y-m-d H:i:s'));

        //         if($userSubscribed_plan_query->exists()) {

        //             $userSubPlan = $userSubscribed_plan_query->first();

        //             if($userSubPlan->end_date < date('Y-m-d H:i:s')){
        //                 $result['code']     = (string) -9;
        //                 $result['message']  = "subscription_expired";
        //                 $result['result']   = [];

        //                 $mainResult[] = $result;
        //                 return response()->json($mainResult);
        //             }

        //             // $total_ads_count = UserSubscription::where('user_id', '=', $user_id)->where('plan_id', '=', $planDetails->plan_id)->count();
        //             $total_ads_count = Property::where('status', '!=', 2)->where('agent_id', '=', $user_id)->where('plan_id', '=', $userSubPlan->id)->count();

        //             dd($total_ads_count);

        //             if($planDetails->number_of_ads >= $total_ads_count){
        //                 $result['code']     = (string) -10;
        //                 $result['message']  = "subscription_property_limit_exceeded";
        //                 $result['result']   = [];

        //                 $mainResult[] = $result;
        //                 return response()->json($mainResult);
        //             }
        //         }
        //         else{
        //             // add plan process goes here
        //             $duration_type = $planDetails->duration_type;
        //             $plan_duration = "-";
        //             $plan_duration_data = Helper::getValidTillDate( date('Y-m-d') ,$duration_type);

        //             $plan_expiry_date = $plan_duration_data['enddate'];
        //             $subscription_type = $planDetails->subscription_type;
        //             $plan_price = $planDetails->subscription_price;


        //         }

        //     } 
        //     else{
        //         $result['code']     = (string) -8;
        //         $result['message']  = "subscription_plan_not_found";
        //         $result['result']   = [];

        //         $mainResult[] = $result;
        //         return response()->json($mainResult);
        //     }
        // }

        // $user = MainUser::find($user_id);
        // if ($user->user_type != config('constants.USER_TYPE_AGENT')) {
        //     $user->user_type = config('constants.USER_TYPE_AGENT');
        //     $user->agent_joined_date = date('Y-m-d H:i:s');
        // }


        $areaDetails = Area::find($area_id);
        $property = [];

        $org_property_price = Helper::getPropertyPriceByPrice($property_price, $areaDetails->default_range, $areaDetails->updated_range);
        $property_price_area_wise = number_format(Helper::tofloat($org_property_price), 3, '.', '');

        $property = Property::find($property_id);
        if ($property) {
            if ($request->hasFile('images')) {
                $uploading_images = count($request->file('images'));
                $uploaded_image_count = $property->propertyImages->count();

                $total = (int) $uploading_images + (int) $uploaded_image_count;
                if ($total > Helper::getMaxImagesUploadLimit()) {
                    $result['code']     = (string) 0;
                    $result['message']  = "max_image_upload_exceeded";
                    $result['result']   = [];

                    $mainResult[] = $result;
                    return response()->json($mainResult);
                }
            }


            $property_name_exists = Property::where('property_name', '=', $property_title)->where('status', '!=', 2)->where('id', '!=', $property_id)->exists();
            $property_slug_exists = Property::where('slug', '=', Str::slug($property_title, '-'))->where('id', '!=', $property_id)->where('status', '!=', 2)->exists();

            if ($property_name_exists || $property_slug_exists) {
                $property_title = $property_title . " " . $property_id;
            }

            $property['property_name'] = $property_title;
            $property['slug'] = Str::slug($property_title, '-');
            $property['property_description'] = @$property_description ?: "";
            $property['property_for'] = $property_for;
            $property['property_type'] = @$property_type_id ?: "";
            $property['area_id'] = $area_id ?: "";
            $property['property_address'] = $property_address ?: "";
            $property['property_address_latitude'] = $latitude ?: "";
            $property['property_address_longitude'] = $longitude ?: "";
            $property['property_amenities_ids'] = $amenities_ids ?: "";
            $property['bedroom_type'] = $bedroom_type_id ?: "";
            $property['total_bedrooms'] = $bedroom_numbers ?: "";
            $property['bathroom_type'] = $bathroom_type_id ?: "";
            $property['total_bathrooms'] = $bathroom_numbers ?: "";
            $property['total_toilets'] = $toilet_numbers ?: "";
            $property['property_sqft_area'] = $area_sqft ?: "";
            $property['base_price'] = $property_price ?: "";
            $property['price_area_wise'] = $property_price_area_wise;
            $property['condition_type_id'] = $condition_type_id ?: "";
            $property['completion_status_id'] = $completion_status_id ?: "";
            // $property['property_subscription_enddate'] = $property_price_area_wise;
            // $property['plan_id'] = $property_price_area_wise;
            $property['updated_by'] = $user_id;
            $property['ip_address'] = $request->getClientIp() ?: null;

            // $addProperty = Property::create($property);
            // $now = date('Y-m-d H:i:s');
            // $plan_duration = Helper::getValidTillDate($now, $planDetails->duration_type);

            // $add_plan = new UserSubscription;
            // $add_plan->user_id = $user_id;
            // $add_plan->subscription_plan_type = 1;
            // $add_plan->plan_id = $plan_id;
            // $add_plan->property_id = $addProperty->id;
            // $add_plan->start_date = $now;
            // $add_plan->end_date = $plan_duration['enddate'];
            // $add_plan->paid_amount = 0;
            // $add_plan->save();

            if ($property->save()) {

                $fetchProperty = Property::find($property->id);

                if ($request->hasfile('images')) {
                    $property_images = [];
                    try {

                    foreach ($request->file('images') as $key => $file) {
                        $filename = 'tmp-' . time() . \Str::slug($file->getClientOriginalName(), '-') . '.' . $file->extension();
                        $ext = $file->getClientOriginalExtension();
                        Storage::disk('public')->put('tmp/' . $filename,  File::get($file));

                        $source_imagebanner = public_path('/storage/tmp/' . $filename);
                        $file_namebanner = "property-" . time() . '-' . str_pad(rand(0, 1000), 4, '0', STR_PAD_LEFT) . '.' . $ext;

                        $upload_dir = public_path('storage/property_images/' . $property->id);
                        $image_destinationbanner = $upload_dir . '/' . $file_namebanner;
                        if (!file_exists($upload_dir)) {
                            \File::makeDirectory($upload_dir, 0777, true);
                        }

                        Helper::correctImageOrientation($source_imagebanner);
                        $compress_image = Helper::compressImage($source_imagebanner, $image_destinationbanner);
                        Helper::correctImageOrientation($image_destinationbanner);
                        // remove temporary images uploaded in folder for resize
                        unlink($source_imagebanner);

                        $property_images[$key]['property_id'] = $property->id;
                        $property_images[$key]['property_image'] = $file_namebanner;
                    }

                    PropertyImages::insert($property_images);
                    } catch (\Throwable $th) {
                        echo ($th->getMessage());
                        exit();
                    }

                }

                if ($deleted_images) {
                    $dlt_images = PropertyImages::whereIn('id', explode(',', $deleted_images))->get();
                    foreach ($dlt_images as $key => $value) {
                        File::delete(public_path('storage/property_images/'  . $property->id . '/' . $value->property_image));
                    }

                    $property_images = PropertyImages::whereIn('id', explode(',', $deleted_images))->delete();
                }

                $response = [];
                $propertyDetails = [];
                $plantDetails_arr = [];

                // $plan = $planDetails->subscriptionPlanDetails;

                $propertyDetails['id'] = (string) $fetchProperty->id;
                $propertyDetails['property_id'] = (string) $fetchProperty->property_id;
                $propertyDetails['property_title'] = $fetchProperty->property_name;
                $property_image_url = PropertyImages::where('property_id', '=', $fetchProperty->id)->orderBy('id', 'asc')->first();

                $image_url = "";
                if ($property_image_url) {
                    $image_url = asset("storage/property_images/" . $fetchProperty->id . '/' . $property_image_url->property_image);
                }

                $area_name = "";
                $country_name = "";
                if ($fetchProperty->area_id) {
                    if ($language_id == 1) {
                        $area_name = @urldecode($fetchProperty->areaDetails->name) ?: "";
                        $country_name = @urldecode($fetchProperty->areaDetails->country->name) ?: "";
                    } else {
                        if (@$fetchProperty->areaDetails->childdata[0]->name) {
                            $area_name = urldecode($fetchProperty->areaDetails->childdata[0]->name) ?: "";
                        } else {
                            $area_name = urldecode($fetchProperty->areaDetails->name) ?: "";
                        }

                        if (@$fetchProperty->areaDetails->country->childdata[0]->name) {
                            $country_name = @urldecode($fetchProperty->areaDetails->country->childdata[0]->name) ?: "";
                        } else {
                            $country_name = @urldecode($fetchProperty->areaDetails->country->name) ?: "";
                        }
                    }
                }

                $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
                $propertyDetails['short_address']  = $short_address;

                $propertyDetails['property_image_url'] = $image_url;
                $propertyDetails['property_for'] = @$fetchProperty->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $fetchProperty->property_for . '.label_key'), $language_id) : "";
                $propertyDetails['property_for_id'] = (string) $fetchProperty->property_for;
                $propertyDetails['property_price'] = (string) $fetchProperty->base_price ?: "";
                $propertyDetails['area_sqft'] = (string) $fetchProperty->property_sqft_area ?: "";

                $is_fav = "0";
                if ($user_id) {
                    $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $fetchProperty->id)->exists();
                }

                $propertyDetails['is_fav'] = (string) $is_fav;
                $propertyDetails['property_address'] = $fetchProperty->property_address ?: "";
                $propertyDetails['bedroom_numbers'] = (string) $fetchProperty->total_bedrooms ?: "";
                $propertyDetails['bathroom_numbers'] = (string) $fetchProperty->total_bathrooms ?: "";
                $propertyDetails['toilet_numbers'] = (string) $fetchProperty->total_toilets ?: "";

                $response["property_details"] = $propertyDetails;
                $response["plan_details"] = $plantDetails_arr;

                $result['code']     = (string) 1;
                $result['message']  = "success";
                $result['result'][] = $response;

                if ($request->input('is_testing') == 1) {
                    // $result['result'] = $response;
                    $mainResult = $result;
                } else {
                    // $result['result'][] = $response;
                    $mainResult[] = $result;
                }

                return response()->json($mainResult);
            } else {
                $result['code']     = (string) -6;
                $result['message']  = "property_not_found";
                $result['result']   = [];

                if ($request->input('is_testing') == 1) {
                    // $result['result'] = $response;
                    $mainResult = $result;
                } else {
                    // $result['result'][] = $response;
                    $mainResult[] = $result;
                }
                return response()->json($mainResult);
            }
        } else {
            $result['code']     = (string) 0;
            $result['message']  = "something_went_wrong";
            $result['result']   = [];

            if ($request->input('is_testing') == 1) {
                // $result['result'] = $response;
                $mainResult = $result;
            } else {
                $mainResult[] = $result;
            }
            return response()->json($mainResult);
        }
    }

    // PROPERTY DELETE

    public function delete_property_image(Request $request)
    {
        $user_id = $request->input('user_id');
        $image_id = $request->input('property_image_id');
        $language_id = @$request->input('language_id') ?: 1;
        $token = $request->input('token');

        if ($image_id) {
            $dlt_images = PropertyImages::find($image_id);
            File::delete(public_path('storage/property_images/'  . $dlt_images->property_id . '/' . $dlt_images->property_image));

            $property_images = PropertyImages::find($image_id)->delete();

            $result['code']     = (string) 1;
            $result['message']  = "image_deleted";

            $mainResult[] = $result;
            return response()->json($mainResult);
        } else {
            $result['code']     = (string) 0;
            $result['message']  = "provide_image_id";

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
    }


    public function property_inquiry(Request $request)
    {
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');
        $language_id = @$request->input('language_id') ?: 1;
        $agent_id = $request->input('agent_id');

        $mobile_no = $request->input('mobile_no');
        $email = $request->input('email');
        $full_name = $request->input('full_name');
        $country_code = $request->input('country_code');
        $message = $request->input('message');

        $setting = Setting::find(1);
        $agent = MainUser::find($agent_id);
        $admin_email = $setting->email;
        $agent_email = urldecode($agent->email);
        $template_id = 7;

        $this->sendEmail($language_id, $agent_email, $email, $full_name, $mobile_no, $country_code, $message, $agent_id, $property_id, "", "", $template_id);

        $result['code']     = (string) 1;
        $result['message']  = "inquiry_for_property_sent_to_agent";

        $mainResult[] = $result;
        return response()->json($mainResult);
    }


    //mail
    public function sendEmail($language_id, $email, $user_email, $name, $mobile_no, $country_code, $inquiry_message, $agent_id, $property_id, $url, $logo, $id)
    {

        $setting = Setting::find(1);
        // dd($setting);
        $templateData = Helper::getEmailTemplateData($language_id, $id);
        // dd($templateData);

        $from_email = $setting['from_email'];
        $data = array('email' => $email, "user_email" => $user_email, 'name' => $name, 'url' => $url, "phone" => $mobile_no, "country_code" => $country_code, "language_id" => $language_id, 'id' =>  $id, "agent_id" => $agent_id, 'property_id' => $property_id, 'logo' => $logo, 'from_email' => $from_email, "inquiry_message" => $inquiry_message);
        // try {
        Mail::send('emails.property_inquiry', $data, function ($message) use ($data, $templateData) {
            $message->to($data['email'], $templateData->title)->subject($templateData->subject);
            $message->from($data['from_email'], 'DOM - Properties');
        });
        // } catch (\Throwable $th) {
        // 	// throw $th;
        // }
    }

    public function similar_property_list(Request $request)
    {

        $language_id = $request->input('language_id') ?: 1;
        $user_id = $request->input('user_id');
        $property_id = $request->input('property_id');
        $page_no = $request->input('page_no');

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
            } else {
                $result['code']     = (string) -7;
                $result['message']  = 'account_not_found';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }
        } // if user id END

        $applied_filters = [];
        // $applied_filters['latitude'] = (string) @$request->input('latitude') ?: "";
        // $applied_filters['longitude'] = (string) @$request->input('longitude') ?: "";

        $applied_filters['property_for_id'] = (string) (($request->input('property_for_id') != "" && $request->input('property_for_id') != null) ? $request->input('property_for_id') : "");
        $applied_filters['max_price'] = (string) @$request->input('max_price') ?: "";
        $applied_filters['min_price'] = (string) @$request->input('min_price') ?: "";
        $applied_filters['property_type_id'] = (string) @$request->input('property_type_id') ?: "";
        $applied_filters['bedroom_type_id'] = (string) @$request->input('bedroom_type_id') ?: "";
        $applied_filters['bedroom_number'] = (string) @$request->input('bedroom_number') ?: "";
        $applied_filters['bathroom_type_id'] = (string) @$request->input('bathroom_type_id') ?: "";
        $applied_filters['bathroom_number'] = (string) @$request->input('bathroom_number') ?: "";
        $applied_filters['toilet_number'] = (string) @$request->input('toilet_number') ?: "";
        $applied_filters['condition_type_id'] = (string) @$request->input('condition_type_id') ?: "";
        $applied_filters['completion_status_id'] = (string) @$request->input('completion_status_id') ?: "";
        $applied_filters['area_id'] = (string) @$request->input('area_id') ?: "";
        $applied_filters['max_area_sqft'] = (string) @$request->input('max_area_sqft') ?: "";

        $response['applied_filters'] = $applied_filters; // set to response array
        $response['sort_by'] = (string) @$request->input('sort_by') ?: "";

        $sort_by = @$request->input('sort_by') ?: 1;
        $sorting = $this->sort_property_by[$sort_by];


        $property = Property::with('subscribedPlanDetails', 'subscribedPlanDetails.subscriptionPlanDetails', 'propertyImages')
            // ->whereHas('subscribedPlanDetails.subscriptionPlanDetails', function ($planQuery) {
            //     $planQuery->where('status', '=', 1);
            // })
            ->find($property_id);

        if ($property) {

            if (@$property->status == 2) {
                $result['code']     = (string) -7;
                $result['message']  = 'property_deleted';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->status == 0) {
                $result['code']     = (string) -6;
                $result['message']  = 'property_is_inactive';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->property_subscription_enddate  < date('Y-m-d H:i:s')) {
                $result['code']     = (string) -5;
                $result['message']  = 'property_subscription_is_expired';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            if (@$property->agentDetails->status != 1) {
                $result['code']     = (string) 5;
                $result['message']  = 'agent_is_not_available';
                $result['result']   = [];

                $mainResult[] = $result;
                return response()->json($mainResult);
            }

            $similar_properties = [];
            $similar = Property::with([
                'areaDetails' => function ($area) {
                    $area->where('status', '=', 1)->where('parent_id', '=', 0);
                },
                'areaDetails.country' => function ($country) {
                    $country->where('status', '=', 1)->where('parent_id', '=', 0);
                },
                'areaDetails.childdata' => function ($areaChild) use ($language_id) {
                    $areaChild->where('language_id', '=', $language_id);
                },
                'areaDetails.country.childdata' => function ($countryChild) use ($language_id) {
                    $countryChild->where('language_id', '=', $language_id);
                },
            ])
            // ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            //     $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
            // })
            ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                $areaDetailsQuery->where('status', '=', 1);
            })
            ->whereHas('agentDetails', function ($agentQuery) {
                $agentQuery->where('status', '=', 1);
            })
            ->where('status', '=', 1)
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->where('property_for', '=', $property->property_for)
            ->where('property_type', '=', $property->property_type)
            ->where('bedroom_type', '=', $property->bedroom_type)
            ->where('bathroom_type', '=', $property->bathroom_type)
            // $similar->where('total_bedrooms', '=', $property->total_bedrooms);
            // $similar->where('total_bathrooms', '=', $property->total_bathrooms);
            // $similar->where('total_toilets', '=', $property->total_toilets);
            ->where('condition_type_id', '=', $property->condition_type_id)
            ->where('completion_status_id', '=', $property->completion_status_id)
            ->where('area_id', '=', $property->area_id)
            ->where('id', '!=', $property->id);

            if ($request->input('property_for_id') != "" || $request->input('property_for_id') != null) {
                $similar->where('property_for', '=', $request->input('property_for_id'));
            }

            if ($request->input('property_type_id')) {
                $similar->where('property_type', '=', $request->input('property_type_id'));
            }

            if ($request->input('bedroom_type_id')) {
                $similar->where('bedroom_type', '=', $request->input('bedroom_type_id'));
            }

            if ($request->input('bathroom_type_id')) {
                $similar->where('bathroom_type', '=', $request->input('bathroom_type_id'));
            }

            if ($request->input('bedroom_number')) {
                $similar->where('total_bedrooms', '=', $request->input('bedroom_number'));
            }

            if ($request->input('bathroom_number')) {
                $similar->where('total_bathrooms', '=', $request->input('bathroom_number'));
            }

            if ($request->input('toilet_number')) {
                $similar->where('total_toilets', '=', $request->input('toilet_number'));
            }

            if ($request->input('condition_type_id')) {
                $similar->where('condition_type_id', '=', $request->input('condition_type_id'));
            }

            if ($request->input('completion_status_id')) {
                $similar->where('completion_status_id', '=', $request->input('completion_status_id'));
            }

            if ($request->input('area_id')) {
                $similar->where('area_id', '=', $request->input('area_id'));
            }

            if ($request->input('max_area_sqft')) {
                $similar->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
            }

            if ($request->input('max_price')) {
                $similar->where('price_area_wise', '<=', $request->input('max_price'));
            }

            if ($request->input('min_price')) {
                $similar->where('price_area_wise', '>=', $request->input('min_price'));
            }

            $total_records = $similar->count();

            $similar = $similar->orderBy($sorting['field'], $sorting['sort_type']);
            $similar = $similar->paginate($this->property_per_page, ['*'], 'page', $page_no);


            $property_list = [];
            $max_price = 0;
            $min_price = "";

            foreach ($similar as $key => $property) {
                // $similar_property = [];

                // $area_name = "";
                // $country_name = "";
                // if ($value->area_id) {
                //     if ($language_id == 1) {
                //         $area_name = @urldecode($value->areaDetails->name) ?: "";
                //         $country_name = @urldecode($value->areaDetails->country->name) ?: "";
                //     } else {
                //         if (@$value->areaDetails->childdata[0]->name) {
                //             $area_name = urldecode($value->areaDetails->childdata[0]->name) ?: "";
                //         } else {
                //             $area_name = urldecode($value->areaDetails->name) ?: "";
                //         }

                //         if (@$value->areaDetails->country->childdata[0]->name) {
                //             $country_name = @urldecode($value->areaDetails->country->childdata[0]->name) ?: "";
                //         } else {
                //             $country_name = @urldecode($value->areaDetails->country->name) ?: "";
                //         }
                //     }
                // }

                // $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
                // $currency = @$value->areaDetails->country->currency_code ?: "KD";
                // $image_url = "";
                // if (@$value->propertyImages[0]->property_image) {
                //     $image_url = asset("storage/property_images/" . $value->id . '/' . $value->propertyImages[0]->property_image);
                // }

                // $similar_property['id'] = (string) $value->id;
                // $similar_property['property_title'] = $value->property_name;
                // $similar_property['property_for'] =  @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
                // $similar_property['property_for_id'] = (string) $value->property_for;
                // $similar_property['property_short_address'] = $short_address;
                // $similar_property['property_price'] = (string) (@$value->price_area_wise ?: 0) . " " . $currency;;
                // $similar_property['image_url'] = $image_url;

                // $is_fav = "0";
                // if ($user_id) {
                //     $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $value->id)->exists();
                // }

                // $similar_property['is_favourite'] = (string) ($is_fav ?: "0");

                // $similar_properties[] = $similar_property;

                $property_arr = [];
                $property_arr['id']  = (string) $property->id;
                $property_arr['property_id']  = (string) @$property->property_id ?: "";
                $property_arr['title']  = @$property->property_name ?: "";
                $property_arr['property_for']  = @$property->property_for ? Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.label_key'), $language_id) : "";
                $property_arr['property_for_id']  = (string) (($property->property_for != "" && $property->property_for != null) ? $property->property_for : "");

                $area_name = "";
                $country_name = "";
                if ($property->area_id) {
                    if ($language_id == 1) {
                        $area_name = @urldecode($property->areaDetails->name) ?: "";
                        $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                    } else {
                        if (@$property->areaDetails->childdata[0]->name) {
                            $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: "";
                        } else {
                            $area_name = urldecode($property->areaDetails->name) ?: "";
                        }

                        if (@$property->areaDetails->country->childdata[0]->name) {
                            $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: "";
                        } else {
                            $country_name = @urldecode($property->areaDetails->country->name) ?: "";
                        }
                    }
                }

                $short_address = ($area_name && $country_name) ? $area_name . ', ' . $country_name : "";
                $property_arr['short_address']  = $short_address;

                $currency = @$property->areaDetails->country->currency_code ?: "KD";

                $base_price = $property->base_price;
                $property_price = Helper::getPropertyPriceByPrice($base_price, $property->areaDetails->default_range, $property->areaDetails->updated_range) ?: 0;

                // set max price and min price
                $max_price = Helper::tofloat($property_price) > $max_price ? Helper::tofloat($property_price) : $max_price;

                if (!isset($min_price) || $min_price == "") {
                    $min_price = Helper::tofloat($property_price);
                }

                $min_price = (Helper::tofloat($property_price) < $min_price) ? Helper::tofloat($property_price) : $min_price;

                $base_price = number_format(Helper::tofloat($property_price), ($property->areaDetails->country->currency_decimal_point ?: 3)) . ' ' . $currency;

                $property_arr['property_price'] = (string) (@$property->price_area_wise ? (number_format(Helper::tofloat($property->price_area_wise), ($property->areaDetails->country->currency_decimal_point ?: 3))) : "0") . ' ' . $currency;
                $property_arr['bathroom_count'] = (string) @$property->total_bathrooms ?: "0";
                $property_arr['bedroom_count'] = (string) @$property->total_bedrooms ?: "0";
                $property_arr['toilet_count'] = (string) @$property->total_toilets ?: "0";
                $property_arr['area_sqft'] = (string) @$property->property_sqft_area ?: "0";

                $property_arr['latitude'] = (string) @$property->property_address_latitude ?: "";
                $property_arr['longitude'] = (string) @$property->property_address_longitude ?: "0";

                $area_value = "";
                if ($property->areaDetails->updated_range > $property->areaDetails->default_range) {
                    // green
                    $area_value = 2;
                } else if ($property->areaDetails->updated_range < $property->areaDetails->default_range) {
                    // red
                    $area_value = 1;
                } else if ($property->areaDetails->updated_range == $property->areaDetails->default_range) {
                    // yellow
                    $area_value = 0;
                }

                $property_arr['area_value'] = (string) $area_value;

                $property_image = "";
                if ($property->propertyImages->count() > 0) {
                    $property_image = asset("storage/property_images/" . $property->id . '/' . $property->propertyImages[0]->property_image);
                }
                $property_arr['image_url'] = (string) $property_image;

                $is_fav = 0;
                if ($user_id) {
                    $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property->id)->exists();
                }
                $property_arr['is_fav'] = (string) $is_fav;
                $property_arr['is_featured'] = (string) ($property->is_featured ?: 0);

                $property_list[] = $property_arr;
            }

            $response['property_list'] = $property_list;

            $result['code']     = (string) 1;
            $result['message']  = 'success';
            $result['total_records']  = (int) $total_records;
            $result['per_page'] = (int) $this->property_per_page;
            $result['result'][] = $response;

            $mainResult[] = $result;
            return response()->json($mainResult);
        } else {
            $result['code']     = (string) 0;
            $result['message']  = 'property_not_found';
            $result['result']   = [];

            $mainResult[] = $result;
            return response()->json($mainResult);
        }
    }
}
