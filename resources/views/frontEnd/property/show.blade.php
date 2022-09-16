@extends('frontEnd.layout')
@section('content')
@php
    use App\Helper\Helper;
    use App\Models\UserFavouriteProperty;
@endphp
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/lightboxed.css') }}">
    <style>
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown), html.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) { 
            height: 100% !important; overflow-y: visible !important; 
        }
    </style>
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('frontend.homePage') }}">{{ $labels['home'] }}</a>
                            </li>
                            <li class="breadcrumb-item"><img src="{{ asset('assets/img/bread.svg') }}" alt="icon" /><a
                                    href="{{ route('frontend.propertylist', ['id' => 'featured']) }}">{{ $labels['featured_properties'] }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><img
                                    src="{{ asset('assets/img/bread.svg') }}"
                                    alt="icon" />{{ @$property->property_name ?: '' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>


    <section class="detail-page-image">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="detail-page-img-box">
                        @php
                            $property_image1 = $property->propertyImages->count() > 0 ? asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[0]->property_image) : asset('assets/dashboard/images/no_image.png');
                        @endphp
                        <img class="lightboxed" rel="group1" src="{{ $property_image1 }}"
                            data-link="{{ $property_image1 }}" alt="image" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="detail-page-right">
                        <div class="row">
                            @if ($property->propertyImages->count() > 1)
                                @foreach ($property->propertyImages as $key => $image)
                                    @if ($key == 0)
                                        @php
                                            continue;
                                        @endphp
                                    @endif
                                    @php
                                        $property_image = $image->property_image ? asset('storage/property_images/' . $property->id . '/' . $image->property_image) : asset('assets/dashboard/images/no_image.png');
                                    @endphp
                                    @if ($key <= 3)
                                        <div class="col-6">
                                            <div class="detail-page-img-box detail-page-img-box-right">
                                                <img class="lightboxed" rel="group1" src="{{ $property_image }}"
                                                    data-link="{{ $property_image }}" alt="image" />
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @php
                                    $property_image4 = @$property->propertyImages[4]->property_image ? asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[4]->property_image) : '';
                                @endphp
                                @if ($property_image4)
                                    <div class="col-6">
                                        <div class="detail-page-img-box detail-page-img-box-right mb-0">
                                            <img class="lightboxed" rel="group1" src="{{ $property_image }}"
                                                data-link="{{ $property_image }}" alt="image" />
                                            <div class="show-all-photo-btn">
                                                <a class="comman-btn  lightboxed" rel="group1"
                                                    href="{{ $property_image }}"
                                                    data-link="{{ $property_image }}">Show All Photos</a>
                                            </div>
                                            @for ($i = 5; $i < $property->propertyImages->count(); $i++)
                                                @php
                                                    $property_image_url = $property->propertyImages[$i]->property_image ? asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[$i]->property_image) : asset('assets/dashboard/images/no_image.png');
                                                @endphp
                                                <a class="lightboxed" rel="group1" href="{{ $property_image_url }}"
                                                    data-link="{{ $property_image_url }}"></a>
                                            @endfor

                                        </div>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="property-detail">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="property-detail-left">
                        <div class="property-detail-top">
                            <div class="property-detail-top-left">
                                <span
                                    class="badge {{ config('constants.PROPERTY_FOR.' . $property->property_for . '.badge_class') }}">{{ $property->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
                                <p>{{ $property->property_id }}</p>
                                <p>{{ $language_id != 1 && @$property->propertyTypeDetails->childdata[0]->type? urldecode($property->propertyTypeDetails->childdata[0]->type): urldecode($property->propertyTypeDetails->type) }}
                                </p>
                            </div>
                            @php
                                $is_fav = 0;
                                if (Auth::guard('web')->check()) {
                                    $is_fav = UserFavouriteProperty::where('user_id', '=', Auth::guard('web')->id())
                                        ->where('property_id', '=', $property->id)
                                        ->exists();
                                    $is_fav = $is_fav ? 1 : 0;
                                }
                            @endphp
                            <div class="property-detail-top-right">
                                <a class="m-0 roperty-detail-icon" href="{{ @$proeprty_deeplink ?: '#' }}"><img
                                        src="{{ asset('assets/img/share.svg') }}" alt="icon" /></a>
                                <span class="roperty-detail-icon {{ $is_fav ? 'heart' : '' }}"
                                    onclick="addRemoveFav(this,{{ $is_fav }},{{ $property->id }})">
                                    <img class="heart-black" src="{{ asset('assets/img/heart-black.svg') }}"
                                        alt="icon" />
                                    <img class="heart-black-icon-fill" src="{{ asset('assets/img/heart-fill.svg') }}"
                                        alt="" />
                                </span>
                                @if(@\Auth::guard('web')->check() && \Auth::guard('web')->id() == $property->agent_id) 
                                    <a class="roperty-detail-icon" href="{{route('frontend.account')}}">
                                        <img src="{{ asset('assets/img/edit.svg') }}" alt="icon" />
                                    </a>
                                @else
                                    <a class="roperty-detail-icon" href="#" data-bs-toggle="modal" data-bs-target="#accountmodal">
                                        <img src="{{ asset('assets/img/user.svg') }}" alt="icon" />
                                    </a>
                                @endif
                            </div>
                        </div>
                        <h2>{{ $property->property_name }}</h2>
                        @php
                            $property_price = (@\Auth::guard('web')->check() && \Auth::guard('web')->id() == $property->agent_id) ? $property->base_price : $property->price_area_wise;
                        @endphp
                        <h3>{{number_format(\Helper::tofloat($property_price), 3, '.', '')}} {{ @$property->areaDetails->country->currency_code ?: 'KD' }}</h3>
                        {{--  <h3>{{ @Auth::guard('web')->id() == $property->agent_id ? $property->base_price : $property->price_area_wise }}  </h3> --}}
                        <ul>
                            <li><img src="{{ asset('assets/img/detail1.svg') }}"
                                    alt="icon" />{{ $property->total_bathrooms }} {{ $labels['bathrooms'] }}</li>
                            <li><img src="{{ asset('assets/img/detail12.svg') }}"
                                    alt="icon" />{{ $property->total_toilets }} {{ $labels['toilet'] }}</li>
                            <li><img src="{{ asset('assets/img/detail3.svg') }}"
                                    alt="icon" />{{ $property->total_bedrooms }} {{ $labels['bedrooms'] }}</li>
                            <li><img src="{{ asset('assets/img/detail3.svg') }}"
                                    alt="icon" />{{ $property->property_sqft_area }} {{ $labels['sqft'] }}</li>
                        </ul>
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
                        @endphp
                        <h5><img src="{{ asset('assets/img/detail-location.svg') }}" alt="icon" />
                            {{ $short_address }}
                        </h5>
                        <div class="description-box">
                            <h4>{{ $labels['description'] }}</h4>
                            <div class="description-text description-text-more">
                                {!! $property->property_description !!}
                            </div>

                            <span class="show-more">{{ $labels['show_more'] }}</span>
                        </div>
                        <div class="amenities-box ">
                            <h4>{{ $labels['amenities'] }}</h4>
                            <ul class="amenities-items">
                                @foreach ($property_amenities as $key => $value)
                                    <li>{{ $language_id != 1 && @$value->childdata[0]->amenity_name? urldecode($value->childdata[0]->amenity_name): urldecode($value->amenity_name) }}
                                    </li>
                                @endforeach
                            </ul>
                            <span class="comman-btn show-all"
                                style="{{ $property_amenities->count() < 10 ? 'display: none' : '' }}"
                                href="#">{{ $labels['show_all_amenities'] }}</span>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="inqurie">
                        <div class="inqurie-agent">
                            @php
                                $profile = asset('assets/dashboard/images/profile.jpg');
                                if (@$property->agentDetails->profile_image) {
                                    $profile = asset('uploads/general_users/' . $property->agentDetails->profile_image);
                                }
                            @endphp
                            <img src="{{ $profile }}" alt="image" class="profile-image"/>
                            <h4>{{ urldecode($property->agentDetails->full_name) }}</h4>
                            <a class="vie-profile"
                                href="{{route('frontend.agent.view', ['id' => encrypt($property->agent_id)])}}">{{ $labels['view_agent_profile'] }}</a>
                            @if (\Auth::guard('web')->check() && $property->agent_id != \Auth::guard('web')->id())
                                <a class="comman-btn" href="{{(\Auth::guard('web')->check()) 
                                    ? route('frontend.conversation.list', ['id' => encrypt($property->agent_id)]) : route('frontend.login')}}">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.23625 0.582164H11.7636C12.9822 0.582013 13.9872 0.582013 14.7826 0.688989C15.6176 0.80124 16.3552 1.04608 16.9456 1.63654C17.5361 2.22701 17.7809 2.96457 17.8932 3.79957C18.0002 4.59496 18 5.59995 18 6.8187V9.05027C18 10.2691 18.0002 11.274 17.8932 12.0694C17.7809 12.9044 17.5361 13.642 16.9456 14.2324C16.3552 14.8229 15.6176 15.0677 14.7826 15.18C13.9872 15.287 12.9822 15.287 11.7636 15.287H7.5662C7.3634 15.287 7.30388 15.2874 7.24979 15.291C6.87568 15.3166 6.51905 15.4583 6.22945 15.6965C6.18742 15.731 6.14387 15.7715 5.99637 15.9104L5.98627 15.9201C5.70904 16.1816 5.48738 16.3908 5.30992 16.5493C5.13694 16.7039 4.96307 16.8497 4.7865 16.957C2.93293 18.0843 0.52123 17.0441 0.0691687 14.9223C0.0260763 14.7201 0.0128192 14.4937 0.00648956 14.2616C1.06579e-05 14.0239 1.07059e-05 13.7191 1.07059e-05 13.338V6.81861C-0.000139994 5.59998 -0.000139967 4.59503 0.106835 3.7996C0.219087 2.9646 0.463925 2.22703 1.05439 1.63657C1.64485 1.04612 2.38242 0.801262 3.21742 0.689018C4.01281 0.582043 5.01779 0.582043 6.23643 0.582194L6.23625 0.582164ZM3.45711 2.47295C2.82157 2.55838 2.52734 2.70905 2.32692 2.90928C2.12668 3.10967 1.97601 3.40392 1.8906 4.03947C1.8017 4.70078 1.79974 5.584 1.79974 6.88217V13.324C1.79974 13.7223 1.79989 14.0022 1.80562 14.2124C1.81164 14.4313 1.8231 14.5172 1.82942 14.547C2.02334 15.4563 3.05678 15.9022 3.85128 15.4191C3.87735 15.4031 3.94771 15.3526 4.11104 15.2066C4.26773 15.0667 4.47144 14.8746 4.76105 14.6014L4.78033 14.5832C4.90042 14.4699 4.99082 14.3845 5.08635 14.3061C5.66554 13.8298 6.37896 13.5464 7.12703 13.4952C7.25027 13.4868 7.37473 13.4868 7.5397 13.4869H11.7001C12.9984 13.4869 13.8815 13.485 14.5428 13.3961C15.1783 13.3106 15.4725 13.16 15.6728 12.9597C15.8732 12.7594 16.0239 12.4651 16.1093 11.8296C16.1982 11.1682 16.2001 10.285 16.2001 8.98685V6.88214C16.2001 5.58397 16.1982 4.70072 16.1093 4.03944C16.0238 3.4039 15.8732 3.10967 15.6728 2.90925C15.4726 2.70901 15.1783 2.55834 14.5428 2.47293C13.8814 2.38403 12.9984 2.38207 11.7001 2.38207H6.30001C5.00184 2.38207 4.11858 2.38403 3.45731 2.47293L3.45711 2.47295Z"
                                            fill="white" />
                                        <path
                                            d="M6.30012 8.23221C6.30012 8.97773 5.69562 9.58223 4.95011 9.58223C4.20459 9.58223 3.6001 8.97773 3.6001 8.23221C3.6001 7.4867 4.20459 6.8822 4.95011 6.8822C5.69562 6.8822 6.30012 7.4867 6.30012 8.23221Z"
                                            fill="white" />
                                        <path
                                            d="M10.3499 8.23221C10.3499 8.97773 9.74543 9.58223 8.99991 9.58223C8.2544 9.58223 7.6499 8.97773 7.6499 8.23221C7.6499 7.4867 8.2544 6.8822 8.99991 6.8822C9.74543 6.8822 10.3499 7.4867 10.3499 8.23221Z"
                                            fill="white" />
                                        <path
                                            d="M14.3997 8.23221C14.3997 8.97773 13.7952 9.58223 13.0497 9.58223C12.3042 9.58223 11.6997 8.97773 11.6997 8.23221C11.6997 7.4867 12.3042 6.8822 13.0497 6.8822C13.7952 6.8822 14.3997 7.4867 14.3997 8.23221Z"
                                            fill="white" />
                                    </svg>
                                    {{ $labels['send_a_message'] }}
                                </a>
                            @endif
                        </div>
                        <form action="" id="contact_agent_form" name="contact_agent_form" method="post">
                            <h3>{{ $labels['contact'] }}</h3>
                            {{--  <div class="">
                                <input type="text" id="i_name" name="i_name" value="{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->full_name) : ''}}" placeholder="{{ $labels['name'] }}" data-error="#i_name_error" required>
                                <span class="error-login" id="i_name_error">sasasaas</span>
                            </div>

                            <div class="">
                                
                                <input type="text" id="i_email" name="i_email" value="{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->full_name) : ''}}" placeholder="{{ $labels['email_address'] }}" data-error="#i_email_error" required>
                                <span class="error-login" id="i_email_error"></span>
                            </div>

                            <div class="">
                                <textarea name="message" id="i_message" name="i_message" placeholder="{{$labels['message']}}" class="w-100" data-error="#i_message_message"></textarea>
                                <span class="error-login" id="i_message_message"></span>
                            </div>  --}}

                            <input type="text" name="i_name" id="i_name" placeholder="{{ $labels['name'] }}" required>
                            <input type="email" name="i_email" id="i_email" placeholder="{{ $labels['email_address'] }}" required>
                            <textarea name="i_message" id="i_message" placeholder="{{ $labels['message'] }}" class="w-100" required></textarea>

                            <input type="hidden" name="property_id" value="{{$property->id}}">
                            <input type="hidden" name="agent_id" value="{{$property->agent_id}}">
                            <button class="comman-btn w-100">{{ $labels['inquire_now'] }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="property-address">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="property-address-heading">
                        <div class="property-address-left">
                            <h4>{{ $labels['property_address'] }}</h4>
                            <p class="mb-0"><img src="{{ asset('assets/img/detail-location.svg') }}"
                                    alt="icon" /><span>{{ $property->property_address }}</span></p>
                        </div>
                        <a href="https://maps.google.com/?q={{ $property->property_address }}" class="comman-btn"
                            target="_blank">{{ $labels['get_directions'] }}</a>
                    </div>

                    {{-- <div class="detail-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13907.208347722504!2d47.97311198876576!3d29.376083612766713!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3fcf9c83ce455983%3A0xc3ebaef5af09b90e!2sKuwait%20City%2C%20Kuwait!5e0!3m2!1sen!2sin!4v1646396752284!5m2!1sen!2sin"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div> --}}
                    <div class="detail-map" id="detail_map" style="height:450px">

                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($similar_properties->count() > 0)
    <section class="similar-properties">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="similar-propties-heading">
                        <h4>{{ $labels['similar_property'] }}</h4>
                        @if ($total_similar > $similar_property_limit)
                            <a href="{{ route('frontend.property.similiar_properties', ['id' => $property->slug])}}">{{ $labels['view_all'] }}</a>
                        @endif
                    </div>
                </div>
                @foreach ($similar_properties as $similar_property)
                    <div class="col-md-4 col-sm-6">
                        <div class="featured-properties-box mb-md-0">
                            <a href="{{ route('frontend.property_details', ['id' => $similar_property->slug]) }}">
                                <div class="featured-properties-img ">
                                    <?php
                                    if ($property->propertyImages->count() > 0) {
                                        $property_image = asset('storage/property_images/' . $similar_property->id . '/' . $similar_property->propertyImages[0]->property_image);
                                    } else {
                                        $property_image = asset('assets/dashboard/images/no_image.png');
                                    }
                                    ?>
                                    <img src="{{ $property_image }}" alt="
                                        {{ $similar_property->property_name }}" />
                                    <span
                                        class="badge {{ config('constants.PROPERTY_FOR.' . $similar_property->property_for . '.badge_class') }}">{{ $similar_property->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
                                </div>
                                <div class="featured-properties-box-content">
                                    @php
                                        $currency = @$similar_property->areaDetails->country->currency_code ?: 'KD';
                                        $is_fav = 0;
                                        if (Auth::guard('web')->check()) {
                                            $is_fav = UserFavouriteProperty::where('user_id', '=', Auth::guard('web')->id())
                                                ->where('property_id', '=', $similar_property->id)
                                                ->exists();
                                        }
                                        $area_name = '';
                                        $country_name = '';
                                        if ($similar_property->area_id) {
                                            if ($language_id == 1) {
                                                $area_name = @urldecode($similar_property->areaDetails->name) ?: '';
                                                $country_name = @urldecode($similar_property->areaDetails->country->name) ?: '';
                                            } else {
                                                if (@$similar_property->areaDetails->childdata[0]->name) {
                                                    $area_name = urldecode($similar_property->areaDetails->childdata[0]->name) ?: '';
                                                } else {
                                                    $area_name = urldecode($similar_property->areaDetails->name) ?: '';
                                                }
                                        
                                                if (@$similar_property->areaDetails->country->childdata[0]->name) {
                                                    $country_name = @urldecode($similar_property->areaDetails->country->childdata[0]->name) ?: '';
                                                } else {
                                                    $country_name = @urldecode($similar_property->areaDetails->country->name) ?: '';
                                                }
                                            }
                                        }
                                        
                                        $short_address = $area_name && $country_name ? $area_name . ', ' . $country_name : '';
                                    @endphp
                                    <p> <img src="{{ asset('assets/img/location.svg') }}"
                                            alt="location">{{ $short_address }}
                                    </p>
                                    <h3>{{ $similar_property->property_name }}</h3>
                                    <h5>{{ number_format($similar_property->price_area_wise, 2) . ' ' . $currency }}</h5>
                                    <div class="featured-properties-icon">
                                        <span title="{{ $labels['bathroom_numbers'] }}"><img
                                                src="{{ asset('assets/img/Featured1.svg') }}"
                                                alt="{{ $labels['bathroom_numbers'] }}" />{{ $similar_property->total_bathrooms ?: 0 }}</span>
                                        <span title="{{ $labels['bedroom_numbers'] }}"><img
                                                src="{{ asset('assets/img/Featured2.svg') }}"
                                                alt="{{ $labels['bedroom_numbers'] }}" />{{ $similar_property->total_bedrooms ?: 0 }}</span>
                                        <span title="{{ $labels['toilet_numbers'] }}"><img
                                                src="{{ asset('assets/img/Featured3.svg') }}"
                                                alt="{{ $labels['toilet_numbers'] }}" />{{ $similar_property->total_toilets ?: 0 }}</span>
                                        <span title="{{ $labels['area_sqft'] }}"><img
                                                src="{{ asset('assets/img/Featured4.svg') }}"
                                                alt="{{ $labels['area_sqft'] }}" />{{ $similar_property->property_sqft_area ?: 0 }}
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

    <!------modal-------->
    <div class="account-popup">
        <div class="modal fade" id="accountmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="model-close">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="edit-detail-box">
                            <h3>{{$labels['report_user']}}</h3>
                            <form action="#" id="report_user_form" name="report_user_form" method="post" enctype="multipart/form-data">
                                <div class="input-outer">
                                    <label for="r_name">{{$labels['name']}}</label>
                                    <input type="text" id="r_name" name="r_name" value="{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->full_name) : ''}}" placeholder="{{$labels['enter_name']}}" data-error="#r_name_error" required>
                                    <span class="error-login" id="r_name_error"></span>
                                </div>
                                <div class="input-outer">
                                    <label for="r_email">{{$labels['email_address']}}</label>
                                    <input type="email" id="r_email" name="r_email" value="{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->email) : ''}}" placeholder="{{$labels['enter_email']}}" data-error="#r_email_error">
                                    <span class="error-login" id="r_email_error"></span>
                                </div>
                                <div class="input-outer">
                                    <label for="r_mobile">{{$labels['phone_number']}}</label>
                                    <div class="mobile-number-code">
                                        <input type="number" id="r_mobile" name="r_mobile" value="{{@Auth::guard('web')->check() ? Auth::guard('web')->user()->mobile_number : ''}}" placeholder="{{$labels['mobile_number']}}" data-error="#r_mobile_error">

                                        <span class="number-code">{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->country_code) : '+965'}}</span>
                                        
                                        <input type="hidden" name="country_code" value="{{@Auth::guard('web')->check() ? urldecode(Auth::guard('web')->user()->country_code) : '+965'}}">
                                    </div>
                                    <span class="error-login" id="r_mobile_error"></span>
                                </div>
                                <div class="input-outer">
                                    <label for="r_message">{{$labels['message']}}</label>
                                    <textarea name="message" id="r_message" name="r_message" placeholder="{{$labels['write_message']}}" data-error="#r_message_message"></textarea>
                                    <span class="error-login" id="r_message_message"></span>
                                </div>
                                <input type="hidden" name="agent_id" value="{{$property->agent_id}}">
                                <button class="comman-btn" type="submit" id="report_user_submit" name="report_user_submit">{{$labels['save']}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('after-scripts')
    <script src="{{asset('assets/frontend/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/frontend/js/additional.min.js')}}"></script>
    <script>

        var report_user_form;
        jQuery(function ($) {

            report_user_form = $("#report_user_form").validate({
                rules: {
                    r_name: {
                        required : true,
                    },
                    r_email: {
                        required : true,
                        email : true,
                    },
                    r_mobile: {
                        required: true,
                        minlength: 8,
                    },
                    r_message: {
                        required: true,
                    },
                },
                messages: {
                    r_name: {
                        required : "{{$labels['please_enter_name']}}",
                    },
                    r_email: {
                        required : "{{$labels['please_enter_email']}}",
                        email : "{{$labels['please_enter_valid_email']}}",
                    },
                    r_mobile: {
                        required: "{{$labels['please_enter_phone_number']}}",
                        minlength: "{{$labels['enter_valid_phone_number']}}",
                    },
                    r_message: {
                        required: "{{$labels['please_enter_message']}}",
                    },

                },
                errorPlacement: function(error, element) {
                    var placement = $(element).data('error');
                    if (placement) {
                        $(placement).empty();
                        $(placement).append(error.text());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(e) {
                    loader_show();
                    var param = $("form#report_user_form").serializeArray();
                    $.ajax({
                        url : "{{route('frontend.report_user')}}",
                        dataType : 'json',
                        data : param,
                        type : 'post',
                        success : function (response){
                            $("#accountmodal").modal('hide');
                            $("#report_user_form")[0].reset();

                            setTimeout(() => {
                                loader_hide();
                            }, 500)
                            if(response.statusCode == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.title,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                            } else{
                                Swal.fire({
                                    icon: 'error',
                                    title: response.title,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                            }
                        }, 
                        error : function (error) {
                            
                            loader_hide();
                        }
                    });
                }
            });
        });



        // inquiry property form
        var contact_agent_form;
        jQuery(function ($) {

            contact_agent_form = $("#contact_agent_form").validate({
                onfocusout: false,
                onkeyup: false,
                onclick: false,
                rules: {
                    i_name: {
                        required : true,
                    },
                    i_email: {
                        required : true,
                        email : true,
                    },
                    i_message: {
                        required: true,
                    }
                },
                messages: {
                    i_name: {
                        required : "{{$labels['please_enter_name']}}",
                    },
                    i_email: {
                        required : "{{$labels['please_enter_email']}}",
                        email : "{{$labels['please_enter_valid_email']}}",
                    },
                    i_message: {
                        required: "{{$labels['please_enter_message']}}",
                    }
                },
                errorPlacement: function(error, element) {
                    {{--  var placement = $(element).data('error');
                    if (placement) {
                        $(placement).empty();
                        $(placement).append(error.text());
                    } else {
                        error.insertAfter(element);
                    }  --}}

                    Swal.fire({
                        icon: 'error',
                        title: error.text(),
                        toast: true,
                        position: 'top-end',
                        heightAuto: false,
                        showCloseButton : true,
                        closeButtonHtml : '&times;',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                },
                submitHandler: function(e) {
                    loader_show();
                    var param = $("form#contact_agent_form").serializeArray();
                    $.ajax({
                        url : "{{route('frontend.contact_user')}}",
                        dataType : 'json',
                        data : param,
                        type : 'post',
                        success : function (response){
                            if(response.statusCode == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.title,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                            } else{
                                Swal.fire({
                                    icon: 'error',
                                    title: response.title,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                            }
                            loader_hide();
                        }, 
                        error : function (error) {
                            loader_hide();
                        }
                    });
                }
            });
        });

    </script>

    <script>
        //readmore
        $(".show-more").click(function() {
            if ($(".description-text").hasClass("description-text-more")) {
                $(this).text("{{ $labels['show_less'] }}");
            } else {
                $(this).text("{{ $labels['show_more'] }}");
            }

            $(".description-text").toggleClass("description-text-more");
        });
        //readmore
        $(".show-all").click(function() {
            if ($(".amenities-items").hasClass("amenities-items-more")) {
                $(this).text("{{ $labels['show_less'] }}");
            } else {
                $(this).text("{{ $labels['show_more'] }}");
            }

            $(".amenities-items").toggleClass("amenities-items-more");
        });

        var map;

        function getData() {
            var property_address = "{{ $property->property_address }}";
            var latitude = "{{ $property->property_address_latitude }}";
            var longitude = "{{ $property->property_address_longitude }}";

            if(latitude && longitude) {
                var dataArr = [];
                dataArr['latitude'] = latitude;
                dataArr['longitude'] = longitude;
                dataArr['formatted_address'] = property_address;

                init_map(dataArr);

            } else{
                $.ajax({
                    url: "{{ route('frontend.getPropMapData') }}",
                    async: true,
                    type: 'post',
                    data: {
                        'address': property_address
                    },
                    dataType: 'json',
                    success: function(data) {
                        //load map
                        init_map(data);
                    }
                });
            }
        }

        function init_map(data) {
            var map_options = {
                zoom: 14,
                center: new google.maps.LatLng(data['latitude'], data['longitude'])
            }
            map = new google.maps.Map(document.getElementById("detail_map"), map_options);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(data['latitude'], data['longitude'])
            });
            infowindow = new google.maps.InfoWindow({
                content: data['formatted_address']
            });
            google.maps.event.addListener(marker, "click", function() {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
    </script>

    <script>
        // Form submit validations with jQuery Validator

        $(document).ready(function () {
            $('#report_user_form').on('submit', function (e){
                var validate_report_user = $("#report_user_form").valid();
                report_user_form.valid();
                if (!validate_report_user) {
                    report_user_form.focusInvalid();
                    return false;
                }  
            });
        });

    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY', 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4') }}&callback=getData"
        async defer></script>
@endpush
