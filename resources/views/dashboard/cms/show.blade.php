@extends('dashboard.layouts.master')
<?php
$title_var = 'title_' . @Helper::currentLanguage()->code;
$title_var2 = 'title_' . env('DEFAULT_LANGUAGE');
?>
@section('title', __('backend.cms'))
@push('after-styles')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
            <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->
@endpush
@section('content')
    <div class="padding edit-package website-crm-show">
        <div class="box">
            <div class="box-header dker">
                <?php
                $title_var = 'title_' . @Helper::currentLanguage()->code;
                $title_var2 = 'title_' . env('DEFAULT_LANGUAGE');
                ?>
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view') }} {{ __('backend.cms') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('cms') }}">{{ __('backend.cms') }}</a>
                </small>
            </div>

            <div class="box-body">
                {{ Form::open(['route' => ['cms'], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data']) }}

                <div class="personal_informations">

                    <!-- <h3>{!! __('backend.cms') !!}</h3>
                            <br> -->
                    <div class="form-group row">
                        <!-- <label class="col-sm-1 form-control-label"></label> -->
                        <div class="col-sm-12">
                            <h4><label>{{ $cms->page_title }}</label></h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <!-- <label class="col-sm-1 form-control-label"></label> -->
                        <div class="col-sm-12">
                            <div class="d-flex flex-wrap mb-4">
                                <textarea class="form-control" id="page_content" name="page_content"
                                disabled autofocus>{!! urldecode($cms->page_content) !!}</textarea>

                            </div>
                        </div>
                    </div>

                    <!-- @if (isset($cms) && !empty($cms))

                        <br>
                        <hr>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <h4><label>{{ $cms->page_title }} </label></h4>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="d-flex flex-wrap mb-4">
                                        <textarea class="form-control child_page_content" id="page_content" name="page_content" disabled
                                            autofocus>{!! urldecode($cms->page_content) !!}</textarea>

                                    </div>
                                </div>
                            </div>
                        
                    @endif -->

                </div>


                <div class="form-group row">

                    <div class="">
                        <a href="{{ route('cms') }}" class="btn btn-default m-t" style="">
                            <i class="material-icons">
                                &#xe5cd;</i>{!! __('backend.cancel') !!}
                        </a>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



    <script>
        $(function() {
            $('.icp-auto').iconpicker({
                placement: '{{ @Helper::currentLanguage()->direction == 'rtl' ? 'topLeft' : 'topRight' }}'
            });
        });

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({
                    value: e.loaded,
                    max: e.total
                });
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
    </script>

    <script>
        CKEDITOR.on('instanceReady', function(ev) {
            document.getElementById('eMessage').innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';

            document.getElementById('eButtons').style.display = 'block';
        });

        function InsertHTML() {
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
        });

        $('.child_page_content').each(function(index,element){

            let content_id = $(element).attr('id');
            CKEDITOR.replace(content_id, {
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
            });
        });

    </script>
@endpush
