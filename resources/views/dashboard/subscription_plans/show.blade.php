@extends('dashboard.layouts.master')
@section('title', __('backend.view_subscription_plan'))
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
                        &#xe02e;</i> {{ __('backend.view_subscription_plan') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('subscription_plans') }}">{{ __('backend.subscription_plans') }}</a> / 
                   <span>{{__('backend.view_subscription_plan')}}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['subscription_plans'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_name') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$subscription_plan->plan_name}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_description') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$subscription_plan->plan_description}}</label>
                        </div>
                    </div>

                    @if (isset($subscription_plan->childdata) && !empty($subscription_plan->childdata) && count($subscription_plan->childdata) > 0)
                        @foreach ($subscription_plan->childdata as $value)
                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_name') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-9 form-control-label">
                                   <label>{{$value->plan_name}}</label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_description') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-9 form-control-label">
                                   <label>{{$value->plan_description}}</label>
                                </div>
                            </div>
                        @endforeach

                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_duration') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$plan_duration['value'] . " " . $plan_duration['label_value']}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.number_of_ads') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$subscription_plan->no_of_plan_post}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_type') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{ Helper::getLabelValueByKey(config('constants.AGENT_TYPE.'. $subscription_plan->plan_type . '.label_key')) }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.subscription_plan_price') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{ $subscription_plan->plan_price>0 ? $subscription_plan->plan_price . ' KD' : "-" }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.no_of_default_featured_post') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$subscription_plan->no_of_default_featured_post}}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.extra_each_normal_post_price') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{{$subscription_plan->extra_each_normal_post_price}} KD</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.created_on') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{!! Carbon::parse($subscription_plan->created_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.lastUpdated') !!} :</label>
                        <div class="col-sm-9 form-control-label">
                           <label>{!! Carbon::parse($subscription_plan->updated_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>

                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ route('subscription_plans') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
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
