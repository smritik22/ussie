@extends('frontEnd.layouts.auth_layout.layout')
@section('content')

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
                        <form action="#" method="POST" name="signin" id="signin" enctype="multipart/form-data">
                            <div class="login-logo-blue">
                                <img src="{{ asset('assets/img/blue-logo.svg') }}" alt="logo" />
                                <h4>{{ $labels['sign_in_to_continue'] }}</h4>
                                <p>{{ $labels['thousands_of_homes_waiting_for_you'] }}</p>
                                <div class="input-outer">
                                    <div class="mobile-number-code">
                                        <input type="number" name="mobile_number" id="mobile_number"
                                            data-error="#mobile_number_error" placeholder="{{ $labels['mobile_number'] }}">
                                        <span class="number-code">+965</span>
                                    </div>
                                    <span class="error-login" id="mobile_number_error"></span>
                                </div>
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" name="password" id="password" data-error="#password_error"
                                            placeholder="{{ $labels['password'] }}">
                                        <div class="toggle-password">
                                            <span class="show-password">{{ $labels['show'] }}</span>
                                            <span class="hide-password">{{ $labels['hide'] }}</span>
                                        </div>
                                    </div>
                                    <span class="error-login" id="password_error"></span>
                                </div>
                                <div class="forget-password-outer">
                                    <a class="forget-password"
                                        href="{{ route('frontend.forget_password') }}">{{ $labels['forgot_Password?'] }}</a>
                                </div>
                                <input value="{{ $labels['login'] }}" type="submit" class="comman-btn" name="submit">
                            </div>
                        </form>
                    </div>
                    <div class="skip">
                        <a href="{{ route('frontend.homePage') }}">{{ $labels['skip'] }}</a>
                    </div>
                    <div class="already account">
                        <p>{{ $labels['dont_have_an_account'] }} <a class="forget-password"
                                href="{{ route('frontend.signup') }}">{{ $labels['signup'] }}</a></p>
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
        $("#signin").validate({
            rules: {
                mobile_number: {
                    required: true
                },
                password: {
                    required: true,
                }
            },
            messages: {
                mobile_number: {
                    required: "{{ $labels['please_enter_phone_number'] }}"
                },
                password: {
                    required: "{{ $labels['please_enter_password'] }}",
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
                var __loginUrl = "{{ route('frontend.login.submit') }}";
                var data = $("#signin").serializeArray();

                $.ajax({
                    url: __loginUrl,
                    data: data,
                    dataType: 'json',
                    type: 'post',
                    success: function(response) {
                        loader_hide();
                        if (response.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                iconColor: '#2A4B9B',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                willClose: () => {
                                    location.reload();
                                }
                            }).then(function(result) {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor: '#bb4f4f',
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['ok']}}",
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            text: "{{$labels['something_went_wrong']}}",
                            iconColor: '#bb4f4f',
                            showConfirmButton: true,
                            confirmButtonText: "{{$labels['ok']}}",
                        });
                    }
                });
            }
        });
    </script>
@endpush
