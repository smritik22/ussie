<?php
// Current Full URL
$fullPagePath = Request::url();
// Char Count of Backend folder Plus 1
{{--  dd(env('BACKEND_PATH'));  --}}
$envAdminCharCount = strlen(env('BACKEND_PATH')) + 1;
// URL after Root Path EX: admin/home
$urlAfterRoot = substr($fullPagePath, strpos($fullPagePath, env('BACKEND_PATH')) + $envAdminCharCount);
{{-- $mnu_title_var = "title_" . @Helper::currentLanguage()->code;
$mnu_title_var2 = "title_" . env('DEFAULT_LANGUAGE'); --}}
?>

<div id="aside" class="app-aside modal fade folded md nav-expand">
    <div class="left navside dark dk" layout="column">
        <div class="navbar navbar-md no-radius">
            <!-- brand -->
            <a class="navbar-brand text-center logo_css" href="{{ route('adminHome') }}">
                <img src="{{ asset('assets/frontend/logo/ussie_logo.svg') }}" alt="Control">
                <!-- <span class="hidden-folded inline">USSIE-TEXI</span> -->
            </a>
            <!-- / brand -->
        </div>
        <div flex class="hide-scroll">
            <nav class="">

                <ul class="nav" ui-nav>
                    <!-- <li class="nav-header hidden-folded">
                        <small class="text-muted">{{ __('backend.main') }}</small>
                    </li> -->

                    <li
                        class="{{ \Request::route()->getName() == 'adminHome' || \Request::route()->getName() == 'dashboardfilter'? 'active': ' ' }}">
                        <a href="{{ route('adminHome') }}" onclick="location.href='{{ route('adminHome') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/frontend/logo/icon_dashboard.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.dashboard') }}</span>
                        </a>
                    </li>

                    <li
                        class="{{ \Request::route()->getName() == 'passenger' || \Request::route()->getName() == 'passenger.create' || \Request::route()->getName() == 'passenger.edit' || \Request::route()->getName() == 'passenger.destroy' || \Request::route()->getName() == 'passenger.show' || \Request::route()->getName() == 'passenger.ride_list' ? 'active' : ' '}}">
                        <a href="{{ route('passenger') }}" onclick="location.href='{{ route('passenger') }}'">
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/icon_passenger_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.passenger_management') }}</span>
                        </a>
                    </li>

                    <li
                        class="{{ \Request::route()->getName() == 'driver' || \Request::route()->getName() == 'driver.create' || \Request::route()->getName() == 'driver.edit' || \Request::route()->getName() == 'driver.destroy' || \Request::route()->getName() == 'driver.show' || \Request::route()->getName() == 'driver.ride_list' ? 'active' : ' '}}">
                        <a href="{{ route('driver') }}" onclick="location.href='{{ route('driver') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/frontend/logo/icon_driver_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.driver_management') }}</span>
                        </a>
                    </li>

                    <li
                        class="{{ \Request::route()->getName() == 'ride' || \Request::route()->getName() == 'ride.create' || \Request::route()->getName() == 'ride.edit' || \Request::route()->getName() == 'ride.destroy' || \Request::route()->getName() == 'ride.show' ? 'active' : ' '}}">
                        <a href="{{ route('ride') }}" onclick="location.href='{{ route('ride') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/frontend/logo/icon_ride_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.ride_management') }}</span>
                        </a>
                    </li>

                    <!-- <li
                        class="{{ \Request::route()->getName() == 'car-type' || \Request::route()->getName() == 'car-type.create' || \Request::route()->getName() == 'car-type.edit' || \Request::route()->getName() == 'car-type.destroy' || \Request::route()->getName() == 'car-type.show' ? 'active' : ' '}}">
                        <a href="{{ route('car-type') }}" onclick="location.href='{{ route('car-type') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/frontend/logo/icon_car_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.car_management') }}</span>
                        </a>
                    </li> -->

                    <?php
                    $currentFolder = 'banner'; // Put folder name here
                    $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                    
                    $currentFolder2 = 'vehicle'; // Put folder name here
                    $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));
                    
                    $currentFolder3 = 'vehicle-modal'; // Put folder name here
                    $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));
                    
                    $currentFolder4 = 'car-type'; // Put folder name here
                    $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen($currentFolder4));
                    
                    // $currentFolder5 = 'webmaster'; // Put folder name here
                    // $PathCurrentFolder5 = substr($urlAfterRoot, 0, strlen($currentFolder5));
                    
                    // $currentFolder6 = 'certificate'; // Put folder name here
                    // $PathCurrentFolder6 = substr($urlAfterRoot, 0, strlen($currentFolder6));

                    // $currentFolder7 = 'featured-addons'; // Put folder name here
                    // $PathCurrentFolder7 = substr($urlAfterRoot, 0, strlen($currentFolder7));
                    
                    ?>
                    <li
                        {{ $PathCurrentFolder == $currentFolder ||$PathCurrentFolder2 == $currentFolder2 ||$PathCurrentFolder3 == $currentFolder3 ||$PathCurrentFolder4 == $currentFolder4  ? 'class=active': '' }}>
                        <a>
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span class="nav-icon">
                                <img src="{{ asset('assets/frontend/logo/icon_vehicle_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.vehicle_management') }}</span>
                        </a>
                        <ul class="nav-sub">
                            
                            <?php
                            $currentFolder = 'car-type'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{ route('car-type') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.car_type') }}</span>
                                </a>
                            </li>
                            
                            <!-- <?php
                            $currentFolder = 'vehicle'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?> -->
                            <li <?php if (Request::is('admin/vehicle')): ?>
                                {{ Request::is('admin/vehicle') ? 'class=active' : '' }}
                            <?php endif ?>>
                                <a href="{{ route('vehicle') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.Vehicle_type') }}</span>
                                </a>
                            </li>

                           

                            <!-- <?php
                            $currentFolder = 'bedroom-type'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?> -->
                            
<!-- 
                            <?php
                            $currentFolder = 'vehicle-modal'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?> -->
                            <li {{ Request::is('admin/vehicle-modal*') ? 'class=active' : '' }}>
                                <a href="{{ route('vehicle-modal') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.Vehicle_modal') }}</span>
                                </a>
                            </li>


                            

                        </ul>
                    </li>
                    {{-- Categories Management --}}
                    <li
                    {{ $PathCurrentFolder == $currentFolder ||$PathCurrentFolder2 == $currentFolder2 ||$PathCurrentFolder3 == $currentFolder3 ||$PathCurrentFolder4 == $currentFolder4  ? 'class=active': '' }}>
                    <a>
                        <span class="nav-caret">
                            <i class="fa fa-caret-down"></i>
                        </span>
                        <span class="nav-icon">
                            <img src="{{ asset('assets/frontend/logo/category-management-svgrepo-com.svg') }}">
                        </span>
                        <span class="nav-text">{{ __('backend.categories_management') }}</span>
                    </a>
                    <ul class="nav-sub">
                        
                        <?php
                        $currentFolder = 'category'; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        ?>
                        <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                            <a href="{{ route('category') }}" class="sub-link">
                                <span class="nav-text">{{ __('backend.categories') }}</span>
                            </a>
                        </li>

                        <?php
                        $currentFolder = 'subCategory'; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        ?>
                        <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                            <a href="{{ route('subCategory') }}" class="sub-link">
                                <span class="nav-text">{{ __('backend.sub_categories') }}</span>
                            </a>
                        </li>
                        
                    </ul>
                </li>

                    <li
                        class="{{ \Request::route()->getName() == 'promocode' || \Request::route()->getName() == 'promocode.create' || \Request::route()->getName() == 'promocode.edit' || \Request::route()->getName() == 'promocode.destroy' || \Request::route()->getName() == 'promocode.show' ? 'active' : ' '}}">
                        <a href="{{ route('promocode') }}" onclick="location.href='{{ route('promocode') }}'">
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/icon_promocode_management.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.promocode_management') }}</span>
                        </a>
                    </li>

                    <li
                        class="{{ \Request::route()->getName() == 'transaction' || \Request::route()->getName() == 'transaction.create' || \Request::route()->getName() == 'transaction.edit' || \Request::route()->getName() == 'transaction.destroy' || \Request::route()->getName() == 'transaction.show' ? 'active' : ' '}}">
                        <a href="{{ route('transaction') }}" onclick="location.href='{{ route('transaction') }}'">
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/icon_transactions.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.transactions') }}</span>
                        </a>
                    </li>
                    <li
                        class="{{ \Request::route()->getName() == 'notification' || \Request::route()->getName() == 'notification.create' || \Request::route()->getName() == 'notification.edit' || \Request::route()->getName() == 'notification.destroy' || \Request::route()->getName() == 'notification.show' ? 'active' : ' '}}">
                        <a href="{{ route('notification') }}" onclick="location.href='{{ route('notification') }}'">
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/Notification.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.notification_management') }}</span>
                        </a>
                    </li>

                    <li
                        class="{{ \Request::route()->getName() == 'payment' || \Request::route()->getName() == 'payment.create' || \Request::route()->getName() == 'payment.edit' || \Request::route()->getName() == 'payment.destroy' || \Request::route()->getName() == 'payment.show' ? 'active' : ' '}}">
                        <a href="{{ route('payment') }}" onclick="location.href='{{ route('payment') }}'">
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/icon_transactions.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.payment_management') }}</span>
                        </a>
                    </li>
                    
                   <!--  <li
                        class="{{ \Request::route()->getName() == 'vehicle' || \Request::route()->getName() == 'vehicle.create' || \Request::route()->getName() == 'vehicle.edit' || \Request::route()->getName() == 'vehicle.destroy' || \Request::route()->getName() == 'vehicle.show' ? 'active' : ' '}}">
                        <a href="{{ route('vehicle') }}" onclick="location.href='{{ route('vehicle') }}'">
                            <span class="nav-icon">
                                <i class="fa fa-users material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.vehicle_management') }}</span>
                        </a>
                    </li> -->

                    




                    <!-- <li class="{{ \Request::route()->getName() == 'generalusers' || \Request::route()->getName() == 'generaluser.create' || \Request::route()->getName() == 'generaluser.edit' || \Request::route()->getName() == 'generaluser.destroy' || \Request::route()->getName() == 'generaluser.show' ? 'active' : ' ' }}">
                        <a href="{{ route('generalusers') }}" onclick="location.href='{{ route('generalusers') }}'">
                            <span class="nav-icon">
                                <i class="fa-users material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.general_user_mngmnt') }}</span>
                        </a>
                    </li> -->

                   <!--  <li class="{{ \Request::route()->getName() == 'agents' || \Request::route()->getName() == 'agent.create' || \Request::route()->getName() == 'agent.edit' || \Request::route()->getName() == 'agent.destroy' || \Request::route()->getName() == 'agent.show' ? 'active' : ' ' }}">
                        <a href="{{ route('agents') }}" onclick="location.href='{{ route('agents') }}'">
                            <span class="nav-icon">
                                <i class="fa-brands fa-adversal material-icons"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.agents_mngmnt') }}</span>
                        </a>
                    </li>
 -->
                    <!-- <li class="{{ \Request::route()->getName() == 'properties' || \Request::route()->getName() == 'property.create' || \Request::route()->getName() == 'property.edit' || \Request::route()->getName() == 'property.destroy' || \Request::route()->getName() == 'property.show' ? 'active' : ' ' }}">
                        <a href="{{ route('properties') }}" onclick="location.href='{{ route('properties') }}'">
                            <span class="nav-icon">
                                <i class="fa-light fa-building material-icons"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.properties') }}</span>
                        </a>
                    </li> -->

                    <!-- <li class="{{ \Request::route()->getName() == 'transaction' || \Request::route()->getName() == 'transaction.show' ? 'active' : ' ' }}">
                        <a href="{{ route('transaction') }}" onclick="location.href='{{ route('transaction') }}'">
                            <span class="nav-icon">
                                <i class="fa-light fa-right-left material-icons"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.transactions') }}</span>
                        </a>
                    </li>

                    <li class="{{ \Request::route()->getName() == 'subscription_plans' || \Request::route()->getName() == 'subscription_plan.create' || \Request::route()->getName() == 'subscription_plan.edit' || \Request::route()->getName() == 'subscription_plan.destroy' || \Request::route()->getName() == 'subscription_plan.show' ? 'active' : ' ' }}">
                        <a href="{{ route('subscription_plans') }}" onclick="location.href='{{ route('subscription_plans') }}'">
                            <span class="nav-icon">
                                <i class="fa-light fa-rocket material-icons"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.subscription_plans') }}</span>
                        </a>
                    </li> -->
                    
                    <?php
                    $currentFolder = 'passenger-report'; // Put folder name here
                    $PathCurrentFolder = substr($urlAfterRoot, 0, strlen(trim($currentFolder)));

                    $currentFolder2 = 'driver-report'; // Put folder name here
                    $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen(trim($currentFolder2)));

                    $currentFolder3 = 'ride-report'; // Put folder name here
                    $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen(trim($currentFolder3)));

                    $currentFolder4 = 'revenue-report'; // Put folder name here
                    $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen(trim($currentFolder4)));
                    ?>

                   <!--  <li class="nav-header hidden-folded">
                        <small class="text-muted">Report</small>
                    </li> -->

                   <li {{ ( $currentFolder == $PathCurrentFolder || $currentFolder2 == $PathCurrentFolder2 || $currentFolder3 == $PathCurrentFolder3 || $currentFolder4 == $PathCurrentFolder4 )? 'class=active' : '' }}>
                        <a>
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span class="nav-icon">
                                <i class="fa fa-bar-chart material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">Report Management</span>
                        </a>
                        <ul class="nav-sub ">

                            <?php
                            $currentFolder2 = 'passenger-report'; // Put folder name here
                            $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen(trim($currentFolder2)));
                            ?>
                            <li {{ trim($PathCurrentFolder) == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{route('passenger-report')}}" class="sub-link">
                                    <span class="nav-text">{!! __("backend.passenger_report") !!}</span>
                                </a>
                            </li>

                             <?php
                            $currentFolder = 'driver-report'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ trim($PathCurrentFolder) == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{route('driver-report')}}" class="sub-link">
                                    <span class="nav-text">{!! __("backend.driver_report") !!}</span>
                                </a>
                            </li>

                            <?php
                            $currentFolder = 'ride-report'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ trim($PathCurrentFolder) == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{route('ride-report')}}" class="sub-link">
                                    <span class="nav-text">{!! __("backend.ride_report") !!}</span>
                                </a>
                            </li>

                            <?php
                            $currentFolder = 'revenue-report'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ trim($PathCurrentFolder) == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{route('revenue-report')}}" class="sub-link">
                                    <span class="nav-text">{!! __("backend.revenue_report") !!}</span>
                                </a>
                            </li>

                        </ul>

                    </li>

                    

                    <!-- <li class="nav-header hidden-folded">
                        <small class="text-muted">{{ __('backend.settings') }}</small>
                    </li>
 -->
                    <?php
                    $currentFolder = 'banner'; // Put folder name here
                    $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                    
                    $currentFolder2 = 'label'; // Put folder name here
                    $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));
                    
                    $currentFolder3 = 'cms'; // Put folder name here
                    $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));
                    
                    $currentFolder4 = 'emailtemplate'; // Put folder name here
                    $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen($currentFolder4));
                    
                    $currentFolder5 = 'webmaster'; // Put folder name here
                    $PathCurrentFolder5 = substr($urlAfterRoot, 0, strlen($currentFolder5));
                    
                    $currentFolder6 = 'certificate'; // Put folder name here
                    $PathCurrentFolder6 = substr($urlAfterRoot, 0, strlen($currentFolder6));

                    $currentFolder7 = 'featured-addons'; // Put folder name here
                    $PathCurrentFolder7 = substr($urlAfterRoot, 0, strlen($currentFolder7));
                    
                    ?>
                    <li
                        {{ $PathCurrentFolder == $currentFolder ||$PathCurrentFolder2 == $currentFolder2 ||$PathCurrentFolder3 == $currentFolder3 ||$PathCurrentFolder4 == $currentFolder4 ||$PathCurrentFolder5 == $currentFolder5 ||$PathCurrentFolder6 == $currentFolder6 || $currentFolder7 == $PathCurrentFolder7 ? 'class=active': '' }}>
                        <a>
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span class="nav-icon">
                               <img src="{{ asset('assets/frontend/logo/icon_website_settings.svg') }}">
                            </span>
                            <span class="nav-text">{{ __('backend.generalSiteSettings') }}</span>
                        </a>
                        <ul class="nav-sub">
                            

                            <?php
                            $currentFolder = 'label'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{ route('label') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.label') }}</span>
                                </a>
                            </li>

                           

                            <?php
                            $currentFolder = 'bedroom-type'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            

                            <?php
                            $currentFolder = 'cms'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{ route('cms') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.cms') }}</span>
                                </a>
                            </li>
                            <?php
                            $currentFolder = 'emailtemplate'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{ route('emailtemplate') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.emailtemplate') }}</span>
                                </a>
                            </li>

                            

                            <?php
                            $currentFolder = 'webmaster'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li {{ $PathCurrentFolder == $currentFolder ? 'class=active' : '' }}>
                                <a href="{{route('webmasterSettings')}}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.generalSettings') }}</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    

                </ul>
            </nav>
        </div>
    </div>
</div>
