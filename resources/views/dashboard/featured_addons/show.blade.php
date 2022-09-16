@extends('dashboard.layouts.master')
@section('title', __('backend.view_featured_addons'))
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
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view_featured_addons') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('featured_addons') }}">{{ __('backend.featured_addons') }}</a> / 
                   <span>{{__('backend.view_featured_addons')}}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['featured_addons'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.no_of_extra_featured_post') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$addon->no_of_extra_featured_post}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.extra_each_featured_post_price') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$addon->extra_each_featured_post_price}} {{Helper::getDefaultCurrency()}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.created_on') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{!! Carbon::parse($addon->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.lastUpdated') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{!! Carbon::parse($addon->updated_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>

                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ route('featured_addons') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
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
