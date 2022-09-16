<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {

        /*print_r(auth()->guard('main_user')->user());
        exit();*/
        /*if (auth()->guard('web')->check()) {
            return redirect()->route('adminHome');
        } */
        if (auth()->guard('main_user')->check()) {
            return redirect(route('frontend.dashboard'));
        } else {
            // return view('frontEnd.login');
            return redirect()->route('admin.login');
        }
    }


}
