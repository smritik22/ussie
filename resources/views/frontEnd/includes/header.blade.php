<header class="{{(@$headerFill ? '' : 'header-fill')}}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="header-inner">
                    <div class="logo">
                        <span class="toggle-mobile-menu d-md-none d-block me-3">
                            <img src="{{ asset('assets/img/three-line.svg') }}" alt="Bar Icon" />
                        </span>
                        <a href="{{ route('frontend.homePage') }}"><img src="{{ asset('assets/img/Logo.svg') }}"
                                alt="logo" /></a>
                    </div>
                    <div class="header-menu">
                        <nav class="navbar navbar-expand-md p-0">
                            <div class="navbar-main-menu">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                        <li class="nav-item">
                                            <a class="nav-link active"
                                                href="{{ route('frontend.propertylist', ['id' => 'buy']) }}">{!! $labels['buy'] !!}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ route('frontend.propertylist', ['id' => 'rent']) }}">{!! $labels['rent'] !!}</a>
                                        </li>
                                        @if (\Auth::guard('web')->check())        
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{route('frontend.property.add')}}">{!! $labels['list_your_property'] !!}</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </nav>
                        <div class="menu-right">
                            <div class="lang">
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ Helper::currentLanguage()->name }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        @foreach (Helper::languagesList() as $item)
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="changeLanguage(event,this,{{ $item->id }})"
                                                    data-sendto="{{ route('frontend.change_language') }}">{!! $item->name !!}</a>
                                            </li>
                                        @endforeach
                                        {{-- <li><a class="dropdown-item" href="#">English</a></li>
                                    <li><a class="dropdown-item" href="#">Arabic</a></li> --}}
                                    </ul>
                                </div>
                            </div>
                            <div class="profile">
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{--  \Auth::guard('web')->id()  --}}
                                        {{--  @if (Auth::guard('web')->check() && Auth::guard('web')->user()->profile_image ) && file_exists(asset('uploads/general_users/'. Auth::guard('web')->user()->profile_image)))
                                            <img src="{{ asset('uploads/general_users/'. Auth::guard('web')->user()->profile_image) }}" alt="" />
                                        @else  --}}
                                            <img src="{{ asset('assets/img/profile.svg') }}" alt="" />
                                        {{--  @endif  --}}
                                        <img class="three-line-icon" src="{{ asset('assets/img/three-line.svg') }}"
                                            alt="icon" />
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        @if (Auth::guard('web')->check())
                                            <li>
                                                <a class="dropdown-item" href="{{route('frontend.account')}}">{!! $labels['account'] !!}</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{route('frontend.subscriptionplans.list')}}">{{ $labels['subscription_plan'] }}</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{route('frontend.property.my_ads')}}">{!! $labels['my_ads'] !!}</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="logoutuser(this)">{!! $labels['logout'] !!}</a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item" href="{{route('frontend.login')}}">{!! $labels['login'] !!}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-mobile-menu">
        <div class="mobile-menu mobile_only">
            <div class="mobile-menu__backdrop"></div>
            <div class="mobile-menu__body">
                <button class="mobile-menu__close" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22.5 10.5001H13.4999V1.49999C13.4999 0.964108 13.2142 0.468904 12.7501 0.200959C12.286 -0.0669863 11.714 -0.0669863 11.2499 0.200959C10.7858 0.468904 10.5001 0.964101 10.5001 1.49999V10.5001H1.49999C0.964108 10.5001 0.468904 10.7858 0.200959 11.2499C-0.0669863 11.714 -0.0669863 12.286 0.200959 12.7501C0.468904 13.2142 0.964101 13.4999 1.49999 13.4999H10.5001V22.5C10.5001 23.0359 10.7858 23.5311 11.2499 23.799C11.714 24.067 12.286 24.067 12.7501 23.799C13.2142 23.5311 13.4999 23.0359 13.4999 22.5V13.4999H22.5C23.0359 13.4999 23.5311 13.2142 23.799 12.7501C24.067 12.286 24.067 11.714 23.799 11.2499C23.5311 10.7858 23.0359 10.5001 22.5 10.5001Z"
                            fill="white" />
                    </svg>
                </button>

                <div class="mobile-menu__panel">
                    <div class="mobile-menu__panel-header">
                        <div class="mobile-menu__panel-title m-0 mobile_logo_box">
                            <div class="top_logo"><a href="{{ route('frontend.homePage') }}"><img
                                        src="{{ asset('assets/img/Logo.svg') }}" alt="" /></a></div>
                        </div>
                    </div>
                    <div class="mobile-menu__panel-body">
                        <ul class="mobile-menu__links">
                            <li data-mobile-menu-item=""><a class="close_menu"
                                    href="{{ route('frontend.propertylist', ['id' => 'buy']) }}">{!! $labels['buy'] !!}</a>
                            </li>
                            <li data-mobile-menu-item=""><a class="close_menu"
                                    href="{{ route('frontend.propertylist', ['id' => 'rent']) }}">{!! $labels['rent'] !!}</a>
                            </li>
                            @if (\Auth::guard('web')->check())                                
                                <li data-mobile-menu-item="">
                                    <a class="close_menu"
                                    href="{{route('frontend.property.add')}}">{!! $labels['list_your_property'] !!}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
