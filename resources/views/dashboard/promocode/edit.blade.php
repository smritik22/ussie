@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __('backend.promocode_management'))
@push("after-styles")
<!-- <style type="text/css">
    .help-block{
        margin-left: 15px;
    }
</style> -->
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
                        &#xe02e;</i> Edit {{ __('backend.promocode') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('promocode') }}">{{ __('backend.promocode_management') }}</a>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('promocode') }}">
                            <!-- <i class="material-icons md-18">×</i> -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('promocode')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['promocode.update',$promocode->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'promocodeForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.promocode_name') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="promocode_name" id="promocode_name" class="form-control" placeholder="Label Key" onfocus="validateMsgHide('promocode_name')" value="{{$promocode->promocode_name}}">
                        </div>
                        @if ($errors->has('promocode_name'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_promocode_name" class='validate'>{{ $errors->first('promocode_name') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.promocode') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="promocode" id="promocode" class="form-control" placeholder="Label Key" onfocus="validateMsgHide('promocode')" value="{{$promocode->promocode}}">
                        </div>
                        @if ($errors->has('promocode'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_promocode" class='validate'>{{ $errors->first('promocode') }}</span>
                                </span>
                            @endif
                    </div>

                     <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.promocode_percentage') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="promocode_percentage" id="promocode_percentage" class="form-control"  onfocus="validateMsgHide('promocode_percentage')" placeholder="{!!  __('backend.promocode_percentage') !!}" value="{{$promocode->promocode_percentage}}">
                        </div>
                        @if ($errors->has('promocode_percentage'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_promocode_percentage" class='validate'>{{ $errors->first('promocode_percentage') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.start_date') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Label Key" onfocus="validateMsgHide('start_date')" value="{{$promocode->start_date}}">
                        </div>
                        @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_start_date" class='validate'>{{ $errors->first('start_date') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.end_date') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="end_date" id="end_date" class="form-control" placeholder="Label Key" onfocus="validateMsgHide('end_date')" value="{{$promocode->end_date}}">
                        </div>
                        @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_end_date" class='validate'>{{ $errors->first('end_date') }}</span>
                                </span>
                            @endif
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Promocode Terms & Condition</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="page_content" name="page_content"
                                autofocus>{{ isset($promocode->page_content) ? urldecode($promocode->page_content) : old('page_content') }}</textarea>
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <label for="photo_file" class="col-sm-2 form-control-label">{!! __('backend.topicPhoto') !!}</label>
                        <div class="col-sm-10">
                            @if ($promocode->promocode_image != '')
                                <div class="row">
                                    <div class="col-sm-12 images">
                                        <div id="user_photo" class="col-sm-4 box p-a-xs">
                                            <a target="_blank" href="{{ $image_url . $promocode->promocode_image }}"><img
                                                    src="{{ $image_url . $promocode->promocode_image }}" class="img-responsive">
                                            </a>
                                            <br>
                                            <div class="delete">
                                                <a onclick="document.getElementById('promocode').style.display='none';document.getElementById('photo_delete').value='1';document.getElementById('undo').style.display='block';"
                                                    class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                                                {{ $promocode->promocode_image }}
                                            </div>
                                        </div>
                                        <div id="undo" class="col-sm-4 p-a-xs" style="display: none">
                                            <a
                                                onclick="document.getElementById('promocode').style.display='block';document.getElementById('photo_delete').value='0';document.getElementById('undo').style.display='none';">
                                                <i class="material-icons">&#xe166;</i> {!! __('backend.undoDelete') !!}
                                            </a>
                                        </div>
    
                                        {!! Form::hidden('photo_delete', '0', ['id' => 'photo_delete']) !!}
                                    </div>
                                </div>
                            @endif
    
                            {!! Form::file('promocode_image', ['class' => 'form-control', 'id' => 'promocode_image', 'accept' => 'image/*']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                            <br>
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('photo'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('photo') }}</span>
                                @endif
                            </span>
                        </div>
                    </div> -->

                    

                   

                    


                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                            <a href="{{ route('promocode')}}" class="btn btn-default m-t">
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

        $(document).ready(function() {
   $( "#start_date" ).datepicker({
    startDate: new Date(),
    autoclose: true
   });


   $( "#end_date" ).datepicker({
        startDate: new Date(),
        autoclose: true
    });

   //$('#start_date').datepicker('setDate', 'today');
  });
    </script>
    <script>
        {{-- CKEDITOR.on('instanceReady', function(ev) {
            document.getElementById('eMessage').innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';

            document.getElementById('eButtons').style.display = 'block';
        }); --}}

        {{-- function InsertHTML() {
            var editor = CKEDITOR.instances.editor1;
            var value = document.getElementById('htmlArea').value;

            if (editor.mode == 'wysiwyg') {
                editor.insertHtml(value);
            } else
                alert('You must be in WYSIWYG mode!');
        }

        function InsertText() {
            var editor = CKEDITOR.instances.editor1;
            var value = document.getElementById('txtArea').value;

            if (editor.mode == 'wysiwyg') {
                editor.insertText(value);
            } else
                alert('You must be in WYSIWYG mode!');
        }

        function SetContents() {
            var editor = CKEDITOR.instances.editor1;
            var value = document.getElementById('htmlArea').value;

            editor.setData(value);
        }

        function GetContents() {
            var editor = CKEDITOR.instances.editor1;
            alert(editor.getData());
        }

        function ExecuteCommand(commandName) {
            var editor = CKEDITOR.instances.editor1;

            if (editor.mode == 'wysiwyg') {
                editor.execCommand(commandName);
            } else
                alert('You must be in WYSIWYG mode!');
        }

        function CheckDirty() {
            var editor = CKEDITOR.instances.editor1;
            alert(editor.checkDirty());
        }

        function ResetDirty() {
            var editor = CKEDITOR.instances.editor1;
            editor.resetDirty();
            alert('The "IsDirty" status has been reset');
        }

        function Focus() {
            CKEDITOR.instances.editor1.focus();
        }

        function onFocus() {
            document.getElementById('eMessage').innerHTML = '<b>' + this.name + ' is focused </b>';
        }

        function onBlur() {
            document.getElementById('eMessage').innerHTML = this.name + ' lost focus';
        } --}}
        
        {{-- CKEDITOR.replace('page_content', {
            on: {
                focus: onFocus,
                blur: onBlur,
                pluginsLoaded: function(evt) {
                    var doc = CKEDITOR.document,
                        ed = evt.editor;
                    if (!ed.getCommand('bold')) doc.getById('exec-bold').hide();
                    if (!ed.getCommand('link')) doc.getById('exec-link').hide();
                }
            }
        }); --}}


        CKEDITOR.replace('page_content');

    </script>
@endpush
