<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Models\Area;
use App\Models\UserFavouriteProperty;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\PropertyCompletionStatus;
use App\Models\PropertyCondition;
use App\Models\PropertyImages;
use App\Models\BathroomTypes;
use App\Models\BedroomTypes;
use App\Models\FavouriteProperty;
use App\Models\MainUsers;
use App\Models\Amenity;
use App\Models\Country;
use App\Models\FeaturedAddons;
use App\Models\ReportUser;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Mail;
use DB;
use Carbon\Carbon;
use Str;
use Illuminate\Support\Facades\Storage;
use File;
use Session;

class PropertyController extends Controller
{
    protected $property_types;
    protected $property_per_page;
    protected $sort_property_by;
    protected $property_details_similar_product_limit;
    protected $my_ads_per_page;
    protected $similar_property_per_page;
    protected $agent_profile_properties_limit;

    public function __construct()
    {
        $this->property_types = [
            "featured" => [
                "is_secondary" => false,
                "label" => "featured_properties",
                "label_secondary" => "",
                "value" => "featured",
            ],
            "area" => [
                "is_secondary" => true,
                "label" => "property_areas",
                "label_secondary" => "area_name"
            ],
            "buy" => [
                "is_secondary" => false,
                "label" => "properties_for_buy",
                "value" => "buy",
            ],
            "rent" => [
                "is_secondary" => false,
                "label" => "properties_for_rent",
                "value" => "rent",
            ],
            "similar" => [
                "is_secondary" => false,
                "label" => "similar_property",
                "value" => "similar_property",
            ]
        ];


        $this->sort_property_by = array(
            1 => array("field" => "id", "sort_type" => "desc"),
            2 => array("field" => "price_area_wise", "sort_type" => "desc"),
            3 => array("field" => "price_area_wise", "sort_type" => "asc"),
        );
        $this->property_per_page = 9;
        $this->property_details_similar_product_limit = 3;
        $this->my_ads_per_page = 4;
        $this->similar_property_per_page = 9;
        $this->agent_profile_properties_limit = 3;
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

    private function getFiltersList($language_id = 1)
    {
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

        $filter_data['property_types'] = $property_types;

        // bedroom type list
        $bedroom_types = BedroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $filter_data['bedroom_types'] = $bedroom_types;

        // Bathroom type list
        $bathroom_types = BathroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $filter_data['bathroom_types'] = $bathroom_types;

        $filter_data['bedroom_number'] = (string) Helper::getMaxBedroomNumbers();
        $filter_data['bathroom_number'] = (string) Helper::getMaxBathroomNumbers();
        $filter_data['toilet_number'] = (string) Helper::getMaxToiletNumbers();

        // property conditions types list
        $condition_types = PropertyCondition::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $filter_data['condition_types'] = $condition_types;

        // Property completion statuses list
        $completion_status = PropertyCompletionStatus::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $filter_data['completion_status'] = $completion_status;

        // Area list
        $areas = Area::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->get();

        $filter_data['area_list'] = $areas;
        $filter_data['area_sqft_list'] = $this->getAreaSqftList();

        $max_property_price = $this->getPropertyMaxPrice();
        $min_property_price = $this->getPropertyMinPrice();

        $filter_data['max_price'] = (string) number_format(Helper::toFloat(Helper::getPropertyPriceByPrice(Helper::toFloat($max_property_price))), 3, '.', '');
        $filter_data['min_price'] = (string) Helper::getPropertyPriceByPrice(Helper::toFloat($min_property_price));

        return $filter_data;
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

    public function property_list(Request $request, $list_type = "")
    {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $property_id = "";
        $property_detail = "";
        $area_id = "";
        $breadcumb_title_arr = [];

        $latitude = Helper::getKuwaitLatLong('latitude');
        $longitude = Helper::getKuwaitLatLong('longitude');
        $is_search = 0;
        $property_type = "";
        $property_for = "";
        $search = "";

        if($list_type) {

            $breadcumb_data = $this->property_types[$list_type];
            $breadcumb_title_arr[] = $labels[$breadcumb_data['label']];
    
            if ($breadcumb_data['is_secondary']) {
                $areaDetails = Area::with(['childdata' => function ($child) use ($language_id) {
                    $child->where('language_id', '=', $language_id);
                }])
                    ->where('slug', '=', $request->area)
                    ->where('parent_id', '=', 0)
                    ->first();
                if ($areaDetails) {
                    $area_name = ($language_id != 1 && @$areaDetails->childdata[0]->name) ? urldecode($areaDetails->childdata[0]->name) : urldecode($areaDetails->name);
                }
                $area_id = $areaDetails->id;
                if ($areaDetails->latitude && $areaDetails->longitude) {
                    $latitude = Helper::tofloat($areaDetails->latitude);
                    $longitude = Helper::tofloat($areaDetails->longitude);
                }
    
                $breadcumb_title_arr[] = $area_name;
            }
    
            
            if($list_type == 'similar') {
                $property_detail = Property::where('slug', '=', $request->input('property'))
                    ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                        $areaDetailsQuery->where('status', '=', 1);
                    })
                    ->whereHas('agentDetails', function ($agentQuery) {
                        $agentQuery->where('status', '=', 1);
                    })
                    ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
                    ->where('status', '=', 1)
                    ->latest('id')
                    ->first();
                if(!$property_detail) {
                    return redirect()->route('frontend.homePage');
                }
    
                $property_id = encrypt($property_detail->id);
                $breadcumb_title_arr[] = $property_detail->property_name;
            }
        } else {
            $property_type = $request->input('ptype');
            $property_for = $request->input('pfor');
            $search = $request->input('s');
            $is_search = 1;
        }

        $filters = $this->getFiltersList($language_id);
        $areasqft_list = $this->getAreaSqftList();
        $currency = @Country::first()->currency_code ?: 'KD';
        $PageTitle = $labels['featured_properties'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";

        return view('frontEnd.property.list', compact('language_id', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'labels', 'breadcumb_title_arr', 'filters', 'areasqft_list', 'currency', 'latitude', 'longitude', 'area_id', 'list_type', 'property_id', 'property_detail', 'property_type', 'property_for', 'search', 'is_search'));
    }


    // AJAX Call data fetchProperties propertylist
    public function getData(Request $request)
    {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = "";
        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->id();
        }
        $page_no = $request->input('page_no') ?: 1;
        $sort_by = $request->input('sort_by') ?: 1;
        $sorting = $this->sort_property_by[$sort_by];
        $list_type = $request->input('list_type') ?: 1;

        $east_long = $request->input('east');
        $west_long = $request->input('west');
        $north_lat = $request->input('north');
        $south_lat = $request->input('south');

        $s_property_type = $request->input('s_property_type');
        $s_property_for = $request->input('s_property_for');
        $s_search = $request->input('s_search');
        $is_search = $request->input('is_search');

        $getMapLocationWithBounds = $request->input('getMapLocationWithBounds');

        $properties_query = Property::with([
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
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->where('status', '=', 1);

        // if ($south_lat && $north_lat && $east_long && $west_long) {
        //     $properties_query = $properties_query->whereRaw(
        //         DB::raw("(CASE WHEN '" . $south_lat . "' < '" . $north_lat . "'
        //                 THEN property_address_latitude BETWEEN '" . $south_lat . "' AND '" . $north_lat . "'
        //                 ELSE property_address_latitude BETWEEN '" . $north_lat . "' AND '" . $south_lat . "' 
        //                 END) 
        //                 AND
        //                 (CASE WHEN '" . $east_long . "' < '" . $west_long . "'
        //                     THEN property_address_longitude BETWEEN '" . $east_long . "' AND '" . $west_long . "'
        //                     ELSE property_address_longitude BETWEEN '" . $west_long . "' AND '" . $east_long . "'
        //                 END)")
        //     );
        // }

        if ($request->input('max_area_sqft')) {
            $properties_query->where('property_sqft_area', '<=', $request->input('max_area_sqft'));
        }

        if ($request->input('max_price')) {
            $properties_query->where('price_area_wise', '<=', $request->input('max_price'));
        }

        if ($request->input('min_price')) {
            $properties_query->where('price_area_wise', '>=', $request->input('min_price'));
        }

        $property_type_id = $request->input('property_type_id');
        if($s_property_type) {
            $get_property_type = PropertyType::where('parent_id', '=', 0)->where('type', 'LIKE', $s_property_type)->latest()->first();

            if($get_property_type) {
                $property_type_id = $get_property_type->id;
            }
        }
        if ($property_type_id) {
            $properties_query->whereIn('property_type', explode(',', $property_type_id));
        }

        if ($request->input('area_id')) {
            $properties_query->where('area_id', '=', $request->input('area_id'));
        }

        $list_type_id = $request->input('list_type_id');
        if($s_property_for) {
            $list_type_id = $s_property_for;
        }
        if($list_type_id == 'buy' || $list_type_id == 'rent') {
            $properties_query->where('property_for', '=', ($list_type_id == 'rent' ? config('constants.PROPERTY_FOR_RENT') : config('constants.PROPERTY_FOR_SALE')) );
        }
        // if ($request->input('property_for_id') != "" && $request->input('property_for_id') != null) {
        //     $properties_query->where('property_for', '=', $request->input('property_for_id'));
        // }

        if ($request->input('bedroom_type_id')) {
            $properties_query->whereIn('bedroom_type', explode(',', $request->input('bedroom_type_id')));
        }

        if ($request->input('bathroom_type_id')) {
            $properties_query->where('bathroom_type', explode(',', $request->input('bathroom_type_id')));
        }

        if ($request->input('bedroom_number')) {
            $properties_query->where('total_bedrooms', '=', $request->input('bedroom_number'));
        }

        if ($request->input('bathroom_number')) {
            $properties_query->where('total_bathrooms', '=', $request->input('bathroom_number'));
        }

        if ($request->input('toilet_number')) {
            $properties_query->where('total_toilets', '=', $request->input('toilet_number'));
        }

        if ($request->input('condition_type_id')) {
            $properties_query->where('condition_type_id', '=', $request->input('condition_type_id'));
        }

        if ($request->input('completion_status_id')) {
            $properties_query->where('completion_status_id', '=', $request->input('completion_status_id'));
        }

        $properties_query = $properties_query->orderBy($sorting['field'], $sorting['sort_type']);

        if ($getMapLocationWithBounds == 1) {

            $properties = $properties_query->paginate($this->property_per_page, ['*'], 'page', $page_no);
            $pagination_data = $properties->toArray();

            if ($pagination_data['total'] > 0) {

                if ($properties->count() == 0) {
                    $properties = $properties_query->paginate($this->property_per_page, ['*'], 'page', $pagination_data['last_page']);

                    $pagination_data = $properties->toArray();
                }
            }
            // get data for map
            $arr = $this->getMapLocationWithBounds($properties, $language_id, $labels);

            // return data 
            return response()->json($arr);
        }

        // dd($properties_query->toSql());
        $properties = $properties_query->paginate($this->property_per_page, ['*'], 'page', $page_no);
        $pagination_data = $properties->toArray();

        $latitude = Helper::getKuwaitLatLong('latitude');
        $longitude = Helper::getKuwaitLatLong('longitude');

        if ($pagination_data['total'] > 0) {

            if ($properties->count() == 0) {
                $properties = $properties_query->paginate($this->property_per_page, ['*'], 'page', $pagination_data['last_page']);

                $pagination_data = $properties->toArray();
            }

            $html = view('frontEnd.property.ajax_list', compact('properties', 'labels', 'language_id', 'list_type', 'user_id', 'pagination_data', 'latitude', 'longitude'))->render();

            $response['statusCode'] = 200;
            $response['list_type'] = $list_type;
            $response['message'] = "";
            $response['html'] = $html;
        } else {
            $response['statusCode'] = 201;
            $response['list_type'] = $list_type;
            $response['message'] = $labels['no_data_is_available'];
            $response['html'] = "";
        }
        return response()->json($response);
    }

    // property listing for map data
    private function getMapLocationWithBounds($properties, $language_id, $labels)
    {
        $property_list = [];
        foreach ($properties as $key => $property) {

            $property_arr = [];
            $property_arr['id']  = $property->id;
            $property_arr['slug']  = $property->slug ?: "";
            $property_arr['property_id']  = $property->property_id ?: "";
            $property_arr['title']  = @$property->property_name ?: "";
            $property_arr['property_for']  = ($property->property_for == config('constants.PROPERTY_FOR_RENT')) ? strtoupper($labels['rent']) : strtoupper($labels['buy']);
            $property_arr['property_for_id']  = (($property->property_for != "" && $property->property_for != null) ? $property->property_for : "");

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

            // set max price and min price
            // $max_price = Helper::tofloat($property_price) > $max_price ? Helper::tofloat($property_price) : $max_price;

            // if (!isset($min_price) || $min_price == "") {
            //     $min_price = Helper::tofloat($property_price);
            // }

            // $min_price = (Helper::tofloat($property_price) < $min_price) ? Helper::tofloat($property_price) : $min_price;

            $property_arr['property_price'] = (@$property->price_area_wise ? (number_format(Helper::tofloat($property->price_area_wise), ($property->areaDetails->country->currency_decimal_point ?: 3))) : 0) . ' ' . $currency;
            $property_arr['bathroom_count'] = $property->total_bathrooms ?: 0;
            $property_arr['bedroom_count'] = $property->total_bedrooms ?: 0;
            $property_arr['toilet_count'] = $property->total_toilets ?: 0;
            $property_arr['area_sqft'] = $property->property_sqft_area ?: 0;

            $property_arr['latitude'] = $property->property_address_latitude ? Helper::tofloat($property->property_address_latitude) : 0;
            $property_arr['longitude'] = $property->property_address_longitude ? Helper::tofloat($property->property_address_longitude) : 0;

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
            $property_arr['image_url'] = $property_image;

            $is_fav = 0;
            if (Auth::guard('web')->check()) {
                $is_fav = UserFavouriteProperty::where('user_id', '=', Auth::guard('web')->id())->where('property_id', '=', $property->id)->exists();
            }
            $property_arr['is_fav'] = $is_fav;

            $property_popup = '<div>
                                    <div class="infowindow_image">
                                        <img src="' . $property_image . '" width="50px" />
                                    </div>
                                    <div class="infowindow_content">
                                        <h4>
                                            <a href="' . route('frontend.property_details', ['id' => $property_arr['slug']]) . '" target="_blank">' . $property_arr['title'] . '</a>
                                        </h4>
                                        <span>' . $short_address . '</span>
                                    </div>
                                </div>';

            $property_arr['property_popup'] = $property_popup;

            $property_list[] = $property_arr;
        }

        return $property_list;
    }

    public function property_details(Request $request, $property_slug = "")
    {
        if ($property_slug == "") {
            return redirect()->route('frontend.not_found');
        }

        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = "";
        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->id();
        }

        // $property = Property::where('slug', '=', $property_slug)
        //             ->where('')
        //             ->where('status', '=', 1); 

        $property_query = Property::with([
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
            ->where('slug', '=', $property_slug)
            ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
                $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
            })
            ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
                $areaDetailsQuery->where('status', '=', 1);
            })
            ->whereHas('agentDetails', function ($agentQuery) {
                $agentQuery->where('status', '=', 1);
            })
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->where('status', '=', 1);

        if ($property_query->count() == 0) {
            return redirect()->route('frontend.not_found');
        }

        $property = $property_query->first();

        $amenities_ids = $property->property_amenities_ids;
        $amenity_arr = [];
        if ($amenities_ids) {
            $amenity_arr = explode(',', $amenities_ids);
        }
        $property_amenities = Amenity::whereIn('id', $amenity_arr)->where('status', '=', 1)->get();


        // SIMILAR PROPERTIES

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
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->where('id', '!=', $property->id)
            ->where('status', '=', 1);

        $similar = $similar->where('property_for', '=', $property->property_for);

        $similar = $similar->where('property_type', '=', $property->property_type);

        $similar = $similar->where('bedroom_type', '=', $property->bedroom_type);

        $similar = $similar->where('bathroom_type', '=', $property->bathroom_type);

        // $similar = $similar->where('total_bedrooms', '=', $property->total_bedrooms);

        // $similar = $similar->where('total_bathrooms', '=', $property->total_bathrooms);

        // $similar = $similar->where('total_toilets', '=', $property->total_toilets);

        $similar = $similar->where('condition_type_id', '=', $property->condition_type_id);

        $similar = $similar->where('completion_status_id', '=', $property->completion_status_id);

        $similar = $similar->where('area_id', '=', $property->area_id);

        $total_similar = $similar->count();

        $similar_properties = $similar->orderBy('id', 'desc')->paginate($this->property_details_similar_product_limit);

        $similar_property_limit = $this->property_details_similar_product_limit;

        $proeprty_deeplink = Helper::getPropertyDeeplink($property->id, $property->slug);
        $PageTitle = @$property->property_name ?: "";
        $PageDescription = @$property->property_description ?: "";
        $PageKeywords = @$property->property_description ?: "";
        $WebmasterSettings = "";

        return view('frontEnd.property.show', compact('language_id', 'labels', 'property', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'proeprty_deeplink', 'property_amenities', 'similar_properties', 'total_similar', 'similar_property_limit'));
    }

    public function getMap(Request $request)
    {
        // url encode the address
        $address = urlencode($request->input('address'));

        // google map geocode api url
        $url = "https://maps.google.com/maps/api/geocode/json?key=".env('GOOGLE_MAPS_KEY')."&address={$address}";
        // get the json response from url
        $resp_json = file_get_contents($url);

        // decode the json response
        $resp = json_decode($resp_json, true);
        // echo "<pre>";
        // print_r($resp);exit();
        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {
            //define empty array
            $data_arr = array();
            // get the important data
            $data_arr['latitude'] = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : '';
            $data_arr['longitude'] = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : '';
            $data_arr['formatted_address'] = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : '';

            // verify if data is exist
            if (!empty($data_arr) && !empty($data_arr['latitude']) && !empty($data_arr['longitude'])) {
                return response()->json($data_arr);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    // report user form submit
    public function report_user(Request $request) {
        $user_id = "";
        if(Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->id();
        }
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $full_name = $request->input('r_name');
        $email = $request->input('r_email');
        $mobile_no = $request->input('r_mobile');
        $message = $request->input('message');
        $country_code = $request->input('country_code');
        $property_id = $request->input('property_id');
        $agent_id = $request->input('agent_id');

        $setting = Setting::find(1);
		$admin_email = $setting->email;

        if($agent_id) {

			$report = new ReportUser;
			$report->uname = $full_name ?: "";
			$report->user_id = $user_id ?: "";
			$report->agent_id = $agent_id;
			$report->email = $email ?: "";
			$report->phone = $mobile_no ?: "";
			$report->country_code = $country_code ?: "";
			$report->report_message = $message ?: "";
	
			$report->save();
			$template_id = 5;
	
			$this->sendEmail($language_id, $admin_email, $email, $full_name, $mobile_no, $country_code, $message, $agent_id, "", "", $template_id);

            $response = [];
            $response['statusCode'] = 200;
            $response['message'] = $labels['user_reported'];
            $response['title'] = $labels['user_reported'];
            $response['url'] = "";
		}
		else {
			$template_id = 9;
			$this->sendEmail($language_id, $admin_email, $email, $full_name, $mobile_no, $country_code, $message, "", "", "", $template_id);
	
            $response = [];
            $response['statusCode'] = 200;
            $response['message'] = $labels['user_reported'];
            $response['title'] = $labels['user_reported'];
            $response['url'] = "";
		}

        return response()->json($response);

    }


    //mail
	public function sendEmail($language_id, $email, $user_email, $name, $mobile_no, $country_code, $report_message, $agent_id ="", $url = "", $logo = "", $id)
	{
		$setting = Setting::find(1);
		// dd($setting);
		$templateData = Helper::getEmailTemplateData($language_id, $id);
		// dd($templateData);

		$from_email = $setting['from_email'];
		$data = array('email' => $email, "user_email" => $user_email, 'name' => $name, 'url' => $url, "phone" => $mobile_no, "country_code" => $country_code, "language_id" => $language_id, 'id' =>  $id, "agent_id" => $agent_id, 'logo' => $logo, 'from_email' => $from_email, "report_message" => $report_message);	
		// try {
		Mail::send('emails.report_agent', $data, function ($message) use ($data, $templateData) {
			$message->to($data['email'], $templateData->title)->subject($templateData->subject);
			$message->from($data['from_email'], 'DOM - Properties');
		});
		// } catch (\Throwable $th) {
		// 	// throw $th;
		// }
	}


    //mail
    public function sendEmailContact($language_id, $email, $user_email, $name, $mobile_no = "", $country_code = "", $inquiry_message, $agent_id, $property_id, $url, $logo, $id)
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

    public function contact_user(Request $request) {
        $property_id = $request->input('property_id');
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $agent_id = $request->input('agent_id');

        $email = $request->input('i_email');
        $full_name = $request->input('i_name');
        $message = $request->input('i_message');
        $mobile_no = "";
        $country_code = "";

        $setting = Setting::find(1);
        $agent = MainUsers::find($agent_id);
        $admin_email = $setting->email;
        $agent_email = urldecode($agent->email);
        $template_id = 7;

        $this->sendEmailContact($language_id, $agent_email, $email, $full_name, $mobile_no, $country_code, $message, $agent_id, $property_id, "", "", $template_id);


        $response = [];
        $response['statusCode'] = 200;
        $response['message'] = $labels['inquiry_for_property_sent_to_agent'];
        $response['title'] = $labels['inquiry_for_property_sent_to_agent'];
        $response['url'] = "";
        return response()->json($response);

    }

    
    public function addRemfavProperty(Request $request)
    {

        $property_id = $request->input('property_id');
        $is_fav = $request->input('is_fav') ?: 0;
        $add_favourite = ($is_fav > 0) ? 0 : 1;

        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $response = [];

        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->id();

            $userFavourite = UserFavouriteProperty::where('user_id', '=', $user_id)->where('property_id', '=', $property_id);

            if ($add_favourite == 0) {
                $userFavourite->delete();

                $response['statusCode'] = 200;
                $response['message'] = $labels['removed_from_favourite'];
                $response['title'] = $labels['removed_from_favourite'];
                $response['url'] = route('frontend.homePage');
                return response()->json($response);
            } else if (!$userFavourite->exists() && $add_favourite == 1) {

                $addFav = new UserFavouriteProperty;
                $addFav->user_id = $user_id;
                $addFav->property_id = $property_id;

                if ($addFav->save()) {

                    $response['statusCode'] = 200;
                    $response['message'] = $labels['added_to_favourite'];
                    $response['title'] = $labels['added_to_favourite'];
                    $response['url'] = "";
                    return response()->json($response);
                } else {
                    $response['statusCode'] = 201;
                    $response['message'] = $labels['failure'];
                    $response['title'] = $labels['failure'];
                    $response['url'] = "";
                    return response()->json($response);
                }
            } else {
                $response['statusCode'] = 200;
                $response['message'] = $labels['already_addeed'];
                $response['title'] = $labels['already_addeed'];
                $response['url'] = "";
                return response()->json($response);
            }
        } else {
            $response = [];
            $response['statusCode'] = 209;
            $response['message'] = $labels['login_required'];
            $response['title'] = $labels['login_required'];
            $response['url'] = route('frontend.login');
            return response()->json($response);
        }
    }

    // add edit property flow
    public function add_property(Request $request, $slug="") {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        // subscription plan list
        $subscription_plans = SubscriptionPlan::with(['childdata' => function ($child) use ($language_id){
            return $child->where('language_id', '=', $language_id);
        }])
        ->where('plan_type', '=', config('constants.AGENTS_TYPE.individual.value'))
        ->where('parent_id', '=', 0)
        ->where('status', '=', 1)
        ->get();

        // Property types list
        $property_types = PropertyType::with(['childdata' => function ($childdata) use($language_id) {
            return $childdata->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->orderBy('type', 'asc')->get();

        // Bedroom type list
        $bedroom_types = BedroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->orderBy('type', 'asc')->get();

        // Bathroom type list
        $bathroom_types = BathroomTypes::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->orderBy('type', 'asc')->get();

        // property conditions types list
        $condition_types = PropertyCondition::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->orderBy('condition_text', 'asc')->get();

        // Property completion statuses list
        $completion_status = PropertyCompletionStatus::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->orderBy('completion_type', 'asc')->get();

        // Amenities list
        $amenities = Amenity::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->where('status','=', 1)->orderBy('amenity_name', 'asc')->get();

        // Area list
        $areas = Area::with(['childdata' => function ($child) use ($language_id) {
            $child->where('language_id', '=', $language_id);
        }])->where('parent_id', '=', 0)->where('status', '=', 1)->orderBy('name', 'asc')->get();

        $userSubscription = UserSubscription::with(['propertiesSubscribed' => function ($properties) {
                return $properties->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
                    ->where('status', '!=', 2);
            }])
            ->whereHas('subscriptionPlanDetails', function($plan){
                return $plan->where('status', '=', 1);
            })
            ->where('user_id', '=', $user_id)
            ->where('end_date', '>=', date('Y-m-d H:i:s'))
            ->latest('id')
            ->first();
        
        $addOns = FeaturedAddons::where('status', '=', 1)->get();

        $property = "";
        if($slug) {
            $property = Property::where('slug', '=', $slug)->where('agent_id', $user_id)->where('status', '=', 1)->latest('id')->first();

            // dd(explode(',', $property->property_amenities_ids));
            if(!$property) {
                return redirect()->route('frontend.account');
            }
        }

        $PageTitle = $property ? $labels['edit_property'] : $labels['add_new_property'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        $propertyimagesLimit = Helper::getMaxImagesUploadLimit();
        return view('frontEnd.property.create', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'subscription_plans', 'property_types','bedroom_types', 'bathroom_types', 'condition_types', 'completion_status', 'amenities', 'areas', 'userSubscription', 'propertyimagesLimit', 'addOns', 'property'));
    }

    public function add_property_submit(Request $request) {

        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $user_id = Auth::guard('web')->id();
        $is_renew = $request->input('is_renew'); // 1- renew plan, 2 - add new plan, 3 - add property in plan
        $property_title = $request->input('property_title');
        $property_for = $request->input('property_for');
        $property_type_id = $request->input('property_type');
        $property_price = $request->input('property_price');
        $area_sqft = $request->input('area_sqft');
        $condition_type_id = $request->input('condition_type_id');
        $completion_status_id = $request->input('completion_status_id');
        $bedroom_type_id = $request->input('bedroom_type');
        $bedroom_numbers = $request->input('total_bedroom');
        $bathroom_type_id = $request->input('bathroom_type');
        $bathroom_numbers = $request->input('total_bathroom');
        $toilet_numbers = $request->input('total_toilets');
        $amenities_ids = $request->input('amenities');
        $area_id = $request->input('area_id');
        $deleted_images = $request->input('is_deleted');
        $property_description = $request->input('description');
        $property_address = $request->input('address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $agent_type = $request->input('agent_type');
        $addon = $request->input('addon');
        $plan_id = @$request->input('plan_id');
        $property_slug = $request->input('property_slug');
        $property_latitude = $request->input('property_latitude');
        $property_longitude = $request->input('property_longitude');

        if($request->input('is_edit') ==1 && $property_slug) {
            $areaDetails = Area::find($area_id);
            $property = [];

            $org_property_price = Helper::getPropertyPriceByPrice($property_price, $areaDetails->default_range, $areaDetails->updated_range);
            $property_price_area_wise = number_format(Helper::tofloat($org_property_price), 3, '.', '');

            $property = Property::where('slug', '=', $property_slug)->latest('id')->first();
            if ($property) {
                $property_id = $property->id;
                if ($request->hasFile('property_images')) {
                    $uploading_images = count($request->file('property_images'));
                    $uploaded_image_count = $property->propertyImages->count();

                    $total = (int) $uploading_images + (int) $uploaded_image_count;
                    if ($total > Helper::getMaxImagesUploadLimit()) {
                        $mainResult['statusCode'] = 202;
                        $mainResult['message'] = $labels['max_image_upload_exceeded'];
                        $mainResult['url'] = "";
                        $mainResult['title'] = $labels['max_image_upload_exceeded'];
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
                $property['property_amenities_ids'] = $amenities_ids ? implode(',', $amenities_ids) : "";
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
                $property['updated_by'] = $user_id;
                $property['property_address_latitude'] = $property_latitude;
                $property['property_address_longitude'] = $property_longitude;
                $property['ip_address'] = $request->getClientIp() ?: null;

                if ($property->save()) {

                    $fetchProperty = Property::find($property->id);

                    if ($request->hasfile('property_images')) {
                        $property_images = [];
                        try {
                            foreach ($request->file('property_images') as $key => $file) {
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
                            // echo ($th->getMessage());
                            throw $th;
                            // exit();
                        }

                    }

                    if ($deleted_images) {
                        $dlt_images = PropertyImages::whereIn('id', $deleted_images )->get();
                        foreach ($dlt_images as $key => $value) {
                            File::delete(public_path('storage/property_images/'  . $property->id . '/' . $value->property_image));
                        }

                        $property_images = PropertyImages::whereIn('id', $deleted_images )->delete();
                    }

                    $mainResult['statusCode'] = 200;
                    $mainResult['message'] = $labels['property_updated_successfully'];
                    $mainResult['url'] = route('frontend.property.my_ads');
                    $mainResult['title'] = $labels['property_updated_successfully'];
                    return response()->json($mainResult);

                } else {
                    $mainResult['statusCode'] = 201;
                    $mainResult['message'] = $labels['something_went_wrong'];
                    $mainResult['url'] = "";
                    $mainResult['title'] = $labels['something_went_wrong'];
                    return response()->json($mainResult);
                }
            } else  {
                $mainResult['statusCode'] = 201;
                $mainResult['message'] = $labels['something_went_wrong'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['something_went_wrong'];
                return response()->json($mainResult);
            }
        }


        $planDetails = SubscriptionPlan::where('parent_id', '=', 0)
            // ->where('status', '=', 1)
            ->find($plan_id);

        if(!$planDetails) {
            $mainResult['statusCode'] = 204;
            $mainResult['message'] = $labels['plan_not_found'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['plan_not_found'];
            return response()->json($mainResult);
        }

        if($planDetails->status != 1) {
            $mainResult['statusCode'] = 204;
            $mainResult['message'] = $labels['plan_is_inactive'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['plan_is_inactive'];
            return response()->json($mainResult);
        }

        $plan_duration = Helper::getValidTillDate(date('Y-m-d H:i:s'), $planDetails->plan_duration_value, $planDetails->plan_duration_type);

        $PaymentID = $request->input('PaymentID');
        $TrackID = $request->input('TrackID');
        $TranID = $request->input('TranID');
        $trnUdf = $request->input('trnUdf');
        $Auth = $request->input('Auth');

        // $userSubscriptionData = UserSubscription::where('user_id', '=', $user_id)->where('plan_id', '=', $plan_id)->latest('id')->first();

        $userSubscriptionData = UserSubscription::with(['propertiesSubscribed' => function ($properties) {
            return $properties->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
                ->where('status', '=', 1);
        }])
        ->whereHas('subscriptionPlanDetails', function($plan){
            return $plan->where('status', '=', 1);
        })
        ->where('user_id', '=', $user_id)
        ->where('end_date', '>=', date('Y-m-d H:i:s'))
        ->latest('id')
        ->first();

        $userSubscriptionId = "";
        $now = date('Y-m-d H:i:s');

        if ($is_renew == 1) {
            if($planDetails->is_free_plan == 1){
                $mainResult['statusCode'] = 203;
                $mainResult['message'] = $labels['not_renewable_plan'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['not_renewable_plan'];
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
            if(@$addon) {

                $addOnData = FeaturedAddons::find($addon);
                $updateUseSubscriptionPlan = UserSubscription::find($userSubscriptionData->id);
                $total_new_price = $updateUseSubscriptionPlan->total_price;

                $defaultSubscriptionNumber = $updateUseSubscriptionPlan->no_of_default_featured_post + $addOnData->no_of_extra_featured_post;

                $updateUseSubscriptionPlan->no_of_default_featured_post = $defaultSubscriptionNumber;
                $updateUseSubscriptionPlan->extra_each_normal_post_price = $addOnData->extra_each_featured_post_price;

                $no_of_plan_post = $updateUseSubscriptionPlan->no_of_plan_post + $addOnData->no_of_extra_featured_post;
                $updateUseSubscriptionPlan->no_of_plan_post = $no_of_plan_post;

                $updateUseSubscriptionPlan->save();

                $userSubscriptionId = $updateUseSubscriptionPlan->id;

            } else{

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
            }

        } else if($is_renew == 3) {

            if($userSubscriptionData->end_date < date('Y-m-d H:i:s')){

                $mainResult['statusCode'] = 205;
                $mainResult['message'] = $labels['plan_expired'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['plan_expired'];
                return response()->json($mainResult);
            }
        }

        $userSubscriptionId = $userSubscriptionData->id;

        $user = MainUsers::find($user_id);
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
        $property['property_amenities_ids'] = @$amenities_ids ? implode(",", $amenities_ids) : "";
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
        $property['is_approved'] = 0;
        $property['property_subscription_enddate'] = $userSubscriptionData->end_date;
        $property['property_address_latitude'] = $property_latitude;
        $property['property_address_longitude'] = $property_longitude;
        // $property['property_subscription_enddate'] = $property_price_area_wise;
        // $property['plan_id'] = $property_price_area_wise;
        $property['status'] = 0;
        $property['language_id'] = $language_id;
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

            $transaction = new Transaction;
            $transaction->trans_no = @$PaymentID ?: "";
            $transaction->property_id = $fetchProperty->id;
            $transaction->subscription_plan_id = $userSubscriptionId;
            $transaction->subscription_type = $userSubscriptionData->id;
            $transaction->agent_id = $user_id;
            $transaction->amount = @$request->input('total_amount_transfer') ?: 0;
            $transaction->area_id = $area_id;
            $transaction->status = 1;

            if ($request->hasFile('property_images')) {
                $property_images = [];
                // try {

                foreach ($request->file('property_images') as $key => $file) {
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

            $logo = asset('assets/frontend/logo/logo.png');
            $url = '';
            $user_email = urldecode(Auth::guard('web')->user()->email);
            $full_name = urldecode(Auth::guard('web')->user()->full_name);
            $mobile_number = Auth::guard('web')->user()->mobile_number;
            $country_code = urldecode(Auth::guard('web')->user()->country_code);
            $template_id = 11;

            $this->sendEmailAddProperty($language_id, $user_email, $full_name, $mobile_number, $country_code, $fetchProperty,$template_id);

            $mainResult['statusCode'] = 200;
            $mainResult['message'] = $labels['property_added_successfully'];
            $mainResult['url'] = route('frontend.property.add.thank_you');
            $mainResult['title'] = $labels['property_added_successfully'];
            return response()->json($mainResult);
        }
        else {
            $mainResult['statusCode'] = 201;
            $mainResult['message'] = $labels['something_went_wrong'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['something_went_wrong'];
            return response()->json($mainResult);
        }

    }

    //mail
	public function sendEmailAddProperty($language_id, $agent_email, $full_name, $mobile_no, $country_code, $property, $template_id, $url = "", $logo = "")
	{
		$setting = Setting::find(1);
        $email = $setting->email;
		// dd($setting);
		$templateData = Helper::getEmailTemplateData($language_id, $template_id);
		// dd($templateData);

		$from_email = $setting['from_email'];
		$data = array('email' => $email, 'full_name' => $full_name, 'agent_email' => $agent_email, 'phone' => $mobile_no, 'country_code' => $country_code, 'url' => $url, 'id' =>  $template_id, 'logo' => $logo, 'from_email' => $from_email, 'property' => $property);
		// try {
		Mail::send('emails.add_property', $data, function ($message) use ($data, $templateData) {
			$message->to($data['email'], $templateData->title)->subject($templateData->subject);
			$message->from($data['from_email'], 'DOM - Properties');
		});
		// } catch (\Throwable $th) {
		// 	// throw $th;
		// }
	}

    public function thank_you_page() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $userSubscription = UserSubscription::where('user_id', '=', $user_id)
        ->where('end_date', '>=', date('Y-m-d H:i:s'))
        ->latest('id')
        ->first();

        $properties = Property::where('agent_id', '=', $user_id)
            ->where('plan_id', '=', $userSubscription->id)
            ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
            ->where('status', '!=', 2)
            ->skip(0)
            ->take(2)
            ->latest('id')
            ->get();

        $PageTitle = $labels['thank_you'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.property.thankyou_page', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'properties', 'user_id', 'userSubscription'));
    }

    public function delete_property(Request $request)
    {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $property_id = $request->input('property_id');

        $property = Property::find($property_id);
        if ($property) {
            $property_images = PropertyImages::where('property_id', $property_id)->get();
            foreach ($property_images as $image) {
                \File::delete(asset('storage/property_images/' . $property_id . '/' . $image->property_image));
            }

            $property_images = PropertyImages::where('property_id', $property_id)->delete();

            $property->status = 2;
            $property->updated_by = $user_id;
            if ($property->save()) {
                $mainResult['statusCode'] = 200;
                $mainResult['message'] = $labels['property_deleted_successfully'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['property_deleted_successfully'];
                return response()->json($mainResult);
            } else {
                $mainResult['statusCode'] = 201;
                $mainResult['message'] = $labels['something_went_wrong'];
                $mainResult['url'] = "";
                $mainResult['title'] = $labels['something_went_wrong'];
                return response()->json($mainResult);
            }
        } else {
            $mainResult['statusCode'] = 202;
            $mainResult['message'] = $labels['property_not_found'];
            $mainResult['url'] = "";
            $mainResult['title'] = $labels['property_not_found'];
            return response()->json($mainResult);
        }
    }

    public function my_ads() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $PageTitle = $labels['my_ads'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.property.my_ads', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
    }

    public function fetch_my_ads(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $page_no = $request->input('page_no') ?: 1;

        $properties = Property::where('agent_id', '=', $user_id)->where('status', '!=', 2);
        $total_records = $properties->count();
        $properties = $properties->latest()->paginate($this->my_ads_per_page, ['*'], 'page', $page_no);

        $html = view('frontEnd.property.loadMyads', compact('properties', 'labels', 'language_id', 'user_id'))->render();

        $mainResult['statusCode'] = 200;
        $mainResult['html'] = $html;
        $mainResult['total_page'] = $properties->lastPage();
        $mainResult['total_records'] = $total_records;
        return response()->json($mainResult);
    }

    public function similar_properties(Request $request, $slug) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $property = Property::where('slug', '=', $slug)
        ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
        })
        ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
            $areaDetailsQuery->where('status', '=', 1);
        })
        ->whereHas('agentDetails', function ($agentQuery) {
            $agentQuery->where('status', '=', 1);
        })
        ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
        ->where('status', '=', 1)
        ->latest('id')
        ->first();

        if($property) {
            $PageTitle = $labels['similar_property'];
            $PageDescription = "";
            $PageKeywords = "";
            $WebmasterSettings = "";
            return view('frontEnd.property.similar', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'property'));
        } 
        else {
            return redirect()->route('frontend.homePage');
        }
    }

    public function fetch_similar_properties(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $page_no = $request->input('page_no') ?: 1;
        $property_slug = $request->input('property_slug');

        $property = Property::where('slug', '=', $property_slug)
        ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
        })
        ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
            $areaDetailsQuery->where('status', '=', 1);
        })
        ->whereHas('agentDetails', function ($agentQuery) {
            $agentQuery->where('status', '=', 1);
        })
        ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
        ->where('status', '=', 1)
        ->latest('id')
        ->first();

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
                ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));

            $similar = $similar->where('property_for', '=', $property->property_for);

            $similar = $similar->where('property_type', '=', $property->property_type);

            $similar = $similar->where('bedroom_type', '=', $property->bedroom_type);

            $similar = $similar->where('bathroom_type', '=', $property->bathroom_type);

            // $similar->where('total_bedrooms', '=', $property->total_bedrooms);

            // $similar->where('total_bathrooms', '=', $property->total_bathrooms);

            // $similar->where('total_toilets', '=', $property->total_toilets);

            $similar = $similar->where('condition_type_id', '=', $property->condition_type_id);

            $similar = $similar->where('completion_status_id', '=', $property->completion_status_id);

            $similar = $similar->where('area_id', '=', $property->area_id);


        $total_records = $similar->count();
        $properties = $similar->latest()->paginate($this->similar_property_per_page, ['*'], 'page', $page_no);

        $html = view('frontEnd.property.load_similar_properties', compact('properties', 'labels', 'language_id', 'user_id'))->render();

        $mainResult['statusCode'] = 200;
        $mainResult['html'] = $html;
        $mainResult['total_page'] = $properties->lastPage();
        $mainResult['total_records'] = $total_records;
        return response()->json($mainResult);
    }

    public function view_agent(Request $request, $agent_id) {
        
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $agent_id = decrypt($agent_id);
        $agent = MainUsers::find($agent_id);
        $list_limit = $this->agent_profile_properties_limit;

        $agent_properties = Property::where('agent_id', '=', $agent_id)
        ->whereHas('subscribedPlanDetails', function ($planQuery) use ($language_id) {
            $planQuery->where('end_date', '>=', date('Y-m-d H:i:s'));
        })
        ->whereHas('areaDetails', function ($areaDetailsQuery) use ($language_id) {
            $areaDetailsQuery->where('status', '=', 1);
        })
        ->whereHas('agentDetails', function ($agentQuery) {
            $agentQuery->where('status', '=', 1);
        })
        ->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'))
        ->where('status', '=', 1);

        $total_properties = $agent_properties->count();
        $agent_properties = $agent_properties->skip(0)->take($list_limit)->latest('id')->get();

        $PageTitle = $labels['view_agent_profile'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.view_agent', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'user_id', 'agent', 'list_limit', 'agent_properties', 'total_properties'));
    }
}
