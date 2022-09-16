@extends('frontEnd.layout')
@section('content')
    @php
        use App\Models\PropertyImages;
        use App\Models\UserFavouriteProperty;
    @endphp
    <section class="add-new-property-thank-you">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="thank-you-img-detail">
                        <img src="{{ asset('assets/img/thank-you.svg') }}" alt="icon" />
                        <h2>{{ $labels['thank_you'] }}</h2>
                        <p class="mb-0">{{ $labels['your_payment_has_been_successfully_received'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="select-plan select-plan-thankyou">
                    <div class="thank-you-plan">
                        <p class="select-plan-heading">{{ $labels['plan_details'] }}</p>
                        <div class="subscription-plan-box-price basic-plan ">
                            <div class="form-check form-check-inline p-0 m-0">
                                <div class="subscription-plan-detail ms-0">
                                    @php
                                        $plan_duration = \Helper::getValidTillDate(date('Y-m-d H:i:s'), $userSubscription->plan_duration_value, $userSubscription->plan_duration_type);
                                    @endphp
                                    <h3>{{ $language_id != 1 && @$userSubscription->plan_name_ar? $userSubscription->plan_name_ar: $userSubscription->plan_name }}
                                        <span>{{ $userSubscription->plan_price . ' ' . \Helper::getDefaultCurrency() }}
                                            / {{ $plan_duration['value'] . ' ' . $plan_duration['label_value'] }}</span>
                                    </h3>
                                    <p>{!! $language_id != 1 && @$userSubscription->plan_description_ar ? $userSubscription->plan_description_ar : $userSubscription->plan_description !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="select-property-detail">
                        <div class="row justify-content-center">
                            @foreach ($properties as $property)
                                <div class="col-md-6 col-sm-6 propertyMainBoxClass">
                                    <div class="featured-properties-box mb-0">
                                        <a target="_blank" href="{{route('frontend.property_details', ['id' => $property->slug])}}">
                                            <div class="featured-properties-img">
                                                @php
                                                    $property_image_url = PropertyImages::where('property_id', '=', $property->id)
                                                        ->orderBy('id', 'asc')
                                                        ->first();
                                                    
                                                    $image_url = asset('assets/dashboard/images/no_image.png');
                                                    if ($property_image_url) {
                                                        $image_url = asset('storage/property_images/' . $property->id . '/' . $property_image_url->property_image);
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
                                                <img src="{{ $image_url }}" alt="" />
                                                <span
                                                    class="badge {{ config('constants.PROPERTY_FOR.' . $property->property_for . '.badge_class') }}">{{ $property->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
                                            </div>
                                            <div class="featured-properties-box-content">
                                                <p> <img src="{{ asset('assets/img/location.svg') }}" alt="location">
                                                    {{ $short_address }}</p>
                                                <h3>{{ $property->property_name }}</h3>
                                                <h5>{{ number_format(Helper::tofloat($property->base_price), 3, '.', '') }}
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
                                            <img class="heart-icon heart-icon-fill"
                                                src="{{ asset('assets/img/heart-fill.svg') }}" alt="" />
                                            <img class="heart-icon heart-icon-border"
                                                src="{{ asset('assets/img/heart.svg') }}" alt="" />
                                        </div>
                                        <div class="my-ads-icon-box">
                                            <span class="my-ads-icon">
                                                <a
                                                    href="{{ route('frontend.property.add', ['id' => $property->slug]) }}">
                                                    <span><img src="{{ asset('assets/img/edit.svg') }}" alt="icon"></span>
                                                </a>
                                                <span onclick="delete_property(this, {{ $property->id }})"><img
                                                        src="{{ asset('assets/img/delete.svg') }}" alt="icon"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="select-plan-img">
                    <img src="{{ asset('assets/img/login-icon.svg') }}" alt="icon" />
                </div>
            </div>
        </div>
    </section>
@endsection
