@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.notification_management'))
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
                <h3>{{ __('backend.notification_management') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('notification') }}">{{ __('backend.notification') }}</a> /
                   <span>{{ __('backend.view_notification') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('notification')}}">
                                    &nbsp; {{ __('backend.backs') }}
                                </a>
                            </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['generalusers'],'method'=>'GET', 'files' => true,'enctype' => 'multipart/form-data' ])}}

                <div class="personal_informations">
                            <table class="table table-bordered m-a-0">
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Notification Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.notificaiton_title') !!}</th>
                                        <td>{{isset($notification_data[0]->title) ? urldecode($notification_data[0]->title) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.notificaiton_description') !!}</th>
                                        <td>{{isset($notification_data[0]->description) ? urldecode($notification_data[0]->description) : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                    $error = \Helper::notification_type(isset($notification_data[0]->notification_type) ? $notification_data[0]->notification_type :'');
                    ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.notification_type') !!}</th>
                                        <td>{{$error}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.notificaiton_from') !!}</th>
                                        <td>{{isset($notification_data[0]->username) ? urldecode($notification_data[0]->username) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.notificaiton_to') !!}</th>
                                        <td>{{isset($notification_data[0]->drivername) ? urldecode($notification_data[0]->drivername) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.read_status') !!}</th>
                                        @if($notification_data[0]->read_status == 1)
                                        <td>Read</td>
                                        @else
                                       <td>Not Read</td>
                                       @endif
                                    </tbody>
                                </tr>
                                
                            </table>
        </div>
                    <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ url()->previous() }}" class="btn btn-default m-t show_button" style="margin: 0 0 0 0px">
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
