@extends('dashboard.layouts.master')
@section('title', $title )
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <style type="text/css">
        .error {
            color: red;
            margin-left: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="padding edit-package">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">
                        &#xe02e;</i> {!! (isset($subscriptionplanData->plan_name)?__('backend.edit_lang_subscription_plan'):__('backend.add_lang_subscription_plan')); !!}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('subscription_plans') }}">{{ __('backend.subscription_plans') }}</a> / 
                    <span>{!! (isset($subscriptionplanData->type)?__('backend.edit_lang_subscription_plan'):__('backend.add_lang_subscription_plan')); !!}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['subscription_plan.storeLang'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'subscription_planForm' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.language') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="subscription_plan_lang" id="subscription_plan_lang" class="form-control" placeholder="Language" value="{{$languageData->title}}" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.subscription_plan_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="plan_name" id="plan_name" value="{{$parentData->plan_name}}" class="form-control" placeholder="{{__('backend.subscription_plan_name')}}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.subscription_plan_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="plan_name" id="plan_name" class="form-control" value="{{ ( isset( $subscriptionplanData->plan_name ) ? $subscriptionplanData->plan_name : old('plan_name')) }}" placeholder="{{__('backend.subscription_plan_name')}}" dir="{{$languageData->direction}}" >

                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('plan_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('plan_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.subscription_plan_description') !!}</label>
                        <div class="col-sm-10">
                            {!! Form::textarea('plan_description', old('plan_description', @$subscriptionplanData->plan_description), ['dir' => $languageData->direction, 'class' => 'form-control', 'id' => 'plan_description', 'placeholder' => __('backend.subscription_plan_description') , 'rows' => 4]) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('plan_description'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('plan_description') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            {!! Form::hidden( 'subscription_plan_language_id', $languageData->id ) !!}
                            {!! Form::hidden( 'subscription_plan_parent_id', $parentData->id ) !!}
                            {!! Form::hidden( 'subscription_plan_id', ( isset( $subscriptionplanData->id ) ? $subscriptionplanData->id:"") ) !!}
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! (isset($subscriptionplanData->type)?__('backend.update'):__('backend.add')); !!}</button>
                            <a href="{{ route('subscription_plans')}}" class="btn btn-default m-t">
                                <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
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
        {{-- $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        }); --}}

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
    <script type="text/javascript">
       
        
    </script>
@endpush
