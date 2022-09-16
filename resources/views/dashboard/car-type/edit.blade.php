@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __('backend.car_management'))
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
        #image{
                margin-top: 10px;
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
                        &#xe02e;</i> Edit {{ __('backend.car_type') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('car-type') }}">{{ __('backend.car_type') }}</a>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('vehicle-modal') }}">
                            <!-- <i class="material-icons md-18">×</i> -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('car-type')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['car-type.update',$car_type[0]->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'cartypeForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.car_type') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="car_type" id="car_type" class="form-control" placeholder="{!!  __('backend.car_type') !!}" onfocus="validateMsgHide('car_type')" value="{{$car_type[0]->car_type}}">
                        </div>
                        @if ($errors->has('car_type'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_car_type" class='validate'>{{ $errors->first('car_type') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.description') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="description" id="description" class="form-control" placeholder="{!!  __('backend.description') !!}" onfocus="validateMsgHide('description')" value="{{$car_type[0]->description}}">
                        </div>
                        @if ($errors->has('description'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_description" class='validate'>{{ $errors->first('description') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.base_fare') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="base_fare" id="base_fare" class="form-control" placeholder="{!!  __('backend.base_fare') !!}"  onfocus="validateMsgHide('base_fare')" value="{{$car_type[0]->base_fare}}">
                        </div>
                        @if ($errors->has('base_fare'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_base_fare" class='validate'>{{ $errors->first('base_fare') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.per_km_charge') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="per_km_charge" id="per_km_charge" class="form-control" placeholder="{!!  __('backend.per_km_charge') !!}"  onfocus="validateMsgHide('per_km_charge')" value="{{$car_type[0]->per_km_charge}}">
                        </div>
                        @if ($errors->has('per_km_charge'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_per_km_charge" class='validate'>{{ $errors->first('per_km_charge') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.per_km_charge_pool') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="per_km_charge_pool" id="per_km_charge_pool" class="form-control" placeholder="{!!  __('backend.per_km_charge_pool') !!}"  onfocus="validateMsgHide('per_km_charge_pool')" value="{{$car_type[0]->per_km_charge_pool}}">
                        </div>
                        @if ($errors->has('per_km_charge_pool'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_per_km_charge_pool" class='validate'>{{ $errors->first('per_km_charge_pool') }}</span>
                                </span>
                            @endif
                    </div>

                   <div class="form-group row">
                        <div class="row">
                            
                        <div class="col-sm-8" style="margin-left: 175px;">
                            @if(isset($car_type[0]->image ) && $car_type[0]->image  != "")
                                    <img id="image_pre" src="{{ $image_url . $car_type[0]->image }}"  width="100px" height="100px"/>
                                     <!-- <button type="button" class="btn btn-dark removeImage"><span class="glyphicon glyphicon-trash"></span> </button> -->
                            @else
                                <img src="{{ asset('uploads/contacts/noimage.png') }}"  width="100px" height="100px" >
                            @endif
                        </div>
                        </div>
                        <label class="col-sm-2 form-control-label">{{ __('backend.topicPhoto') }}</label>
                        <div class="col-sm-10">
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'image', 'accept' => 'image/png, image/gif, image/jpeg']) !!}
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
                            <a href="{{ route('car-type')}}" class="btn btn-default m-t">
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

 
<script type="text/javascript">
    document.getElementById('base_fare').onkeypress = function (e) {

// // 46 is the keypress keyCode for period

// http://www.asquare.net/javascript/tests/KeyCode.html

if (e.keyCode === 46 && this.value.split('.').length === 2) {

return false;

}   
}

 document.getElementById('per_km_charge').onkeypress = function (e) {

// // 46 is the keypress keyCode for period

// http://www.asquare.net/javascript/tests/KeyCode.html

if (e.keyCode === 46 && this.value.split('.').length === 2) {

return false;

}   
}


 document.getElementById('per_km_charge_pool').onkeypress = function (e) {

// // 46 is the keypress keyCode for period

// http://www.asquare.net/javascript/tests/KeyCode.html

if (e.keyCode === 46 && this.value.split('.').length === 2) {

return false;

}   
}

</script>
    <script>
        image.onchange = evt => {
    // alert('hello')
        const [file] = image.files
        if (file) {
            image_pre.src = URL.createObjectURL(file)
        }
        }
        {{-- $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        }); --}}

        {{-- function sendFile(file, editor, welEditable, lang) {
            data = new FormData();
            data.append("file", file);
            data.append("_token", "{{csrf_token()}}");
            $.ajax({
                data: data,
                type: 'POST',
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
                    return myXhr;
                },
                url: "{{ route("topicsPhotosUpload") }}",
                cache: false,
                contentType: false,
                processData: false,
                success: function (url) {
                    var image = $('<img>').attr('src', '{{ asset("uploads/topics/") }}/' + url);
                    @foreach(Helper::languagesList() as $ActiveLanguage)
                        @if($ActiveLanguage->box_status)
                    if (lang == "{{ $ActiveLanguage->code }}") {
                        $('.summernote_{{ $ActiveLanguage->code }}').summernote("insertNode", image[0]);
                    }
                    @endif
                    @endforeach
                }
            });
        } --}}

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
