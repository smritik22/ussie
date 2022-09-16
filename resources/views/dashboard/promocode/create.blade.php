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
                        &#xe02e;</i> {{ __('backend.newpromocode') }}
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
                {{Form::open(['route'=>['promocode.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'promocodeForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.promocode_name') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="promocode_name" id="promocode_name" class="form-control" placeholder="{!!  __('backend.promocode_name') !!}" onfocus="validateMsgHide('promocode_name')" value="{{old('promocode_name')}}">
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
                            <input type="text" name="promocode" id="promocode" class="form-control" placeholder="{!!  __('backend.promocode') !!}" onfocus="validateMsgHide('promocode')" value="{{old('promocode')}}">
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
                            <input type="text" name="promocode_percentage" id="promocode_percentage" class="form-control"  onfocus="validateMsgHide('promocode_percentage')" placeholder="{!!  __('backend.promocode_percentage') !!}" value="{{old('promocode_percentage')}}">
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
                            <input type="text" name="start_date" id="start_date" class="form-control" placeholder="{!!  __('backend.start_date') !!}" onfocus="validateMsgHide('start_date')" value="{{old('start_date')}}">
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
                            <input type="text" name="end_date" id="end_date" class="form-control" placeholder="{!!  __('backend.end_date') !!}" onfocus="validateMsgHide('end_date')" value="{{old('end_date')}}">
                        </div>
                        @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <span style="color: red;" id="error_end_date" class='validate'>{{ $errors->first('end_date') }}</span>
                                </span>
                            @endif
                    </div>
                    <div class="form-group row">
                        <cms class="col-sm-2 form-control-cms">Promocode Terms & Condition</cms>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="page_content" name="page_content" autofocus ></textarea>
                        </div>

                    </div>
                    <!-- <div class="form-group row">
                        <label for="image" class="col-sm-2 form-control-label">{!! __('backend.promocode_image') !!}</label>
                        <div class="col-sm-10">
                            {!! Form::file('promocode_image', ['class' => 'form-control', 'id' => 'promocode_image', 'accept' => 'image/svg']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.aminityImageTypes') !!}
                            </small>
                                @if(!empty(@$errors) && @$errors->has('promocode_image'))
                            <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('promocode_image') }}</span>
                            </span>
                                @endif
                        </div>
                    </div> -->

<!--                     <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.labelValue') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="label_value" id="label_value" class="form-control" value="{{old('label_value')}}" placeholder="Label Value">
                        </div>
                    </div> -->

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
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
    <script src="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.js") }}"></script>
    <script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">   
$(document).ready(function () {   
$('#submitDetail').on('click', function () {   
var myForm = $("form#promocodeForm");   
if (myForm) {   
$(this).prop('disabled', true);   
$(myForm).submit();   
}   
});   
});   
</script>
     <script type="text/javascript">
        // $( function() {
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

//    $( function() {
//     $( "#end_date" ).datepicker({
//         startDate: new Date()
//     });
// }):

    </script>

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
    <script>
            {{-- CKEDITOR.on( 'instanceReady', function( ev ) {
        document.getElementById( 'eMessage' ).innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';
                                                                                                                                                            
        document.getElementById( 'eButtons' ).style.display = 'block';
    });

    function InsertHTML() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'htmlArea' ).value;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.insertHtml( value );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function InsertText() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'txtArea' ).value;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.insertText( value );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function SetContents() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'htmlArea' ).value;

        editor.setData( value );
    }

    function GetContents() {
        var editor = CKEDITOR.instances.editor1;
        alert( editor.getData() );
    }

    function ExecuteCommand( commandName ) {
        var editor = CKEDITOR.instances.editor1;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.execCommand( commandName );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function CheckDirty() {
        var editor = CKEDITOR.instances.editor1;
        alert( editor.checkDirty() );
    }

    function ResetDirty() {
        var editor = CKEDITOR.instances.editor1;
        editor.resetDirty();
        alert( 'The "IsDirty" status has been reset' );
    }

    function Focus() {
        CKEDITOR.instances.editor1.focus();
    }

    function onFocus() {
        document.getElementById( 'eMessage' ).innerHTML = '<b>' + this.name + ' is focused </b>';
    }

    function onBlur() {
        document.getElementById( 'eMessage' ).innerHTML = this.name + ' lost focus';
    }
            
        CKEDITOR.replace('page_content', {
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
