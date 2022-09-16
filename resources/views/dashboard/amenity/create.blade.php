@extends('dashboard.layouts.master')
@section('title', __('backend.topicNew') . " " . __('backend.amenity'))
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
                        &#xe02e;</i> {{ __('backend.topicNew') }} {{ __('backend.area') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('amenity') }}">{{ __('backend.amenities') }}</a> / 
                    <span>{{ __('backend.topicNew') }} {{ __('backend.amenity') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('amenity') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['amenity.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'amenityForm' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.amenity_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="amenity_name" id="amenity_name" class="form-control" placeholder="{{__('backend.amenity_name')}}" value="{{old('amenity_name')}}" maxlength="100">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('amenity_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('amenity_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-sm-2 form-control-label">{!! __('backend.aminity_image') !!}</label>
                        <div class="col-sm-10">
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'image', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.aminityImageTypes') !!}
                            </small>
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('image'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('image') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('amenity')}}" class="btn btn-default m-t">
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
@endpush
