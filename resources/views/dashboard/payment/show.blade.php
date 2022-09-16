@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.payment_management'))
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
                <h3>{{ __('backend.payment_management') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('payment') }}">{{ __('backend.payment') }}</a> /
                   <span>{{ __('backend.view_payment') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('payment')}}">
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
                                
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_id') !!}</th>
                                        <td>{{isset($payment_data[0]->ride_id) ? urldecode($payment_data[0]->ride_id) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.amount') !!}</th>
                                        <td>{{$get_currency}}{{isset($payment_data[0]->total_amount) ? urldecode($payment_data[0]->total_amount) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.deducation_amount') !!}</th>
                                        <td>{{$get_currency}}{{isset($payment_data[0]->deducation) ? urldecode($payment_data[0]->deducation) : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                             
                             $payment_release_date = \Helper::converttimeTozone($payment_data[0]->payment_release_date);
                            ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.payment_release_date') !!}</th>
                                        <td>{{$payment_release_date}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.payment_status') !!}</th>
                                         @if($payment_data[0]->payment_status==1)
                                       <td>Approve</td>
                                       @else
                                       <td>Pending</td>
                                       @endif
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Ride Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.pickup_licaiotn') !!}</th>
                                        <td>{{isset($payment_data[0]->pickup_address) ? urldecode($payment_data[0]->pickup_address) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.drop_licaiotn') !!}</th>
                                        <td>{{isset($payment_data[0]->dest_address) ? urldecode($payment_data[0]->dest_address) : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                             
                             $start_date = \Helper::converttimeTozone($payment_data[0]->ride_start_date);
                            ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_start_date') !!}</th>
                                        <td>{{ $start_date }}</td>
                                    </tbody>
                                </tr>
                                <?php
                             
                             $end_date = \Helper::converttimeTozone($payment_data[0]->ride_end_date);
                            ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_end_date') !!}</th>
                                        <td>{{ $end_date }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_km') !!}</th>
                                        <td>{{isset($payment_data[0]->ride_km) ? urldecode($payment_data[0]->ride_km) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_estimate_fare') !!}</th>
                                        <td>{{$get_currency}}{{isset($payment_data[0]->actual_fare) ? urldecode($payment_data[0]->actual_fare) : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                                $error = \Helper::orderStatusadmin(isset($payment_data[0]->ride_detail_status) ? $payment_data[0]->ride_detail_status :'');
                                ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.ride_status') !!}</th>
                                        <td>{{ $error }}</td>
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
                                        <td>{{isset($payment_data[0]->username) ? urldecode($payment_data[0]->username) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.passenger_mobile') !!}</th>
                                        <td>{{isset($payment_data[0]->usermobile) ? $payment_data[0]->usermobile :''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th colspan="5" style="text-align:center;">Driver Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.driver_name') !!}</th>
                                        <td>{{isset($payment_data[0]->drivername) ? urldecode($payment_data[0]->drivername) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.driver_mobile') !!}</th>
                                        <td>{{isset($payment_data[0]->drivermobile) ? $payment_data[0]->drivermobile :''}}</td>
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
