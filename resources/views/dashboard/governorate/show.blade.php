@extends('dashboard.layouts.master')
@section('title', __('backend.view_governorate'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
@endpush
@section('content')
<?php
use Illuminate\Support\Carbon;
?>
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3>{{ __('backend.view_governorate') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('governorate') }}">{{ __('backend.governorates') }}</a> /
                   <span>{{ __('backend.view_governorate') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('governorate') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['governorate'],'method'=>'GET', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label"><b>{!!  __('backend.governorate_name') !!} :</b></label>
                        <div class="col-sm-9 form-control-label">
                           <label><b>{{urldecode($governorate->name)}}</b></label>
                        </div>
                    </div>
                    
                    @if (isset($governorate->childdata) && !empty($governorate->childdata) && count($governorate->childdata) > 0)

                        @foreach ($governorate->childdata as $value)
                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">{!!  __('backend.governorate_name') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-9 form-control-label">
                                   <label>{{urldecode($value->name)}}</label>
                                </div>
                            </div>
                        @endforeach

                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.country_name') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{urldecode($governorate->country->name)}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.lastUpdated') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{!! Carbon::parse($governorate->updated_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                            <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.back') !!}
                        </a>
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
@push("after-scripts")
    <script src="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.js") }}"></script>
    <script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

 

    <script>

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({value: e.loaded, max: e.total});
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
    </script>
@endpush
