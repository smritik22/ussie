<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Auth;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Config;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
        //
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
        return view("dashboard.categories.list");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.categories.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validateRequest();
        $formFileName = "image";
        $fileFinalName_ar = "";
        
        if (request()->has('image')) 
        {
            
            $avatarName = time().'.'.request()->image->getClientOriginalExtension();
            $file = request()->file('image');
            $name=$avatarName;
            $destinationPath = public_path('uploads/categories');
            // echo "<pre>";print_r($destinationPath);exit();
            $imagePath = $destinationPath. "/".  $name;
            $file->move($destinationPath, $name);
            
            
        }
        
        $categories = new Category();
        $categories->name = $request->name;
        $categories->image = $avatarName;
        $categories->status = 1;
        // $categories->created_at = date('Y-m-d H:i:s');
        
        $categories->save(); 

        return redirect()->route('category')
            ->with('success', $this->title.' '.__('backend.addDone'));
   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = base64_decode($id);
    	// echo "<pre>";print_r($id);exit();
        $categories = Category::find($id);
        $image_url = isset($this->image_uri) ? $this->image_uri : '';
        // echo "<pre>";print_r($image_url);exit();
        return view('dashboard.categories.show', compact('categories','image_url'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = base64_decode($id);
        $categories = Category::where('id',$id)->get();
        $image_url = isset($this->image_uri) ? $this->image_uri : '';
        // echo "<pre>";print_r($vehicle_modal);exit;
        return view('dashboard.categories.edit', compact('categories','image_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($id);
        $categories = Category::find($id);

        $formFileName = "image";
        $fileFinalName_ar = "";

        if (request()->has('image')) 
        {

            $avatarName = time().'.'.request()->image->getClientOriginalExtension();
            $file = request()->file('image');
            $name=$avatarName;
                $destinationPath = public_path('uploads/categories');
                // echo "<pre>";print_r($destinationPath);exit();
                $imagePath = $destinationPath. "/".  $name;
                $file->move($destinationPath, $name);

             $categories->update([                 
                'image' => $avatarName,
            ]);
        }
        $categories->name = $request->name;
        // $categories->image = $avatarName;
        $categories->status = 1;
        $categories->updated_at = date('Y-m-d H:i:s');
        $categories->save(); 

        return redirect()->route('category')->with('success', $this->title.' '.__('backend.saveDone'));
   
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = base64_decode($id);
        $categories = Category::find($id);
        $categories->status = 3;
        $categories->save();
        
        return redirect()->route('category')
            ->with('success', 'Category deleted successfully');
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'name' => 'required',
                'image' => 'mimes:png,jpeg,jpg,gif,svg',
            ]);

        }
        else{

            $validateData =request()->validate([
                'name' => 'required|unique:categories',
                'image' => 'required|mimes:png,jpeg,jpg,gif,svg',
            ]);
            
        }

        return $validateData;
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

        $totalAr = Category::where('status','!=',3);
        if($searchValue!= ""){
            $totalAr = $totalAr->where(function ($query) use($searchValue){
                return $query->where('name', 'LIKE', '%'.$searchValue.'%')
                            ->orWhere('description', 'LIKE', '%'.$searchValue.'%');
                            // ->orWhere(DB::raw("CONCAT(`country_code`, ' ', `mobile_number`)"), 'LIKE','%'.urlencode($searchValue).'%')
                            // ->orWhere('created_at', 'LIKE', '%'.urlencode($searchValue).'%');
            });
        }

        // dd($totalAr->toSql());
        // $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $name = isset($data->name) ? $data->name : '';

            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">deactive</span></i>';
            }

            $show = route('category.show', ['id' => base64_encode($data->id)]);
            $edit = route('category.edit', ['id' => base64_encode($data->id)]);
            $delete = route('category.delete', ['id' => base64_encode($data->id)]);

            $image = $this->no_image;
            if($data->image){
                $checkFile = $this->image_uri . '/' . $data->image;
                $image = $checkFile;
            }
            $image_show = '<a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a>';

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="' . $edit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small></a> ';

            $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($name).'"> <span class="fa fa-trash text-danger"></span> </a> ';
            $date = \Helper::converttimeTozone($data->created_at);
           
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" onchange="checkChange();" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "name" => urldecode($name),
                "image" => $image_show,
                "created_at" => $date,
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
                if($request->status==0){
                    $message = "category inactive successfully.";
                }elseif ($request->status==1) {
                    $message = "category active successfully.";
                }else{
                    $message = "category deleted successfully.";
                }
                    Category::whereIn('id',$ids)->update(['status' => $status]);

                return response()->json(['success' => true,'msg'=>$message]);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
    }
}
