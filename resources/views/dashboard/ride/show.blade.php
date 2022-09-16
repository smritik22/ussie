@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.view_ride_details'))
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
$get_currency = \Helper::getDefaultCurrency();

?>
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3>{{ __('backend.ride_management') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('ride') }}">{{ __('backend.ride_management') }}</a> /
                   <span>{{ __('backend.view_ride_details') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('ride')}}">
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
                                        <th colspan="5" style="text-align:center;">Ride Details</th>
                                    </tbody>
                                </tr>
                                
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_id') !!}</th>
                                        <td>{{isset($ride_data[0]->id) ? $ride_data[0]->id : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                                 $date = \Helper::converttimeTozone($ride_data[0]->start_date);
                                 ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_date_time') !!}</th>
                                        <td>{{ $date }}
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_km') !!}</th>
                                        <td>{{isset($ride_data[0]->ride_km) ? $ride_data[0]->ride_km : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.estimated_fare') !!}</th>
                                        <td>{{$get_currency}}{{isset($ride_data[0]->estimate_fare) ? $ride_data[0]->estimate_fare : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.actual_fare') !!}</th>
                                        <td>{{$get_currency}}{{isset($ride_data[0]->actual_fare) ? $ride_data[0]->actual_fare : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.pickup_licaiotn') !!}</th>
                                        <td>{{isset($ride_data[0]->pickup_address) ? urldecode($ride_data[0]->pickup_address) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.drop_licaiotn') !!}</th>
                                        <td>{{isset($ride_data[0]->dest_address) ? urldecode($ride_data[0]->dest_address) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Passenger Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.passenger_name') !!}</th>
                                        <td>{{isset($ride_data[0]->username) ? urldecode($ride_data[0]->username) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.passenger_mobile') !!}</th>
                                        <td>{{isset($ride_data[0]->usermobile) ? $ride_data[0]->usermobile :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Driver Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.passenger_name') !!}</th>
                                        <td>{{isset($ride_data[0]->drivername) ? urldecode($ride_data[0]->drivername) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.passenger_mobile') !!}</th>
                                        <td>{{isset($ride_data[0]->drivermobile) ? $ride_data[0]->drivermobile :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Vehicle Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.Vehicle_type') !!}</th>
                                        <td>{{isset($ride_data[0]->vehicle_type_name) ? $ride_data[0]->vehicle_type_name :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.vehicle_number') !!}</th>
                                        <td>{{isset($ride_data[0]->vehicle_number) ? $ride_data[0]->vehicle_number :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.Vehicle_modal') !!}</th>
                                        <td>{{isset($ride_data[0]->vehicle_model_name) ? $ride_data[0]->vehicle_model_name :''}}</td>
                                    </tbody>
                                </tr>
                                 <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Ride Status</th>
                                    </tbody>
                                </tr>
                                <?php
                    $error = \Helper::orderStatusadmin(isset($ride_data[0]->id) ? $ride_data[0]->id :'');
                    ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.status') !!}</th>
                                        <td>{{ $error }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Rate & Review</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.driver_rate') !!}</th>
                                        <td>{{isset($ride_data[0]->driver_ratings) ? $ride_data[0]->driver_ratings :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.driver_review') !!}</th>
                                        <td>{{isset($ride_data[0]->driver_review) ? $ride_data[0]->driver_review :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Payment Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.paid_amount') !!}</th>
                                        <td>{{$get_currency}}{{isset($ride_data[0]->customer_total_amount) ? $ride_data[0]->customer_total_amount :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.admin_commission') !!}</th>
                                        <td>{{$get_currency}}{{isset($ride_data[0]->admin_commission) ? $ride_data[0]->admin_commission :''}}</td>
                                    </tbody>
                                </tr>

                            </table>
                        
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
