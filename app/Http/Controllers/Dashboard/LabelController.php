<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
// use App\Models\WebmasterSection;
use App\Models\Label;
use App\Models\Language;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Throwable;
use Yajra\Datatables\Datatables;

class LabelController extends Controller
{

    private $title = "Label";

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
        $labels = Label::orderby('id', 'desc')->get();

        $label = count($labels);
        return view("dashboard.label.list", compact("labels","label"));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if( !(\Helper::check_permission(5,2)) ){
        //     return Redirect::to(route('NoPermission'))->send();
        // }

          // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        return view("dashboard.label.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $label = new Label();
        $this->validateRequest();
        $label->Label_key = $request->label_key;
        $label->Label_value = $request->label_value;
        $label->status = 1;
        $label->parentid = 0;
        $label->language_id = 1;
        $label->created_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('label')
            ->with('success', $this->title.' '.__('backend.addDone'));
    }

    // public function storeLang(Request $request){

    //     $this->validateRequest('',$request->label_language_id);
    //     $parentData = Label::find($request->label_parent_id);
    //     $newUser = Label::updateOrCreate([
    //         'language_id' =>  $request->label_language_id,
    //         'parentid' =>  $parentData->id,
    //     ],[
    //         'labelname' =>  $parentData->labelname,
    //         'labelvalue' =>  $request->label_value,
    //         'status' =>  1,
    //         'created_at' =>  date('Y-m-d H:i:s'),
    //         'updated_at' =>  date('Y-m-d H:i:s'),
    //     ]);
    //     return redirect()->route('label')->with('success', __('backend.saveDone'));
    //     // dd($request);
    //     // $this->validateRequest('',$request->label_language_id);
    //     // $parentData = Label::find($request->label_parent_id);
    //     // $checkExists = Label::where('labelname','',$parentData->labelname)->where('label_language_id','=',$request->label_language_id)->count();


    //     // if($checkExists>0){
    //     //     return redirect();
    //     // }

    //     // try{
    //     //     $label = new Label();
    //     //     $label->language_id = $request->label_language_id;
    //     //     $label->labelname = $parentData->labelname;
    //     //     $label->labelvalue = $request->label_value;
    //     //     $label->parentid = $parentData->id;
    //     //     $label->status = 1;
    //     //     $label->created_at = date('Y-m-d H:i:s');
    //     //     $label->updated_at = date('Y-m-d H:i:s');
    //     //     $label->save();
    //     //     return redirect()->route('label')->with('success', 'Label created successfully.');

    //     // }catch(Throwable $t){
    //     //     dd($t);
    //     // }
    // }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $label = Label::find($id);
        return view('dashboard.label.edit', compact('label'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $this->validateRequest($id);
        $label = Label::find($id);
        //$label->labelname = $request->label_key;
        $label->Label_value = $request->label_value;
        $label->status = 1;
        $label->updated_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('label')->with('success', $this->title.' '.__('backend.saveDone'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $label = Label::find($id);
        $label->status = 2;
        $label->save();
        
        return redirect()->route('label')
            ->with('success', 'Label deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $label = Label::find($id);
        return view('dashboard.label.show', compact('label'));
    }

    public function validateRequest($id="",$childId="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'label_value' => 'required|max:50',
            ]);

        }else if($childId != ''){
            $validateData =request()->validate([
                'label_value' => 'required|max:50',
            ]);
        }
        else{

            $validateData =request()->validate([
                'label_key' => 'required|unique:labels,Label_value,2,status|max:50',
                'label_value' => 'required|max:50',
            ]);
            
        }

        return $validateData;
    }
    public function langedit(Request $request,$parentId,$langId)
    {
        $label = Label::where('parentid','=',$parentId)->where('language_id','=',$langId);
        $islabelExists = $label->count();
        $label_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Label::find($parentId);
        $labelData = [];
        if($islabelExists>0){
            $labelData = $label->first();
        }
        return view('dashboard.label.addLang',compact('languageData','parentData','labelData'));
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
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='id';
        }elseif ($columnIndex==1) {
             $sort='Label_key';
        }elseif ($columnIndex==2) {
            $sort='Label_value';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Label::with('childdata')->where('status','!=','2');
        // echo "<pre>";print_r($totalAr);exit();

        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('Label_key', 'like', '%' . $searchValue . '%')
                     ->orWhere('Label_value', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
          
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $labelShow =  route('label.show',['id'=>$data->id]);
            $labelEdit =  route('label.edit',['id'=>$data->id]);
            $delete = route('label.delete', ['id' => encrypt($data->id)]);
            $options = "";
            // if( \Helper::check_permission(5,1) ){
                $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$labelShow.'" title="Show"> </a>';
            // }

            // if( \Helper::check_permission(5,3) ){
                $options .= '<a class="btn btn-sm success paddingset" href="'.$labelEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($data->Label_value).'"> <span class="fa fa-trash text-danger"></span> </a> ';
            // }
            
            // if( \Helper::check_permission(5,2) && \Helper::check_permission(5,3) ){
                // $language = Language::where('id',">",'1')->get();
                // foreach($language as $k=>$lang) 
                // {
                //     $langEdit =  route('label.editlang',[$data->id,$lang->id]);
                //     $options .= '<a class="btn" href="'.$langEdit.'" title="'.$lang->title.'">'.strtoupper($lang->code).'</a>';
                // }
            // }
            $data_arr[] =array(
              "id" =>   isset($data->id) ? $data->id : '' ,
              "labelname" =>   isset($data->Label_key) ? $data->Label_key : '' ,
              "labelvalue" =>   isset($data->Label_value) ? $data->Label_value : '' ,
              "options" => isset($options) ? $options : '' ,
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
