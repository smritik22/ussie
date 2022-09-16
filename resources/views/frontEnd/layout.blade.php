<!DOCTYPE html>
<html lang="{{ @Helper::currentLanguage()->code }}" dir="{{ @Helper::currentLanguage()->direction }}">
<head>
    @include('frontEnd.includes.head')
    {{-- @include('frontEnd.includes.colors') --}}
    <div class="fullpage-loader" id="full_page_loader">
        <img src="{{asset('assets/img/full-page1.gif')}}" alt="gif">
    </div>
    <script>
        const base_url = window.location.origin;
        {{-- console.log("Time until head called: ", Date.now()-timerStart); --}}
    </script>

</head>

<body class="{{ \Helper::currentLanguage()->code == 'ar' ? 'arabic' : '' }}">
    <div id="wrapper">
        <!-- start header -->
        @include('frontEnd.includes.header')
        <!-- end header -->
        <div name="location" class="d-none" id="location"></div>
        <input type="hidden" name="cur_latitude" id="cur_latitude">
        <input type="hidden" name="cur_longitude" id="cur_longitude">

        <!-- Content Section -->
        <div class="contents">
            @yield('content')
        </div>
        <!-- end of Content Section -->

        <!-- start footer -->
        @include('frontEnd.includes.footer')
        <!-- end footer -->
    </div>
    @include('frontEnd.includes.foot')
    @yield('footerInclude')

    @if (Helper::GeneralSiteSettings('style_preload'))
        <div id="preloader"></div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $(window).load(function() {
                    $('#preloader').fadeOut('slow', function() {
                        // $(this).remove();
                    });
                });
            });
        </script>
    @endif
    @if (Helper::GeneralSiteSettings('style_header'))
        <script type="text/javascript">
            window.onscroll = function() {
                myFunction()
            };
            var header = document.getElementsByTagName("header")[0];
            var sticky = header.offsetTop;

            function myFunction() {
                if (window.pageYOffset >= sticky) {
                    header.classList.add("sticky");
                } else {
                    header.classList.remove("sticky");
                }
            }
        </script>
    @endif
</body>

</html>
