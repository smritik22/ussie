@extends('dashboard.layouts.master')
@section('title', __('backend.area'))
@push("after-styles")
    <link href="{{ asset(' assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

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
                        &#xe02e;
                        </i> {!! (isset($areaData->id))?__('backend.editLangArea'): __('backend.addLangArea') !!}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('area') }}">{{ __('backend.areas') }}</a> / 
                    <span>{!! (isset($areaData->id))?__('backend.editLangArea'): __('backend.addLangArea') !!}</span> 
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('area') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['area.storeLang'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'areaForm' ])}}
                {{csrf_field()}}
                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.language') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="area_lang" id="area_lang" class="form-control" placeholder="Area Language" value="{{$languageData->title}}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{__('backend.area_name')}} [{{\Helper::LangFromId(1)->code}}]</label>
                        <div class="col-sm-9">
                            <input type="text" name="area_parent_name" id="area_parent_name" class="form-control" placeholder="Area Parent Name" value="{{urldecode($parentData->name)}}" disabled>
                        </div>
                    </div>

                    {{-- This is the one --}}
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{__('backend.area_name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="area_name" id="area_name" class="form-control" placeholder="Area Name" value="{{old('area_name', (isset($areaData->id)?urldecode($areaData->name):''))}}" dir="{{$languageData->direction}}">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('area_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('area_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" name="area_language_id" value="{{$languageData->id}}">
                            <input type="hidden" name="area_parent_id" value="{{$parentData->id}}">
                            <input type="hidden" name="area_id" value="{{isset($areaData->id)?$areaData->id:''}}">
                            
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! (isset($areaData->id)?__('backend.update'):__('backend.add')); !!}</button>
                            <a href="{{ route('area')}}" class="btn btn-default m-t">
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
@endpush
