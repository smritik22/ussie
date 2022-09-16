@extends('dashboard.layouts.master')
@section('title', __('backend.edit_featured_addons'))
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
                        &#xe02e;</i> {{ __('backend.edit_featured_addons') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('featured_addons') }}">{{ __('backend.featured_addons') }}</a> /
                    <span> {{ __('backend.edit_featured_addons') }}</span>

                </small>
            </div>

            <div class="box-body">
                {{Form::open(['route'=>['featured_addon.update',$addon->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'addonForm' ])}}

                @csrf

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.no_of_extra_featured_post') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="no_of_extra_featured_post" id="no_of_extra_featured_post" class="form-control" onkeydown="return restrictInput(this,event,'digits')"
                                placeholder="{{ __('backend.no_of_extra_featured_post') }}" value="{{ old('no_of_extra_featured_post', @$addon->no_of_extra_featured_post ?: "") }}"
                                maxlength="5">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('no_of_extra_featured_post'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('no_of_extra_featured_post') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.extra_each_featured_post_price') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="extra_each_featured_post_price" id="extra_each_featured_post_price" class="form-control decimal"
                                placeholder="{{ __('backend.extra_each_featured_post_price') }}" value="{{ old('extra_each_featured_post_price', @$addon->extra_each_featured_post_price ?: "") }}"
                                maxlength="15">

                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('extra_each_featured_post_price'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('extra_each_featured_post_price') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    
                </div>

                <div class="form-group row m-t-md">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                        <a href="{{ route('featured_addons')}}" class="btn btn-default m-t">
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
