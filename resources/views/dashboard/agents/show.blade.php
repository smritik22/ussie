@extends('dashboard.layouts.master')
@section('title', __('backend.view_agent'))
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
                <h3>{{ __('backend.view_agent') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('agents') }}">{{ __('backend.agents_mngmnt') }}</a> /
                   <span>{{ __('backend.view_agent') }}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['agents'],'method'=>'GET', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label for="photo_file" class="col-sm-2 form-control-label">{!! __('backend.topicPhoto') !!}</label>
                        <div class="col-sm-10">
                            @if ($user->profile_image != '')
                                <div class="row">
                                    <div class="col-sm-12 images">
                                        <div id="user_photo" class="col-sm-4 box p-a-xs">
                                            <a target="_blank" href="{{ $image_url . $user->profile_image }}"><img
                                                    src="{{ $image_url . $user->profile_image }}" class="img-responsive">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                            <div>-</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.full_name') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{urldecode($user->full_name)}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.property_agent_type') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{Helper::getLabelValueByKey(config('constants.AGENT_TYPE.' . $user->agent_type . '.label_key'))}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.email') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{urldecode($user->email)}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.mobile_number') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{ urldecode($user->country_code). ' ' . urldecode($user->mobile_number)}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.agent_total_properties') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{ $user->properties->count()?:0}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.lastUpdated') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{!! Carbon::parse($user->updated_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ route('agents') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
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
