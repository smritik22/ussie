@extends('frontEnd.layout')
@section('content')
@php
    use App\Helper\Helper;
    use App\Models\UserFavouriteProperty;
@endphp
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('frontend.homePage')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><img src="{{asset('assets/img/bread.svg')}}"
                                    alt="icon" />{{$labels['agent_profile']}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="inner-pading agent-detail-user">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="agent-detail-user-inner">
                        <div class="inqurie-agent">
                            <div class="inqurie-agent-img">
                                @php
                                    $profile = asset('assets/dashboard/images/profile.jpg');
                                    if (@$agent->profile_image) {
                                        $profile = asset('uploads/general_users/' . $agent->profile_image);
                                    }
                                @endphp
                                <img src="{{$profile}}" class="profile-image" alt="image">
                            </div>
                            <div class="inqurie-agent-detail">
                                <h4 class="text-start">{{urldecode(@$agent->full_name) ?: ""}}</h4>
                                @if (@$agent->user_short_address)    
                                <p class="mb-0">
                                    <img class="me-2" src="{{asset('assets/img/detail-location.svg')}}" alt="icon"> {{@$agent->user_short_address ?: ""}}
                                </p>
                                @endif
                            </div>
                        </div>
                        @if ($user_id == $agent->id) 
                        <a class="inqurie-agent-icon " href="{{route('frontend.account')}}">
                            <img src="{{asset('assets/img/edit.svg')}}" alt="">
                        </a>
                        @else
                        <div class="inqurie-agent-icon " data-bs-toggle="modal" data-bs-target="#accountmodal">
                            <img src="{{asset('assets/img/user.svg')}}" alt="">
                        </div>

                        @endif
                    </div>
                    @if (@$agent->about_user)
                        <div class="agent-about">
                            <h5>{{$user_id == $agent->id ? $labels['about_me'] : $labels['about']}}</h5>
                            <p class="mb-0">{{$agent->about_user}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($agent_properties->count() > 0)
    <section class="agent similar-properties pt-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="agent-propties-heading">
                        <h2>{{$labels['property_listing']}} @if($total_properties > $list_limit) <a href="list-property.html">{{$labels['view_all']}}</a> @endif</h2>
                    </div>
                </div>
                @foreach ($agent_properties as $property) 
                    <div class="col-md-4 col-sm-6">
                        <div class="featured-properties-box mb-md-0">
                            <a href="{{ route('frontend.property_details', ['id' => $property->slug]) }}">
                                
                                <div class="featured-properties-img ">
                                    <?php
                                        if ($property->propertyImages->count() > 0) {
                                            $property_image = asset('storage/property_images/' . $property->id . '/' . $property->propertyImages[0]->property_image);
                                        } else {
                                            $property_image = asset('assets/dashboard/images/no_image.png');
                                        }
                                    ?>
                                    <img src="{{ $property_image }}" alt="alt="
                                        {{ $property->property_name }}"" />
                                    <span
                                        class="badge {{ config('constants.PROPERTY_FOR.' . $property->property_for . '.badge_class') }}">{{ $property->property_for == config('constants.PROPERTY_FOR_RENT')? strtoupper($labels['rent']): strtoupper($labels['buy']) }}</span>
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
                                    <p> 
                                        <img src="{{ asset('assets/img/location.svg') }}" alt="location">{{ $short_address }}
                                    </p>
                                    <h3>{{ $property->property_name }}</h3>
                                    <h5>{{ number_format($property->price_area_wise, 2) . ' ' . $currency }}</h5>
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

    <div class="agent-detail-btn">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="agent-btn">
                        <a class="comman-btn" href="message.html">Send a Message</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user_id != $agent->id)
    <!------modal-------->
        <div class="account-popup">
            <div class="modal fade" id="accountmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="model-close">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="edit-detail-box">
                            <h3>Report user</h3>
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
                                <input type="hidden" name="property_id" value="{{$property->id}}">
                                <input type="hidden" name="agent_id" value="{{$property->agent_id}}">
                                <button class="comman-btn" type="submit" id="report_user_submit" name="report_user_submit">{{$labels['save']}}</button>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        @endif
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
                    }
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
    </script>
@endpush