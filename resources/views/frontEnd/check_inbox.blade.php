@extends('frontEnd.layouts.app')
@section('title','Login')
@section('content')
<div class="login">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-6 login-left left-forgot">
                <div class="logo forgot-image">
                    <img src="{{ asset('assets/frontend/images/forgot-bg-image.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6 login-right">
                <form action="">
                    <div class="form-group">
                        <h2>Check Your Inbox</h2>
                        <p class="check">Email to reset password has been sent to your<br> email. Kindly check it</p>
                    </div>
                    <div class="form-group form-btn">
                        <button><a href="{{ route('frontend.loginpage')}}">Back</a></button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection