@php
    use App\Models\UserFavouriteProperty;
@endphp
@foreach ($properties as $property)
<div class="col-xxl-4 col-lg-6 col-sm-6">
    <div class="featured-properties-box">
        <a href="{{ route('frontend.property_details', ['id' => $property->PropertyDetails->slug]) }}">
            <div class="featured-properties-img">
                @php
                    $area_name = '';
                    $country_name = '';
                    if ($property->PropertyDetails->area_id) {
                        if ($language_id == 1) {
                            $area_name = @urldecode($property->PropertyDetails->areaDetails->name) ?: '';
                            $country_name = @urldecode($property->PropertyDetails->areaDetails->country->name) ?: '';
                        } else {
                            if (@$property->PropertyDetails->areaDetails->childdata[0]->name) {
                                $area_name = urldecode($property->PropertyDetails->areaDetails->childdata[0]->name) ?: '';
                            } else {
                                $area_name = urldecode($property->PropertyDetails->areaDetails->name) ?: '';
                            }
                    
                            if (@$property->PropertyDetails->areaDetails->country->childdata[0]->name) {
                                $country_name = @urldecode($property->PropertyDetails->areaDetails->country->childdata[0]->name) ?: '';
                            } else {
                                $country_name = @urldecode($property->PropertyDetails->areaDetails->country->name) ?: '';
                            }
                        }
                    }
                    $short_address = $area_name && $country_name ? $area_name . ', ' . $country_name : '';
                    
                    $currency = @$property->PropertyDetails->areaDetails->country->currency_code ?: 'KD';
                    
                    $property_image = asset('assets/dashboard/images/no_image.png');
                    if ($property->PropertyDetails->propertyImages->count() > 0) {
                        $property_image = asset('storage/property_images/' . $property->PropertyDetails->id . '/' . $property->PropertyDetails->propertyImages[0]->property_image);
                    }
                    
                    $is_fav = '0';
                    if ($user_id) {
                        $is_fav = UserFavouriteProperty::where('user_id', '=', $user_id)
                            ->where('property_id', '=', $property->PropertyDetails->id)
                            ->exists();
                        $is_fav = $is_fav ? 1 : 0;
                    }
                @endphp
                <img src="{{ $property_image }}" alt="{{ $property->PropertyDetails->property_name }}" />
                <span class="badge {{ config('constants.PROPERTY_FOR.' . $property->PropertyDetails->property_for . '.badge_class') }}">{{ $property->PropertyDetails->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
            </div>
            <div class="featured-properties-box-content">
                <p> <img src="{{ asset('assets/img/location.svg') }}" alt="location">{{ $short_address }}</p>
                <h3>{{ $property->PropertyDetails->property_name }}</h3>
                <h5>{{ number_format(Helper::tofloat($property->PropertyDetails->price_area_wise), 3, '.', '') }}
                    {{ $currency }}</h5>
                    <div class="featured-properties-icon">
                        <span><img src="{{ asset('assets/img/Featured1.svg') }}"
                                alt="" />{{ $property->PropertyDetails->total_bathrooms }}</span>
                        <span><img src="{{ asset('assets/img/Featured2.svg') }}"
                                alt="" />{{ $property->PropertyDetails->total_bedrooms }}</span>
                        <span><img src="{{ asset('assets/img/Featured3.svg') }}"
                                alt="" />{{ $property->PropertyDetails->total_toilets }}</span>
                        <span><img src="{{ asset('assets/img/Featured4.svg') }}"
                                alt="" />{{ $property->PropertyDetails->property_sqft_area }} Sqft</span>
                    </div>
            </div>
        </a>
        <div class="heart-icon-box {{ $is_fav == 1 ? 'heart' : ''}}" onclick="addRemoveFav(this,{{ $is_fav }},{{ $property->PropertyDetails->id }},1)">
            <img class="heart-icon heart-icon-fill" src="{{asset('assets/img/heart-fill.svg')}}" alt="" />
            <img class="heart-icon heart-icon-border" src="{{asset('assets/img/heart.svg')}}" alt="" />
        </div>
    </div>
</div>
@endforeach