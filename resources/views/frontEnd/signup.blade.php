@extends('frontEnd.layouts.auth_layout.layout')
@section('content')
    <style>
        .swal2-confirm.swal2-styled{
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
            box-shadow : none;
        }
        .swal2-styled.swal2-deny:focus {
            box-shadow : none;
        }
        .swal2-styled.swal2-cancel:focus {
            box-shadow : none;
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
    <div class="loin-outer">
        <div class="row m-0">
            <div class="col-lg-6 login-left-outer d-lg-block d-none">
                <div class="login-left">
                    <!-- <div class="login-logo">
                                <img src="{{ asset('assets/img/Logo.svg') }}" alt="logo" />
                            </div> -->
                    <img src="{{ asset('assets/img/login-icon.svg') }}" alt="icon" />
                </div>
            </div>
            <div class="col-lg-6 login-right-outer ">
                <div class="login-right">
                    <div class="login-form">

                        <form action="{{ route('frontend.signup.submit') }}" name="signup" id="signup"
                            enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="login-logo-blue">
                                <img src="{{ asset('assets/img/blue-logo.svg') }}" alt="logo" />
                                <h4>{{ $labels['signup'] }}</h4>
                                <p>{{ $labels['thousands_of_homes_waiting_for_you'] }}</p>

                                {{-- @if (Session::has('errorMessage') || Session::has('error'))
                                <div class="padding p-b-0">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="alert alert-danger m-b-0">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                {{ Session::get('errorMessage') }}
                                                {{ Session::get('error') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif --}}

                                <div class="input-outer">
                                    <input type="text" name="full_name" id="full_name"
                                        placeholder="{{ $labels['full_name'] }}" value="{{ old('full_name') }}"
                                        data-error="#full_name_error">
                                    @if (!empty(@$errors) && @$errors->has('full_name'))
                                        <span class='error-login'>{{ $errors->first('full_name') }}</span>
                                    @endif
                                    <span class='error-login' id="full_name_error"></span>
                                </div>
                                <div class="input-outer">
                                    <input type="text" name="email" id="email"
                                        placeholder="{{ $labels['email_address'] }}" value="{{ old('email') }}"
                                        data-error="#email_error">
                                    @if (!empty(@$errors) && @$errors->has('email'))
                                        <span class='error-login'>{{ $errors->first('email') }}</span>
                                    @endif
                                    <span class='error-login' id="email_error"></span>
                                </div>
                                <div class="input-outer">
                                    <div class="mobile-number-code">
                                        <input type="number" name="mobile_number" id="mobile_number"
                                            placeholder="{{ $labels['mobile_number'] }}"
                                            value="{{ old('mobile_number') }}" data-error="#mobile_number_error">
                                        <span class="number-code">+965</span>
                                        <input type="hidden" name="country_code" value="+965">
                                    </div>
                                    @if (!empty(@$errors) && @$errors->has('mobile_number'))
                                        <span class='error-login'>{{ $errors->first('mobile_number') }}</span>
                                    @endif
                                    <span class='error-login' id="mobile_number_error"></span>
                                </div>
                                <div class="input-outer">
                                    <input type="password" name="password" id="password"
                                        placeholder="{{ $labels['password'] }}" data-error="#password_error" {{--  oninput="CheckPassword(this);"  --}} required>

                                    @if (!empty(@$errors) && @$errors->has('password'))
                                        <span class='error-login'>{{ $errors->first('password') }}</span>
                                    @endif
                                    <span class='error-login' id="password_error"></span>
                                </div>
                                <div class="forget-password-outer text-start">
                                    <p>
                                        {{ $labels['by_creating_an_account_youre_agreed_to_our'] }}
                                        <a href="{{ route('frontend.terms_and_conditions') }}" class="forget-password">
                                            {{ $labels['terms_conditions'] }}
                                        </a>
                                        {{ $labels['and'] }}
                                        <a href="{{ route('frontend.privacy_policy') }}" class="forget-password">
                                            {{ $labels['privacy_policy'] }}
                                        </a>
                                    </p>
                                </div>
                                <button href="#" type="submit" class="comman-btn"
                                    id="signup_btn">{{ $labels['signup'] }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="skip">
                        <a href="{{ route('frontend.homePage') }}">{{ $labels['skip'] }}</a>
                    </div>
                    <div class="already account">
                        <p>{{ $labels['already_have_an_account'] }} <a class="forget-password"
                                href="{{ route('frontend.login') }}">{{ $labels['login'] }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="{{ asset('assets/frontend/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/additional.min.js') }}"></script>

    <script>
        $.validator.addMethod("pwcheck", function(value) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/.test(value)
        });

        $("#signup").validate({
            rules: {
                full_name: "required",
                email: {
                    required: true,
                    email : true,
                },
                mobile_number: {
                    required: true
                },
                password: {
                    required: true,
                    pwcheck : true,
                }
            },
            messages: {
                full_name: "{{$labels['please_enter_name']}}",
                email: {
                    required: "{{$labels['please_enter_email']}}",
                    email: "{{$labels['please_enter_valid_email']}}",
                },
                mobile_number: {
                    required: "{{$labels['please_enter_phone_number']}}"
                },
                password: {
                    required: "{{$labels['please_enter_password']}}",
                    pwcheck : "{{$labels['password_validation']}}",
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
                loader_show();
                var __url = "{{route('frontend.signup.submit')}}";
                var $data = $("#signup").serializeArray();
                $.ajax({
                    url : __url,
                    type: 'post',
                    data : $data,
                    dataType : 'json',
                    success : function (response) {
                        loader_hide();
                        if(response.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                iconColor : '#2A4B9B',
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['verify_create_account']}}",
                                timer: 3000,
                                timerProgressBar: true,
                                willClose : () => {
                                    location.href = response.url;
                                }
                            }).then(function (res){
                                location.href = response.url;
                            });
                        } else{
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            });
                        }
                    }, 
                    error : function (err) {
                        loader_hide();
                        console.error(err);

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
        })

        function CheckPassword(inputtxt) {
            var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
            if (inputtxt.value.match(passw)) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endpush
