    <meta charset="UTF-8">
    <title>{{ $PageTitle }} {{ $PageTitle != '' ? ' | ' : '' }}
        {{ $labels['site_title'] }}</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $PageDescription }}" />
    <meta name="keywords" content="{{ $PageKeywords }}" />
    <meta name="author" content="{{ URL::to('') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="{{asset('assets/img/favicon.png')}}" type="image/gif">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/owl.carousel.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/ui-slider.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/arabic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/responsive.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/custom.css')}}">

    <!-- Favicon and Touch Icons -->
    @if (Helper::GeneralSiteSettings('style_fav') != '')
        <link href="{{ URL::asset('uploads/settings/' . Helper::GeneralSiteSettings('style_fav')) }}"
            rel="shortcut icon" type="image/png">
    @else
        <link href="{{ URL::asset('/assets/img/favicon.png') }}" rel="shortcut icon" type="image/png">
    @endif

    <meta property='og:title'
        content='{{ $PageTitle }} {{ $PageTitle == '' ? Helper::GeneralSiteSettings('site_title_' . trans('backLang.boxCode')) : '' }}' />
    @if (@$Topic->photo_file != '')
        <meta property='og:image' content='{{ URL::asset('uploads/topics/' . @$Topic->photo_file) }}' />
    @elseif(Helper::GeneralSiteSettings('style_apple') != '')
        <meta property='og:image'
            content='{{ URL::asset('uploads/settings/' . Helper::GeneralSiteSettings('style_apple')) }}' />
    @else
        <meta property='og:image' content='{{ URL::asset('uploads/settings/nofav.png') }}' />
    @endif
    <meta property="og:site_name"
        content="{{ Helper::GeneralSiteSettings('site_title_' . trans('backLang.boxCode')) }}">
    <meta property="og:description" content="{{ $PageDescription }}" />
    <meta property="og:url" content="{{ url()->full() }}" />
    <meta property="og:type" content="website" />
    
    {{-- Google Tags and google analytics --}}
    {{--  @if ($WebmasterSettings->google_tags_status && $WebmasterSettings->google_tags_id != '')
        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{!! $WebmasterSettings->google_tags_id !!}');
        </script>
        <!-- End Google Tag Manager -->
    @endif  --}}
