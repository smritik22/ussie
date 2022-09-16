@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __('backend.categories_management'))
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
                        &#xe02e;</i> {{ __('backend.categories') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('category') }}">{{ __('backend.categories') }}</a>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('category') }}">
                            <!-- <i class="material-icons md-18">×</i> -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('category')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['category.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'categoriesForm' ])}}

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
                        <label for="image" class="col-sm-2 form-control-label">{!! __('backend.image') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'image', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                                @if(!empty(@$errors) && @$errors->has('image'))
                            <span class="help-block">
                                    <span  style="color: red;" id="error_image" class='validate'>{{ $errors->first('image') }}</span>
                            </span>
                                @endif
                        </div>
                    </div>


                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('category')}}" class="btn btn-default m-t">
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

$(document).ready(function () {   
$('#submitDetail').on('click', function () {   
var myForm = $("form#categoriesForm");   
if (myForm) {   
$(this).prop('disabled', true);   
$(myForm).submit();   
}   
});   
});   
</script>


 
<!--   <script type="text/javascript">
        function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
    // console.log(charCode); 
    // return false;
    return !(charCode > 31 && (charCode < 46 || charCode > 57));
}
    </script> -->
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
