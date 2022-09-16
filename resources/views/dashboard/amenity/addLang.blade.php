@extends('dashboard.layouts.master')
@section('title', __('backend.addLangAmenity'))
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
                        </i> {!! (isset($amenityData->id))?__('backend.editLangAmenity'): __('backend.addLangAmenity') !!}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('amenity') }}">{{ __('backend.amenities') }}</a> / 
                    <span>{!! (isset($areaData->id))?__('backend.editLangAmenity'): __('backend.addLangAmenity') !!}</span> 
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('amenity') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['amenity.storeLang'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'amenityForm' ])}}
                {{csrf_field()}}
                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.language') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="amenity_lang" id="amenity_lang" class="form-control" placeholder="Amenity Language" value="{{$languageData->title}}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{__('backend.amenity_name')}} [{{\Helper::LangFromId(1)->code}}]</label>
                        <div class="col-sm-9">
                            <input type="text" name="amenity_parent_name" id="amenity_parent_name" class="form-control" placeholder="Amenity Parent Name" value="{{urldecode($parentData->amenity_name)}}" disabled>
                        </div>
                    </div>

                    {{-- This is the one --}}
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{__('backend.amenity_name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" name="amenity_name" id="amenity_name" class="form-control" placeholder="{{__('backend.amenity_name')}}" value="{{old('amenity_name', (isset($amenityData->id)?urldecode($amenityData->amenity_name):''))}}" dir="{{$languageData->direction}}">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('amenity_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('amenity_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" name="amenity_language_id" value="{{$languageData->id}}">
                            <input type="hidden" name="amenity_parent_id" value="{{$parentData->id}}">
                            <input type="hidden" name="amenity_id" value="{{isset($amenityData->id)?$amenityData->id:''}}">
                            
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! (isset($amenityData->id)?__('backend.update'):__('backend.add')); !!}</button>
                            <a href="{{ route('amenity')}}" class="btn btn-default m-t">
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
