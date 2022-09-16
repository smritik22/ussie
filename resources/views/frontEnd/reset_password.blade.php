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
                        <form action="#" name="reset_password_form_name" id="reset_password_form" enctype="multipart/form-data" method="POST">
                            {{ csrf_field() }}
                            <div class="login-logo-blue">
                                <img src="{{asset('assets/img/blue-logo.svg')}}" alt="logo" />
                                <h4>{{$labels['create_a_new_password']}}</h4>
                                <p>{{$labels['your_new_password_must']}}</p>
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" name="password" id="password"  placeholder="{{$labels['new_password']}}" data-error="#password_error">
                                        <div class="toggle-password">
                                            <span class="show-password">{{$labels['show']}}</span>
                                            <span class="hide-password">{{$labels['hide']}}</span>
                                        </div>
                                    </div>
                                    <span class="error-login" id="password_error"></span>
                                </div>
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" name="confirm_password" id="confirm_password" placeholder="{{$labels['confirm_password']}}" data-error="#confirm_password_error">
                                        <div class="toggle-password">
                                            <span class="show-password">{{$labels['show']}}</span>
                                            <span class="hide-password">{{$labels['hide']}}</span>
                                        </div>
                                    </div>
                                    <span class="error-login" id="confirm_password_error"></span>
                                </div>
                                <input type="submit" name="reset_password" id="reset_password" class="comman-btn" value="{{$labels['submit']}}" >
                            </div>
                        </form>
                    </div>
                    <div class="skip-back">
                        <a href="{{route('frontend.forget_password')}}"><img src="{{asset('assets/img/skip-back.svg')}}" alt="icon" /></a>
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
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/.test(value);
        });

        $("#reset_password_form").validate({
            rules : {
                password: {
                    required:true,
                    pwcheck : true,
                },
                confirm_password: {
                    equalTo : "#password",
                },
            },
            messages: {
                password: {
                    required: "{{$labels['please_enter_password']}}",
                    pwcheck : "{{$labels['password_validation']}}",
                },
                confirm_password : {
                    equalTo : "{{$labels['confirm_password_did_not_matched']}}",
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
                var __url = "{{route('frontend.reset_password.submit')}}";
                var $data = $("#reset_password_form").serializeArray();
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
                                confirmButtonText: "{{$labels['ok']}}",
                                timer: 3000,
                                timerProgressBar: true,
                                willClose : () => {
                                    location.href = response.url;
                                }
                            }).then(function (res){
                                location.href = response.url;
                            });
                        }
                        else if(response.statusCode == 205){
                            Swal.fire({
                                icon: 'error',
                                text: response.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            }).then(function (res){
                                location.replace(response.url)
                            });
                        } 
                        else if(response.statusCode == 203){
                            Swal.fire({
                                icon: 'info',
                                text: response.message,
                                iconColor : '#2A4B9B',
                                showConfirmButton : true,
                                confirmButtonText: "{{$labels['ok']}}",
                            });
                        }
                        else {
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
                            iconColor : '#bb4f4f',
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
    </script>
@endpush