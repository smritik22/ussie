@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.view_general_user'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <style type="text/css">
        .driver_table{
                margin-left: 360px;
    font-size: 20px;
    margin-bottom: 15px;


        }
    </style>

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
                <h3>{{ __('backend.view_driver_user') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('driver') }}">{{ __('backend.driver_management') }}</a> /
                   <span>{{ __('backend.view_driver_user') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('driver')}}">
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
                                        <th>{!! __('backend.topicPhoto') !!}</th>
                                        <td>@if(isset($user->customer_image) && $user->customer_image != "")
                                            <a target="_blank" href="{{ $image_url . $user->customer_image }}"><img id="image" src="{{ $image_url . $user->customer_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <a target="_blank" href="{{ asset('public/uploads/profile.png') }}"><img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</a></td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.full_name') !!}</th>
                                        <td>{{urldecode($user->name)}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.email') !!}</th>
                                        <td>{{urldecode($user->email)}}
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.mobile_number') !!}</th>
                                        <td>{{ urldecode($user->country_code). ' ' . urldecode($user->mobile_number)}}</td>
                                    </tbody>
                                </tr>
                                <?php
                    $date = \Helper::converttimeTozone($user->updated_at);
                    ?>
                                
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.lastUpdated') !!}</th>
                                        
                                        <td>{{ $date }}</td>
                                    </tbody>
                                </tr>

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.driver_license') !!}</th>
                                        
                                        @foreach($driver_document_liciense as $liciens)
                                        <td>@if(isset($driver_document) && $driver_document != "")
                                            <a target="_blank" href="{{ $image_uri_document . $liciens->document_image }}"><img id="image" src="{{ $image_uri_document . $liciens->document_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</td>
                                        @endforeach
                                    </tbody>
                                </tr>

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.document') !!}</th>
                                        
                                        @foreach($driver_document_authority as $authority)
                                        <td>@if(isset($driver_document) && $driver_document != "")
                                            <a target="_blank" href="{{ $image_uri_document . $authority->document_image }}"><img id="image" src="{{ $image_uri_document . $authority->document_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</td>
                                        @endforeach
                                    </tbody>
                                </tr>

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.document') !!}</th>
                                        
                                        @foreach($driver_document_territor as $territor)
                                        <td>@if(isset($driver_document) && $driver_document != "")
                                            <a target="_blank" href="{{ $image_uri_document . $territor->document_image }}"><img id="image" src="{{ $image_uri_document . $territor->document_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</td>
                                        @endforeach
                                    </tbody>
                                </tr>

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.document') !!}</th>
                                        
                                        @foreach($driver_document_police as $police)
                                        <td>@if(isset($driver_document) && $driver_document != "")
                                            <a target="_blank" href="{{ $image_uri_document . $police->document_image }}"><img id="image" src="{{ $image_uri_document . $police->document_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</td>
                                        @endforeach
                                    </tbody>
                                </tr>

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.document') !!}</th>
                                        
                                        @foreach($driver_document_vehicle_documentaion as $vehicle_documentaion)
                                        <td>@if(isset($driver_document) && $driver_document != "")
                                            <a target="_blank" href="{{ $image_uri_document . $vehicle_documentaion->document_image }}"><img id="image" src="{{ $image_uri_document . $vehicle_documentaion->document_image }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif</td>
                                        @endforeach
                                    </tbody>
                                </tr>


                                
                                
                               <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Vehicle Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Sr No.</th>
                                        <th style="border-left: 1px solid #dfdfdf !important">Car Type</th>
                                        <th style="border-left: 1px solid #dfdfdf !important">Vehicle Number</th>
                                        <th style="border-left: 1px solid #dfdfdf !important">Vehicle Capacity</th>
                                        <th style="border-left: 1px solid #dfdfdf !important">Vehicle Make</th>
                                        <th style="border-left: 1px solid #dfdfdf !important">Vehicle Modal</th>
                                        <!-- <th style="border-left: 1px solid #dfdfdf !important">Vehicle Documentaion</th> -->
                                        <!-- <th style="border-left: 1px solid #dfdfdf !important">Material Details</th> -->
                                    </tbody>
                                </tr>
                                <?php $i = 1; ?>
                                @foreach($driver_vehicle as $key => $data)

                                <tr>
                                    <tbody>
                                        <td>{{$i}}</td>
                                        <td>{{ isset($data->car_type_name) ? $data->car_type_name : ''}}</td>
                                          <td>{{ isset($data->vehicle_number) ? $data->vehicle_number : ''}}</td>
                                          <td>{{ isset($data->vehicle_seat_capacity) ? $data->vehicle_seat_capacity : ''}}</td>
                                          <td>{{ isset($data->vehicle_type_name) ? $data->vehicle_type_name : ''}}</td>
                                          <td>{{ isset($data->vehicle_model_name) ? $data->vehicle_model_name : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php $i++; ?>
                                @endforeach 
                                


                            </table>
        </div>
                
                
                <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ route('driver') }}" class="btn btn-default m-t show_button" style="margin: 0 0 0 0px">
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
