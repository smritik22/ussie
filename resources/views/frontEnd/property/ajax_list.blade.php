@php
use App\Models\UserFavouriteProperty;
@endphp
{{-- <div class="{{($list_type==1) ? 'featured-list' : 'featured-map-view'}}"> --}}
@if ($list_type == 2)
    <div class="featured-map-list">
@endif
<div class="row">
    @foreach ($properties as $property)
        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
            <div class="featured-properties-box">
                <a href="{{ route('frontend.property_details', ['id' => $property->slug]) }}">
                    <div class="featured-properties-img">
                        @php
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
                            
                            $currency = @$property->areaDetails->country->currency_code ?: 'KD';
                            
                            $property_image = asset('assets/dashboard/images/no_image.png');
                            if ($property->propertyImages->count() > 0) {
                                $property_image = asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[0]->property_image);
                            }
                            
                            $is_fav = '0';
                            if ($user_id) {
                                $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)
                                    ->where('property_id', '=', $property->id)
                                    ->exists();
                                $is_fav = $is_fav ? 1 : 0;
                            }
                        @endphp
                        <img src="{{ $property_image }}" alt="{{ $property->property_name }}" />

                        <span
                            class="badge {{ config('constants.PROPERTY_FOR.' . $property->property_for . '.badge_class') }}">{{ $property->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
                    </div>
                    <div class="featured-properties-box-content">
                        <p> <img src="{{ asset('assets/img/location.svg') }}" alt="location">{{ $short_address }}</p>
                        <h3>{{ $property->property_name }}</h3>
                        @php
                            $property_price = (@\Auth::guard('web')->check() && \Auth::guard('web')->id() == $property->agent_id) ? $property->base_price : $property->price_area_wise;
                        @endphp
                        {{-- <h5>{{number_format(Helper::tofloat($property->price_area_wise))}} </h5> --}}
                        <h5>{{ number_format(Helper::tofloat($property_price), 3, '.', '') }}
                            {{ $currency }}</h5>
                        <div class="featured-properties-icon">
                            <span><img src="{{ asset('assets/img/Featured1.svg') }}"
                                    alt="" />{{ $property->total_bathrooms }}</span>
                            <span><img src="{{ asset('assets/img/Featured2.svg') }}"
                                    alt="" />{{ $property->total_bedrooms }}</span>
                            <span><img src="{{ asset('assets/img/Featured3.svg') }}"
                                    alt="" />{{ $property->total_toilets }}</span>
                            <span><img src="{{ asset('assets/img/Featured4.svg') }}"
                                    alt="" />{{ $property->property_sqft_area }} Sqft</span>
                        </div>
                    </div>
                </a>
                <div class="heart-icon-box {{ $is_fav ? 'heart' : '' }}"
                    onclick="addRemoveFav(this,{{ $is_fav }},{{ $property->id }})">
                    <img class="heart-icon heart-icon-fill" src="{{ asset('assets/img/heart-fill.svg') }}" alt="" />
                    <img class="heart-icon heart-icon-border" src="{{ asset('assets/img/heart.svg') }}" alt="" />
                </div>
            </div>
        </div>
    @endforeach


    <div class="col-12">
        <div class="pagination-box">
            <ul>
                <li
                    class="paginatiob-left {{ $pagination_data['current_page'] != 1 ? 'pagination-icon-active' : '' }}">
                    <a href="#" onclick="gotoPage(this,event);"
                        style="{{ 1 >= $pagination_data['current_page'] ? 'pointer-events: none;' : '' }}"
                        data-page="{{ $pagination_data['current_page'] - 1 }}">
                        <img src="{{ asset('assets/img/pagination-ivon.svg') }}" alt="icon2" />
                    </a>
                </li>
                {{--  {!! $properties->render() !!}  --}}
                @for ($i = 1; $i <= $pagination_data['last_page']; $i++)
                    {{--  @if ($i == 3 && $pagination_data['last_page'] > $i + 2)
                        <li class="pagination-item">...</li>
                        @php
                            $i = $pagination_data['last_page'] - 1;
                        @endphp
                    @else  --}}
                        <li class="pagination-item {{ $i == $pagination_data['current_page'] ? 'active' : '' }}"
                            data-page="{{ $i }}">
                            <a href="#"
                                style="{{ $i == $pagination_data['current_page'] ? 'pointer-events: none;' : '' }}"
                                onclick="gotoPage(this,event);"
                                data-page="{{ $i }}">{{ $i }}</a>
                        </li>
                    {{--  @endif  --}}
                @endfor
                <li
                    class="paginatiob-right {{ $pagination_data['current_page'] != $pagination_data['last_page'] ? 'pagination-icon-active' : '' }}">
                    <a href="#" onclick="gotoPage(this,event);"
                        style="{{ $pagination_data['last_page'] <= $pagination_data['current_page'] ? 'pointer-events: none;' : '' }}"
                        data-page="{{ $pagination_data['last_page'] > $pagination_data['current_page']? $pagination_data['current_page'] + 1: $pagination_data['current_page'] }}">
                        <img src="{{ asset('assets/img/pagination-ivon.svg') }}" alt="icon" />
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
@if ($list_type == 2)
    </div>
@endif
{{-- </div> --}}
