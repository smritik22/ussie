@extends('dashboard.layouts.master')
@section('title', __('backend.edit_property'))
@push('after-styles')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
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
                        &#xe02e;</i> {{ __('backend.edit_property') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('properties') }}">{{ __('backend.properties') }}</a>

                </small>
            </div>

            <div class="box-body">
                {{ Form::open(['route' => ['property.update', encrypt($property->id)],'method' => 'POST','files' => true,'enctype' => 'multipart/form-data','id' => 'editForm','accept-charset' => 'utf-8']) }}

                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_agent') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                value="{{ @urldecode($property->agentDetails->full_name ?? '-') }}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_name') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="property_name" id="property_name" class="form-control"
                                placeholder="{{ __('backend.property_name') }}"
                                value="{{ old('property_name', $property->property_name) }}" maxlength="200">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_name'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_images') !!}</label>
                        <div class="col-sm-9">
                            <input type="file" name="property_images[]" id="property_images" class="form-control"  accept="image/*"
                                placeholder="{{ __('backend.property_images') }}" multiple max="2">
                            <span class="help-block" id="property_images_input_error">
                                @if (!empty(@$errors) && @$errors->has('property_images'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('property_images') }}</span>
                                @endif
                            </span>
                        </div>


                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="mt-1 text-center uploaded_images" style="display: none">
                                <h3>New Uploaded Images</h3>
                                <div class="images-preview-div"> </div>
                            </div>
                        </div>
                    </div>

                    @if( $property->propertyImages->count() > 0 )
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="mt-1 text-center old_images">
                                    <h3>Old Images</h3>
                                    <div class="old-images-div">
                                        <div class="row">
                                            @foreach ($property->propertyImages as $key => $image)
                                                <div class="col-sm-3 property_images_old">
                                                    <div id="property_photo_{{ $image->id }}">
                                                        <img src="{{URL::asset('storage/property_images/' . $property->id . '/' . $image->property_image )}}"
                                                            alt="Property image" height="170px">
                                                        <br>
                                                        <div class="delete">
                                                            <a onclick="deleteImage(this)" data-image_id="{{$image->id}}"
                                                                class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                                                            {{ $image->property_image }}
                                                        </div>
                                                    </div>
                                                    <div id="undo_{{ $image->id }}" class="col-sm-4 p-a-xs" style="display: none">
                                                        <a onclick="undoDeleteImage(this)" data-image_id="{{$image->id}}">
                                                            <i class="material-icons">&#xe166;</i> {!!  __('backend.undoDelete') !!}
                                                        </a>
                                                    </div>
                                                    {!! Form::checkbox('deleted_image[]', $image->id, "", ['class'=>'hidden-checkboxes', 'id' => 'is_deleted_'.$image->id,'style'=>'display:none']) !!}
                                                    {{--  {!! Form::hidden('property_photo_delete_'.$image->id,'0', array('id'=>'property_photo_delete_'.$image->id , 'class' => 'delete_photos')) !!}  --}}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3">{!! __('backend.property_description') !!}</label>
                        <div class="col-sm-9">
                            {!! Form::textarea('property_description', old('property_description', $property->property_description), ['class' => 'form-control', 'id' => 'property_description', 'rows' => '3', 'maxlength' => 500, 'placeholder' => __('backend.property_description')]) !!}
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_description'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_description') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3">{!! __('backend.property_address') !!}</label>
                        <div class="col-sm-9">
                            {!! Form::textarea('property_address', old('property_address', $property->property_address), ['class' => 'form-control', 'id' => 'property_address', 'rows' => '3', 'maxlength' => 500, 'placeholder' => __('backend.property_address')]) !!}
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_address'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_address') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.country') !!}</label>
                        <div class="col-sm-9">
                            <select name="country" id="country" class="form-control"
                                value="{{ old('country', @$property->areaDetails->country_id) }}"
                                onchange="getGovernorateList(this)">
                                <option value="" aria-readonly="true">Select Country</option>
                                @if ($countries)
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ old('country', @$property->areaDetails->country_id) == $value->id ? 'selected' : '' }}>
                                            {{ urldecode($value->name) }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('country'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('country') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.governorate') !!}</label>
                        <div class="col-sm-9">
                            <select name="governorate" id="governorate" class="form-control"
                                value="{{ old('governorate', @$property->areaDetails->governorate_id) }}"
                                onchange="getAreaList(this)">
                                <option value="" aria-readonly="true">Select Governorate</option>
                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('governorate'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('governorate') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.area') !!}</label>
                        <div class="col-sm-9">
                            <select name="area" id="area" class="form-control"
                                value="{{ old('area', @$property->area_id) }}">
                                <option value="" aria-readonly="true">Select Area</option>
                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('area'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('area') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_type') !!}</label>
                        <div class="col-sm-9">
                            <select name="property_type" id="property_type" class="form-control"
                                value="{{ old('property_type', @$property->propertyTypeDetails->type) }}">
                                <option value="" aria-readonly="true" disabled>Select Property Type</option>
                                @foreach ($property_types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('property_type', @$property->propertyTypeDetails->id) == $type->id ? 'selected' : ' ' }}>
                                        {!! $type->type !!}</option>
                                @endforeach
                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_type'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_type') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_for') !!}</label>
                        <div class="col-sm-9">
                            <select name="property_for" id="property_for" class="form-control"
                                value="{{ old('property_for', @$property->property_for) }}">
                                <option value="" aria-readonly="true" disabled>Select Property Type</option>
                                @foreach (config('constants.PROPERTY_FOR') as $proFor)
                                    <option value="{{ $proFor['value'] }}"
                                        {{ old('property_for', @$property['property_for']) == $proFor['value'] ? 'selected' : ' ' }}>
                                        {!! Helper::getLabelValueByKey($proFor['label_key']) !!}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_for'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_for') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_sqft_area') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="property_sqft_area" id="property_sqft_area"
                                class="form-control decimal" placeholder="{{ __('backend.property_sqft_area') }}"
                                maxlength="10" value="{{ old('property_sqft_area', @$property->property_sqft_area) }}"
                                {{-- onkeydown="return restrictInput(this, event, 'integer')" --}}>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_sqft_area'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_sqft_area') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_price') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="property_price" id="property_price" class="form-control decimal"
                                placeholder="{{ __('backend.property_price') }}" maxlength="15"
                                value="{{ old('property_price', @$property->base_price) }}" {{-- onkeydown="return restrictInput(this, event, 'integer')" --}}>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_price'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_price') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_amenities') !!}</label>
                        <div class="col-sm-9">
                            <select name="amenities[]" id="amenities" class="form-control select-amenities"
                                multiple="multiple" value="{{ old('amenities[]', @$property->property_amenities_ids) }}">
                                <option value="" aria-readonly="true" disabled>Select Property Amenities</option>
                                @foreach ($amenities as $amenity)
                                    <option value="{{ $amenity['id'] }}"
                                        {{ @is_array(old('amenities[]', @explode(',', $property['property_amenities_ids']))) &&in_array($amenity['id'], old('amenities[]', @explode(',', $property['property_amenities_ids'])))? 'selected': ' ' }}>
                                        {{ urldecode($amenity->amenity_name) }}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('property_amenities'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('property_amenities') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_condition') !!}</label>
                        <div class="col-sm-9">
                            <select name="condition" id="condition" class="form-control"
                                value="{{ old('condition', @$property->condition_type_id) }}">
                                <option value="" aria-readonly="true">Select Property Condition</option>
                                @foreach ($property_conditions as $condition)
                                    <option value="{{ $condition['id'] }}"
                                        {{ old('condition', $property['condition_type_id']) == $condition->id ? 'selected' : ' ' }}>
                                        {{ urldecode($condition->condition_text) }}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('condition'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('condition') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_completion_status') !!}</label>
                        <div class="col-sm-9">
                            <select name="completion_status" id="completion_status" class="form-control"
                                value="{{ old('completion_status', $property->completion_status_id) }}">
                                <option value="" aria-readonly="true">Select Property Completion Status</option>
                                @foreach ($property_completions as $completion_status)
                                    <option value="{{ $completion_status['id'] }}"
                                        {{ old('completion_status', $property['completion_status_id']) == $completion_status->id ? 'selected' : ' ' }}>
                                        {{ urldecode($completion_status->completion_type) }}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('completion_status'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('completion_status') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_bedroom_type') !!}</label>
                        <div class="col-sm-9">
                            <select name="bedroom_type" id="bedroom_type" class="form-control"
                                value="{{ old('bedroom_type', $property->bedroom_type) }}">
                                <option value="" aria-readonly="true">Select Property Bedroom Type</option>
                                @foreach ($bedroom_types as $bedroom_type)
                                    <option value="{{ $bedroom_type['id'] }}"
                                        {{ old('bedroom_type', $property['bedroom_type']) == $bedroom_type['id'] ? 'selected' : ' ' }}>
                                        {{ $bedroom_type['type'] }}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('bedroom_type'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('bedroom_type') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_total_bedrooms') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="total_bedrooms" id="total_bedrooms" class="form-control"
                                placeholder="{{ __('backend.property_total_bedrooms') }}" maxlength="5"
                                value="{{ old('total_bedrooms', @$property->total_bedrooms) }}"
                                onkeydown="return restrictInput(this, event, 'digits')">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('total_bedrooms'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('total_bedrooms') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_bathroom_type') !!}</label>
                        <div class="col-sm-9">
                            <select name="bathroom_type" id="bathroom_type" class="form-control"
                                value="{{ old('bathroom_type', $property->bathroom_type) }}">
                                <option value="" aria-readonly="true">Select Property Bedroom Type</option>
                                @foreach ($bathroom_types as $bathroom_type)
                                    <option value="{{ $bathroom_type['id'] }}"
                                        {{ old('bathroom_type', $property['bathroom_type']) == $bathroom_type['id'] ? 'selected' : ' ' }}>
                                        {{ $bathroom_type->type }}</option>
                                @endforeach

                            </select>
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('bedroom_type'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('bedroom_type') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_total_bedrooms') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="total_bathrooms" id="total_bedrooms" class="form-control"
                                placeholder="{{ __('backend.property_total_bedrooms') }}" maxlength="5"
                                value="{{ old('total_bedrooms', @$property->total_bedrooms) }}"
                                onkeydown="return restrictInput(this, event, 'digits')">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('total_bedrooms'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('total_bedrooms') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.property_total_toilets') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="total_toilets" id="total_toilets" class="form-control"
                                placeholder="{{ __('backend.property_total_toilets') }}" maxlength="5"
                                value="{{ old('total_toilets', @$property->total_toilets) }}"
                                onkeydown="return restrictInput(this, event, 'digits')">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('total_toilets'))
                                    <span style="color: red;"
                                        class='validate'>{{ $errors->first('total_toilets') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.latitude') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="latitude" id="latitude" class="form-control decimal"
                                placeholder="{{ __('backend.latitude') }}" maxlength="15"
                                value="{{ old('latitude', @$property->property_address_latitude) }}">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('latitude'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('latitude') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!! __('backend.longitude') !!}</label>
                        <div class="col-sm-9">
                            <input type="text" name="longitude" id="longitude" class="form-control decimal"
                                placeholder="{{ __('backend.longitude') }}" maxlength="15"
                                value="{{ old('longitude', @$property->property_address_longitude) }}">
                            <span class="help-block">
                                @if (!empty(@$errors) && @$errors->has('longitude'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('longitude') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>


                    {{-- personal information class ended --}}
                </div>
                <div class="form-group row m-t-md">
                    <div class="offset-sm-3 col-sm-9">
                        <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i
                                class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
                        <a href="{{ route('properties') }}" class="btn btn-default m-t">
                            <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
                        </a>
                    </div>
                </div>


                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>

    <script>

        function getFileUploadLimit(){
            var fileUploadLimit = {{ @$maxImageUploads?:20}};
            var numberOfChecked = $('.hidden-checkboxes:checkbox:checked').length;
            var totalCheckboxes = $('.hidden-checkboxes:checkbox').length;
            var numberNotChecked = totalCheckboxes - numberOfChecked;
            var remainUploads = fileUploadLimit - numberNotChecked;

            return remainUploads;
        }

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({
                    value: e.loaded,
                    max: e.total
                });
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }

        var old_country = "{{ old('country', @$property->areaDetails->country_id) }}";
        var old_governorate = "{{ old('governorate', @$property->areaDetails->governorate_id) }}";
        var old_area = "{{ old('area', $property->area_id) }}";


        function getGovernorateList() {

            let country_id = $('#country').val();
            let url = "{{ route('area.governorateList') }}";
            if (country_id) {

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'country_id': country_id
                    },
                    beforeSend: function() {
                        $('#governorate').html(
                            '<option value="" aria-readonly="true">Select Governorate</option>');
                    },
                    success: function(resultData) {
                        if (resultData) {
                            var govrns = '';
                            var selected = "";
                            $.each(resultData, (index, value) => {
                                selected = "";
                                if (old_governorate == value.id) {
                                    selected = "selected";
                                }
                                govrns += '<option value="' + value.id + '" ' + selected + '>' + value
                                    .name + '</option>';
                            });
                            $('#governorate').append(govrns);

                            if (old_governorate) {
                                setTimeout(() => {
                                    getAreaList(old_area);
                                    old_governorate = "";
                                }, 100)

                            }

                            old_country = "";
                        }
                    },
                    error: function(err) {
                        console.erro(err);
                    }
                });
            } else {
                $('#governorate').find('option:selected').prop('selected', false);
                $('#governorate').html('<option value="" aria-readonly="true">Select Governorate</option>');
                $('#governorate').val('');

                $('#area').find('option:selected').prop('selected', false);
                $('#area').html('<option value="" aria-readonly="true">Select Area</option>');
                $('#area').val('');
            }
        }

        function getAreaList() {
            let governorate_id = $('#governorate').val();
            let url = "{{ route('area.areaList') }}";
            if (governorate_id) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'governorate': governorate_id
                    },
                    beforeSend: function() {
                        $('#area').html('<option value="" aria-readonly="true">Select Area</option>');
                    },
                    success: function(resultData) {
                        if (resultData) {
                            var areas = '';
                            var selected = "";
                            $.each(resultData, (index, value) => {
                                selected = "";
                                if (old_area == value.id) {
                                    selected = "selected";
                                }
                                areas += '<option value="' + value.id + '" ' + selected + '>' + value
                                    .name + '</option>';
                            });
                            $('#area').append(areas);
                            old_area = "";
                        }
                    },
                    error: function(err) {
                        console.error(err);
                    }
                });
            } else {
                $('#area').find('option:selected').prop('selected', false);
                $('#area').html('<option value="" aria-readonly="true">Select Area</option>');
                $('#area').val('');
            }
        }

        if (old_country) {
            getGovernorateList(old_governorate, old_area);
            old_country = "";
        }

        $(document).ready(function() {
            $('.select-amenities').select2({
                closeOnSelect: false,
                dropdownAutoWidth: true
            });

            document.getElementById('latitude').onkeypress = function(e) {
                // 46 is the keypress keyCode for period
                if (e.keyCode === 46 && this.value.split('.').length === 2) {
                    return false;
                }
            }

            document.getElementById('longitude').onkeypress = function(e) {
                // 46 is the keypress keyCode for period
                if (e.keyCode === 46 && this.value.split('.').length === 2) {
                    return false;
                }
            }

            $(function() {
                // Multiple images preview with JavaScript
                var previewImages = function(input, imgPreviewPlaceholder) {

                    var imageUploadLimit = getFileUploadLimit();
                    if (input.files) {
                        var filesAmount = input.files.length;
                        $(imgPreviewPlaceholder).html("");

                        if(filesAmount==0){
                            $(".uploaded_images").hide();
                        }
                        else if(filesAmount > imageUploadLimit){
                            $('#property_images').val("");
                            var messageToShow;
                            if(imageUploadLimit>0){
                                messageToShow = 'You can now upload only '+imageUploadLimit+' images.'
                            }else{
                                messageToShow = "You can not upload images now."
                            }
                            $(".uploaded_images").hide();
                            $("#property_images_input_error").html(`<span style="color: red;" class='validate'>{{__('backend.propertyMaxImageLimitExceded')}} ${messageToShow} </span>`);

                            {{--  setTimeout(()=>{
                                $("#property_images_input_error").hide(1200, () => {
                                    $("#property_images_input_error").html("");
                                })
                            },3000);  --}}
                        }
                        else{
                            $("#property_images_input_error").html("");
                            for (i = 0; i < filesAmount; i++) {
                                var reader = new FileReader();
                                reader.onload = function(event) {
                                    $($.parseHTML('<img>')).attr('src', event.target.result).css({
                                        'height': '200px',
                                        'width': 'auto',
                                        'margin': '10px'
                                    }).appendTo(imgPreviewPlaceholder);
                                }
                                reader.readAsDataURL(input.files[i]);
                            }
    
                            $(".uploaded_images").show();
                        }

                    }
                };
                $('#property_images').on('change', function() {
                    previewImages(this, 'div.images-preview-div');
                });
            });

            if(getFileUploadLimit() <= 0){
                let messageToShow = "You can not upload images now.";
                $("#property_images_input_error").html(`<span style="color: red;" class='validate'>{{__('backend.propertyMaxImageLimitExceded')}} ${messageToShow} </span>`);
                $('#property_images').attr('disabled',true);
            }

            $("#editForm").on("submit", function (e){ 
                
            })

        });

        function getDeletedImagesCount(commonClass="deleted"){
            var total_deleted = $(`.${commonClass}`).filter(function(){
                return +this.value == 1;
            }).length;
            return total_deleted;
        }

        function deleteImage(element){
            var __element = $(element);
            var image_id = __element.data('image_id');
            document.getElementById('property_photo_'+image_id).style.display='none';
            document.getElementById('property_photo_'+image_id).style.display='none';
            {{--  document.getElementById('property_photo_delete_'+image_id).value='1';  --}}
            document.getElementById('is_deleted_'+image_id).checked = true;
            document.getElementById('undo_'+image_id).style.display='block';
        }

        function undoDeleteImage(element){
            var __element = $(element);
            var image_id = __element.data('image_id');

            document.getElementById('property_photo_'+image_id).style.display='block';
            {{--  document.getElementById('property_photo_delete_'+image_id).value='0';  --}}
            document.getElementById('is_deleted_'+image_id).checked = false;
            document.getElementById('undo_'+image_id).style.display='none';
        }
        
    </script>
@endpush
