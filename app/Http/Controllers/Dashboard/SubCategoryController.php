<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Auth;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Config;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Setting;

class SubCategoryController extends Controller
{
    private $uploadPath = "uploads/categories/";
    protected $image_uri = "";
    protected $no_image = "";
    private $title = "Categories";
 
    // Define Default Variables
 
    public function __construct()
    {
        $this->middleware('auth');
        $this->setImagePath();
        $this->no_image = asset('assets/dashboard/images/no_image.png');
 
        // Check Permissions
        if (@Auth::user()->permissions != 0 && Auth::user()->permissions != 1) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }
 
     public function getImagePath(){
        return asset($this->uploadPath);
    }
 
    public function setImagePath(){
        $this->image_uri = $this->getImagePath() . '/';
    }
 
 
     public function getUploadPath()
    {
        return $this->uploadPath;
    }
 
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("dashboard.subCategories.list");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    	$category = Category::orderby('id','desc')->where('status',1)->get();
        return view("dashboard.subCategories.create", compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest();
        $subCategory = new SubCategory();
        // $validations = $this->validateRequest($request);
        // if ($validations->fails()) {
        //     return redirect()
        //                 ->back()
        //                 ->withErrors($validations)
        //                 ->withInput();
        // }      
        $subCategory->name = $request->name;
        $subCategory->category_id = $request->category_id;
        $subCategory->status = 1;
        $subCategory->created_at = date('Y-m-d H:i:s');
        $subCategory->save(); 

        return redirect()->route('subCategory')
            ->with('success', $this->title.' '.__('backend.addDone'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = base64_decode($id);
        $sub_category = DB::table('sub_categories')
        ->leftjoin('categories','categories.id','=','sub_categories.category_id')
        ->select('sub_categories.*','categories.name as categoryname')
        ->where('sub_categories.id',$id)
        ->get();
        return view('dashboard.subCategories.show', compact('sub_category'));
   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = base64_decode($id);
        $sub_category = DB::table('sub_categories')
        ->leftjoin('categories','categories.id','=','sub_categories.category_id')
        ->select('sub_categories.*','categories.name as categoryname')
        ->where('sub_categories.id',$id)
        ->get();

        $category = Category::orderby('id','desc')->where('status',1)->get();
        // echo "<pre>";print_r($category);exit;
        return view('dashboard.subCategories.edit', compact('sub_category','category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($id);
        $sub_category = SubCategory::find($id);
        //$label->labelname = $request->label_key;
        $sub_category->name = $request->name;
        $sub_category->category_id = $request->category_id;
        $sub_category->status = 1;
        $sub_category->updated_at = date('Y-m-d H:i:s');
        $sub_category->save(); 

        return redirect()->route('subCategory')->with('success', $this->title.' '.__('backend.saveDone'));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = base64_decode($id);
        $categories = SubCategory::find($id);
        $categories->status = 3;
        $categories->save();
        
        return redirect()->route('subCategory')
            ->with('success', 'SubCategory  deleted successfully');
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'name' => 'required',
               'category_id' => 'required',
            ]);

        }
        else{

            $validateData =request()->validate([
                'name' => 'required|unique:sub_categories',
                'category_id' => 'required',
            ]);
            
        }

        return $validateData;
    }


    public function anyData(Request $request)
    {
        // echo "<pre>"; print_r($request); exit;
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
        $ride_status = $request->get('ride_status');
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'name';
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = DB::table('sub_categories')
        ->leftjoin('categories','categories.id','=','sub_categories.category_id')
        ->select('sub_categories.*','categories.name as categoryname')
    	->where('sub_categories.status','!=',3);
        // echo "<pre>";print_r($totalAr->toArray());exit();

        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('sub_categories.name', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('categories.name', 'LIKE', '%'.$searchValue.'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $categoryname = isset($data->categoryname) ? $data->categoryname : '';
            $name = isset($data->name) ? $data->name : '';

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('subCategory.show', ['id' => base64_encode($data->id)]);
            $edit = route('subCategory.edit', ['id' => base64_encode($data->id)]);
            $delete = route('subCategory.delete', ['id' => base64_encode($data->id)]);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($name).'"> <span class="fa fa-trash text-danger"></span> </a> ';
            $date = \Helper::converttimeTozone($data->created_at);
            // $aminity_image = $this->no_image;
            // if($data->image){
            //     $checkFile = $this->_image_uri . '/' . $data->image;
            //     $aminity_image = $checkFile;
            // }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "categoryname" => urldecode($categoryname),
                "name" => urldecode($name),
                "created_at" => $date,
                // "mobile_number" => $phone,
                // "join_date" => Carbon::parse($data->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A'),
                // "is_driver" => $is_driver,
                "status" => $status,
                "options" => $options,
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

    public function updateAll(Request $request)
    {
        if($request->ajax())
        {
            // echo "<pre>";print_r($request->toArray());exit();
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                if($request->status==2){
                    $message = "Sub Category inactive successfully.";
                }elseif ($request->status==1) {
                    $message = "Sub Category active successfully.";
                }else{
                    $message = "Sub Category deleted successfully.";
                }
                    SubCategory::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
    }
}
