@extends('dashboard.layouts.master')
@section('title','Dashboard')
@push("after-styles")
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/flags.css') }}" type="text/css"/>
@endpush
@section('content')
<?php 
use App\Models\User;
?>
    <div class="padding p-b-0 upskild-dashboard">
        <div class="margin">
            <div class="row">
                 <div class="col-xs-6">
                <h5 class="m-b-0 _300">{{ __('backend.hi') }} <span
                        class="text-primary">{{ Auth::user()->name }}</span>, {{ __('backend.welcomeBack') }}
                </h5>
                </div>
                 <div class="col-xs-6">
                <form action="{{ route('dashboardfilter')}}" method="post" style="padding-left: %;">
                    @csrf
                        
                    
                        <input type="text" class="form-control" style="color: #001645;font-weight:500;width: 220px;margin-right: 8px;" value="{{ isset($filterdate)?$filterdate:old('date_filter') }}" name="date_filter" id="date_filter"/>
                        <input type="submit" name="filter_submit" class="btn btn-primary" value="Filter" />
                        <a href="{{ route('adminHome')}}"><input type="button" name="clear" class="btn btn-danger" value="Clear"  /></a>
                </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('passenger')}}">
                                <div class="pull-left m-r">
                                    <img src="{{ asset('assets/frontend/logo/icon_dash_passenger.svg') }}">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Number of Passengers</div>
                                        <h4 class="m-a-0 text-md _600">{{ isset($total_users) ? $total_users : '' }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('driver')}}">
                                <div class="pull-left m-r">
                                    <img src="{{ asset('assets/frontend/logo/icon_dash_driver.svg') }}">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Number of Drivers</div>
                                        <h4 class="m-a-0 text-md _600">{{ isset($total_driver_users) ? $total_driver_users : '' }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="#">
                                <div class="pull-left m-r">
                                    <img src="{{ asset('assets/frontend/logo/icon_dash_generate.svg') }}">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Revenue Generated</div>
                                        <h4 class="m-a-0 text-md _600">{{ isset($total_revenue_generate[0]['amount_sum']) ? $total_revenue_generate[0]['amount_sum'] : '0' }}</h4>
                                </div>
                            </a>
                        </div>
                    </div> -->
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('ride')}}">
                                <div class="pull-left m-r">
                                    <img src="{{ asset('assets/frontend/logo/icon_dash_rides.svg') }}">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Number of Rides</div>
                                        <h4 class="m-a-0 text-md _600">{{ isset($total_ride) ? $total_ride : '' }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('ride.active_ride_list') }}">
                                <div class="pull-left m-r">
                                    <img src="{{ asset('assets/frontend/logo/icon_dash_active_rides.svg') }}">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Active Rides</div>
                                        <h4 class="m-a-0 text-md _600">{{ isset($total_active_ride) ? $total_active_ride : '' }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>

                    
                    

                    

                    
                    
                    <div class="col-xs-12">
                       
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="card-head">
                            <h6>Users</h6>
                        </div>
                         <div  style="width: 300px; height: 300px; " class="card-graphs">
                           {!! $userProfileGraph->container() !!}
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                            {!! $userProfileGraph->script() !!}
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="card-head">
                            <h6>Total Revenue</h6>
                        </div>
                         <div  style="width: 600px; height: 300px; " class="card-graphs card-cart">
                           {!! $revenueChart->container() !!}
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                            {!! $revenueChart->script() !!}
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
@endsection
@push("after-scripts")
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function () {
            let dateInterval = getQueryParameter('date_filter');
           let start = "<?php echo ($start);?> ";
           let end = "<?php echo ($end);?> ";
            
            if (dateInterval) {
                dateInterval = dateInterval.split(' - ');
                start = dateInterval[0];
                end = dateInterval[1];
            }
            $('#date_filter').daterangepicker({
                "showDropdowns": true,
                "showWeekNumbers": true,
                "alwaysShowCalendars": true,
                startDate: start,
                endDate: end,
                locale: {
                    format: 'MM/DD/YYYY',
                    firstDay: 1,
                },
            });
        });
        function getQueryParameter(name) {
            const url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    </script>  
@endpush