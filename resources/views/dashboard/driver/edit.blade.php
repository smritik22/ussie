@extends('dashboard.layouts.master')
@section('title', __('backend.editDriverUser'))
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
                        &#xe02e;</i> {{ __('backend.editDriverUser') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('driver') }}">{{ __('backend.driver_management') }}</a> / 
                    <span>{{ __('backend.editDriverUser') }}</span>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('driver')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['driver.update',$user->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'userForm' ])}}
                {{ csrf_field() }}
                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.full_name') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('full_name', old('name',urldecode($user->name)), ['class' => 'form-control', 'id' => 'full_name', 'maxlength' => '100']) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('full_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('full_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.email') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::email('email', old('email',urldecode($user->email)), ['class' => 'form-control', 'id' => 'email', 'maxlength' => '100']) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('email'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.mobile_number') !!} <span class="valid_field">*</span></label>

                        
                        <div class="col-sm-8">
                            {!! Form::text('mobile_number', old('mobile_number',urldecode($user->mobile_number)), ['class' => 'form-control', 'id' => 'mobile_number', 'maxlength' => '15']) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('mobile_number'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('mobile_number') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    

                    <div class="form-group row">
                        <div class="row">
                            
                        <div class="col-sm-8" style="margin-left: 175px;">
                            @if(isset($user->customer_image ) && $user->customer_image  != "")
                                    <img id="image_pre" src="{{ $image_url . $user->customer_image }}"  width="100px" height="100px"/>
                                     <!-- <button type="button" class="btn btn-dark removeImage"><span class="glyphicon glyphicon-trash"></span> </button> -->
                            @else
                                <img src="{{ asset('uploads/contacts/noimage.png') }}"  width="100px" height="100px" >
                            @endif
                        </div>
                        </div>
                        <label class="col-sm-2 form-control-label">{{ __('backend.topicPhoto') }}</label>
                        <div class="col-sm-10">
                            {!! Form::file('photo', ['class' => 'form-control', 'id' => 'photo', 'accept' => 'image/png, image/gif, image/jpeg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                            <!-- <input type="hidden" name="image" value="{{ isset($entity->image)?$entity->image:old('image') }}" > -->
                        </div>
                        
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                            <a href="{{ route('driver')}}" class="btn btn-default m-t">
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
    <script src="{{asset('assets/dashboard/js/inputFilter.js')}}"></script>


    <script>
        photo.onchange = evt => {
    // alert('hello')
        const [file] = photo.files
        if (file) {
            image_pre.src = URL.createObjectURL(file)
        }
        }
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


        $(document).ready(function() {
            $("#mobile_number").inputFilter(function(value) {
              return /^\d*$/.test(value);    // Allow digits only, using a RegExp
            });
        });

    </script>
@endpush
