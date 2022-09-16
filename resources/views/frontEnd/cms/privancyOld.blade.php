@extends('frontEnd.layouts.app')
@section('title','Dashboard')
@section('content')
    
    @section('data')
        <div class="content-body profile-main-body">
            <div class="body-head">
                <h2>{{ isset($cms->page_name) ? $cms->page_name : '' }}</h2>
            </div>
            <div class="body-content">
                <div class="profile-body">
                    <div class="body-content contactUs-body about-bday">
                         {!! isset($cms->description_eng) ? $cms->description_eng : '' !!}
                    </div>
                </div>
            </div>
        </div>
    @endsection
@endsection