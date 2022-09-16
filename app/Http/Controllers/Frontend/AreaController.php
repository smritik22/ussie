<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Helpers\Helper;

class AreaController extends Controller
{
    protected $spotlight_per_page;
    protected $indicator_image_url;

    public function __construct()
    {
        $this->spotlight_per_page = 20;
        $this->indicator_image_url = "assets/indicators/";
    }

    public function index(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $PageTitle = $labels['property_areas']; 
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.area.list', compact('PageTitle','PageDescription', 'PageKeywords', 'WebmasterSettings','labels', 'language_id'));
    }

    public function getData(Request $request) {

        $page = @$request->page_no ? : 1;
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);

        $spotlight = Area::with(['childdata' => function ($child) use ($language_id) {
                $child->where('language_id', '=', $language_id);
            }])
            // ->whereHas('properties', function($property) {
            //     $property->where('status', '=', 1)->where('property_subscription_enddate', '>=', date('Y-m-d H:i:s'));
            // })
            ->where('parent_id', '=', 0)
            ->where('status', 1);
        
        $total_spotlight = $spotlight->get()->count();
        $spotlight = $spotlight->orderby('name','asc')
                        ->paginate($this->spotlight_per_page, ['*'], 'page', $page);
        if($spotlight->count() == 0){
            return 2;
        }
        $image_url = 'assets/dashboard/images/areas/';
        $html = view('frontEnd.area.getData', compact('spotlight', 'language_id', 'labels', 'image_url'));
        
        // $spotlight_arr = [];
        // foreach ($spotlight as $key => $value) {
        //     $area_arr = [];
        //     $area_arr['id'] = $value->id;
        //     $area_name = $value->name;
        //     if ($language_id > 1) {
        //         $area_name = @$value->childdata[0]->name ?: $value->name;
        //     }
        //     $area_arr['area_name'] = urldecode($area_name);
        //     $area_arr['area_slug_name'] = $value->slug;
        //     $image_url = "";
        //     if($value->image){
        //         $image_url = asset('assets/dashboard/images/areas/'. $value->image);
        //     }
        //     $area_arr['image_url'] = $image_url;
        //     $spotlight_arr[] = $area_arr;
        // }
        // $response['spotlight'] = $spotlight_arr;

        // $result['code']           = (string) 1;
        // $result['message']        = 'success';
        // $result['total_records']  = (int) $total_spotlight;
        // $result['per_page']       = (int) $this->spotlight_per_page;
        // $result['result'][]       = $response;

        // $mainResult[] = $result;
        // return response()->json($mainResult);

        return $html;
    }
}
