@extends('frontEnd.layout')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/lightboxed.css') }}">
    <section class="breadcrumb-outer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('frontend.homePage')}}">{{$labels['home']}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><img src="{{asset('assets/img/bread.svg')}}"
                                    alt="icon" />{{$labels['contact_us']}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="inner-pading contact">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{$labels['contact_us']}}</h2>
                </div>

                <div class="col-lg-4 contact-form-detail">
                    <div class="contact-form-detail-outer">
                        <h5>{{$labels['lets_talk']}}</h5>
                        <div class="contact-detail-box-outer">
                            <div class="contact-detail-box">
                                <div class="contact-detail-box-img">
                                    <img src="{{asset('assets/img/phone.svg')}}" alt="icon" />
                                </div>
                                <div class="contact-detail-box-text">
                                    <h6>{{$labels['phone']}}</h6>
                                    <a href="tel:{{@$setting->phone ?: ''}}"><span></span>{{@$setting->phone ?: ''}}</a>
                                </div>

                            </div>
                            <div class="contact-detail-box">
                                <div class="contact-detail-box-img">
                                    <img src="{{asset('assets/img/mail.svg')}}" alt="icon" />
                                </div>
                                <div class="contact-detail-box-text">
                                    <h6>{{$labels['mail']}}</h6>
                                    <a href="mailto:{{@$setting->email ?: ""}}">{{@$setting->email ?: ""}}</a>
                                </div>
                            </div>
                            <div class="contact-detail-box">
                                <div class="contact-detail-box-img">
                                    <img src="{{asset('assets/img/location-green.svg')}}" alt="icon" />
                                </div>
                                <div class="contact-detail-box-text">
                                    <h6>{{$labels['address']}}</h6>
                                    <span>{{@$setting->address ?: ""}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 contact-form">
                    <div class="contact-form-outer">
                        <h5>{{$labels['get_in_touch']}}</h5>
                        <form action="" id="contact_us_form" name="contact_us_form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-outer">
                                        <input type="text" name="name" id="name" data-error="#name_error" placeholder="{{$labels['your_name']}}" required>
                                        <span class="error-login" id="name_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-outer">
                                        <input type="email" name="email" id="email" placeholder="{{$labels['your_email']}}" data-error="#email_error" required>
                                        <span class="error-login" id="email_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-outer">
                                        <div class="mobile-number-code">
                                            <input type="number" name="mobile" id="mobile" placeholder="{{$labels['mobile_number']}}" data-error="#mobile_error" required>
                                            <span class="number-code">+965</span>
                                            <input type="hidden" name="country_code" value="+965">
                                        </div>
                                        <span class="error-login" id="mobile_error"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-outer">
                                        <textarea name="message" id="message" data-error="#message_error" placeholder="{{$labels['enter_your_message']}}" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="comman-btn">{{$labels['submit']}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <div class="contact-map">
        <div class="contact-map-outer">
            <iframe
                {{--  src="https://maps.google.com/maps?q={{@urlencode($setting->address) ?: ""}}"  --}}
                src="https://maps.google.com/maps?hl={{Helper::currentLanguage()->code}}&q={{@$setting->address ?: ""}}&t=&z=12&ie=UTF8&iwloc=B&output=embed"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="{{asset('assets/frontend/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/frontend/js/additional.min.js')}}"></script>
    <script>
        var contact_us_form;
        jQuery(function ($) {

            contact_us_form = $("#contact_us_form").validate({
                rules: {
                    name: {
                        required : true,
                    },
                    email: {
                        required : true,
                        email : true,
                    },
                    mobile: {
                        required: true,
                    },
                    message : {

                    },
                },
                messages: {
                    name: {
                        required : "{{$labels['please_enter_name']}}",
                    },
                    email: {
                        required : "{{$labels['please_enter_email']}}",
                        email : "{{$labels['please_enter_valid_email']}}",
                    },
                    mobile: {
                        required: "{{$labels['please_enter_phone_number']}}",
                        minlength: "{{$labels['enter_valid_phone_number']}}",
                    },
                    message: {

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

                    var param = $("form#contact_us_form").serializeArray();
                    $.ajax({
                        url : "{{route('frontend.contactus.submit')}}",
                        dataType : 'json',
                        data : param,
                        type : 'post',
                        success : function (response){
                            $("#contact_us_form")[0].reset();

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

                                setTimeout(() => {
                                    location.href = response.url;
                                }, 800)
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
