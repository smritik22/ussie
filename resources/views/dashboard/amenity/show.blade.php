@extends('dashboard.layouts.master')
@section('title', __('backend.view_amenity'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
@endpush
@section('content')
<?php
use Illuminate\Support\Carbon;
?>
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3>{{ __('backend.view_amenity') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('amenity') }}">{{ __('backend.amenities') }}</a> /
                   <span>{{ __('backend.view_amenity') }}</span>
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
                {{Form::open(['route'=>['amenity'],'method'=>'GET', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.amenity_name') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{urldecode($amenity->amenity_name)}}</label>
                        </div>
                    </div>
                    
                    @if (isset($amenity->childdata) && !empty($amenity->childdata) && count($amenity->childdata) > 0)
                        
                        @foreach ($amenity->childdata as $value)
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">{!!  __('backend.amenity_name') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-10 form-control-label">
                                   <label>{{urldecode($value->amenity_name)}}</label>
                                </div>
                            </div>
                        @endforeach

                    @endif


                    <div class="form-group row">
                        <label for="photo_file"
                               class="col-sm-2 form-control-label">{!!  __('backend.topicPhoto') !!}</label>
                        <div class="col-sm-10">
                            @if($amenity->image!="")
                                <div class="row">
                                    <div class="col-sm-12 images">
                                        <div id="user_photo" class="col-sm-4 box p-a-xs">
                                            <a target="_blank" href="{{ $image_url . '/' . $amenity->image }}"><img src="{{ $image_url . '/' . $amenity->image }}" class="img-responsive">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <label>-</label>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.lastUpdated') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{!! Carbon::parse($amenity->updated_at)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A') !!}</label>
                        </div>
                    </div>
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ route('amenity') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                            <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.back') !!}
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
