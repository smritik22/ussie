@extends('frontEnd.layouts.app')
@section('title','Login')
@section('content')
<div class="login">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-6 login-left left-forgot">
            <div class="logo forgot-image">
                <img src="{{asset('assets/frontend/images/forgot-bg-image.png') }}" alt="">
            </div>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-6 login-right">
            {!! Form::open(['route' => ['frontend.setPassword'], 'method' => 'POST','name' => 'setPassword', 'id' => 'setPassword', 'autocomplete' => 'off','enctype' => 'multipart/form-data']) !!}
                @include('frontEnd.shared.messages')
                <div class="form-group">
                    <h2>Reset Password</h2>
                    <p>Email to reset password has been sent to your<br> email, kindly check it</p>
                </div>
                <input type="hidden" name="token" value="{{isset($token) ? $token : ''}}">
                <input type="hidden" name="email" value="{{isset($email) ? $email : ''}}">
                <div class="form-group login-details">
                    <div class="floating-form">
                        <div class="floating-label">
                            <input class="floating-input" name="password" type="password" placeholder=" ">
                            <span class="highlight"></span>
                            <label>New Password</label>
                            <span class="text-danger login-error">
                                @if ($errors->has('password'))
                                    <strong>{{ $errors->first('password') }}</strong>
                                @endif
                            </span>
                        </div>
                        <div class="floating-label">
                            <input class="floating-input" name="password_confirmation" type="password" placeholder=" ">
                            <span class="highlight"></span>
                            <label>Confirm Password</label>
                            <span class="text-danger login-error">
                                @if ($errors->has('password_confirmation'))
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group form-btn">
                    <button>Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
 @endsection