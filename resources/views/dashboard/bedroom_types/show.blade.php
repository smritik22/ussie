@extends('dashboard.layouts.master')
@section('title', __('backend.view_bedroom_type'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
@endpush
@section('content')
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view_bedroom_type') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('bedroom_type') }}">{{ __('backend.bedroom_type') }}</a> / 
                   <span>{{__('backend.view_bedroom_type')}}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['bedroom_type'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.bedroom_type') !!} :</label>
                        <div class="col-sm-10 form-control-label">
                           <label>{{$bedroom_type->type}}</label>
                        </div>
                    </div>

                    @if (isset($bedroom_type->childdata) && !empty($bedroom_type->childdata) && count($bedroom_type->childdata) > 0)
                        @foreach ($bedroom_type->childdata as $value)
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">{!!  __('backend.bedroom_type') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-10 form-control-label">
                                   <label>{{$value->type}}</label>
                                </div>
                            </div>
                        @endforeach

                    @endif
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ route('bedroom_type') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
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
