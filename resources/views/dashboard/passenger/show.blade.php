@extends('dashboard.layouts.master')
@extends('dashboard.layouts.table')
@section('title', __('backend.view_general_user'))
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
                <h3>{{ __('backend.view_passenger_user') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ route('passenger') }}">{{ __('backend.passenger_management') }}</a> /
                   <span>{{ __('backend.view_passenger_user') }}</span>
                </small>
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
                                            @endif
                                        </a>
                                    </td>
                                    </tbody>
                                    <tbody>
                                        <th>{!! __('backend.topicPhoto') !!}</th>
                                        <td>@if(isset($user->file_1) && $user->file_1 != "")
                                            <a target="_blank" href="{{ $image_url . $user->file_1 }}"><img id="image" src="{{ $image_url . $user->file_1 }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <a target="_blank" href="{{ asset('public/uploads/profile.png') }}"><img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif
                                        </a>
                                    </td>
                                    </tbody>
                                    <tbody>
                                        <th>{!! __('backend.topicPhoto') !!}</th>
                                        <td>@if(isset($user->file_2) && $user->file_2 != "")
                                            <a target="_blank" href="{{ $image_url . $user->file_2 }}"><img id="image" src="{{ $image_url . $user->file_2 }}" class="thumbnail" width="100px" height="100px" /></a>
                                            @else
                                            <a target="_blank" href="{{ asset('public/uploads/profile.png') }}"><img src="{{ asset('public/uploads/profile.png') }}" width="100px" height="100px">
                                            @endif
                                        </a>
                                    </td>
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
                                        <th>{!!  __('backend.gender') !!}</th>
                                        <td>{{urldecode($user->gender)}}
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.address') !!}</th>
                                        <td>{{urldecode($user->address)}}
                                            <div id="map" style="width: 500px; height: 400px;"></div>
                                            {{-- {{json_encode($markers)}} --}}
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.hobbies') !!}</th>
                                        <td>{{urldecode($user->hobbies)}}
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!!  __('backend.lastUpdated') !!}</th>
                                        
                                        <td>{{ $date }}</td>
                                    </tbody>
                                </tr>
                            </table>
                    
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ route('passenger') }}" class="btn btn-default m-t show_button" style="margin: 0 0 0 0px">
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


<script src="http://maps.google.com/maps/api/js?sensor=false"
type="text/javascript"></script>
    <script type="text/javascript">
        var locations = <?php echo json_encode($markers) ?>;
        console.log('Test:'+locations);
        // var lat = parseFloat(document.getElementById(locations['address_latitude']).value);
    // var lng = parseFloat(document.getElementById('lng').value);
        // console.log(lat)
        var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: new google.maps.LatLng(36.1716, 115.1391),
        mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        // for (i = 0; i < locations.length; i++) {
            console.log('Test:'+locations['address_latitude']);
            // return false;
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations['address_latitude'], locations['address_longitude']),
            map: map
        });
        // }
    </script>

@endpush
