<meta charset="utf-8"/>
<title>@yield('title')- {{ __('backend.ussie_aussie_ride_share') }}</title>
<meta name="description" content=""/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
<link rel="apple-touch-icon" href="{{ asset('assets/frontend/logo/logo.png')}}">
<meta name="apple-mobile-web-app-title" content="Smartend">
{{-- <base href="{{ route('adminHome') }}"> --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="mobile-web-app-capable" content="yes">
<link rel="shortcut icon" sizes="196x196" href="{{ asset('assets/frontend/logo/logo.png')}}">
@stack('before-styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/animate.css/animate.min.css') }}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/animate.css/animate.min.css') }}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/fonts/glyphicons/glyphicons.css') }}" type="text/css"/>
{{-- <link rel="stylesheet" href="{{ asset('assets/dashboard/fonts/font-awesome/css/font-awesome.min.css') }}" type="text/css"/> --}}
<link rel="stylesheet" href="{{ asset('assets/fa/css/all.min.css') }}" type="text/css"/>
{{-- <link rel="stylesheet" href="{{ asset('assets/fa/css/fontawesome.min.css') }}" type="text/css"/> --}}


<link rel="stylesheet" href="{{ asset('assets/dashboard/fonts/material-design-icons/material-design-icons.css') }}"
      type="text/css"/>
{{-- <link rel="stylesheet" href="{{ asset('assets/dashboard/fonts/materialize-v1.0.0/materialize/css/materialize.min.css') }}" type="text/css"/> --}}

<link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap/dist/css/bootstrap.min.css') }}"
      type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/app.css') }}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/font.css') }}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/topic.css') }}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/custom.css') }}" type="text/css"/>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('ckeditor/styles.js') }}"></script>

{{-- @if( @Helper::currentLanguage()->direction=="rtl")
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap-rtl/dist/bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/app.rtl.css') }}">
@endif --}}
@stack('after-styles')
