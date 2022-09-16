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
                        <form action="#" id="login_otp_varify" method="post" name="login_otp_varify" enctype="multipart/form-data">
                            <div class="login-logo-blue">
                                <img src="{{asset('assets/img/blue-logo.svg')}}" alt="logo" />
                                <h4>{{$labels['otp']}}</h4>
                                <p>{{$labels['enter_otp_sent_to_your']}}  {{ @urldecode($users->country_code) ?: ""}} <span>{{ @$users->mobile_number ?: ""}}</span></p>
                                <div class="input-outer-otp">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="mobile" name="mobile" value="{{@$users->mobile_number ?: ""}}">
                                    <input type="hidden" name="otp_verify_type" value="{{(\Session::get('isForgotPass') !=1 )? config('constants.otp_varify_type_register') : config('constants.otp_varify_type_forgot_password')}}" id="otp_verify_type">

                                    <div class="input-outer">
                                        <input type="text" maxlength="1" id="digit-1" class="otp__input input" name="digit[]" data-next="digit-2" inputmode="numeric" onkeypress="onKeyDownEvent(1, event)" onkeyup="onKeyUpEvent(1, event)" onfocus="onFocusEvent(1)">
                                    </div>
                                    <div class="input-outer">
                                        <input type="text" maxlength="1" id="digit-2" class="otp__input input" name="digit[]" data-next="digit-3" inputmode="numeric" onkeypress="onKeyDownEvent(2, event)" data-previous="digit-1" onkeyup="onKeyUpEvent(2, event)" onfocus="onFocusEvent(2)">
                                    </div>
                                    <div class="input-outer">
                                        <input type="text" maxlength="1" id="digit-3" class="otp__input input" name="digit[]" data-next="digit-4" inputmode="numeric" onkeypress="onKeyDownEvent(3, event)" data-previous="digit-2" onkeyup="onKeyUpEvent(3, event)" onfocus="onFocusEvent(3)">
                                    </div>
                                    <div class="input-outer">
                                        <input type="text" maxlength="1" id="digit-4" class="otp__input input" name="digit[]" data-next="digit-5" inputmode="numeric" onkeypress="onKeyDownEvent(4, event);" data-previous="digit-3" onkeyup="onKeyUpEvent(4, event)" onfocus="onFocusEvent(4)">
                                    </div>
                                </div>
                                
                                <div class="input-outer">
                                    <small class="error-login showOtpValidation text-danger" style="opacity: 0; cursor: default;">{{$labels['please_enter_otp']}}</small>
                                </div>
                                <p class="otp-timmer">{{$labels['expiring_in']}} <span id="some_div">{{$invert == 1 ? '00:00' : $elapsed}}</span></p>
                                <input type="submit" class="comman-btn otp_verify_btn" value="{{$labels['verify_create_account']}}">
                            </div>
                        </form>
                    </div>
                    <div class="skip-back">
                        <a href="{{route('frontend.signup')}}"><img src="{{asset('assets/img/skip-back.svg')}}" alt="icon" /></a>
                    </div>
                    <div class="already account">
                        <p>{{$labels['dont_recieve_code']}} <a class="forget-password" href="#" onclick="otp_resend()">{{$labels['request_again']}}</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
@push('after-scripts')
    <script>

        function getCodeBoxElement(index) {
            return document.getElementById('digit-' + index);
          }
          function onKeyUpEvent(index, event) {
            const eventCode = event.which || event.keyCode;
            if (getCodeBoxElement(index).value.length === 1) {
              if (index !== 4) {
                getCodeBoxElement(index+ 1).focus();
              } 
            //   else {
            //     getCodeBoxElement(index).blur();
            //     // Submit code
            //     console.log('submit code ');
            //   }
            }
            if (eventCode === 8 && index !== 1) {
              getCodeBoxElement(index - 1).focus();
            }
          }
          function onFocusEvent(index) {
            for (item = 1; item < index; item++) {
              const currentElement = getCodeBoxElement(item);
              if (!currentElement.value) {
                  currentElement.focus();
                  break;
              }
            }
          }
    
          function onKeyDownEvent(index, event) {
            let keyCode = event.which;
            if ( (keyCode != 8 || keyCode ==32 ) && (keyCode < 48 || keyCode > 57)) event.preventDefault();
          }
    
    
        $(document).ready(function() {

            $('#login_otp_varify').submit(function(e){
                e.preventDefault();
                let isValue = true;
                $(this).find('.otp__input').each((index,element) => {
                    let inputValue = $(element).val();
                    if( $.trim(inputValue) == "" || inputValue == "undefined" ){
                        isValue = false;
                    }
                });
    
                if(!isValue){
                    $('.showOtpValidation').text("{{$labels['please_enter_otp']}}");
                    $('.showOtpValidation').animate({'opacity': 1}, 1000);
                    $('.showOtpValidation').css({'cursor' : 'text'});
                    setTimeout(()=>{
                        $('.showOtpValidation').animate({'opacity': 0}, 2000);
                        $('.showOtpValidation').css({'cursor': 'default'});
                    },5000);

                    return false;
                }

                var __submiturl = "{{route('frontend.varify_otp.submit')}}";
                var params = $('#login_otp_varify').serializeArray();
                loader_show();
                $.ajax({
                    type : 'post',
                    url : __submiturl,
                    data : params,
                    dataType : 'json',
                    success : function (result) {
                        loader_hide();
                        if(result.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                text: result.message,
                                iconColor : '#2A4B9B',
                                showConfirmButton: true,
                                confirmButtonText: "{{$labels['ok']}}",
                            }).then(function (res){
                                location.href = result.url;
                            });
                        } else if(result.statusCode == 203) {
                            Swal.fire({
                                icon: 'info',
                                text: result.message,
                                iconColor : '#2A4B9B',
                                showConfirmButton: true,
                                showDenyButton: true,
                                denyButtonText: "{{$labels['resend_otp']}}",
                                denyButtonText : "{{$labels['cancel']}}",
                            }).then(function (result){
                                if (result.isConfirmed) {
                                    otp_resend();
                                } else if (result.dismiss === Swal.DismissReason.deny) {
                                    location.replace(result.url);
                                } else {
                                    location.reload();
                                }
                            });
                        } else if(result.statusCode == 204 || result.statusCode == 201){
                            Swal.fire({
                                icon: 'error',
                                text: result.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : false,
                                timer: 3000,
                                timerProgressBar: true,
                                willClose : () => {
                                    
                                }
                            });
                        }
                        else if(result.statusCode == 206 || result.statusCode == 205){
                            Swal.fire({
                                icon: 'error',
                                text: result.message,
                                iconColor : '#bb4f4f',
                                showConfirmButton : false,
                                timer: 3000,
                                timerProgressBar: true,
                                willClose : () => {
                                    location.replace(result.url);
                                }
                            });
                        }
                    },
                    error : function (error) {
                        loader_hide();
                        console.error(error);
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

                })
            });

            var timer2 = $("#some_div").text();
            var interval = setInterval(function() {
                var timer = timer2.split(':');
                var minutes = parseInt(timer[0], 10);
                var seconds = parseInt(timer[1], 10);
                --seconds;
                minutes = (seconds < 0) ? --minutes : minutes;
                if (seconds == 0 && minutes < 1) {
                    clearTimeout(interval);
                    doSomething();
                }
                
                if (minutes < 0) {
                    clearInterval(interval);
                }
                else{
                    seconds = (seconds < 0) ? 59 : seconds;
                    seconds = (seconds < 10) ? '0' + seconds : seconds;
                    minutes = (minutes < 10) ? '0' + minutes : minutes;
                    $('#some_div').html(  minutes + ':' + seconds );
                    timer2 = minutes + ':' + seconds;
                }
                
    
            }, 1000);
    
    
            function doSomething() {
                $('.otp_verify_btn').attr('disabled','disabled');
                Swal.fire({
                    icon: 'info',
                    text: "{{$labels['otp_has_been_expired_resend_otp']}}",
                    iconColor : '#2A4B9B',
                    showConfirmButton: true,
                    showDenyButton: true,
                    confirmButtonText: "{{$labels['resend_otp']}}",
                    denyButtonText : "{{$labels['cancel']}}",
                }).then(function (result){
                    if (result.isConfirmed) {
                        otp_resend();
                    } else if (result.dismiss === Swal.DismissReason.deny) {
                        location.replace("{{route('frontend.signup')}}");
                    } else {
                        location.reload();
                    }
                });
            }
        });
    
        function otp_resend() {
            event.preventDefault();
            let mobile = document.getElementById("mobile").value;
            let url_link = "{{route('frontend.resend_otp')}}";
            
            $.ajax({
                url: url_link,
                type: "POST",
                data: {
                    "_token": "{{csrf_token()}}",
                    "mobile": mobile,
                },
                dataType: "json",
                success: function(result) {
                    if (result.statusCode==200) {
                        Swal.fire({
                            icon: 'info',
                            text: result.message,
                            iconColor : '#2A4B9B',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            willClose : () => {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            iconColor : "#bb4f4f",
                            text: result.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            willClose : () => {
                                
                            }
                        });
                    }
                }
            });
        }
        
    </script>
@endpush
