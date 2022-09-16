@extends('dashboard.layouts.master')
@section('title','Show location')
@section('content')
<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />

<style>
 .select2-container 
   {
    width: 100% !important;
   }
    .pac-container {
        z-index: 10000 !important;
    }
</style>
<div class="padding edit-program">
    <div class="box">
        <div class="box-header dker">
            <h3>School Location</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                <a href="{{ route('school-admin') }}">School Admin Mgmt</a> /
                <a href="javascript:void(0)">School Location</a>
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('school-admin')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-body">
            {{Form::open(['route'=>['school-location.updateLocation'],'method'=>'POST', 'id' => 'editlocationForm','files' => true])}}
                <div class="errors_message"></div>
                <input type="hidden" name="schoolProfile_id" value="{{ isset($location->school_profile_id) ?  $location->school_profile_id : '' }}">
                <input type="hidden" name="location_id" value="{{ isset($location->id) ?  $location->id : '' }}">
                <div class="mutli_location">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">School Code
                        </label>
                        <div class="col-sm-10">
                            {!! Form::number('school_code',old('school_code', isset($location->school_code) ? $location->school_code : ''), ['id' => 'school_code', 'class' => 'form-control', 'placeholder' => 'School Code']) !!}
                        </div>
                    </div>
                    <div class="address_block_edit">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Address
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('address',old('address', isset($location->address) ? $location->address : ''), ['id' => 'address', 'class' => 'form-control addresslocation_edit', 'placeholder' => 'Address' ]) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Latitude
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('latitude',old('latitude', isset($location->latitude) ? $location->latitude : ''), ['id' => 'latitude', 'class' => 'form-control latitude', 'placeholder' => 'Latitude' ]) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Longitude
                            </label>
                            <div class="col-sm-10">
                               {!! Form::text('longitude',old('longitude', isset($location->longitude) ? $location->longitude : ''), ['id' => 'longitude', 'class' => 'form-control longitude', 'placeholder' => 'Longitude' ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">City
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('city',old('city', isset($location->city) ? $location->city : ''), ['id' => 'city', 'class' => 'form-control city', 'placeholder' => 'City' ]) !!}
                            </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">State
                        </label>
                        <div class="col-sm-10">
                            {!! Form::text('state',old('state', isset($location->state) ? $location->state : ''), ['id' => 'state', 'class' => 'form-control state', 'placeholder' => 'State',]) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">Post Code
                        </label>
                        <div class="col-sm-10">
                           {!! Form::text('post_code', old('post_code', isset($location->post_code) ? $location->post_code : ''), ['id' => 'post_code', 'class' => 'form-control post_code', 'placeholder' => 'Post Code' ]) !!}
                        </div>
                    </div>
                    <div class="form-group row assign-check">
                        <label for="name" class="col-sm-2 form-control-label">Assign Pre Set Package
                        </label>
                        <?php
                        $programArray = [];
                        if(isset($location->package_id))
                        {
                            $programArray = explode(",",$location->package_id);
                            
                        }
                       
                        ?>
                                 
                        <div class="col-sm-10 check-box">
                         @if(isset($packages) && !empty($packages))
                            <select name="program[]" id="country_id"
                                            class="form-control select-program " multiple="true">
                              @foreach($packages as $data)
                              <option value="{{ $data->id}}" @if(in_array($data->id, $programArray)) selected @endif>{{ $data->package_name}}</option>
                                @endforeach
                              </select>
                         @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-2">
                        <a href="{{ url()->previous() }}" class="btn btn-default m-t">
                            <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.back') !!}
                        </a>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
@push("after-scripts")
<script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $("#editlocationForm :input").prop("disabled", true);
        $('.select-program').select2();
        
    </script>
@endpush