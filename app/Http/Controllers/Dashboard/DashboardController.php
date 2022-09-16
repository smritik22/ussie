<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\PropertyChart;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Carbon\Carbon;
use DB;
// use App\Charts\UserChart;
use App\Charts\UserProfileChart;
use App\Charts\RevenueChart;
use App\Models\MainUsers;
use App\Models\Property;
use App\Models\RideDetails;
use App\Models\Payment;
// use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Auth;
use Helper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request->date_filter != "") {

            $parts = explode(' - ', $request->date_filter);
            $filterdate = $request->date_filter;


            $start = Carbon::createFromFormat('m/d/Y', $parts[0])->format('Y-m-d');
            $end = Carbon::createFromFormat('m/d/Y', $parts[1])->format('Y-m-d');

            // echo "<pre>";
            // print_r($start);
            // print_r($end);
            // exit();

            // Plateform Users Data
            // $total_users = MainUsers::where('status', '!=', 2)->whereBetween('created_at', [$start, $end])->count();
            // $total_agent_users = MainUsers::where('status', '!=', 2)->whereBetween('created_at', [$start, $end])->where('user_type', '=', config('constants.USER_TYPE_AGENT'))->count();
            // $total_agent_individual = MainUsers::where('status', '!=', 2)->whereBetween('created_at', [$start, $end])->where('user_type', '=', config('constants.USER_TYPE_AGENT'))->count();
            // $total_agent_company = MainUsers::where('status', '!=', 2)->whereBetween('created_at', [$start, $end])->where('user_type', '=', config('constants.USER_TYPE_AGENT'))->count();


            $total_users = MainUsers::where('status', '!=', 2)->where('user_type','!=',2)->whereBetween('created_at', [$start, $end])->count();
            $total_driver_users = MainUsers::where('status', '!=', 2)->where('user_type','!=',1)->whereBetween('created_at', [$start, $end])->count();
            $total_revenue_generate = Payment::where('status', '!=', 2)->select(DB::raw('SUM(total_amount) AS amount_sum'))->whereBetween('created_date', [$start, $end])->get()->toArray();


            $total_ride = RideDetails::where('status', '!=', 2)->whereBetween('created_date', [$start, $end])->count();
            $active_status = [2,4,5,6];
            $total_active_ride = RideDetails::where('status', '!=', 2)->whereIn('ride_status',$active_status)->whereBetween('created_date', [$start, $end])->count();
            // $total_active_ride = RideDetails::where('status', '!=', 2)->where('ride_status','!=','1')->whereBetween('created_date', [$start, $end])->count();

            // Plateform agents properties data
           

            // Subscriptions and revenue
            // $subscriptions_number = UserSubscription::whereBetween('created_at', [$start, $end])->count();
            $subscriptions_number = 0;
            // $revenue_generated = UserSubscription::whereBetween('created_at', [$start, $end])->sum('total_price');
            $revenue_generated = 0;


            /*
            |====================================================
            |
            | CHART & GRAPHS STARTS FROM HERE
            | 
             */

            // Users Graph Data fetch
            $user_customer_active = MainUsers::where('status', '=', 1)->whereBetween('created_at', [$start, $end])->where('user_type','=',1)->count();
            $user_customer_deactive = MainUsers::where('status', '=', 0)->whereBetween('created_at', [$start, $end])->where('user_type','=',1)->count();

            $user_driver_active = MainUsers::where('status', '=', 1)->whereBetween('created_at', [$start, $end])->where('user_type','=',2)->count();
            $user_driver_deactive = MainUsers::where('status', '=', 0)->whereBetween('created_at', [$start, $end])->where('user_type','=',2)->count();

            // Create Users Graph
            $userProfileGraph = new UserProfileChart;

            $userProfileGraph->labels(['Active Customer', 'Inactive Customer', 'Active Driver', 'Inactive Driver']);
            $userProfileGraph->dataset('Users', 'pie', [$user_customer_active, $user_customer_deactive, $user_driver_active, $user_driver_deactive])->color('#fff')->backgroundcolor(['#3699FF', 'red', '#2a4b9b', '#4a1006']);

            // Property Graph fetch data
           

            //Create Property Graph
            

            // REVENUE GRAPH DATA FETCH 
            // $payment_revenue = [];

            // $payment_revenue = Transaction::select(\DB::raw("sum(amount) as revenue"), \DB::raw('DATE_FORMAT(created_at, "%M") as Month'))
            //     ->whereYear('created_at', date('Y'))
            //     ->groupBy(\DB::raw("Month"))
            //     ->get()->toArray();

            $payment_revenue = Payment::select(\DB::raw("sum(total_amount) as revenue"), \DB::raw('DATE_FORMAT(created_date, "%M") as Month'))
                ->whereYear('created_date', date('Y'))
                ->whereBetween('created_date',[$start,$end])
                ->groupBy(\DB::raw("Month"))
                ->get()->toArray();


            $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
            $revenue = [];

            if ($payment_revenue) {
                $mon = array_reduce($payment_revenue, function ($a, $b) {
                    isset($a[$b['Month']]) ? $a[$b['Month']]['revenue'] : $a[$b['Month']] = $b;
                    return $a;
                });

                foreach ($months as $key => $value) {

                    if (isset($mon[$value]['revenue'])) {
                        array_push($revenue, $mon[$value]['revenue']);
                    } else {
                        array_push($revenue, 0);
                    }
                }
            }

            $revenueChart = new RevenueChart;
            $revenueChart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
            $revenueChart->dataset('Monthly Transaction', 'bar', $revenue)->color('#2a4b9b')->backgroundcolor("#2a4b9b");
            $start = $parts[0];
            $end = $parts[1];

            return view('dashboard.home', compact('start', 'end', 'total_users', 'total_driver_users', 'total_revenue_generate', 'total_ride', 'total_active_ride','userProfileGraph','revenueChart'));
        } else {

            $start = '';
            $end = '';

            // Plateform Users Data

            //Shubham Comment 01-07-2022
            $total_users = MainUsers::where('status', '!=', 2)->where('user_type','!=',2)->count();
            $total_driver_users = MainUsers::where('status', '!=', 2)->where('user_type','!=',1)->count();
            $total_revenue_generate = Payment::where('status', '!=', 2)->select(DB::raw('SUM(total_amount) AS amount_sum'))->get()->toArray();

            // $total_revenue_generate_count = 
            // echo "<pre>";print_r($total_revenue_generate);exit();
            $total_ride = RideDetails::where('status', '!=', 2)->count();
            $active_status = [2,4,5,6];
            $total_active_ride = RideDetails::where('status', '!=', 2)->whereIn('ride_status',$active_status)->count();
            // echo "<pre>";print_r($total_active_ride);exit();

            // Subscriptions and revenue
            // $subscriptions_number = UserSubscription::count();
            $subscriptions_number = 0;
            // $revenue_generated = UserSubscription::sum('total_price');
            $revenue_generated = 0;


            /*
            |====================================================
            |
            | CHART & GRAPHS STARTS FROM HERE
            | 
             */

            $start = Carbon::now()->format('m-d-Y');
            $end = Carbon::now()->format('m-d-Y');

            // Users Graph Data fetch
            $user_customer_active = MainUsers::where('status', '=', 1)->where('user_type','=',1)->count();
            $user_customer_deactive = MainUsers::where('status', '=', 0)->where('user_type','=',1)->count();
            // echo "<pre>";print_r($user_customer_deactive);exit();
            $user_driver_active = MainUsers::where('status', '=', 1)->where('user_type','=',2)->count();
            $user_driver_deactive = MainUsers::where('status', '=', 0)->where('user_type','=',2)->count();

            // Create Users Graph
            $userProfileGraph = new UserProfileChart;

            $userProfileGraph->labels(['Active Customer', 'Inactive Customer', 'Active Driver', 'Inactive Driver']);
            $userProfileGraph->dataset('Users', 'pie', [$user_customer_active, $user_customer_deactive, $user_driver_active, $user_driver_deactive])->color('#fff')->backgroundcolor(['#3699FF', 'red', '#2a4b9b', '#4a1006']);

            // Property Graph fetch data
            // $property_rent_active = Property::where('status', '=', 1)->where('property_for', '=', config('constants.PROPERTY_FOR_RENT'))->count();
            // $property_rent_active_deactive = Property::where('status', '=', 0)->where('property_for', '=', config('constants.PROPERTY_FOR_RENT'))->count();

            // $property_sell_active = Property::where('status', '=', 1)->where('property_for', '=', config('constants.PROPERTY_FOR_SALE'))->count();
            // $property_sell_active_deactive = Property::where('status', '=', 0)->where('property_for', '=', config('constants.PROPERTY_FOR_SALE'))->count();

            //Create Property Graph
            // $propertyChart = new PropertyChart;

            // $propertyChart->labels(['Active for Rent', 'Inactive for Rent', 'Active for Sell', 'Inactive for Sell']);
            // $propertyChart->dataset('Properties for Rent & Sell', 'pie', [$property_rent_active, $property_rent_active_deactive, $property_sell_active, $property_sell_active_deactive])->color('#fff')->backgroundcolor(['#3699FF', 'red', '#2a4b9b', '#4a1006']);

            // REVENUE GRAPH DATA FETCH 
            // $payment_revenue = 0;

            $payment_revenue = Payment::select(\DB::raw("sum(total_amount) as revenue"), \DB::raw('DATE_FORMAT(created_date, "%M") as Month'))
                ->whereYear('created_date', date('Y'))
                ->groupBy(\DB::raw("Month"))
                ->get()->toArray();


            $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
            $revenue = [];

            if ($payment_revenue) {
                $mon = array_reduce($payment_revenue, function ($a, $b) {
                    isset($a[$b['Month']]) ? $a[$b['Month']]['revenue'] : $a[$b['Month']] = $b;
                    return $a;
                });

                foreach ($months as $key => $value) {

                    if (isset($mon[$value]['revenue'])) {
                        array_push($revenue, $mon[$value]['revenue']);
                    } else {
                        array_push($revenue, 0);
                    }
                }
            }

            $revenueChart = new RevenueChart;
            $revenueChart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
            $revenueChart->dataset('Monthly Transaction', 'bar', $revenue)->color('#2a4b9b')->backgroundcolor("#2a4b9b");


            return view('dashboard.home', compact('start', 'end', 'total_users', 'total_driver_users', 'total_revenue_generate', 'total_ride', 'total_active_ride','userProfileGraph','revenueChart'));
        }
    }
}
