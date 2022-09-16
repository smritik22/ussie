@extends('dashboard.layouts.master')
@section('title', __('backend.edit_subscription_plan'))
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
                        &#xe02e;</i> {{ __('backend.edit_subscription_plan') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('subscription_plans') }}">{{ __('backend.subscription_plans') }}</a> /
                    <span> {{ __('backend.edit_subscription_plan') }}</span>

                </small>
            </div>

            <div class="box-body">
                {{Form::open(['route'=>['subscription_plan.update',$subscription_plan->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'subscription_planForm' ])}}

                @csrf
                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="plan_name" id="plan_name" class="form-control"
                                placeholder="{{ __('backend.subscription_plan_name') }}" value="{{ old('plan_name',@$subscription_plan->plan_name) }}"
                                maxlength="100">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_name'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_description') !!}</label>
                        <div class="col-sm-10">

                            <textarea name="plan_description" id="plan_description" class="form-control" rows="5" placeholder="{{ __('backend.subscription_plan_description') }}">{{ old('plan_description', @$subscription_plan->plan_description) }}</textarea>

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_description'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_description') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_duration') !!}</label>
                        <div class="col-sm-7">
                            <input type="text" name="plan_duration_value" id="plan_duration_value" class="form-control"
                                placeholder="{{ __('backend.subscription_plan_duration') }}" value="{{ old('plan_duration_value', @$subscription_plan->plan_duration_value) }}"
                                maxlength="4" onkeydown="return restrictInput(this,event,'digits')">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_duration_value'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_duration_value') }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-sm-3">
                            <select name="plan_duration_type" id="plan_duration_type" class="form-control" value="{{old('plan_duration_type', @$subscription_plan->plan_duration_type)}}">
                                @foreach (config('constants.PLAN_DURATION_TYPE') as $key => $item)
                                    <option value="{{$key}}" {{old('plan_duration_type', @$subscription_plan->plan_duration_type) == $key? "selected" : "" }}>{{Helper::getLabelValueByKey($item['label_key'])}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--  <div class="col-sm-10">
                            <select name="duration_type" id="duration_type" class="form-control" value="{{old('duration_type')}}">
                                <option value="1">1 Day</option>
                                <option value="2">1 Week</option>
                                <option value="3">1 Month</option>
                                <option value="4">3 Months</option>
                                <option value="5">6 Months</option>
                                <option value="6">1 Year</option>
                            </select>
                        </div>  --}}
                    </div>

                    {{--  <div class="form-group row">  --}}
                        {{--  <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_duration') !!}</label>  --}}
                        {{--  <div class="col-sm-8">
                            <input type="text" name="plan_duration" id="plan_duration" class="form-control"
                                placeholder="{{ __('backend.subscription_plan_duration') }}" value="{{ old('plan_duration') }}"
                                maxlength="4" onkeydown="return restrictInput(this,event,'digits')">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_duration'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_duration') }}</span>
                                @endif
                            </span>
                        </div>  --}}
                        {{--  <div class="col-sm-10">
                            <select name="duration_type" id="duration_type" class="form-control" value="{{old('duration_type')}}">
                                <option value="1" {{(old('duration_type',@$subscription_plan->duration_type) == "1") ? "selected" : " "}}>1 Day</option>
                                <option value="2" {{(old('duration_type',@$subscription_plan->duration_type) == "2") ? "selected" : " "}}>1 Week</option>
                                <option value="3" {{(old('duration_type',@$subscription_plan->duration_type) == "3") ? "selected" : " "}}>1 Month</option>
                                <option value="4" {{(old('duration_type',@$subscription_plan->duration_type) == "4") ? "selected" : " "}}>3 Months</option>
                                <option value="5" {{(old('duration_type',@$subscription_plan->duration_type) == "5") ? "selected" : " "}}>6 Months</option>
                                <option value="6" {{(old('duration_type',@$subscription_plan->duration_type) == "6") ? "selected" : " "}}>1 Year</option>
                            </select>
                        </div>  --}}
                    {{--  </div>  --}}

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.number_of_ads') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="no_of_plan_post" id="no_of_plan_post" class="form-control"
                                placeholder="{{ __('backend.number_of_ads') }}" value="{{ old('no_of_plan_post', @$subscription_plan->no_of_plan_post) }}"
                                maxlength="5">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('no_of_plan_post'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('no_of_plan_post') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.is_featured') !!}</label>
                        <div class="col-sm-10">
                            {!! Form::checkbox('is_featured', 1, old('is_featured' ,  @$subscription_plan->is_featured)?true:false, ['class' => 'from-control-label','style'=> 'margin-top:15px;','id'=> 'is_featured']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_type') !!}</label>
                        <div class="col-sm-10">
                            <select name="plan_type" id="plan_type" class="form-control">
                                <option value="">Select Subscription Type</option>
                                @foreach (config('constants.AGENT_TYPE') as $item)
                                    <option @if(old('plan_type', @$subscription_plan->plan_type) == $item['value']) @selected(true) @endif value="{{$item['value']}}" >{{ Helper::getLabelValueByKey($item['label_key']) }}</option>
                                @endforeach
                            </select>

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_type'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_type') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.subscription_plan_price') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="plan_price" id="plan_price" class="form-control decimal"
                                placeholder="{{ __('backend.subscription_plan_price') }}" value="{{ old('plan_price', (@$subscription_plan->plan_price>0) ?$subscription_plan->plan_price :"") }}"
                                maxlength="15">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('plan_price'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('plan_price') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.is_free_plan') !!}</label>
                        <div class="col-sm-10">
                            {!! Form::checkbox('is_free', 1, (old('is_free', $subscription_plan->is_free) == 1)?true:false, ['class' => 'from-control-label','style'=> 'margin-top:15px;','id'=> 'is_free']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.no_of_default_featured_post') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="no_of_default_featured_post" id="no_of_default_featured_post" class="form-control" onkeydown="return restrictInput(this,event,'digits')"
                                placeholder="{{ __('backend.no_of_default_featured_post') }}" value="{{ old('no_of_default_featured_post', @$subscription_plan->no_of_default_featured_post) }}"
                                maxlength="5">
    
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('no_of_default_featured_post'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('no_of_default_featured_post') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.extra_each_normal_post_price') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="extra_each_normal_post_price" id="extra_each_normal_post_price" class="form-control decimal"
                                placeholder="{{ __('backend.extra_each_normal_post_price') }}" value="{{ old('extra_each_normal_post_price', @$subscription_plan->extra_each_normal_post_price) }}"
                                maxlength="15">
    
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('extra_each_normal_post_price'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('extra_each_normal_post_price') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    
                </div>

                <div class="form-group row m-t-md">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                        <a href="{{ route('subscription_plans')}}" class="btn btn-default m-t">
                            <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.cancel') !!}
                        </a>
                </div>

                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
@push("after-scripts")
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
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
