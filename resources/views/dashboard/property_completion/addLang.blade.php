@extends('dashboard.layouts.master')
@section('title', $title )
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
                        &#xe02e;</i> {!! (isset($completionData->completion_text)?__('backend.edit_lang_completion'):__('backend.add_lang_completion')); !!}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('completion') }}">{{ __('backend.completion') }}</a> / 
                    <span>{!! (isset($completionData->completion_text)?__('backend.edit_lang_completion'):__('backend.add_lang_completion')); !!}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['completion.storeLang'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'completionForm' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.language') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="completion_lang" id="completion_lang" class="form-control" placeholder="completion Language" value="{{$languageData->title}}" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.completion') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="completion_type" id="completion_type" value="{{$parentData->completion_type}}" class="form-control" placeholder="{{__('backend.completion')}}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.completion') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="completion" id="completion" class="form-control" value="{{ ( isset( $completionData->completion_type ) ? $completionData->completion_type : old('completion')) }}" placeholder="{{__('backend.completion')}}" dir="{{$languageData->direction}}" >

                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('completion'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('completion') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-3 col-sm-9">
                            {!! Form::hidden( 'completion_language_id', $languageData->id ) !!}
                            {!! Form::hidden( 'completion_parent_id', $parentData->id ) !!}
                            {!! Form::hidden( 'completion_id', ( isset( $completionData->id ) ? $completionData->id:"") ) !!}
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! (isset($completionData->completion_type)?__('backend.update'):__('backend.add')); !!}</button>
                            <a href="{{ route('completion')}}" class="btn btn-default m-t">
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
        {{-- $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        }); --}}

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
