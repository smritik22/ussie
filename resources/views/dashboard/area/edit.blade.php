@extends('dashboard.layouts.master')
@section('title', __('backend.edit_area'))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <style type="text/css">
        .error {
            color: red;
            margin-left: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="padding edit-package">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.edit_area') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('area') }}">{{ __('backend.areas') }}</a> / 
                    <span>{{ __('backend.edit_area') }}</span>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('area') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['area.update',$area->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'areaForm' ])}}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.country') !!}</label>
                        <div class="col-sm-10">
                            <select name="country" id="country" class="form-control" value="{{old('country',$area->country_id)}}" onchange="getGovernorateList(this)">
                                <option value="" aria-readonly="true" >Select Country</option>
                                @if ($countries)
                                    @foreach ($countries as $key => $value)
                                        <option value="{{$value->id}}" {{ (old('country',$area->country_id) == $value->id )? "selected":''}}>{{urldecode($value->name)}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('country'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('country') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.governorate') !!}</label>
                        <div class="col-sm-10">
                            <select name="governorate" id="governorate" class="form-control" value="{{old('governorate',$area->governorate_id)}}">
                                <option value="" aria-readonly="true" >Select Governorate</option>
                            </select>
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('governorate'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('governorate') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.area_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="area_name" id="area_name" class="form-control" placeholder="Area Name" value="{{old('area_name',urldecode($area->name))}}" maxlength="100">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('area_name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('area_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.latitude') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="latitude" id="latitude" class="form-control decimal" placeholder="Latitude" value="{{old('latitude', $area->latitude)}}" maxlength="20">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('latitude'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('latitude') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.longitude') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="longitude" id="longitude" class="form-control decimal" placeholder="Longitude" value="{{old('longitude', $area->longitude)}}" maxlength="20">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('longitude'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('longitude') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.area_default_range') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="default_range" id="default_range" class="form-control decimal" placeholder="{!!  __('backend.area_default_range') !!}" value="{{old('default_range', $area->default_range)}}" maxlength="2">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('default_range'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('default_range') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.area_updated_range') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="updated_range" id="updated_range" class="form-control decimal" placeholder="{!!  __('backend.area_updated_range') !!}" value="{{old('updated_range', $area->updated_range)}}" maxlength="2">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('updated_range'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('updated_range') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-sm-2 form-control-label">{!! __('backend.topicPhoto') !!}</label>
                        <div class="col-sm-10">
                            @if ($area->image != '')
                                <div class="row">
                                    <div class="col-sm-12 images">
                                        <div id="user_photo" class="col-sm-4 box p-a-xs">
                                            <a target="_blank" href="{{  $image_url . $area->image }}"><img
                                                    src="{{ $image_url .  $area->image }}" class="img-responsive">
                                            </a>
                                            <br>
                                            <div class="delete">
                                                <a onclick="document.getElementById('user_photo').style.display='none';document.getElementById('photo_delete').value='1';document.getElementById('undo').style.display='block';"
                                                    class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                                                {{ $area->photo }}
                                            </div>
                                        </div>
                                        <div id="undo" class="col-sm-4 p-a-xs" style="display: none">
                                            <a
                                                onclick="document.getElementById('user_photo').style.display='block';document.getElementById('photo_delete').value='0';document.getElementById('undo').style.display='none';">
                                                <i class="material-icons">&#xe166;</i> {!! __('backend.undoDelete') !!}
                                            </a>
                                        </div>
    
                                        {!! Form::hidden('photo_delete', '0', ['id' => 'photo_delete']) !!}
                                    </div>
                                </div>
                            @endif
    
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'image', 'accept' => 'image/*']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                        </div>
                    </div>


                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                            <a href="{{ route('area')}}" class="btn btn-default m-t">
                                <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
                            </a>
                    </div>
                </div>


                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
@push("after-scripts")
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


    <script>
        $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        });

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({value: e.loaded, max: e.total});
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
    </script>
    <script type="text/javascript">
        
        function getGovernorateList(old_governorate){

            let country_id = $('#country').val();
            let url = "{{route('area.governorateList')}}";
            if(country_id){
                
                $.ajax({
                    url : url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token' : "{{csrf_token()}}",
                        'country_id' : country_id
                    },
                    beforeSend:function(){
                        $('#governorate').html('<option value="" aria-readonly="true">Select Governorate</option>');
                    },
                    success:function(resultData){
                        if(resultData){
                            var govrns = '';
                            var selected = "";
                            $.each(resultData, (index,value) => {
                                if(old_governorate==value.id) {
                                    selected = "selected";
                                }
                                govrns += '<option value="'+value.id+'" '+selected+'>'+value.name+'</option>';
                            });
                            $('#governorate').append(govrns);
                        }
                    },
                    error:function(err){
                        console.erro(err);
                    }
                });
            }else{
                $('#governorate').find('option:selected').prop('selected', false);
                $('#governorate').html('<option value="" aria-readonly="true">Select Governorate</option>');
                $('#governorate').val('');
            }
        }

        $(document).ready(function(e){
            let old_country = "{{old('country',$area->country_id)}}";
            let old_governorate = "{{old('governorate',$area->governorate_id)}}";

            if(old_country){
                getGovernorateList(old_governorate);
            }
        });

    </script>
@endpush
