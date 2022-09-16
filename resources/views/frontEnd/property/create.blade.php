@extends('frontEnd.layout')
@section('content')
    <style>
        .swal2-confirm.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #2A4B9B;
            line-height: 24px;
            background-color: #2A4B9B;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

        .swal2-styled.swal2-confirm:focus {
            box-shadow: none;
        }

        .swal2-styled.swal2-deny:focus {
            box-shadow: none;
        }

        .swal2-styled.swal2-cancel:focus {
            box-shadow: none;
        }

        .swal2-deny.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #bb4f4f;
            line-height: 24px;
            background-color: #bb4f4f;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

        .swal2-cancel.swal2-styled {
            padding: 12px 22px 12px 22px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #555555;
            line-height: 24px;
            background-color: #555555;
            color: #fff;
            display: inline-block;
            transition: all .4s;
        }

    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/lightboxed.css') }}">

    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('frontend.homePage') }}">{{ $labels['home'] }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><img
                                    src="{{ asset('assets/img/bread.svg') }}"
                                    alt="icon" />{{ $labels['add_new_property'] }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="add-new-property">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{ @$property ? $labels['edit_property'] : $labels['add_new_property'] }}</h2>
                    <div class="add-new-property-box">
                        <ul class="nav " id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="addproperty-tab" data-bs-toggle="tab"
                                    data-bs-target="#addproperty" type="button" role="tab" aria-controls="addproperty"
                                    aria-selected="true">1. {{ @$property ? $labels['edit_property'] : $labels['add_property'] }}</button>
                            </li>
                            @if (!@$property)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="selectplan-tab" data-bs-toggle="tab"
                                        data-bs-target="#selectplan" type="button" role="tab" aria-controls="selectplan"
                                        aria-selected="false" @if(!$property) disabled @endif>2. {{ $labels['select_plan'] }} </button>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="addproperty" role="tabpanel"
                                aria-labelledby="addproperty-tab">
                                <form action="#" name="property_details" id="property_details" enctype="multipart/form-data"
                                    method="POST">
                                    <div class="add-new-property-field">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for="property_title"
                                                            class="form-label">{{ $labels['property_title'] }}</label>
                                                        <input type="text" class="form-control" id="property_title"
                                                            name="property_title" data-error="#property_title_error" value="{{@$property->property_name ?: ''}}"
                                                            placeholder="{{ $labels['enter_title'] }}">
                                                    </div>
                                                    <span class="error-login" id="property_title_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for="" class="form-label">{!! $labels['property_for'] !!}</label>
                                                    <div class="row">
                                                        @foreach (config('constants.PROPERTY_FOR') as $item)
                                                            <div class="col-6">
                                                                <div class="form-check form-check-inline me-0 ps-0">
                                                                    <label class="form-check-label {{ (@$property->property_for == $item['value']) ?'checked1' : ''}}"
                                                                        for="property_type_{{ $item['value'] }}">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="property_for"
                                                                            id="property_type_{{ $item['value'] }}"
                                                                            data-error="#property_for_error"
                                                                            value="{{ $item['value'] }}" @checked((@$property->property_for == $item['value']) ? true : false)>
                                                                        <span
                                                                            class="agent-type-name">{{ $labels[$item['label_key']] }}</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <span class="error-login" id="property_for_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for="property_type"
                                                        class="form-label">{{ $labels['property_type'] }}</label>
                                                    <div class="add-property-field-select">
                                                        <select name="property_type" id="property_type"
                                                            data-error="#property_type_error">
                                                            @foreach ($property_types as $item)
                                                                <option value="{{ $item->id }}" @selected((@$property->property_type == $item->id) ? true : false)>
                                                                    {{ $language_id != 1 && @$item->childdata[0]->type ? $item->childdata[0]->type : $item->type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <span id="property_type_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for="area_sqft"
                                                            class="form-label">{{ $labels['area_sqft'] }}</label>
                                                        <input type="number" name="area_sqft" class="form-control"
                                                            id="area_sqft" data-error="#area_sqft_error"
                                                            placeholder="{{ $labels['enter_area'] }}" value="{{@$property->property_sqft_area ?: ""}}">
                                                    </div>
                                                    <span id="area_sqft_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for="property_price"
                                                            class="form-label">{{ $labels['price'] }}</label>
                                                        <input type="number" name="property_price" class="form-control"
                                                            id="property_price" data-error="#property_price_error"
                                                            placeholder="{{ $labels['price'] }}" value="{{@$property->base_price ?: ""}}">
                                                    </div>
                                                    <span id="property_price_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for="bedroom_type"
                                                        class="form-label">{{ $labels['bedroom'] }}</label>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="add-property-field-select">
                                                                <select name="bedroom_type" data-error="#bedroom_type_error"
                                                                    id="bedroom_type">
                                                                    @foreach ($bedroom_types as $item)
                                                                        <option value="{{ $item->id }}" @selected((@$property->bedroom_type == $item->id )? true: false)>
                                                                            {{ $language_id != 1 && @$item->childdata[0]->type ? $item->childdata[0]->type : $item->type }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <span id="bedroom_type_error" class="error-login"></span>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="add-property-field-select">
                                                                <select name="total_bedroom" id="total_bedroom"
                                                                    data-error="#total_bedroom_error">
                                                                    @for ($i = 1; $i <= \Helper::getMaxBedroomNumbers(); $i++)
                                                                        <option value="{{ $i }}" @selected((@$property->total_bedrooms == $i) ? true: false)>
                                                                            {{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            <span id="total_bedroom_error" class="error-login"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for="bathroom_type"
                                                        class="form-label">{{ $labels['bathroom'] }}</label>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="add-property-field-select">
                                                                <select name="bathroom_type" id="bathroom_type"
                                                                    data-error="#bathroom_type_error">
                                                                    @foreach ($bathroom_types as $item)
                                                                        <option value="{{ $item->id }}" @selected((@$property->bathroom_type == $item->id) ? true: false)>
                                                                            {{ $language_id != 1 && @$item->childdata[0]->type ? $item->childdata[0]->type : $item->type }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <span id="bathroom_type_error" class="error-login"></span>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="add-property-field-select">
                                                                <select name="total_bathroom" id="total_bathroom"
                                                                    data-error="#total_bathroom_error">
                                                                    @for ($i = 1; $i <= \Helper::getMaxBathroomNumbers(); $i++)
                                                                        <option value="{{ $i }}" @selected((@$property->total_bathrooms == $i) ? true: false)>
                                                                            {{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for="total_toilets"
                                                        class="form-label">{{ $labels['toilet_numbers'] }}</label>
                                                    <div class="add-property-field-select">
                                                        <select name="total_toilets" id="total_toilets"
                                                            data-error="#total_toilets_error">
                                                            @for ($i = 1; $i <= \Helper::getMaxToiletNumbers(); $i++)
                                                                <option value="{{ $i }}" @selected((@$property->total_toilets == $i) ? true: false)>{{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <span id="total_toilets_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for=""
                                                            class="form-label">{{ $labels['amenities'] }}</label>
                                                        <ul class="language">
                                                            @foreach ($amenities as $amenity)
                                                                <li class="{{(@$property->property_amenities_ids && in_array($amenity->id, explode(',', $property->property_amenities_ids))) ? 'checked1' : ''}}">
                                                                    <input type="checkbox" name="amenities[]"
                                                                        id="amenity{{ $amenity->id }}"
                                                                        class="input-hidden"
                                                                        data-error="#amenities_error" @checked((@$property->property_amenities_ids && in_array($amenity->id, explode(',', $property->property_amenities_ids))) ? true : false) value="{{$amenity->id}}">
                                                                    <label class="eng-box"
                                                                        for="amenity{{ $amenity->id }}">
                                                                        <span>{{ $language_id != 1 && @$amenity->childdata[0]->amenity_name? urldecode($amenity->childdata[0]->amenity_name): urldecode($amenity->amenity_name) }}</span>
                                                                    </label>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <span id="amenities_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="add-property-field">
                                                    <label for="area_id"
                                                        class="form-label">{{ $labels['area'] }}</label>
                                                    <div class="add-property-field-select">
                                                        <select name="area_id" id="area_id" data-error="#area_id_error" value="{{@$property->area_id ?: ''}}">
                                                            @foreach ($areas as $item)
                                                                <option value="{{ $item->id }}" @selected((@$property->area_id == $item->id)? true : false)>
                                                                    {{ $language_id != 1 && @$item->childdata[0]->name? urldecode($item->childdata[0]->name): urldecode($item->name) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <span id="area_id_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for="description"
                                                            class="form-label">{{ $labels['description'] }}</label>
                                                        <textarea name="description" id="description" placeholder="{{ $labels['enter_description'] }}"
                                                            data-error="#description_error">{{@$property->property_description ?: ""}}</textarea>
                                                    </div>
                                                    <span id="description_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <div class="">
                                                        <label for="address"
                                                            class="form-label find-on-map">{{ $labels['address'] }} <a
                                                                class="" id="find_on_map" target="_blank"
                                                                href="{{@$property->property_address ? 'https://maps.google.com/?q=' . $property->property_address : '#'}}">{{ $labels['find_on_map'] }}</a></label>
                                                        <textarea name="address" id="address" placeholder="{{ $labels['enter_address'] }}"
                                                            data-error="#address_error">{{@$property->property_address ?: ""}}</textarea>
                                                        
                                                        <input type="hidden" name="property_latitude" id="property_latitude" value="">
                                                        <input type="hidden" name="property_longitude" id="property_longitude" value="">
                                                    </div>
                                                    <span id="address_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="add-property-field add-property-field-img">
                                                    <div class="form-field" id="appendImages">
                                                        <label for="uploadDoc" class="uploadDoc form-label">
                                                            <img src="{{ asset('assets/img/add-img.svg') }}"
                                                                alt="Plus Icon" />
                                                        </label>
                                                        <input type="file" class="form-control" id="uploadDoc" accept="image/*" data-error="#uploadDoc_error" name="property_images[]" multiple>
                                                        @if (@$property->propertyImages)
                                                            @foreach (@$property->propertyImages as $key => $image)    
                                                                <div class="uploaded-img-box old-images">
                                                                    <img src="{{asset('storage/property_images/' . $property->id . '/' . $image->property_image)}}" alt="" class="profile-image" />
                                                                    <span class="remove" onclick="removeOldimage(this);" data-propunique="{{$image->id}}">
                                                                        <img src="{{ asset('assets/img/close.svg') }}" alt="" />
                                                                    </span>
                                                                    <input type="checkbox" name="is_deleted[]" id="property_image_{{$image->id}}" value="{{$image->id}}" style="display: none;">
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <span id="uploadDoc_error" class="error-login"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="add-property-btn">
                                                    <input type="hidden" name="property_slug" value="{{@$property->slug}}" id="property_slug">
                                                    <input type="hidden" name="is_edit" id="is_edit" value="{{@$property->id ? 1 : 0}}">
                                                    <input type="submit" id="property_details_submit"
                                                        class="comman-btn" value="{{ @$property ? $labels['save'] : $labels['next'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="selectplan" role="tabpanel" aria-labelledby="selectplan-tab">
                                <div class="add-new-property-field">
                                    <form action="#" name="select_plan" id="select_plan" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="add-property-field">
                                                    <label for=""
                                                        class="form-label">{{ $labels['agent_type'] }}</label>
                                                    <div class="row">
                                                        @foreach (config('constants.AGENT_TYPE') as $key => $item)
                                                            <div class="col-6">
                                                                <div class="form-check form-check-inline me-0 ps-0">
                                                                    @php
                                                                        $is_checked = \Auth::guard('web')->user()->user_type == config('constants.USER_TYPE_AGENT') && \Auth::guard('web')->user()->agent_type == $item['value'] ? 1 : 0;
                                                                    @endphp
                                                                    <label
                                                                        class="form-check-label {{ $is_checked ? 'checked1' : '' }}"
                                                                        for="agentTypeRadio{{ $key }}">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="agent_type"
                                                                            id="agentTypeRadio{{ $key }}"
                                                                            {{ $is_checked ? 'checked' : '' }}
                                                                            value="{{ $item['value'] }}" data-error="#agent_type_error">
                                                                        <span
                                                                            class="agent-type-name">{{ $labels[$item['label_key']] }}</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <span id="agent_type_error" class="error-login"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                if(!@$userSubscription) {
                                                    $is_renew = 2;
                                                } else if(@$userSubscription && $userSubscription->no_of_plan_post > $userSubscription->propertiesSubscribed->count()) {
                                                    $is_renew = 3;
                                                } else {
                                                    $is_renew = 1;
                                                }
                                            @endphp
                                            <input type="hidden" name="is_renew" id="is_renew" value="{{$is_renew}}">
                                            @if (@!$userSubscription)
                                                {{-- if user dont have any active plan --}}
                                                <div class="col-12">
                                                    <div class="select-plan">
                                                        <p class="select-plan-heading">{{ $labels['select_plan'] }}</p>
                                                        @foreach ($subscription_plans as $key => $value)
                                                            <div class="subscription-plan-box-price"
                                                                style="background-color : {{ $value->bg_color }}">
                                                                <div class="form-check form-check-inline p-0 m-0">
                                                                    <label class="form-check-label prime-plan"
                                                                        for="subscriptionPlanRadio{{ $key }}">
                                                                        <div class="subscription-plan-name">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="subscription_plan" data-price="{{$value->plan_price}}"
                                                                                id="subscriptionPlanRadio{{ $key }}"
                                                                                value="{{ $value->id }}">
                                                                        </div>
                                                                        <div class="subscription-plan-detail">
                                                                            @php
                                                                                $duration = \Helper::getValidTillDate(date('Y-m-d H:i:s'), $value->plan_duration_value, $value->plan_duration_type);
                                                                            @endphp
                                                                            <h3>{!! $language_id != 1 && @$value->childdata[0]->plan_name ? $value->childdata[0]->plan_name : $value->plan_name !!} @if (!$value->is_free_plan)
                                                                                    <span>
                                                                                        {{ $value->plan_price . ' ' . \Helper::getDefaultCurrency() }}
                                                                                        /
                                                                                        {{ $duration['value'] . ' ' . $duration['label_value'] }}</span>
                                                                                @endif
                                                                            </h3>
                                                                            <p>{!! $language_id != 1 && @$value->childdata[0]->plan_description ? $value->childdata[0]->plan_description : $value->plan_description !!}</p>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="select-plan-img">
                                                            <img src="{{ asset('assets/img/login-icon.svg') }}"
                                                                alt="icon" />
                                                        </div>
                                                    </div>
                                                    <div class="subscription-btn">
                                                        <input type="button" id="add_property_submit" class="comman-btn"
                                                            value="{{ $labels['post_an_ad'] }}">
                                                    </div>
                                                </div>
                                            @else
                                                <input type="hidden" name="plan_id" value="{{$userSubscription->plan_id}}">
                                                {{-- if user have an active plan --}}
                                                <div class="col-12">
                                                    <div class="select-plan">
                                                        <div class="subscription-details">
                                                            @if ($userSubscription->no_of_plan_post <= $userSubscription->propertiesSubscribed->count())
                                                                <p class="select-plan-heading">
                                                                    {{ $labels['subscription_details'] }}
                                                                </p>
                                                                <div class="subscription-details-box">
                                                                    <img src="{{ asset('assets/img/error.svg') }}"
                                                                        alt="icon" />
                                                                    @php
                                                                        $plan_name = $language_id != 1 && @$userSubscription->plan_name_ar ? $userSubscription->plan_name_ar : $userSubscription->plan_name;
                                                                        
                                                                        $plan_description = $language_id != 1 && @$userSubscription->plan_description_ar ? $userSubscription->plan_description_ar : $userSubscription->plan_description;
                                                                        
                                                                        $message = '';
                                                                        if ($userSubscription->is_free_plan) {
                                                                            $message = $labels['you_have_reached_the_limit_of_the_Free_plan'];
                                                                        } else {
                                                                            $message = strtr($labels['you_have_reached_limit_of_plan'], ['{$PLAN}' => $plan_name]);
                                                                        }
                                                                    @endphp
                                                                    <span>{{ $message }}</span>
                                                                </div>

                                                                @if (!$userSubscription->is_free_plan)
                                                                    <input type="button" class="comman-btn"
                                                                        id="renew_plan_submit" data-price="{{$userSubscription->subscriptionPlanDetails->plan_price ?: 0}}"
                                                                        value="{{ $labels['renew_plan'] }}">
                                                                @endif
                                                            @else
                                                            @endif
                                                        </div>
                                                        @if ($userSubscription->no_of_plan_post <= $userSubscription->propertiesSubscribed->count())
                                                            <p class="select-plan-heading">{{$labels['or_choose_below_add']}}</p>
                                                            @foreach ($addOns as $k => $item) 
                                                            <div class="subscription-plan-box-price renew-plan">
                                                                <div class="form-check form-check-inline p-0 m-0">
                                                                    <label class="form-check-label prime-plan" for="addOn{{$k}}">
                                                                        <div class="subscription-plan-name">
                                                                            <input class="form-check-input" type="radio" name="addon" id="addOn{{$k}}" value="{{$item->id}}" data-price="{{$item->extra_each_featured_post_price ?: 0}}">
                                                                        </div>
                                                                        <div class="subscription-plan-detail">
                                                                            <h3>{{strtr( $labels['number_ad_property'], ['{$number}' => ($item->no_of_extra_featured_post ?: 0)])}}<span>{{$item->extra_each_featured_post_price ?: 0}} {{\Helper::getDefaultCurrency()}}</span></h3>
                                                                            @php
                                                                                $duration =Helper::getValidTillDate(date('Y-m-d H:i:s'), $userSubscription->plan_duration_value, $userSubscription->plan_duration_type);
                                                                            @endphp
                                                                            <p>{{$duration['value'] . ' ' . $duration['label_value']}}</p>
                                                                        </div>
                                                                        
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        @endif
                                                        <div class="select-plan-img">
                                                            <img src="{{asset('assets/img/login-icon.svg')}}" alt="icon" />
                                                        </div>
                                                    </div>
                                                    @php
                                                        if($userSubscription->no_of_plan_post <= $userSubscription->propertiesSubscribed->count()){
                                                            $btn_text = $labels['submit_pay'];
                                                            $submit_type = 1;
                                                        }else {
                                                            $btn_text = $labels['submit'];
                                                            $submit_type = 2;
                                                        }
                                                    @endphp
                                                    <div class="subscription-btn">
                                                        <input type="button" id="subscription_btn" class="comman-btn" data-submit_type="{{$submit_type}}"
                                                            value="{{ $btn_text }}">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <input type="hidden" name="PaymentID" id="PaymentID" value="">
                                        <input type="hidden" name="TrackID" id="TrackID" value="">
                                        <input type="hidden" name="TranID" id="TranID" value="">
                                        <input type="hidden" name="trnUdf" id="trnUdf" value="">
                                        <input type="hidden" name="Auth" id="Auth" value="">
                                        <input type="hidden" name="total_amount_transfer" id="total_amount_transfer" value="">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/frontend/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/additional.min.js') }}"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAPS_KEY','AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4')}}"></script>

    <script>
        function removeimage(element) {
            $(element).parent(".uploaded-img-box").remove();
        }

        $("#property_details").validate({
            rules: {
                property_title: {
                    required: true,
                },
                'property_for': {
                    required: true,
                },
                property_type: {
                    required: true,
                },
                area_sqft: {
                    required: true,
                },
                bedroom_type: {
                    required: true,
                },
                total_bedroom: {
                    required: true,
                },
                bathroom_type: {
                    required: true,
                },
                total_bathroom: {
                    required: true,
                },
                'amenities[]': {
                    required: true,
                },
                area_id: {
                    required: true,
                },
                description: {
                    required: true,
                },
                address: {
                    required: true,
                },
                uploadDoc: {
                    required: true,
                    accept: 'image/*',
                }
            },
            messages: {
                property_title: {
                    required: "{{ $labels['please_enter_property_title'] }}",
                },
                'property_for': {
                    required: "{{ $labels['please_select_property_for'] }}",
                },
                property_type: {
                    required: "{{ $labels['please_selete_property_type'] }}",
                },
                area_sqft: {
                    required: "{{ $labels['please_enter_property_area_sqft'] }}",
                },
                bedroom_type: {
                    required: "{{ $labels['please_select_bedroom_type'] }}",
                },
                total_bedroom: {
                    required: "{{ $labels['please_select_bedroom_number'] }}",
                },
                bathroom_type: {
                    required: "{{ $labels['please_select_bathroom_type'] }}",
                },
                total_bathroom: {
                    required: "{{ $labels['please_select_bathroom_number'] }}",
                },
                'amenities[]': {
                    required: "{{ $labels['please_select_amenities'] }}",
                },
                area_id: {
                    required: "{{ $labels['please_select_area'] }}",
                },
                description: {
                    required: "{{ $labels['please_add_description'] }}",
                },
                address: {
                    required: "{{ $labels['please_enter_address'] }}",
                },
                'property_images[]': {
                    required: "{{ $labels['please_add_atleast_one_image'] }}",
                    accept: "{{ $labels['upload_only_images'] }}",
                }
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
            success: "valid",
            submitHandler: function(e) {
                event.preventDefault();
                if($("#is_edit").val() == 1) {
                    callPaymentGateway();
                } else {
                    $("#selectplan-tab").removeAttr('disabled');
                    $("#selectplan-tab").click();
                }
            }
        });

        // Plan selection / subscription 

        $("#select_plan").validate({
            rules: {
                'agent_type' : {
                    required : function() {
                        return ($("#is_edit").val() == 1) ? false : true;
                    },
                }
            },
            messages : {
                'agent_type' : {
                    required : "{{$labels['select_agent_type']}}"
                }
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
            success: "valid",
            submitHandler: function(e) {
                loader_show();
                event.preventDefault();
                var __url = "{{route('frontend.signup.submit')}}";
                var frm = $('#property_details');
                var $data = new FormData(frm[0]);

                {{--  var file_data = $('input[type="file"][name="property_images[]"]')[0].files; // for multiple files
                for(var i = 0;i<file_data.length;i++){
                    $data.append("property_images[]", file_data[i]);
                }  --}}
                var other_data = $('#select_plan').serializeArray();
                $.each(other_data,function(key,input){
                    $data.append(input.name,input.value);
                });
                let __addPropertyLink = "{{route('frontend.property.submit')}}";

                {{--  var geocoder = new google.maps.Geocoder();
                var address = $("#address").val();

                geocoder.geocode( { 'address': address}, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {
                        var latitude = results[0].geometry.location.latitude;
                        var longitude = results[0].geometry.location.longitude;
                        alert(latitude);
                    } 
                });   --}}

                $.ajax({
                    url : __addPropertyLink,
                    data : $data,
                    type : 'post',
                    dataType : 'json',
                    contentType: false,
                    processData: false,
                    success : function (response) {
                        loader_hide();
                        if(response.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                iconColor : '#2A4B9B',
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['ok']}}",
                                timer: 3000,
                                timerProgressBar: true,
                                willClose : () => {
                                    location.href = response.url;
                                }
                            }).then(function (res){
                                location.href = response.url;
                            });
                        } else if(response.statusCode == 201) {
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            }).then(function (res) {
                                location.reload();
                            });
                        }
                    },
                    error : function (err) {
                        loader_hide();

                        Swal.fire({
                            icon: 'error',
                            text: "{{$labels['something_went_wrong']}}",
                            showConfirmButton: true,
                            confirmButtonText: "{{$labels['ok']}}",
                        }).then(function (res) {
                            location.reload();
                        });
                    }
                });
            }
        });

        function changeAddress() {
            let Address = $('#address').val();
            var __addressUrl = "https://maps.google.com/?q="+Address;
            $('#find_on_map').attr('href', __addressUrl);
        }

        function removeOldimage(element) {
            $(element).siblings('input[type="checkbox"][name="is_deleted[]"]').attr('checked', 'checked');
            $(element).parents('.uploaded-img-box').hide();
        }

        var winopened;
        function callPaymentGateway() {

            {{--  var payment_response = popup("{{route('frontend.payment.success')}}", 'payment');  --}}

            var is_renew = $("#is_renew").val();
            if($.inArray(parseInt(is_renew), [1,2])) {
                var total_amount;
                let addonVal = $('input[name="addon"]:checked').data('price');
                if(is_renew == 1) {
                    total_amount = ("#renew_plan_submit").data('price');
                } 
                else if( is_renew == 2) {
                    if( typeof(addonVal) != "undefined" && addonVal !== null ) {
                        total_amount = addonVal;
                    } else {
                        total_amount = $('input[name="subscription_plan"]:checked').data('price');
                    }
                }

                $("#total_amount_transfer").val(total_amount);
                loader_show();
    
                $.ajax({
                    url : "{{route('frontend.payment')}}",
                    data : {"user_id" : "{{Auth::guard('web')->id()}}", "payable_amount" : total_amount, "language_id" : "{{$language_id}}", "is_web":1},
                    type : 'post',
                    dataType : 'json',
                    success : function (response) {
                        if(response[0].code == 1) {
                            popup(response[0].redirect_url, '_blank');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                iconColor : '#bb4f4f',
                                text: "{{$labels['something_went_wrong']}}",
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['ok']}}",
                            }).then(function (res) {
                                location.reload();
                            });
                            return false;
                        }

                        loader_hide();
                    },
                    error : function (err) {
                        loader_hide();
                        Swal.fire({
                            icon: 'error',
                            iconColor : '#bb4f4f',
                            text: "{{$labels['something_went_wrong']}}",
                            showConfirmButton: true,
                            confirmButtonText: "{{$labels['ok']}}",
                        }).then(function (res) {
                            location.reload();
                        });
                        return false;
                    }
                });
            } else {
                $('#select_plan').submit();
            }

        }
        
        function popup(mylink, windowname) {
            winopened = window.open(mylink, windowname); 
            winopened.onblur = () => winopened.focus();
            {{--  return false;   --}}
        } 

        {{--  function targetopener(mylink, closeme, closeonly) { 
            if (! (winopened.focus && winopened.opener)) return true; 
            winopened.opener.focus(); 
            if (! closeonly) winopened.opener.location.href=mylink.href; 
            if (closeme) winopened.close(); 
            return false; 
        }  --}}

        function resultFetched(response) {
            winopened.close();
            console.info(response);

            if(response.Result == "{{config('constants.UPAY_RESULT.success')}}") {
                $('#PaymentID').val(response.PaymentID);
                $('#TrackID').val(response.TranID);
                {{--  $('#TranID').val(response.TranID);  --}}
                $('#trnUdf').val(response.trnUdf);
                $('#Auth').val(response.Auth);
            } else {
                Swal.fire({
                    icon: 'error',
                    iconColor : '#bb4f4f',
                    text: "{{$labels['payment_failed']}}",
                    showConfirmButton: true,
                    confirmButtonText: "{{$labels['ok']}}",
                });
            }
            // alert('Yes this is called now');
        }

        $().ready(function(events) {

            $("#find_on_map").click(function(e){
                e.preventDefault();
                let Address = $('#address').val();
                if(!Address) {
                    $("#address_error").text("{{$labels['please_enter_address']}}");
                    return false;
                }

                var __addressUrl = "https://maps.google.com/?q="+Address;
                window.open(__addressUrl,'_blank');
            });

            $("#add_property_submit").click( function (e) {
                $("#is_renew").val(2);
                callPaymentGateway();
            });

            $("#renew_plan_submit").click( function (e) {
                $("#is_renew").val(1);
                callPaymentGateway();
            });

            $("#subscription_btn").click( function (e) {
                let subType = $(this).data('submit_type');
                if(subType == 1) { // pay for subscription
                    $("#is_renew").val(2);
                    callPaymentGateway();
                } else {
                    $("#is_renew").val(3);
                    $("#select_plan").submit();
                }
            });

            $('#selectplan-tab').on('click', function(e) {
                {{--  $("#property_details").submit();  --}}
                if (!$("#property_details").valid()) {
                    e.preventDefault();
                    return false;
                }
            });

            $("#uploadDoc").on('change', function(event) {
                var files = event.target.files; //FileList object
                // console.log('Helloo');
                //if(files.length > 0) {
                    $("#appendImages").find('.new-added').remove();
                //}
                for (var i = 0; i < files.length; i++) {
                    // console.log('Imaeg : ' + files[i]);
                    var file = files[i];
                    if (typeof(FileReader) != "undefined") {

                        if (file.type.match('image')) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('#appendImages').append(`<div class="uploaded-img-box new-added">
                                    <img src="${e.target.result}" alt="" class="profile-image" />
                                    {{--  <span class="remove" onclick="removeimage(this);"><img src="{{ asset('assets/img/close.svg') }}" alt="" /></span>  --}}
                                </div>`);
                            }

                            // image_holder.show();
                            // reader.readAsDataURL($(this)[0].files[i]);
                            reader.readAsDataURL(file);
                            //}

                        }
                    } else {
                        alert("This browser does not support FileReader.");
                    }
                }

            });

            $("#address").on('change', function(e) {
                var geocoder = new google.maps.Geocoder();
                var address = $("#address").val();

                geocoder.geocode( {'address':address}, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {
                        var latitude = results[0].geometry.location.lat();
                        var longitude = results[0].geometry.location.lng();
                        
                        $('#property_latitude').val(latitude);
                        $('#property_longitude').val(longitude);
                    } 
                }); 
            });

        });
    </script>
@endpush
