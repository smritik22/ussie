@extends('frontEnd.layout')
@section('content')
    @php
    use App\Models\UserFavouriteProperty;
    @endphp
    <div class="banner">
        <div class="owl-carousel owl-theme">
            <div class="item"><img src="{{ asset('assets/img/banner1.jpg') }}" alt="banner-img" /></div>
            <div class="item"><img src="{{ asset('assets/img/banner.png') }}" alt="banner-img" /></div>
        </div>
        <div class="banner-text">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="banner-inner-text">
                            <h1>{{ $labels['buy_and_rent'] }} <span>{!! $labels['buy_rent_properties_in_a_fastest_way'] !!}</span></h1>
                            <p>{!! $labels['now_you_can_find_more_then_number_property_choices'] !!}</p>
                            <div class="search-bar">
                                <form id="search_form" method="GET" action="#" enctype="multipart/form-data">
                                    <div class="search-location">
                                        <label for="location" class="form-label">{{ $labels['location'] }}</label>
                                        <input type="text" class="form-control" id="search_text"
                                            placeholder="{{ $labels['enter_city_area_building_name'] }}">
                                    </div>
                                    <div class="property-type">
                                        <label for="property_type"
                                            class="form-label">{{ $labels['property_type'] }}</label>
                                        <select class="form-control" name="property_type" id="property_type">
                                            @foreach ($property_type as $type)
                                                <option value="{{ strtolower($type->type) }}">
                                                    {{ $language_id != 1 && @$type->childdata[0]->type ? $type->childdata[0]->type : $type->type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="type-for">
                                        <label for="property_for"
                                            class="form-label">{{ $labels['property_for'] }}</label>
                                        <select class="form-control" name="property_for" id="property_for">
                                            <option
                                                value="{{ config('constants.PROPERTY_FOR_TEXT.sell.front_label_key') }}">
                                                {{ $labels['buy'] }}</option>
                                            <option
                                                value="{{ config('constants.PROPERTY_FOR_TEXT.rent.front_label_key') }}">
                                                {{ $labels['rent'] }}</option>
                                        </select>
                                    </div>
                                    <button type="submit" class=""><img
                                            src="{{ asset('assets/img/search-icon.svg') }}" alt="icon" /><span
                                            class="d-sm-none d-block ms-3">{{ $labels['search'] }}</span></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section-pading property">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{ $labels['property_areas'] }} <a
                            href="{{ route('frontend.area_list') }}">{{ $labels['view_all'] }}</a></h2>
                </div>
                <!-- <div class="property-areas"> -->

                @foreach ($area_list as $area)
                    <div class="col-md-2">
                        <div class="property-areas-box">
                            <a
                                href="{{ route('frontend.propertylist', ['id' => 'area']) . '?' . http_build_query(['area' => $area->slug]) }}">
                                <img src="{{ $area->image ? asset('assets/dashboard/images/areas/' . $area->image) : '' }}"
                                    alt="{{ $language_id != 1 && @$area->childdata[0]->name ? urldecode($area->childdata[0]->name) : urldecode($area->name) }}" />
                                <h4>{{ $language_id != 1 && @$area->childdata[0]->name ? urldecode($area->childdata[0]->name) : urldecode($area->name) }}
                                </h4>
                            </a>
                        </div>
                    </div>
                @endforeach
                <!-- </div> -->
            </div>
        </div>
    </section>
    @if ($properties->count() > 0)
        <section class="section-pading featured-properties">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>{{ $labels['featured_properties'] }} <a
                                href="{{ route('frontend.propertylist', ['id' => 'featured']) }}">{{ $labels['view_all'] }}</a>
                        </h2>
                    </div>

                    @foreach ($properties as $property)
                        <div class="col-md-4 col-sm-6">
                            <div class="featured-properties-box">
                                <a href="{{ route('frontend.property_details', ['id' => $property->slug]) }}">
                                    <div class="featured-properties-img">
                                        <?php
                                        if ($property->propertyImages->count() > 0) {
                                            $property_image = asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[0]->property_image);
                                        } else {
                                            $property_image = asset('assets/dashboard/images/no_image.png');
                                        }
                                        ?>
                                        <img src="{{ $property_image }}" alt="{{ $property->property_name }}" />
                                        <span
                                            class="badge badge-{{ $property->property_for == config('constants.PROPERTY_FOR_RENT') ? 'rent' : 'buy' }}">{{ \Helper::getLabelValueByKey(config('constants.PROPERTY_FOR.' . $property->property_for . '.front_label_key'), $language_id) }}</span>
                                    </div>
                                    <div class="featured-properties-box-content">
                                        @php
                                            $currency = @$property->areaDetails->country->currency_code ?: 'KD';
                                            $is_fav = 0;
                                            if (Auth::guard('web')->check()) {
                                                $is_fav = UserFavouriteProperty::where('user_id', '=', Auth::guard('web')->id())
                                                    ->where('property_id', '=', $property->id)
                                                    ->exists();
                                            }
                                            $area_name = '';
                                            $country_name = '';
                                            if ($property->area_id) {
                                                if ($language_id == 1) {
                                                    $area_name = @urldecode($property->areaDetails->name) ?: '';
                                                    $country_name = @urldecode($property->areaDetails->country->name) ?: '';
                                                } else {
                                                    if (@$property->areaDetails->childdata[0]->name) {
                                                        $area_name = urldecode($property->areaDetails->childdata[0]->name) ?: '';
                                                    } else {
                                                        $area_name = urldecode($property->areaDetails->name) ?: '';
                                                    }
                                            
                                                    if (@$property->areaDetails->country->childdata[0]->name) {
                                                        $country_name = @urldecode($property->areaDetails->country->childdata[0]->name) ?: '';
                                                    } else {
                                                        $country_name = @urldecode($property->areaDetails->country->name) ?: '';
                                                    }
                                                }
                                            }
                                            
                                            $short_address = $area_name && $country_name ? $area_name . ', ' . $country_name : '';
                                        @endphp
                                        <p> <img src="{{ asset('assets/img/location.svg') }}" alt="location">
                                            {{ $short_address }}</p>
                                        <h3>{{ $property->property_name }}</h3>
                                        @php
                                            $property_price = @\Auth::guard('web')->check() && \Auth::guard('web')->id() == $property->agent_id ? $property->base_price : $property->price_area_wise;
                                        @endphp
                                        <h5>{{ number_format(Helper::tofloat($property_price), 3, '.', '') . ' ' . $currency }}
                                        </h5>
                                        <div class="featured-properties-icon">
                                            <span title="{{ $labels['bathroom_numbers'] }}"><img
                                                    src="{{ asset('assets/img/Featured1.svg') }}"
                                                    alt="{{ $labels['bathroom_numbers'] }}" />{{ $property->total_bathrooms ?: 0 }}</span>
                                            <span title="{{ $labels['bedroom_numbers'] }}"><img
                                                    src="{{ asset('assets/img/Featured2.svg') }}"
                                                    alt="{{ $labels['bedroom_numbers'] }}" />{{ $property->total_bedrooms ?: 0 }}</span>
                                            <span title="{{ $labels['toilet_numbers'] }}"><img
                                                    src="{{ asset('assets/img/Featured3.svg') }}"
                                                    alt="{{ $labels['toilet_numbers'] }}" />{{ $property->total_toilets ?: 0 }}</span>
                                            <span title="{{ $labels['area_sqft'] }}"><img
                                                    src="{{ asset('assets/img/Featured4.svg') }}"
                                                    alt="{{ $labels['area_sqft'] }}" />{{ $property->property_sqft_area ?: 0 }}
                                                {{ $labels['sqft'] }}</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="heart-icon-box {{ $is_fav > 0 ? 'heart' : '' }}"
                                    onclick="addRemoveFav(this, {{ $is_fav }}, {{ $property->id }})">
                                    <img class="heart-icon heart-icon-fill" src="{{ asset('assets/img/heart-fill.svg') }}"
                                        alt="" />
                                    <img class="heart-icon heart-icon-border" src="{{ asset('assets/img/heart.svg') }}"
                                        alt="" />
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif

    <section class="section-pading download pt-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="download-inner">
                        <div class="download-text">
                            <h4>{{ $labels['download_our_dom_app_now'] }}</h4>
                            <p>{{ $labels['get_the_latest_update_from_us'] }}</p>
                        </div>
                        <div class="download-btn">
                            <a href="{{ Helper::getPlayStoreAppLink() }}" target="__blank">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.50183 0.283279C3.09173 0.0183056 2.5992 -0.0617942 2.13721 0.0472553L10.2558 8.16597L12.8871 5.53468L3.50183 0.283279Z"
                                        fill="white" />
                                    <path
                                        d="M1.12972 0.707275C0.903221 1.00832 0.772949 1.37792 0.772949 1.7689V16.2307C0.772949 16.6217 0.903296 16.9913 1.12972 17.2923L9.42221 8.99984L1.12972 0.707275Z"
                                        fill="white" />
                                    <path
                                        d="M16.3214 7.4562L13.9566 6.13306L11.0898 8.99984L13.9568 11.8667L16.3218 10.5435C16.8884 10.226 17.2267 9.64904 17.2267 8.99984C17.2267 8.35065 16.8884 7.77367 16.3214 7.4562Z"
                                        fill="white" />
                                    <path
                                        d="M10.2554 9.83374L2.13721 17.9519C2.27033 17.9832 2.40578 18.0001 2.54123 18.0001C2.87595 18.0001 3.20955 17.9049 3.50138 17.7164L12.8868 12.465L10.2554 9.83374Z"
                                        fill="white" />
                                </svg>
                                {{ $labels['google_play'] }}</a>
                            <a class="app-store-btn" href="{{ Helper::getAppStoreAppLink() }}" target="__blank">
                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M14.4722 10.6385C14.4823 9.87196 14.6866 9.1204 15.0662 8.4537C15.4457 7.78701 15.988 7.22683 16.6429 6.82524C16.2269 6.23249 15.678 5.74467 15.0398 5.40055C14.4017 5.05642 13.6918 4.86548 12.9667 4.8429C11.4202 4.68093 9.92037 5.76611 9.13231 5.76611C8.32872 5.76611 7.11557 4.85909 5.80849 4.88585C4.96326 4.91297 4.1395 5.15803 3.41752 5.59715C2.69553 6.03626 2.09995 6.65444 1.68884 7.39142C-0.0948814 10.466 1.23408 14.9863 2.94013 17.4693C3.79386 18.6869 4.79164 20.0467 6.0973 19.9988C7.37472 19.946 7.85207 19.1862 9.39429 19.1862C10.9224 19.1862 11.3715 19.9988 12.7019 19.9679C14.0732 19.946 14.9368 18.7453 15.7602 17.5165C16.3738 16.6491 16.8459 15.6903 17.1591 14.6757C16.3636 14.3396 15.6848 13.7777 15.2071 13.0599C14.7294 12.3421 14.4738 11.5 14.4722 10.6385Z"
                                        fill="white" />
                                    <path
                                        d="M11.9541 3.20625C12.7016 2.31146 13.0699 1.16127 12.9808 0C11.8378 0.119065 10.7818 0.663465 10.0235 1.52461C9.65313 1.94497 9.36948 2.43398 9.1887 2.9637C9.00793 3.49342 8.93358 4.05347 8.9699 4.61185C9.54158 4.6183 10.1073 4.49513 10.6242 4.25163C11.1412 4.00813 11.5959 3.65068 11.9541 3.20625Z"
                                        fill="white" />
                                </svg>
                                {{ $labels['app_store'] }}</a>
                        </div>
                        <img class="bg-bulding" src="{{ asset('assets/img/bg_footer.svg') }}" alt="bg_footer" />
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('after-scripts')
    <script>
        function initMap() {
            const center = {
                lat: 50.064192,
                lng: -130.605469
            };
            // Create a bounding box with sides ~10km away from the center point
            const defaultBounds = {
                north: center.lat + 0.1,
                south: center.lat - 0.1,
                east: center.lng + 0.1,
                west: center.lng - 0.1,
            };
            const input = document.getElementById("search_text");
            const options = {
                bounds: defaultBounds,
                componentRestrictions: {
                    country: "ku"
                },
                fields: ["address_components", "geometry", "icon", "name"],
                strictBounds: false,
                types: ["establishment"],
            };
            const autocomplete = new google.maps.places.Autocomplete(input, options);
        }

        $().ready(function() {
            $("#search_form").on('submit', function(e) {
                e.preventDefault();
                let property_type = $("#property_type").val();
                let property_for = $("#property_for").val();
                let search_text = $("#search_text").val();

                var _searchPropertyUrl = "{{ route('frontend.propertylist') }}";
                _searchPropertyUrl += '?ptype=' + property_type + '&pfor=' + property_for + '&s=' +
                    search_text;

                console.log({
                    _searchPropertyUrl
                });
                window.location.href = _searchPropertyUrl;
            })
        })
    </script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY', 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4') }}&libraries=places&callback=initMap">
    </script>
@endpush
