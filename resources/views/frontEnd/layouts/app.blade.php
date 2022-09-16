@extends('frontEnd.layouts.master')

@section('page')
	<!-- Navigation And Header-->
	@if(@auth()->guard('main_user')->check())
		<?php $auth_user = auth()->guard('main_user')->user();
		?>
			@if($auth_user->user_type == 1)
				@include('frontEnd.school.header_sidebar')
			@else
				@include('frontEnd.school.single_header_sidebar')
			@endif
		@if(\Request::route()->getName() != 'frontend.schoolProfile')
		@endif
	@endif
    @yield('content')

	{{-- @include('layouts.front.partials.footer') --}}
@stop