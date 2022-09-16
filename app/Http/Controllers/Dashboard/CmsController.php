<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\WebmasterSection;
use App\Models\Cms;
use App\Models\Language;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\Datatables\Datatables;

class CmsController extends Controller
{
    private $title = "Cms";

    // Define Default Variables

    public function __construct()
    {
        // if( !(\Helper::check_permission(6,1))) {
        //     return redirect()->route('NoPermission');
        // }
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // if( !(\Helper::check_permission(6,1))) {
        //     return redirect()->route('NoPermission');
        // }

        $cms = Cms::get();

        // echo "<pre>";print_r($cms->toArray());exit();

        $cmsData = count($cms);

          // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        return view("dashboard.cms.list",
            compact("cms","cmsData"));

    }

    public function create()
    {

        // Check Permissions
        // if ( !(\Helper::check_permission(6,2)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }

          // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        return view("dashboard.cms.create");
    }


    public function store(Request $request)
    {       
    //     if ( !(\Helper::check_permission(6,2)) ) {
    //         return Redirect::to(route('NoPermission'))->send();
    //     }

        $this->validateRequest();
        $cms = new Cms();

        $cms->page_title = $request->page_name;
        // $cms->slugged_name = Str::slug($request->page_name,"-", \Helper::currentLanguage()->code);
        $cms->page_content = $request->page_content;
        $cms->status = 1;
        // $cms->create_by = Auth::user()->id;
        // $cms->update_by = Auth::user()->id;
        // $cms->language_id = 1;
        // $cms->parentid = 0;
        // $cms->created_date = date('Y-m-d H:i:s');
        $cms->save(); 
        
        return redirect()->route('cms')->with('success', $this->title.' '.__('backend.addDone'));
    }

    public function storeLang(Request $request){
        // if ( !(\Helper::check_permission(6,2)) || !(\Helper::check_permission(6,3)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }
        $this->validateRequest();
        
        // dd($request->toArray());
        $parentData = Cms::find($request->cms_parent_id);
        $lang_id = $request->cms_language_id;
        $parent_id = $parentData->id;
        $cms_id = $request->cms_page_id;
        $page_name = $request->page_name;
        $content = $request->page_content;

        $side_image_name = "side_image";
        $video = "video";
        $profile_doc = "profile_doc";

        $side_image_final_name_ar = "";

        if ($request->$side_image_name != "") {
            $side_image_final_name_ar = 'about_us_' .time() . rand(1111,
                    9999) . '.' . $request->file($side_image_name)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";
            //$path = $this->getUploadPath();
            $request->file($side_image_name)->move($uploadPath, $side_image_final_name_ar);
            
            $updateDoc2 = Cms::find($cms_id);
            $updateDoc2->document_two = $side_image_final_name_ar;
            $updateDoc2->save();
        }

        $video_name_ar = "";
        if ($request->$video != "") {
            $video_name_ar = 'about_us_Video_' .time() . rand(1111,
                    9999) . '.' . $request->file($video)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";

            //$path = $this->getUploadPath();

            $request->file($video)->move($uploadPath, $video_name_ar);

            $updateURL = Cms::find($cms_id);
            $updateURL->url = $video_name_ar;
            $updateURL->save();
        }

        $profile_doc_name_ar = "";
        if ($request->$profile_doc != "") {
            $profile_doc_name_ar = 'profile_' .time() . rand(1111,
                    9999) . '.' . $request->file($profile_doc)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";

            //$path = $this->getUploadPath();

            $request->file($profile_doc)->move($uploadPath, $profile_doc_name_ar);

            $updateDoc1 = Cms::find($cms_id);
            $updateDoc1->document_one = $profile_doc_name_ar;
            $updateDoc1->save();
        }

        // echo $lang_id . ' : ' . $parent_id;exit();
        // $language = Language::find($lang_id);
        $newUser = Cms::updateOrCreate([
            'language_id' => $lang_id,
            'parentid' => $parent_id,
        ],[
            'page_name' => $page_name,
            // 'slugged_name' => Str::slug($page_name,"-", $language->code),
            'description' => $content,
            'status' => 1,
            'create_by' => Auth::user()->id,
            'update_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            // 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        // dd($newUser->toSql());
        return redirect()->route('cms')->with('success', $this->title.' '.__('backend.addDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if ( !(\Helper::check_permission(6,3)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }

        $cms = Cms::find($id);
        // echo "<pre>";print_r($cms->toArray());exit();
        return view('dashboard.cms.edit', compact('cms'));
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

        // if ( !(\Helper::check_permission(6,3)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }

        // echo "<pre>";print_r($request->toArray());exit();
        
        $this->validateRequest();

        $cms = Cms::find($id);

        $side_image_name = "side_image";
        $video = "video";
        $profile_doc = "profile_doc";

        $side_image_final_name_ar = "";
        if ($request->$side_image_name != "") {
            
            $side_image_final_name_ar = urlencode($cms->page_name) . '_' . time() . rand(1111, 9999) . '.' . $request->file($side_image_name)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";
            $request->file($side_image_name)->move($uploadPath, $side_image_final_name_ar);

            $cms->document_two = $side_image_final_name_ar;
        }

        $video_name_ar = "";
        if ($request->$video != "") {
            $video_name_ar = 'about_us_Video_' .time() . rand(1111,9999) . '.' . $request->file($video)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";
            //$path = $this->getUploadPath();
            $request->file($video)->move($uploadPath, $video_name_ar);

            $cms->url = $video_name_ar;
        }

        $profile_doc_name_ar = "";
        if ($request->$profile_doc != "") {
            $profile_doc_name_ar = 'profile_' .time() . rand(1111, 9999) . '.' . $request->file($profile_doc)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/settings/";
            //$path = $this->getUploadPath();
            $request->file($profile_doc)->move($uploadPath, $profile_doc_name_ar);
            
            $cms->document_one = $profile_doc_name_ar;
        }


        $cms->page_title = $request->page_name;
        // $cms->slugged_name =  Str::slug($request->page_name,"-");
        $cms->page_content = $request->page_content;
        $cms->status = 1;
        // $cms->update_by = Auth::user()->id;
        // $cms->created_date = date('Y-m-d H:i:s');
        // $cms->updated_date = date('Y-m-d H:i:s');
        $cms->save(); 

        return redirect()->route('cms')->with('success', $this->title.' '.__('backend.saveDone'));
    }


    public function destroy($id)
    {
        $id = decrypt($id);
        $cms = Cms::find($id);
        $cms->status = 2;
        $cms->save();
        
        return redirect()->route('cms')
            ->with('success', 'Cms deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if ( !(\Helper::check_permission(6,1)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }

        $cms = Cms::find($id); 
        // echo "<pre>";print_r($cms->toArray());exit();
        // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        return view('dashboard.cms.show',compact('cms'));
    }

    public function cmsedit(Request $request,$parentId,$langId)
    {
        // if ( !(\Helper::check_permission(6,3)) ) {
        //     return Redirect::to(route('NoPermission'))->send();
        // }

        // $cms = Cms::where('parentid','=',$parentId)->where('language_id','=',$langId);
        $cms = Cms::get();
        $iscmsExists = $cms->count();
        $cms_lang = $langId;
        $languageData = Language::find($langId);
        $parentData = Cms::find($parentId);
        $cmsData = [];
        if($iscmsExists>0){
            $cmsData = $cms->first();
            // dd($cmsData->toArray());
        }

        // $except_ids = [2,9,11,13];
        return view('dashboard.cms.addLang',compact('languageData','parentData','cmsData'));
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'page_name' => 'required|max:50',
                'page_content' => 'required',
            ]);

            // if(in_array($id,[2,9,11,13])){
            //     $validateData= request()->validate([
            //         'side_image' => 'mimes:png,jpeg,jpg,gif,svg',
            //         'video' => 'mimes:mp4,mov,ogg,qt|max:20000',
            //         'profile_doc' => 'mimes:png,jpeg,jpg,gif,svg,pdf,doc,docx'
            //     ]);
            // }

        }else{

            $validateData =request()->validate([
                'page_name' => 'required|max:50',
                'page_content' => 'required',
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
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='cms.id';
        }elseif ($columnIndex==1) {
            $sort='cms.page_title';
        }else{
            $sort='cms.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

       /* $data =  Packages::with(['weeks','weeks.shoutout','weeks.takeAction'])->where('status','!=','2');*/

        // $totalAr = Cms::where('status','!=',2)->where('parentid','=',0);
        $totalAr = Cms::where('status','!=',2);

       
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('page_title', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

       /* print_r($totalAr);
        exit;*/
        $data_arr=[];

        foreach ($totalAr as $key => $data) 
        {   
            $cmsshow=  route('cms.show',['id'=>$data->id]);
            $cmsedit =  route('cms.edit',['id'=>$data->id]);
            $delete = route('cms.delete', ['id' => encrypt($data->id)]);
            $options = "";

            // if( \Helper::check_permission(6,1) ){
                $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$cmsshow.'" title="Show"> </a>';
            // }
           
            // if( \Helper::check_permission(6,3) ){
                $options .= '<a class="btn btn-sm success paddingset" href="'.$cmsedit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                $options .= '<a class="btn paddingset delete-data" onclick="deleteData(this);" href="javascript:void(0)" data-href="'.$delete.'" title="Delete" data-name="'.urldecode($data->Label_value).'"> <span class="fa fa-trash text-danger"></span> </a> ';
            // }

            // if(\Helper::check_permission(6,2) && \Helper::check_permission(6,3)){
                $language = Language::where('id',">",'1')->get();
                // foreach($language as $k=>$lang) 
                // {
                //     $langEdit =  route('cms.editCms',[$data->id,$lang->id]);
                //     $options .= '<a class="btn" href="'.$langEdit.'" title="'.$lang->title.'">'.strtoupper($lang->code).'</a>';
                // }
            // }

            $data_arr[] =array(
              "name" =>   isset($data->page_title) ? $data->page_title: '',
            //   "description" =>  isset($data->description) ? $data->description : '',
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
}
