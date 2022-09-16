<!DOCTYPE html>
<html lang="{{ @Helper::currentLanguage()->code }}" dir="{{ @Helper::currentLanguage()->direction }}">

<head>
    @include('frontEnd.includes.head')
    <div class="fullpage-loader" id="full_page_loader">
        <img src="{{ asset('assets/img/full-page1.gif') }}" alt="gif">
    </div>
    <script>
        const base_url = window.location.origin;
        {{-- console.log("Time until head called: ", Date.now()-timerStart); --}}
    </script>

</head>

<body class="{{ \Helper::currentLanguage()->code == 'ar' ? 'arabic' : '' }}">
    <div id="wrapper">
        <!-- start header -->
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
        <!-- end footer -->
    </div>
    @include('frontEnd.includes.foot')
    @yield('footerInclude')
</body>

</html>
