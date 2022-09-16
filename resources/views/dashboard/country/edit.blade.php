@extends('dashboard.layouts.master')
@section('title', __('backend.edit_country'))
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
                        &#xe02e;</i> {{ __('backend.edit_country') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('country') }}">{{ __('backend.countries') }}</a> / 
                    <span>{{ __('backend.edit_country') }}</span>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('country') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['country.update',$country->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'countryForm' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.country_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="country_name" id="country_name" class="form-control" placeholder="Country Name" value="{{old('country_name',urldecode($country->name))}}" maxlength="100">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('country_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('country_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.country_code') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="country_code" id="country_code" class="form-control" placeholder="Country Code" maxlength="4" value="{{old('country_code',$country->country_code)}}">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('country_code'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('country_code') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.currency_code') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="currency_code" id="currency_code" class="form-control" placeholder="{!!  __('backend.currency_code') !!}" value="{{old('currency_code',$country->currency_code)}}" maxlength="4">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('currency_code'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('currency_code') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.currency_decimal_point') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="currency_decimal_point" id="currency_decimal_point" class="form-control" placeholder="{!!  __('backend.currency_decimal_point') !!}" value="{{old('currency_decimal_point',$country->currency_decimal_point)}}" maxlength="1">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('currency_decimal_point'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('currency_decimal_point') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                            <a href="{{ route('country')}}" class="btn btn-default m-t">
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
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


    <script>
        $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        });

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
