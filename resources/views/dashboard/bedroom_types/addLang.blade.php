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
                        &#xe02e;</i> {!! (isset($bedroomTypeData->type)?__('backend.edit_lang_bedroom_type'):__('backend.add_lang_bedroom_type')); !!}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('bedroom_type') }}">{{ __('backend.bedroom_types') }}</a> / 
                    <span>{!! (isset($bedroomTypeData->type)?__('backend.edit_lang_bedroom_type'):__('backend.add_lang_bedroom_type')); !!}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['bedroom_type.storeLang'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'bedroom_typeForm' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.language') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="bedroom_type_lang" id="bedroom_type_lang" class="form-control" placeholder="Language" value="{{$languageData->title}}" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.bedroom_type') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="bedroom_type" id="bedroom_type" value="{{$parentData->type}}" class="form-control" placeholder="{{__('backend.bedroom_type')}}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.bedroom_type') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="type" id="type" class="form-control" value="{{ ( isset( $bedroomTypeData->type ) ? $bedroomTypeData->type : old('type')) }}" placeholder="{{__('backend.bedroom_type')}}" dir="{{$languageData->direction}}" >

                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('type'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('type') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            {!! Form::hidden( 'bedroom_type_language_id', $languageData->id ) !!}
                            {!! Form::hidden( 'bedroom_type_parent_id', $parentData->id ) !!}
                            {!! Form::hidden( 'bedroom_type_id', ( isset( $bedroomTypeData->id ) ? $bedroomTypeData->id:"") ) !!}
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! (isset($bedroomTypeData->type)?__('backend.update'):__('backend.add')); !!}</button>
                            <a href="{{ route('bedroom_type')}}" class="btn btn-default m-t">
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
