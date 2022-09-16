@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __('backend.passenger_management'))
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
                <?php
                $title_var = "title_" . @Helper::currentLanguage()->code;
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                ?>
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.newPassengerAdd') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('passenger') }}">{{ __('backend.passenger_management') }}</a>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('passenger') }}">
                            <!-- <i class="material-icons md-18">×</i> -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('passenger')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['passenger.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'passengerForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->

                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.fullName') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control" onfocus="validateMsgHide('name')" placeholder="{!!  __('backend.fullName') !!}" value="{{old('name')}}">
                        </div>
                        @if ($errors->has('name'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_name" class='validate'>{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.email') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" class="form-control" onfocus="validateMsgHide('email')" placeholder="{!!  __('backend.email') !!}" value="{{old('email')}}">
                        </div>
                        @if ($errors->has('email'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_email" class='validate'>{{ $errors->first('email') }}</span>
                                </span>
                            @endif
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.mobile_number') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control"  onfocus="validateMsgHide('mobile_number')" placeholder="{!!  __('backend.mobile_number') !!}" value="{{old('mobile_number')}}">
                        </div>
                        @if ($errors->has('mobile_number'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_mobile_number" class='validate'>{{ $errors->first('mobile_number') }}</span>
                                </span>
                            @endif
                    </div>
                    <div class="form-group row">
                        <label for="image" class="col-sm-2 form-control-label">{!! __('backend.image') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::file('customer_image', ['class' => 'form-control', 'id' => 'customer_image', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                                @if(!empty(@$errors) && @$errors->has('customer_image'))
                            <span class="help-block">
                                    <span  style="color: red;" id="error_image" class='validate'>{{ $errors->first('customer_image') }}</span>
                            </span>
                                @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="file_1" class="col-sm-2 form-control-label">{!! __('backend.file_1') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::file('file_1', ['class' => 'form-control', 'id' => 'file_1', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                                @if(!empty(@$errors) && @$errors->has('file_1'))
                            <span class="help-block">
                                    <span  style="color: red;" id="error_file_1" class='validate'>{{ $errors->first('file_1') }}</span>
                            </span>
                                @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="file_2" class="col-sm-2 form-control-label">{!! __('backend.file_2') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::file('file_2', ['class' => 'form-control', 'id' => 'file_2', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                                @if(!empty(@$errors) && @$errors->has('file_2'))
                            <span class="help-block">
                                    <span  style="color: red;" id="error_file_1" class='validate'>{{ $errors->first('file_2') }}</span>
                            </span>
                                @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.gender') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            Male:<input type="radio" name="gender" id="male" value="male" checked>&nbsp;
                            Female:<input type="radio" name="gender" id="female" value="female">
                        </div>
                        @if ($errors->has('gender'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_gender" class='validate'>{{ $errors->first('gender') }}</span>
                                </span>
                            @endif
                    </div>

                     
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.address') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="address" id="address-input" class="form-control map-input" onfocus="validateMsgHide('address')" placeholder="{!!  __('backend.address') !!}" value="{{old('address')}}">
                            <input type="hidden" name="address_latitude" id="address_latitude" value="0">
                            <input type="hidden" name="address_longitude" id="address_longitude" value="0">
                        </div>
                        @if ($errors->has('address'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_address" class='validate'>{{ $errors->first('address') }}</span>
                                </span>
                            @endif
                    </div>
                    {{-- <div id="address-map-container" style="width:100%;height:400px; ">
                        <div style="width: 100%; height: 100%" id="address-map"></div>
                    </div> --}}

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.hobbies') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                             <input type="checkbox" name="hobbies[]" id="music"   value="music" >Music &nbsp;
                             <input type="checkbox" name="hobbies[]" id="sports"    value="sports">Sports &nbsp;
                             <input type="checkbox" name="hobbies[]" id="book"   value="book" >Book Reading &nbsp;
                             <input type="checkbox" name="hobbies[]" id="traveling"    value="traveling">Traveling &nbsp;
                             <input type="checkbox" name="hobbies[]" id="art"   value="art" >Art 
                        </div>
                        @if ($errors->has('hobbies'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_hobbies" class='validate'>{{ $errors->first('hobbies') }}</span>
                                </span>
                            @endif
                    </div>


                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('passenger')}}" class="btn btn-default m-t">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script> --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places" async defer></script>
<script type="text/javascript">


$(document).ready(function () {   
$('#submitDetail').on('click', function () {   
var myForm = $("form#passengerForm");   
if (myForm) {   
$(this).prop('disabled', true);   
$(myForm).submit();   
}   
});   
});   
</script>

    {{-- <script>
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
    </script> --}}
    <script type="text/javascript">

        var searchInput = 'address-input';

        $(document).ready(function () {
            var autocomplete;
            autocomplete = new google.maps.places.Autocomplete((document.getElementById(searchInput)), {
                types: ['geocode'],
            });
            
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var near_place = autocomplete.getPlace();
                document.getElementById('address_latitude').value = near_place.geometry.location.lat();
                document.getElementById('address_longitude').value = near_place.geometry.location.lng();
                
                // document.getElementById('latitude_view').innerHTML = near_place.geometry.location.lat();
                // document.getElementById('longitude_view').innerHTML = near_place.geometry.location.lng();
            });
        });        
    </script>
@endpush
