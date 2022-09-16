@php
use App\Models\UserFavouriteProperty;
@endphp
@foreach ($properties as $property)
    <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6 propertyMainBoxClass">
        <div class="featured-properties-box">
            <a href="{{ route('frontend.property_details', ['id' => $property->slug]) }}">
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
                <div class="featured-properties-img">
                    <img src="{{ $property_image }}" alt="{{ $property->property_name }}" />
                    @if($property->property_subscription_enddate <= date('Y-m-d H:i:s'))
                        <span class="badge badge-expired">{{strtoupper($labels['expired'])}}</span>
                    @endif
                </div>
                <div class="featured-properties-box-content">
                    <p> <img src="{{asset('assets/img/location.svg')}}" alt="location">{{$short_address}}</p>
                    <h3>{{ $property->property_name }}</h3>
                    @php
                        $property_price = (@\Auth::guard('web')->check() && \Auth::guard('web')->id() == $property->agent_id) ? $property->base_price : $property->price_area_wise;
                    @endphp
                    <h5 class="my-ads-prize ">{{ number_format(Helper::tofloat($property_price), 3, '.', '') }}
                        {{ $currency }} </h5>
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
            <div class="heart-icon-box {{ $is_fav ? 'heart' : '' }}"  onclick="addRemoveFav(this,{{ $is_fav }},{{ $property->id }})">
                <img class="heart-icon heart-icon-fill" src="{{asset('assets/img/heart-fill.svg')}}" alt="" />
                <img class="heart-icon heart-icon-border" src="{{asset('assets/img/heart.svg')}}" alt="" />
            </div>
            <div class="my-ads-icon-box">
                <span class="my-ads-icon">
                    <a href="{{route('frontend.property.add', ['id' => $property->slug])}}">
                        <span><img src="{{asset('assets/img/edit.svg')}}" alt="icon" /></span>
                    </a>
                    <span onclick="delete_property(this, {{ $property->id }})"><img src="{{asset('assets/img/delete.svg')}}" alt="icon" /></span>
                </span>
            </div>
        </div>
    </div>
@endforeach
