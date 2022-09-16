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
                        &#xe02e;</i> Edit {{ __('backend.categories') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('category') }}">{{ __('backend.categories') }}</a>

                </small>
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
                {{Form::open(['route'=>['category.update',$categories[0]->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'cartypeForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.name') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control" placeholder="{!!  __('backend.name') !!}" onfocus="validateMsgHide('name')" value="{{$categories[0]->name}}">
                        </div>
                        @if ($errors->has('name'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_name" class='validate'>{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                    </div>

                   <div class="form-group row">
                        <div class="row">
                            
                        <div class="col-sm-8" style="margin-left: 175px;">
                            @if(isset($categories[0]->image ) && $categories[0]->image  != "")
                                    <img id="image_pre" src="{{ $image_url . $categories[0]->image }}"  width="100px" height="100px"/>
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
