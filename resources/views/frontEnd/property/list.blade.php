@extends('frontEnd.layout')
@section('content')

    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('frontend.homePage') }}">{{ $labels['home'] }}</a>
                            </li>
                            @if ($is_search)
                                <li class="breadcrumb-item active" aria-current="page" ><img
                                    src="{{ asset('assets/img/bread.svg') }}" alt="icon" />{{$labels['property_list']}}</li>
                            @else    
                                @php
                                    $count = count($breadcumb_title_arr);
                                @endphp
                                @foreach ($breadcumb_title_arr as $key => $title)
                                    <li class="breadcrumb-item {{ $key == $count - 1 ? 'active' : '' }}"
                                        @if ($key == $count - 1) aria-current="page" @endif><img
                                            src="{{ asset('assets/img/bread.svg') }}" alt="icon" /> {{ $title }}</li>
                                @endforeach
                            @endif
                        </ol>
                    </nav>
                    <input type="hidden" name="area_id" value="{{$area_id}}" id="area_id">
                    <input type="hidden" name="property_id" value="{{$property_id}}" id="property_id">
                    <input type="hidden" name="list_type" value="{{$list_type}}" id="list_type_id">

                    <input type="hidden" id="s_property_type" name="s_property_type" value="{{$property_type}}" id="s_property_type">
                    <input type="hidden" id="s_property_for" name="s_property_for" value="{{$property_for}}" id="s_property_for">
                    <input type="hidden" id="s_search" name="s_search" value="{{$search}}" id="s_search">
                    <input type="hidden" id="is_search" name="is_search" value="{{$is_search}}" id="is_search">
                </div>
            </div>
        </div>
    </section>
    <section class="featured-properties-list">
        <div class="container">
            <div class="row">
                {{-- ========================================================
                    |  
                    |   filter list included  
                    |
                    ======================================================== --}}

                @include('frontEnd.property.filter')

                {{-- Property list started --}}

                <div class="col-lg-9 featured-properties-right-outer">
                    <div class="featured-properties-right" id="property_list_top">
                        <h3 class="featured-properties-heading">{{ $labels['property_list'] }}</h3>
                        <div class="featured-properties-sorting">
                            <div class="featured-properties-sorting-box">
                                <label>{{ $labels['sort_by'] }}</label>
                                <select class="form-control" name="sort_by" id="sort_by">
                                    <option value="3">{{ $labels['price_low_to_high'] }}</option>
                                    <option value="2">{{ $labels['price_high_to_low'] }}</option>
                                </select>
                            </div>
                            <div class="view-grid">
                                <a class="list-view list-view-box active" data-list_type="1" href="">
                                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.05058 5.25287C1.32921 5.25287 1.59642 5.36355 1.79345 5.56058C1.99047 5.7576 2.10115 6.02482 2.10115 6.30345C2.10115 6.58208 1.99047 6.8493 1.79345 7.04632C1.59642 7.24334 1.32921 7.35402 1.05058 7.35402C0.771946 7.35402 0.504728 7.24334 0.307707 7.04632C0.110686 6.8493 0 6.58208 0 6.30345C0 6.02482 0.110686 5.7576 0.307707 5.56058C0.504728 5.36355 0.771946 5.25287 1.05058 5.25287Z"
                                            fill="#424458" />
                                        <path
                                            d="M1.05058 10.5058C1.32921 10.5058 1.59642 10.6165 1.79345 10.8135C1.99047 11.0105 2.10115 11.2777 2.10115 11.5564C2.10115 11.835 1.99047 12.1022 1.79345 12.2992C1.59642 12.4963 1.32921 12.607 1.05058 12.607C0.771946 12.607 0.504728 12.4963 0.307707 12.2992C0.110686 12.1022 0 11.835 0 11.5564C0 11.2777 0.110686 11.0105 0.307707 10.8135C0.504728 10.6165 0.771946 10.5058 1.05058 10.5058Z"
                                            fill="#424458" />
                                        <path
                                            d="M1.05058 0C1.32921 0 1.59642 0.110685 1.79345 0.307707C1.99047 0.504728 2.10115 0.771947 2.10115 1.05058C2.10115 1.32921 1.99047 1.59643 1.79345 1.79345C1.59642 1.99047 1.32921 2.10115 1.05058 2.10115C0.771946 2.10115 0.504728 1.99047 0.307707 1.79345C0.110686 1.59643 0 1.32921 0 1.05058C0 0.771947 0.110686 0.504728 0.307707 0.307707C0.504728 0.110685 0.771946 0 1.05058 0Z"
                                            fill="#424458" />
                                        <path
                                            d="M14.9493 0C15.2279 0 15.4951 0.110685 15.6922 0.307707C15.8892 0.504728 15.9999 0.771947 15.9999 1.05058C15.9999 1.32921 15.8892 1.59643 15.6922 1.79345C15.4951 1.99047 15.2279 2.10115 14.9493 2.10115H4.08329C3.80466 2.10115 3.53744 1.99047 3.34042 1.79345C3.1434 1.59643 3.03271 1.32921 3.03271 1.05058C3.03271 0.771947 3.1434 0.504728 3.34042 0.307707C3.53744 0.110685 3.80466 0 4.08329 0H14.9493Z"
                                            fill="#424458" />
                                        <path
                                            d="M14.9493 5.25287C15.2279 5.25287 15.4951 5.36355 15.6922 5.56058C15.8892 5.7576 15.9999 6.02482 15.9999 6.30345C15.9999 6.58208 15.8892 6.8493 15.6922 7.04632C15.4951 7.24334 15.2279 7.35402 14.9493 7.35402H4.08329C3.80466 7.35402 3.53744 7.24334 3.34042 7.04632C3.1434 6.8493 3.03271 6.58208 3.03271 6.30345C3.03271 6.02482 3.1434 5.7576 3.34042 5.56058C3.53744 5.36355 3.80466 5.25287 4.08329 5.25287H14.9493Z"
                                            fill="#424458" />
                                        <path
                                            d="M14.9493 10.5058C15.2279 10.5058 15.4951 10.6165 15.6922 10.8135C15.8892 11.0105 15.9999 11.2777 15.9999 11.5564C15.9999 11.835 15.8892 12.1022 15.6922 12.2992C15.4951 12.4963 15.2279 12.607 14.9493 12.607H4.08329C3.80466 12.607 3.53744 12.4963 3.34042 12.2992C3.1434 12.1022 3.03271 11.835 3.03271 11.5564C3.03271 11.2777 3.1434 11.0105 3.34042 10.8135C3.53744 10.6165 3.80466 10.5058 4.08329 10.5058H14.9493Z"
                                            fill="#424458" />
                                    </svg>
                                </a>
                                <a class="list-view list-view-map" data-list_type="2" href="#">
                                    <svg width="13" height="16" viewBox="0 0 13 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.2858 0C2.81417 0 0 2.81417 0 6.2858C0 9.5348 1.99645 12.7289 5.92884 15.8749C6.13763 16.0417 6.43407 16.0417 6.64284 15.8749C10.5751 12.7289 12.5717 9.53461 12.5717 6.2858C12.5717 2.81417 9.75751 0 6.28588 0H6.2858ZM6.2858 3.42862C4.7078 3.42862 3.42862 4.7078 3.42862 6.2858C3.42862 7.8638 4.7078 9.14298 6.2858 9.14298C7.8638 9.14298 9.14298 7.8638 9.14298 6.2858C9.14298 4.7078 7.8638 3.42862 6.2858 3.42862Z"
                                            fill="#B6BAC6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>



                    <div class="featured-list" id="properties_list_show">

                    </div>


                    {{-- ===================================================
                        MAP VIEW STARTED
                        =================================================== --}}
                    <div class="featured-map-view">
                        <div class="map" id="map_with_pin" style="width:100%;height:450px;border:0; ">
                            {{-- <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13907.208347722504!2d47.97311198876576!3d29.376083612766713!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3fcf9c83ce455983%3A0xc3ebaef5af09b90e!2sKuwait%20City%2C%20Kuwait!5e0!3m2!1sen!2sin!4v1646396752284!5m2!1sen!2sin"
                                 style="border:0;" allowfullscreen="" loading="lazy"></iframe> --}}
                        </div>

                    </div>

                    {{-- loader --}}
                    <div class="filtter-loader d-none">
                        <img src="{{ asset('assets/img/section.gif') }}" alt="gif">
                    </div>

                    {{-- Error Messages to show --}}
                    <div class="text-center d-none" id="something_went_wrong" style="padding: 20% 0 10% 0">
                        <span id='message'>{{ $labels['something_went_wrong'] }}</span>
                        <br><br>
                        <a href="#" id="refresh_page" onclick="event.preventDefault();location.reload();"
                            class="forget-password d-none">
                            {{ $labels['refresh_page'] }}
                        </a>
                    </div>


                </div>

            </div>

            <input type="hidden" name="east" id="east">
            <input type="hidden" name="west" id="west">
            <input type="hidden" name="north" id="north">
            <input type="hidden" name="south" id="south">
        </div>
    </section>
@endsection

@push('after-scripts')
    
    <script>
        function sliderStopped(event, ui) {
            loadData();
        }

        function filter_loader_show() {
            $('.filtter-loader').removeClass('d-none');
        }

        function filter_loader_hide() {
            $('.filtter-loader').addClass('d-none');
        }

        function applyFilter(element, e) {
            e.preventDefault();
            loadData();
        }

        function gotoPage(__this, e) {
            e.preventDefault();
            $('.pagination-item').removeClass('active');
            loadData('', $(__this).data('page'));
        }

        function clearfilters() {
            $('input[type="checkbox"]').prop('checked', false);
            $('input[type="radio"]').prop('checked', false);
            $("#price").attr('data-val_min', $("#price").data('min'));
            $("#price").attr('data-val_max', $("#price").data('max'));
            
            var list_type = $('.list-view.active').data('list_type');
            if(list_type == 1) {
                loadData();
            }else{
                initialize();
            }
        }

        var ready = 1;

        function loadData(list_type = null, page_no = null) {

            if (!list_type) {
                list_type = $('.list-view.active').data('list_type');
            }

            if (list_type == 1) {
                filter_loader_show();
            }
            // if(ready){
            ready = 0;
            let timerStart = Date.now();
            let __url = "{{ route('frontend.fetchPropertyList') }}";

            let longitude = "";
            let latitude = "";
            let property_for_id = "";
            let min_price = $("#slider-3").slider("values", 0);
            let max_price = $("#slider-3").slider("values", 1);

            let property_type_id = $("input[name='property_type[]']:checked").map(function() {
                return this.value;
            }).get().join(',');

            let bedroom_type_id = $("input[name='bedroom_types[]']:checked").map(function() {
                return this.value;
            }).get().join(',');

            let bedroom_number = "";

            let bathroom_type_id = $("input[name='bathroom_types[]']:checked").map(function() {
                return this.value;
            }).get().join(',');

            let bathroom_number = "";
            let toilet_number = "";
            let max_area_sqft = $('input[name="area_sqft"]:checked').val();
            let condition_type_id = $('input[name="condition_type"]:checked').val();
            let completion_status_id = $('input[name="completion_status"]:checked').val();
            let area_id = $("#area_id").val();
            let property_id = $("#property_id").val();
            let sort_by = $('select[name="sort_by"]').val();
            if (!page_no) {
                page_no = $('.pagination-item.active').data('page');
                if (!page_no) {
                    page_no = 1;
                }
            }
            
            let east = $('#east').val();
            let west = $('#west').val();
            let north = $('#north').val();
            let south = $('#south').val();
            
            let list_type_id = $("#list_type_id").val();
            let s_property_type = $("#s_property_type").val();
            let s_property_for = $("#s_property_for").val();
            let s_search = $("#s_search").val();
            let is_search = $("#is_search").val();

            $.ajax({
                url: __url,
                type: 'post',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "longitude": longitude,
                    "latitude": latitude,
                    "property_for_id": property_for_id,
                    "max_price": max_price,
                    "min_price": min_price,
                    "property_type_id": property_type_id,
                    "bedroom_type_id": bedroom_type_id,
                    "bedroom_number": bedroom_number,
                    "bathroom_type_id": bathroom_type_id,
                    "bathroom_number": bathroom_number,
                    "toilet_number": toilet_number,
                    "condition_type_id": condition_type_id,
                    "completion_status_id": completion_status_id,
                    "area_id": area_id,
                    "property_id" : property_id,
                    "max_area_sqft": max_area_sqft,
                    "sort_by": sort_by,
                    "page_no": page_no,
                    "list_type": list_type,
                    "east" : east,
                    "west" : west,
                    "north" : north,
                    "south" : south,
                    "list_type_id" : list_type_id,
                    "s_property_type" : s_property_type,
                    "s_property_for" : s_property_for,
                    "s_search" : s_search,
                    "is_search" : is_search,
                },
                success: function(result) {
                    $('.featured-list,.featured-map-view,#something_went_wrong,#refresh_page').addClass(
                        'd-none');

                    if (result.statusCode == 201) {
                        $('#something_went_wrong').find('#message').text(result.message);
                        if(result.list_type == 2) {
                            $('.featured-map-view').removeClass('d-none');
                            $('.featured-map-list').remove();
                        }
                        $('#something_went_wrong').removeClass('d-none');
                    } else if (result.statusCode == 202) {
                        $('#something_went_wrong').find('#message').text(result.message);
                        $('#refresh_page').removeClass('d-none');
                        $('#something_went_wrong').removeClass('d-none');
                    } else if (result.statusCode == 200) {
                        if (result.list_type == 1) {
                            $('.featured-list').html(result.html).removeClass('d-none');
                        } else {
                            $('.featured-map-list').remove();
                            $('.featured-map-view .map').after(result.html);
                            $('.featured-map-view').removeClass('d-none');
                        }
                    }

                    console.info("Time fetching: ", Date.now() - timerStart);

                    /* changes after success needed */

                    setTimeout(() => {
                        filter_loader_hide();
                    }, 600);

                    ready = 1;
                },
                error: function(error) {
                    console.error(error);
                    console.info("Time response: ", Date.now() - timerStart);
                    $('.featured-list,.featured-map-view').addClass('d-none');
                    $('#something_went_wrong').removeClass('d-none');
                    filter_loader_hide();
                }
            });
            //}
        }

        $(document).ready(function() {

            loadData();

            $('.list-view').on('click', function(e) {
                e.preventDefault();
                if($('.list-view.active').data('list_type') == 2) {
                    initialize();
                }else{
                    $("#east").val('');
                    $("#west").val('');
                    $("#north").val('');
                    $("#south").val('');
                }
                loadData();
            })

            $('#sort_by').on('change', function(e) {
                e.preventDefault();
                loadData();
            });

            $('input[type="checkbox"], input[type="radio"]').on('input', function(e) {
                e.preventDefault();
                loadData();
                if($('.list-view.active').data('list_type') == 2) {
                    initialize();
                }
            });

        });
    </script>

    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDLMt-ql3hc0NXMA1O2VeNWjt51qm3x3kc"></script> --}}

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key={{env('GOOGLE_MAPS_KEY','AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4')}}" async></script>
    <script>
        var markersArray = [];

        function initialize(ready=false) {
            console.log('intializing map...');
            var originalMapCenter = new google.maps.LatLng({{$latitude}}, {{$longitude}});
            var map = new google.maps.Map(document.getElementById('map_with_pin'), {
                zoom: 8,
                center: originalMapCenter
            });

            var bounds = new google.maps.LatLngBounds();
            // var image = 'assets/img/indicate/Grren_nav.png';
            var green_image = 'assets/img/indicate/Grren_nav.png';
            var yellow_image = 'assets/img/indicate/Yellow_nav.png';
            var red_image = 'assets/img/indicate/Red_Nav.png';
            var image = "{{ asset('assets/img/map-view.svg') }}";
            var infowindow = new google.maps.InfoWindow();

            google.maps.event.addListener(map, 'tilt_changed', function() {
                // alert("tilt_changed");
                var north = map.getBounds().getNorthEast().lat().toFixed(7);
                var south = map.getBounds().getSouthWest().lat().toFixed(7);
                var east = map.getBounds().getNorthEast().lng().toFixed(7);
                var west = map.getBounds().getSouthWest().lng().toFixed(7);

                filterMap(east, west, north, south, markersArray, 0);
            })

            google.maps.event.addListener(map, 'dragend', function() {
                // alert("dragend");
                var north = map.getBounds().getNorthEast().lat().toFixed(7);
                var south = map.getBounds().getSouthWest().lat().toFixed(7);
                var east = map.getBounds().getNorthEast().lng().toFixed(7);
                var west = map.getBounds().getSouthWest().lng().toFixed(7);

                if (markersArray) {
                    for (i = 0; i < markersArray.length; i++) {
                        markersArray[i].setMap(null);
                    }
                    markersArray.length = 0;
                }

                filterMap(east, west, north, south, markersArray, 1);
                if($('.list-view.active').data('list_type') == 2) {
                    loadData();
                }
            });

            google.maps.event.addListener(map, 'zoom_changed', function() {
                // alert("zoom_changed");
                var north = map.getBounds().getNorthEast().lat().toFixed(7);
                var south = map.getBounds().getSouthWest().lat().toFixed(7);
                var east = map.getBounds().getNorthEast().lng().toFixed(7);
                var west = map.getBounds().getSouthWest().lng().toFixed(7);

                if (markersArray) {
                    for (i = 0; i < markersArray.length; i++) {
                        markersArray[i].setMap(null);
                    }
                    markersArray.length = 0;
                }

                filterMap(east, west, north, south, markersArray, 1);
                if($('.list-view.active').data('list_type') == 2) {
                    loadData();
                }
            });


            // map pin reload

            function reloadmarkers(LocationData) {

                // for (var i in LocationData) {
                    // console.log(image);
                    var p = LocationData;

                    let marker = new google.maps.Marker({
                        {{--  position : { lat: p['latitude'],lng: p['longitude'] },  --}}
                        position: new google.maps.LatLng(p['latitude'], p['longitude']),
                        map: map,
                        icon: image
                    });
                    markersArray.push(marker);

                    var xyz = p['property_popup'];

                    google.maps.event.addListener(marker, 'mouseover', (function(marker,content,infowindow) {
                        return function() {
                            marker.setIcon(image);
                            infowindow.setContent(xyz);
                            infowindow.open(map, marker);
                        }
                    })(marker,xyz,infowindow));

                    google.maps.event.addListener(marker, 'click', (function(marker,content,infowindow) {
                        return function() {
                            marker.setIcon(image);
                            infowindow.setContent(xyz);
                            infowindow.open(map, marker);
                        }
                    })(marker,xyz,infowindow));

                    {{--  google.maps.event.addListener(marker, 'mouseout', (function(marker, i) {
                        return function() {
                            marker.setIcon(image);
                            infowindow.close();
                        }
                    })(marker, i));  --}}

                // }
            }
            

            function filterMap(east, west, north, south, markersArray, is_bound) {

                list_type = $('.list-view.active').data('list_type');

                $("#east").val(east);
                $("#west").val(west);
                $("#north").val(north);
                $("#south").val(south);

                if (list_type == 1) {
                   return;
                }

                let timerStart = Date.now();

                let longitude = "";
                let latitude = "";
                let property_for_id = "";
                let min_price = $("#slider-3").slider("values", 0);
                let max_price = $("#slider-3").slider("values", 1);

                let property_type_id = $("input[name='property_type[]']:checked").map(function() {
                    return this.value;
                }).get().join(',');

                let bedroom_type_id = $("input[name='bedroom_types[]']:checked").map(function() {
                    return this.value;
                }).get().join(',');

                let bedroom_number = "";

                let bathroom_type_id = $("input[name='bathroom_types[]']:checked").map(function() {
                    return this.value;
                }).get().join(',');
                let bathroom_number = "";
                let toilet_number = "";
                let max_area_sqft = $('input[name="area_sqft"]:checked').val();
                let condition_type_id = $('input[name="condition_type"]:checked').val();
                let completion_status_id = $('input[name="completion_status"]:checked').val();
                let area_id = "";
                let sort_by = $('select[name="sort_by"]').val();
                page_no = $('.pagination-item.active').data('page');
                if (!page_no) {
                    page_no = 1;
                }

                let list_type_id = $("#list_type_id").val();
                let s_property_type = $("#s_property_type").val();
                let s_property_for = $("#s_property_for").val();
                let s_search = $("#s_search").val();
                let is_search = $("#is_search").val();

                var dataArr = {
                    'getMapLocationWithBounds': '1',
                    'is_bound': is_bound,
                    'east': east,
                    'west': west,
                    'north': north,
                    'south': south,
                    "_token": "{{ csrf_token() }}",
                    "longitude": longitude,
                    "latitude": latitude,
                    "property_for_id": property_for_id,
                    "max_price": max_price,
                    "min_price": min_price,
                    "property_type_id": property_type_id,
                    "bedroom_type_id": bedroom_type_id,
                    "bedroom_number": bedroom_number,
                    "bathroom_type_id": bathroom_type_id,
                    "bathroom_number": bathroom_number,
                    "toilet_number": toilet_number,
                    "condition_type_id": condition_type_id,
                    "completion_status_id": completion_status_id,
                    "area_id": area_id,
                    "max_area_sqft": max_area_sqft,
                    "sort_by": sort_by,
                    "page_no": page_no,
                    "list_type": list_type,
                    "list_type_id" : list_type_id,
                    "s_property_type" : s_property_type,
                    "s_property_for" : s_property_for,
                    "s_search" : s_search,
                    "is_search" : is_search,
                }

                var mapAjaxUrl = "{{ route('frontend.fetchPropertyList') }}";
                $.ajax({
                    type: "POST",
                    url: mapAjaxUrl,
                    data: dataArr,
                    success: function(data_one) {
                        if (typeof data_one == 'object') {
                            var LocationData = data_one;
                            if (markersArray) {
                                for (i = 0; i < markersArray.length; i++) {
                                    markersArray[i].setMap(null);
                                }
                                markersArray.length = 0;
                            }

                            // Reload All Markers
                            
                            for (var i in LocationData) {
                                reloadmarkers(LocationData[i]);
                            }
                            // return;
                            // Add Event Listener For MouseOver
                            $(".image_onhover").mouseover(function() {

                                var parent = $(this).parent().parent().parent().attr('id');
                                console.log({parent});

                                var q = parent;
                                var p = LocationData[q];

                                var latlng = new google.maps.LatLng(p['latitude'], p['longitude']);
                                bounds.extend(latlng);
                                var marker = new google.maps.Marker({
                                    position: latlng,
                                    map: map,
                                    icon: image
                                });


                                var xyz = p['property_popup'];

                                infowindow.setContent(xyz);
                                infowindow.open(map, marker);
                                marker.setIcon(image);

                            });

                            // Add Event Listener For MouseLeave
                            $(".image_onhover").mouseleave(function() {
                                infowindow.close();
                                for (var i in LocationData) {
                                    reloadmarkers(LocationData[i]);
                                }
                            });
                        }
                    }
                });
            }

        } // initialize END
    </script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize"></script> --}}
@endpush
