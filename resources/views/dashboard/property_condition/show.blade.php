@extends('dashboard.layouts.master')
@section('title', __('backend.view_condition'))
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
                        &#xe02e;</i> {{ __('backend.view_condition') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('condition') }}">{{ __('backend.condition') }}</a> / 
                   <span>{{__('backend.view_condition')}}</span>
                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['condition'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.condition') !!} :</label>
                        <div class="col-sm-9 label-border">
                           <label>{{$condition->condition_text}}</label>
                        </div>
                    </div>

                    @if (isset($condition->childdata) && !empty($condition->childdata) && count($condition->childdata) > 0)
                        @foreach ($condition->childdata as $value)
                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">{!!  __('backend.condition') !!} [{{ \Helper::LangFromId($value->language_id)->code }}] :</label>
                                <div class="col-sm-9 label-border">
                                   <label>{{$value->condition_text}}</label>
                                </div>
                            </div>
                        @endforeach

                    @endif
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3">
                        <a href="{{ route('condition') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
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
