<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use App\Models\WebmasterSection;
use Auth;
use File;
use Illuminate\Config;
use App\Http\Requests\ChangePasswordRequest;
use Redirect;
use Helper;
use Hash;

class UsersController extends Controller
{
    private $uploadPath = "uploads/users/";

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');

        // Check Permissions
        if (@Auth::user()->permissions != 0 && Auth::user()->permissions != 1) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END

        if (@Auth::user()->permissionsGroup->view_status) {
            $Users = User::where('created_by', '=', Auth::user()->id)->orwhere('id', '=', Auth::user()->id)->orderby('id',
                'asc')->paginate(env('BACKEND_PAGINATION'));
            // $Permissions = Permissions::where('created_by', '=', Auth::user()->id)->orderby('id', 'asc')->get();
        } else {
            $Users = User::orderby('id', 'asc')->paginate(env('BACKEND_PAGINATION'));
            // $Permissions = Permissions::orderby('id', 'asc')->get();
        }
        $Permissions = [];
        return view("dashboard.users.list", compact("Users", "Permissions", "GeneralWebmasterSections"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END
        $Permissions = Permissions::orderby('id', 'asc')->get();

        return view("dashboard.users.create", compact("GeneralWebmasterSections", "Permissions"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'photo' => 'mimes:png,jpeg,jpg,gif,svg',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);


        // Start of Upload Files
        $formFileName = "photo";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadPath();
            $request->file($formFileName)->move($path, $fileFinalName_ar);
        }
        // End of Upload Files

        $User = new User;
        $User->name = $request->name;
        $User->email = $request->email;
        $User->password = bcrypt($request->password);
        $User->permissions_id = $request->permissions_id;
        $User->photo = $fileFinalName_ar;
        $User->connect_email = $request->connect_email;
        $User->connect_password = $request->connect_password;
        $User->status = 1;
        $User->created_by = Auth::user()->id;
        $User->save();

        return redirect()->action('Dashboard\UsersController@index')->with('doneMessage', __('backend.addDone'));
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if (@Auth::user()->permissionsGroup->view_status) {
            $Users = User::where('created_by', '=', Auth::user()->id)->orwhere('id', '=', Auth::user()->id)->find($id);
        } else {
            $Users = User::find($id);
        }
        if (!empty($Users)) {
            return view("dashboard.users.edit", compact("Users"));
        } else {
            return redirect()->action('Dashboard\UsersController@index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $User = User::find($id);
        // dd($User->toArray());
        if (!empty($User)) {


            $this->validate($request, [
                'photo' => 'mimes:png,jpeg,jpg,gif,svg',
                'name' => 'required|max:30',
            ]);

            if ($request->email != $User->email) {
                $this->validate($request, [
                    'email' => 'required|email|unique:user_admin',
                ]);
            }
            // Start of Upload Files
            $formFileName = "photo";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(1111,
                        9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $uploadPath = public_path()."/uploads/users/";
                // dd($uploadPath);

                //$path = $this->getUploadPath();

                $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
            }

            // echo "No images";
            // exit();
            // End of Upload Files

            //if ($id != 1) {
                $User->name = $request->name;
                $User->email = $request->email;
                if ($request->password != "") {
                    $User->password = bcrypt($request->password);
                }
            //}
            if ($request->photo_delete == 1) {
                // Delete a User file
                if ($User->photo != "") {
                    File::delete($this->getUploadPath() . $User->photo);
                }

                $User->photo = "";
            }
            if ($fileFinalName_ar != "") {
                // Delete a User file
                if ($User->photo != "") {
                    File::delete($this->getUploadPath() . $User->photo);
                }

                $User->photo = $fileFinalName_ar;
            }

            // $User->connect_email = $request->connect_email;
            // if ($request->connect_password != "") {
            //     $User->connect_password = $request->connect_password;
            // }

            $User->status = $request->status;
            $User->updated_by = Auth::user()->id;
            $User->save();
            return redirect()->action('Dashboard\UsersController@edit', $id)->with('doneMessage', __('backend.saveDone'));
        } else {
            return redirect()->action('Dashboard\UsersController@index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if (@Auth::user()->permissionsGroup->view_status) {
            $User = User::where('created_by', '=', Auth::user()->id)->find($id);
        } else {
            $User = User::find($id);
        }
        if (!empty($User) && $id != 1) {
            // Delete a User photo
            if ($User->photo != "") {
                File::delete($this->getUploadPath() . $User->photo);
            }

            $User->delete();
            return redirect()->action('Dashboard\UsersController@index')->with('doneMessage', __('backend.deleteDone'));
        } else {
            return redirect()->action('Dashboard\UsersController@index');
        }
    }


    /**
     * Update all selected resources in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param buttonNames , array $ids[]
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        //
        if ($request->ids != "") {
            if ($request->action == "activate") {
                User::wherein('id', $request->ids)
                    ->update(['status' => 1]);

            } elseif ($request->action == "block") {
                User::wherein('id', $request->ids)->where('id', '!=', 1)
                    ->update(['status' => 0]);

            } elseif ($request->action == "delete") {
                // Delete User photo
                $Users = User::wherein('id', $request->ids)->where('id', '!=', 1)->get();
                foreach ($Users as $User) {
                    if ($User->photo != "") {
                        File::delete($this->getUploadPath() . $User->photo);
                    }
                }

                User::wherein('id', $request->ids)->where('id', "!=", 1)
                    ->delete();

            }
        }
        return redirect()->action('Dashboard\UsersController@index')->with('doneMessage', __('backend.saveDone'));
    }

    public function changePassword(Request $request)
    {
        //
        // General for all pages
        // $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END
        // $Permissions = Permissions::orderby('id', 'asc')->get();

        return view("dashboard.users.change_password");
    }


    //update password
    public function updatePassword(Request $request)
    {
        // echo "string";exit();
        // echo "<pre>";print_r($request->get('current_password'));exit();
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirmation');
        
        $validatedData = $request->validate([
            'current_password' => 'required',
            // 'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            ],

            [
            'password.confirmed' => 'The new-password and confirm new-password field does not match.'
            ]);
        if (!(Hash::check($request->get('current_password'), Auth::user()->password)))
        {
            // echo "string";exit();
        // The passwords matches
            // dd(5);
        return redirect()->back()->with("errorMessage","Your current password does not match. Please try again.");
        }

        if(strcmp($request->get('current_password'), $request->get('password')) == 0)
        {
        //Current password and new password are same
        return redirect()->back()->with("errorMessage","New Password cannot be same as your current password. Please choose a different password.");
        }
        if ($password != $password_confirm)
        {
           return redirect()->back()->with("errorMessage","Password do not match with comfirm password.");
        }
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return redirect()->back()->with("doneMessage","Password changed successfully !");
    }

}
