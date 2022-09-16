@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.revenue_report'))
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
                <h3>{{ __('backend.revenue_report') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('revenue-report') }}">{{ __('backend.revenue_report') }}</a> /
                   <span>{{ __('backend.view_revenue_report') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('revenue-report')}}">
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
                                        <th colspan="5" style="text-align:center;">Transaction Details</th>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.transaction_number') !!}</th>
                                        <td>{{isset($ride_data[0]->transaction_id) ? urldecode($ride_data[0]->transaction_id) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.transaction_amount') !!}</th>
                                        <td>{{$get_currency}}{{isset($ride_data[0]->total_amount) ? urldecode($ride_data[0]->total_amount) : ''}}</td>
                                    </tbody>
                                </tr>
                                <?php
                             // $transaction_date = Carbon::parse($ride_data[0]->transaction_date)->format(env('DATE_FORMAT','Y-m-d') . ' h:i A')
                             $transaction_date = \Helper::converttimeTozone($ride_data[0]->transaction_date);
                            ?>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.transaction_date') !!}</th>
                                        <td>{{isset($transaction_date) ? urldecode($transaction_date) : ''}}</td>
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
                                        <th>{!!  __('backend.driver_name') !!}</th>
                                        <td>{{isset($ride_data[0]->drivername) ? urldecode($ride_data[0]->drivername) : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.driver_mobile') !!}</th>
                                        <td>{{isset($ride_data[0]->drivermobile) ? $ride_data[0]->drivermobile :''}}</td>
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
