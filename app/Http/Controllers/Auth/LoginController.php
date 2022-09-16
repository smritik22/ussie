<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use Session;
use DB;
use Mail;
use Cookie;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = 'admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['status' => 1]);
    }

    public function showMainuserLoginForm()
    {
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminHome');
        } else {
            return view('auth.login');
        }
    }


    public function adminLogin(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|email|exists:user_admin,email',
                'password' => 'required',
            ],
            [
                'email.exists' => 'These credentials do not match our records.',
            ]
        );
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $remember_me)) {
            if ($remember_me == true) {
                // echo "string";exit();
                $minutes = 120;

               Cookie::queue('admin_email',  $request->input('email'), $minutes);
             Cookie::queue('admin_password', $request->input('password'), $minutes);
            } else {
                \Cookie::queue(\Cookie::forget('admin_email'));
                \Cookie::queue(\Cookie::forget('admin_password'));
            }
            $user = User::where(["email" => $request->input('email')])->first();
    
            // echo "Cookie<pre>";print_r(Cookie());exit();
            Auth::login($user, $remember_me);
            return redirect(route('adminHome'));
        } else {

            return back()->withInput($request->input())->with('error', 'Your email or password is invalid.');
        }
    }


    public function forgotpass(Request $request)
    {
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminHome');
        } else {
            return view('auth.passwords.email');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mainuserforgot(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $result = $this->validateRequest();
        $exists = User::where('email', '=', $result['email'])->count();

        if ($exists > 0) {

            $User = User::where('email', '=', $result['email'])->first();
            $id = $User->id;
            $password = Str::random(10);

            $updatepsw = User::where('id', $id)->update(array(
                'password' => Hash::make($password),
            ));

            $logo = '';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            // $email = 'svapnil.p@mailinator.com';
            $password = $password;
            $name = $User->name;

            $ismail = $this->attachment_email($email, $password, $name, $url, $logo);

            return back()->with('success', 'Check Your email and get new password.');
        } else {
            // echo "string";exit();
            return back()->with('error', 'Email does not exist in the system.');
        }
    }

    public function attachment_email($email, $password, $name, $url, $logo)
    {
        // echo "string";exit();
        $setting = Setting::find(1);
        // $WebmasterSetting = WebmasterSetting::find(1);
        // echo "<pre>";print_r($WebmasterSetting);exit();
        $from_email = $setting['email'];
        $from_name = urldecode($setting['from_name']);
        // $copyright = urldecode($WebmasterSetting['copyright_en']);
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'url' => $url, 'id' => '2', 'logo' => $logo, 'from_email' => $from_email,'from_name' => $from_name);
        Mail::send('password', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['from_name'])->subject('Password has been reset succesfully!');
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['from_name']);
        });
    }

    public function logoutMainUser()
    {
        Auth::logout();
        // \Cookie::queue(\Cookie::forget('admin_email'));
        // \Cookie::queue(\Cookie::forget('admin_password'));
        Session::flush();
        return redirect()->route('admin.login');
    }

    public function validateRequest()
    {
        $validateData = request()->validate([
            'email' => 'required',
            // 'email' => 'required|email|exists:user_admin',
        ]);
        return $validateData;
    }
}
