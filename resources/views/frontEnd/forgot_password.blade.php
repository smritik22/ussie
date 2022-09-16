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
    <div class="loin-outer">
        <div class="row m-0">
            <div class="col-lg-6 login-left-outer d-lg-block d-none">
                <div class="login-left">
                    <!-- <div class="login-logo">
                        <img src="{{asset('assets/img/Logo.svg')}}" alt="logo" />
                    </div> -->
                    <img src="{{asset('assets/img/login-icon.svg')}}" alt="icon" />
                </div>
            </div>
            <div class="col-lg-6 login-right-outer ">
                <div class="login-right">
                    <div class="login-form">
                        <form action="#" name="forgot_password_form" class="forgot_password_form" id="forgot_password_form" enctype="multipart/form-data">
                            <div class="login-logo-blue">
                                <img src="{{asset('assets/img/blue-logo.svg')}}" alt="logo" />
                                <h4>{{$labels['forgot_Password']}}</h4>
                                <p>{{$labels['please_enter_the_mobile']}}</p>
                                <div class="input-outer">
                                    <div class="mobile-number-code">
                                        <input type="number" name="mobile_number" id="mobile_number" class="mobile_number" placeholder="{{$labels['mobile_number']}}" data-error="#mobile_error">
                                        <span class="number-code">+965</span>
                                    </div>
                                    <span class="error-login" id="mobile_error"></span>
                                </div>
                                <input type="hidden" name="country_code" value="+965">
                                <input type="submit" id="forgot_submit" class="comman-btn" value="{{$labels['send']}}">
                            </div>
                        </form>
                    </div>
                    <div class="skip-back">
                        <a href="{{route('frontend.login')}}"><img src="{{asset('assets/img/skip-back.svg')}}" alt="icon" /></a>
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
        $('#forgot_password_form').validate({
            rules: {
                mobile_number: {
                    required: true
                },
            },
            messages: {
                mobile_number: {
                    required: "{{ $labels['please_enter_phone_number'] }}"
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
            success: "valid",
            submitHandler: function(e) {
                loader_show();
                var __url = "{{ route('frontend.forgot_password.submit') }}";
                var data = $("#forgot_password_form").serializeArray();

                $.ajax({
                    url: __url,
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
                                showConfirmButton: true,
                                confirmButtonText : "{{$labels['verify_otp']}}",
                                showCancelButton : true,
                                cancelButtonText : "{{$labels['cancle']}}",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = response.url;
                                } else if (result.isDenied) {
                                    location.reload();
                                }
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
                    error : function (err) {
                        loader_hide();
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
        })
    </script>
@endpush