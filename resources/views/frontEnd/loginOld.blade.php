@extends('frontEnd.layouts.app')
@section('title','Login')
@section('content')
    <div class="login">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-6 login-left">
                <div class="logo">
                    <a href="#"><img src="{{asset('assets/frontend/logo/logo.png') }}" alt="logo"></a>
                    <h1>Welcome to<br> <strong>DOM</strong></h1>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6 login-right">
                {!! Form::open(['route' => ['frontend.login'], 'method' => 'POST','name' => 'login_school', 'id' => 'login_school', 'autocomplete' => 'off','enctype' => 'multipart/form-data']) !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    @include('frontEnd.shared.messages')
                    <div id="success_file_popup"></div>
                    <div class="form-group">
                        <h2>Login</h2>
                        <p>Welcome back! Please login to your<br> account.</p>
                    </div>
                    <div class="form-group login-details">
                        <div class="floating-form">
                            <div class="floating-label">
                                {!! Form::text('email', old('email'), ['class' => 'floating-input','placeholder'=>' ' ,'id' => 'email']) !!}
                                <span class="highlight"></span>
                                <label>Email</label>
                                <span class="text-danger login-error">
                                    @if ($errors->has('email'))
                                        <strong>{{ $errors->first('email') }}</strong>
                                    @endif
                                </span>
                            </div>
                            <div class="floating-label">
                                 {!! Form::password('password',['id' => 'password', 'class' => 'floating-input','placeholder' => ' ' ]) !!}
                                <span class="highlight"></span>
                                <label>Password</label>
                                <button class="eye" type="button">
                                    <span class="open-eye"><img src="{{asset('assets/frontend/images/eye_open.png') }}" alt=""></span>
                                    <span class="close-eye"><img src="{{asset('assets/frontend/images/eye_close.svg') }}" alt=""></span>
                                </button>
                                <span class="text-danger login-error">
                                    @if ($errors->has('password'))
                                        <strong>{{ $errors->first('password') }}</strong>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-btn">
                        <button type="submit">Login</button>
                        <a href="{{ route('frontend.forget_password')}}" class="forgot">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
 @endsection
 @section('script')
    <script type="text/javascript">
        $('.eye').click(function(){
            var type = $("#password").attr("type");
            if(type == "text")
            {
                $("#password").attr("type", "password");
                $(".close-eye").css('display','none');
                $(".open-eye").css('display','block');

            }
            else
            {
                $("#password").attr("type", "text");
                $(".open-eye").css('display','none');
                $(".close-eye").css('display','block');
            }
             
        });
        $('#login_school').on("submit",function(e) {
            e.preventDefault();

            var frm = $('#login_school');
            $('#success_file_popup').empty();
            $('.removeError').empty();
            var formData = new FormData(frm[0]);
            $.ajax({
                url: "{{ route('frontend.login')}}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) 
                {
                    if(response.success == true)
                    {
                        var newUrl = response.route;
                        window.location.href = newUrl;

                    }
                    else
                    {
                        $('#success_file_popup').append( messages('alert-danger', response.msg));
                           setTimeout(function() {
                           $('#success_file_popup').empty();
                           }, 5000);

                    }
                },
                error:function(err) {
                    if (err.status == 422)
                    {
                      
                        $.each(err.responseJSON.errors, function (i, error) {
                            var el = $(document).find('[name="'+i+'"]');
                            el.after($('<span class="removeError" style="color: red;">'+error[0]+'</span>'));
                        });
                    }
                }
           });

        });
    </script>
 @endsection